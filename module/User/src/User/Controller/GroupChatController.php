<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class GroupChatController extends AbstractActionController
{
    public function indexAction()
    {
        $form = new \Zend\Form\Form;
        $form->setAttributes(array(
            'class' => 'container form-horizontal',
            'id'    => 'chatForm',
        ));
        
        $form->add(array(
            'name' => 'message',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
                'id'    => 'text',
            ),
            'options' => array(
                'label' => 'Your message:',
            ),
        ));
        
        $form->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Send Message',
            ),            
        ));
        
        $view = new ViewModel(array(
            'form' => $form,
        ));
        $view->setTerminal(false);
        return $view;
    }
    
    public function messagesListAction()
    {
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $chatMessagesTG = $sm->get('ChatMessagesTableGateway');
        
        $allMessages = $chatMessagesTG->select();
        $messagesList = array();
        foreach ($allMessages as $currentMessage) {
            $user = $userTable->getUserByColumn(array('id' => $currentMessage->user_id));
            $messageData = array();
            $messageData['user']    = $user->name;
            $messageData['message'] = $currentMessage->message;
            $messageData['stamp']   = $currentMessage->stamp;
            $messagesList[] = $messageData;
        }
    
    
        $view = new ViewModel(array(
            'messagesList' => $messagesList,
        ));
        $view->setTemplate('user/group-chat/chat-list');
        $view->setTerminal(true);
        return $view;
    }
    
    public function addMessageAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $this->redirect()->toRoute('user/chat');
        }
        
        $sm = $this->getServiceLocator();
        $user = $sm->get('LoggedInUser');
        
        $messageGroup = array();
        $messageGroup['user_id'] = $user->id;
        $messageGroup['message'] = trim(strip_tags($request->getPost()->get('message')));
        $messageGroup['stamp']   = date('Y-m-d H:i:s');
        
        $chatMessagesTG = $sm->get('ChatMessagesTableGateway');
        if (is_array($messageGroup) && !empty($messageGroup)) {
            $chatMessagesTG->insert($messageGroup);
        }
        
        return $this->redirect()->toRoute('user/chat');
    }
}
