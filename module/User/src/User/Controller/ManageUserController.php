<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class ManageUserController extends AbstractActionController
{
    public function indexAction()
    {
        $session = new Container('deleteUser');
        $deleteUser = $session->message;
        $session->getManager()->getStorage()->clear('deleteUser');
        
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $paginator = $userTable->getAll(true);
        $paginator->setCurrentPageNumber((int)$this->params()->fromRoute('page', 1));
        $paginator->setItemCountPerPage(3);
        
        $view = new ViewModel(array(
            'paginator'  => $paginator,
            'deleteUser' => $deleteUser,
        ));
        return $view;
    }
    
    public function editAction()
    {
        $error = false;
        $message = '';
        
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }
              
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $user = $userTable->getUserByColumn(array('id' => $id));
        
        $form = $sm->get('UserEditForm');
        $form->bind($user);
                
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            $emailOld = $user->email;
            $emailNew = $form->get('email')->getValue();
            
            if ($userTable->getUserByColumn(array('email' => $emailNew)) && $emailNew !== $emailOld) {
                $message = 'Email Address exist already';
            }
                       
            if ($form->isValid() && !$message) {                                
                $userTable->saveUser($user);
                return $this->redirect()->toRoute('user/manage');
            } else {
                $error = true;
            }
        }
            
        $view = new ViewModel(array(
            'id'      => $id,
            'error'   => $error,
            'form'    => $form,
            'message' => $message,
        ));
        return $view;
    }
    
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $request = $this->getRequest();
        if (!$id || !$request->isPost()) {
            return $this->redirect()->toRoute('home');
        }
        
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        
        $authService = $sm->get('AuthService');
        $authService->clearIdentity();
        
        $userTable->deleteUserByColumn(array('id' => $id));
        
        $session = new Container('deleteUser');
        $session->message = 'User successfully deleted';
        
        return $this->redirect()->toRoute('user/manage');
    }
}
