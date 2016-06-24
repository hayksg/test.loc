<?php

namespace User\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct()
    {
        parent::__construct('login');
        
        $this->setAttributes(array(
            'class' => 'form-horizontal',
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
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Login',
                'class' => 'btn btn-default',
            ),
        ));
    }
}
