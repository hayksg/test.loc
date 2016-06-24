<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use User\Form\LoginForm;

class LoginController extends AbstractActionController
{
    protected $authService;
    
    public function indexAction()
    {
        $form = new LoginForm();
        
        $view = new ViewModel();
        $view->setVariables(array(
            'form' => $form,
        ));
        return $view;
    }
    
    public function processAction()
    {
        $message = '';
        
        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute('home');
        }
        
        $sm = $this->getServiceLocator();
        $form = $sm->get('LoginForm');
        $form->setData($this->request->getPost());
        
        if ($form->isValid()) {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            
            $this->getAuthService($sm)->getAdapter()->setIdentity($email);
            $this->getAuthService($sm)->getAdapter()->setCredential($password);
            $result = $this->getAuthService($sm)->authenticate();
            
            if ($result->isValid()) {
                $this->getAuthService($sm)->getStorage()->write($email);
                return $this->redirect()->toRoute('home');
            } else {
                $message = 'Incorrect Email Address or Password';
                $error = false;
            } 
        }
        
        $view = new ViewModel(array(
            'error'   => $error,
            'form'    => $form,
            'message' => $message,
        ));
        $view->setTemplate('user/login/index');
        return $view;
    }
    
    public function logoutAction()
    {
        $sm = $this->getServiceLocator();
        $authService = $this->getAuthService($sm);
        $authService->clearIdentity();
        
        return $this->redirect()->toRoute('home');
    }
    
    protected function getAuthService($sm)
    {
        if (!$this->authService) {
            $this->authService = $sm->get('AuthService');
        }
        return $this->authService;
    }
}
