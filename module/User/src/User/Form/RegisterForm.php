<?php

namespace User\Form;

use Zend\Form\Form;

class RegisterForm extends Form
{
    public function __construct()
    {
        parent::__construct('register');
        
        $this->setAttributes(array(
            'class' => 'form-horizontal',
        ));
        
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type'  => 'text',
                'id'    => 'name',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Name:',
            ),
        ));
        
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'email',
                'id'    => 'email',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Email:',
            ),
        ));
        
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
                'id'    => 'password',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Password:',
            ),
        ));
        
        $this->add(array(
            'name' => 'confirmPassword',
            'attributes' => array(
                'type'  => 'password',
                'id'    => 'confirmPassword',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Confirm Password:',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Register',
                'class' => 'btn btn-default',
            ),    
        ));
    }    
}
