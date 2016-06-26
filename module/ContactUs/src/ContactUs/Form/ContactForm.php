<?php

namespace ContactUs\Form;

use Zend\Form\Form;

class ContactForm extends Form
{
    public function __construct()
    {
        parent::__construct('index');
        
        $this->setAttributes(array(
            'class' => 'form-horizontal',
        ));
        
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
                'id'    => 'name',
            ),
            'options' => array(
                'label' => 'Name:',
            ),
        ));
        
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'temailext',
                'class' => 'form-control',
                'id'    => 'email',
            ),
            'options' => array(
                'label' => 'Email:',
            ),
        ));
        
        $this->add(array(
            'name' => 'subject',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
                'id'    => 'subject',
            ),
            'options' => array(
                'label' => 'Subject:',
            ),
        ));
        
        $this->add(array(
            'name' => 'message',
            'type' => 'textarea',
            'attributes' => array(             
                'class' => 'form-control',
                'id'    => 'message',
            ),
            'options' => array(
                'label' => 'Message:',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Send',
            ),           
        ));
    }
}
