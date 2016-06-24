<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use User\Form\RegisterForm;
use User\Model\User;

class RegisterController extends AbstractActionController
{
    public function indexAction() 
    {
        $form = new RegisterForm();
        
        $view = new ViewModel();
        $view->setVariables(array(
            'form' => $form,
        ));
        
        return $view;
    } 
    
    public function processAction()
    {
        $messages = [];
        
        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute('home');
        }
        
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        
        $form = $sm->get('RegisterForm');
        $form->setData($this->request->getPost());
        
        if ($userTable->getUserByColumn(array('email' => $this->request->getPost('email')))) {
            $messages[] = 'Email address exists already';
        }
        if ($this->request->getPost('password') !== $this->request->getPost('confirmPassword')) {
            $messages[] = 'Passwords do not match';
        }
        
        if ($form->isValid() && empty($messages)) {
            $user = new User();
            $user->exchangeArray($form->getData());

            $userTable->saveUser($user);
            
            return $this->redirect()->toRoute(null, array(
                'controller' => 'register',
                'action'     => 'confirm',
            ));
        }
        
        $view = new ViewModel(array(
            'error' => true,
            'form'  => $form,
            'messages'  => $messages,
        ));
        $view->setTemplate('user/register/index');
        return $view;
    }
    
    public function confirmAction()
    {       
        $view = new ViewModel();         
        return $view;
    }
}
