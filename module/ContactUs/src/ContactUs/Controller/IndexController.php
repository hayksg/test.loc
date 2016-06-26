<?php

namespace ContactUs\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Session\Container;

use ContactUs\Form\ContactForm;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $session = new Container('sendMail');
        $sendMail = $session->message;
        $session->getManager()->getStorage()->clear('sendMail');
        
        $form = new ContactForm();
        
        $view = new ViewModel(array(
            'form' => $form,
            'sendMail' => $sendMail,
        ));
        return $view;
    }
    
    public function processAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $this->redirect()->toRoute('contact-us');
        }
        
        $sm = $this->getServiceLocator();
        $form = $sm->get('ContactForm');
        $form->setData($request->getPost());
        
        if ($form->isValid()) {
            $mail = new Mail\Message();
            $mail->setSubject($request->getPost('subject'));
            $mail->setBody($request->getPost('message'));
            $mail->setFrom($request->getPost('email'), $request->getPost('name'));
            $mail->addTo('test@test.com', 'Admin');
            
            $transport = new Mail\Transport\SendMail();
            $transport->send($mail);
            
            $session = new Container('sendMail');
            $session->message = 'Message sent';
            
            return $this->redirect()->toRoute('contact-us');
        }
    
        $view = new ViewModel(array(
            'form'  => $form,
            'error' => true,
        ));
        $view->setTemplate('contact-us/index/index');
        return $view;
    }
}
