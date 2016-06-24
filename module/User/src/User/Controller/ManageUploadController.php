<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\File\Transfer\Adapter\Http;
use Zend\Session\Container;

use User\Model\Upload;

class ManageUploadController extends AbstractActionController
{
    public function indexAction()
    {
        $session = new Container('deleteUpload');
        $deleteUpload = $session->message;
        $session->getManager()->getStorage()->clear('deleteUpload');

        $user = $this->getLoggedInUser();
        
        $sm = $this->getServiceLocator();
        $userTable   = $sm->get('UserTable');
        $uploadTable = $sm->get('UploadTable');
        $myUploads = $uploadTable->getUploadsByColumns(array('user_id' => $user->id));
        
        $sharedUploads = $uploadTable->sharedUploadsForUser($user->id);
        $sharedList = array();
        foreach ($sharedUploads as $sharedUpload) {
            $sharedUploadOwner = $userTable->getUserByColumn(array('id' => $sharedUpload->user_id));
            $sharedData = array();
            $sharedData['owner'] = $sharedUploadOwner->name;
            $sharedData['label'] = $sharedUpload->label;
            $sharedList[$sharedUpload->id] = $sharedData;
        }
               
        $view = new ViewModel(array(
            'myUploads'    => $myUploads,
            'deleteUpload' => $deleteUpload,
            'sharedList'   => $sharedList,
        ));
        return $view;
    }
    
    public function addAction()
    {
        $error = false;
        $message = '';
        
        $sm = $this->getServiceLocator();
        $form = $sm->get('UploadAddForm');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            $uploadTable = $sm->get('UploadTable');
            
            $fileArray = $this->params()->fromFiles('filename', '');
            $fileName = $fileArray['name'];
            $filePath = $this->getUploadLocation();
            
            if ($uploadTable->getUploadByColumns(array('filename' => $fileName))) {
                if (mb_strlen($fileName) > 10) {
                    $message = mb_substr($fileName, 0, 10) . '....';
                } else {
                    $message = $fileName;
                }
            }
            
            if ($form->isValid() && !$message) {   
                $adapter = new Http();
                $adapter->setDestination($filePath);
                
                if ($adapter->receive($fileName)) {
                    $user = $this->getLoggedInUser();
                    
                    $dataArray = array();
                    $dataArray['label']    = $request->getPost('label');
                    $dataArray['filename'] = $fileName;
                    $dataArray['user_id']  = $user->id;
                    
                    $upload = new Upload();
                    $upload->exchangeArray($dataArray);
 
                    $uploadTable->saveUpload($upload);
                }
                return $this->redirect()->toRoute('user/upload');  
            } else {
                $error = true;
            }
        }

        $view = new ViewModel(array(
            'error'   => $error,
            'form'    => $form,
            'message' => $message,
        ));
        return $view;
    }
    
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('user/upload');
        }
        
        $session = new Container('UploadSharedAlready');
        $uploadSharedAlready = $session->message;
        $session->getManager()->getStorage()->clear('UploadSharedAlready');
        
        $session = new Container('deleteSharing');
        $deleteSharing = $session->message;
        $session->getManager()->getStorage()->clear('deleteSharing');
        
        $sm = $this->getServiceLocator();
        $uploadTable = $sm->get('UploadTable');
        $upload = $uploadTable->getUploadByColumns(array('id' => $id));
        
        $form = $sm->get('UploadEditForm');
        $form->bind($upload);
        
        //////////   For section 'Add Sharing'   //////////////////////////////
        
        $uploadAddSharingForm = $sm->get('UploadAddSharingForm');
        $userTable = $sm->get('UserTable');
        $allUsers = $userTable->getAll();
        $usersList = array();
        foreach ($allUsers as $currentUser) {
            if ($upload->user_id == $currentUser->id) continue;
            $usersList[$currentUser->id] = $currentUser->name;
        }
        
        $uploadAddSharingForm->get('uploadId')->setValue($id);
        $uploadAddSharingForm->get('userId')->setValueOptions($usersList);
        
        //////////   End section 'Add Sharing'   //////////////////////////////
        
        //////////   For section 'Members who have access to this upload'   ///
        
        $sharedUploads = $uploadTable->getSharedUploads($id);
        $sharedData = array();
        foreach ($sharedUploads as $sharedUpload) {           
            $sharedForUser = $userTable->getUserByColumn(array('id' => $sharedUpload->user_id));
            $sharedData[$sharedUpload->id] = $sharedForUser->name;
        }
        
        //////////   End section 'Members who have access to this upload'   ///
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $uploadTable->editUpload($upload);
                return $this->redirect()->toRoute('user/upload');
            }
        }

        $view = new ViewModel(array(
            'id'   => $id,
            'form' => $form,
            'uploadAddSharingForm' => $uploadAddSharingForm,
            'uploadSharedAlready'  => $uploadSharedAlready,
            'sharedData' => $sharedData,
            'deleteSharing' => $deleteSharing,
        ));
        return $view;
    }
    
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $request = $this->getRequest();
        if (!$id || !$request->isPost()) {
            return $this->redirect()->toRoute('user/upload');
        }
        
        $sm = $this->getServiceLocator();
        $uploadTable = $sm->get('UploadTable');
        
        $filePath = $this->getUploadLocation();
        $upload = $uploadTable->getUploadByColumns(array('id' => $id));
        
        unlink($filePath . '/' . $upload->filename);
        
        $session = new Container('deleteUpload');
        $session->message = 'Upload successfully deleted';

        $uploadTable->deleteUploadByColumns(array('id' => $id));  
        return $this->redirect()->toRoute('user/upload');
    }
    
    public function getUploadLocation()
    {
        $sm = $this->getServiceLocator();
        $config = $sm->get('config');
        return $config['module_config']['upload_location'];
    }
    
    public function getLoggedInUser()
    {
        $sm = $this->getServiceLocator();
        $authService = $sm->get('AuthService');
        $email = $authService->getStorage()->read();
        
        $userTable = $sm->get('UserTable');
        $user = $userTable->getUserByColumn(array('email' => $email));
        return $user;
    }
    
    public function downloadAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);       
        if (!$id) {
            return $this->redirect()->toRoute('user/upload');
        }
        
        $filePath = $this->getUploadLocation();
        
        $sm = $this->getServiceLocator();
        $uploadTable = $sm->get('UploadTable');
        $upload = $uploadTable->getUploadByColumns(array('id' => $id));
        
        $file = $filePath . '/' . $upload->filename;
        if (is_file($file)) {
            /*
            $downloadFile = file_get_contents($file);
            
            $response = $this->getEvent()->getResponse();
            $response->getHeaders()->addHeaders(array(
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . basename($file) . '"',
            ));
            $response->setContent($downloadFile);
            return $response;
            */
            
            $response = new \Zend\Http\Response\Stream();
            $response->setStream(fopen($file, 'r'));
            $response->setStreamName(basename($file));
            $response->setStatusCode(200);
            
            $headers = new \Zend\Http\Headers();
            $headers->addHeaders(array(
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . basename($file) . '"',
                'Content-Length' => filesize($file),
                'Cache-Control' => 'no-store, must-revalidate',
            ));
            
            $response->setHeaders($headers);
            return $response;
        }
        
        return $this->redirect()->toRoute('user/upload');
    }
    
    public function addSharingAction()
    {  
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $this->redirect()->toRoute('user/upload');
        }
        
        $uploadId = (int)$request->getPost('uploadId');
        $userId   = (int)$request->getPost('userId');
        
        $sm = $this->getServiceLocator();
        $uploadTable = $sm->get('UploadTable');
        
        if ($uploadTable->getSharedUploadByColumns(array('upload_id' => $uploadId, 'user_id' => $userId))) {
            $session = new Container('UploadSharedAlready');
            $session->message = 'Upload shared already';
        } else {
            $uploadTable->addSharing($uploadId, $userId);
        }
        
        return $this->redirect()->toRoute('user/upload', array(
            'action' => 'edit',
            'id'     => $uploadId,
        ));
    }
    
    public function deleteSharingAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $request = $this->getRequest();
        if (!$id || !$request->isPost()) {
            return $this->redirect()->toRoute('user/upload');
        }
        
        $sm = $this->getServiceLocator();
        $uploadTable = $sm->get('UploadTable');
        $upload = $uploadTable->getSharedUploadByColumns(array('id' => $id));
        
        $uploadId = $upload->upload_id;
        $userId   = $upload->user_id;
        
        $session = new Container('deleteSharing');
        $session->message ='Sharing for user canceled';
        
        $uploadTable->removeSharing($uploadId, $userId);
        
        return $this->redirect()->toRoute('user/upload', array(
            'action' => 'edit',
            'id'     => $uploadId,
        ));
    }
}
