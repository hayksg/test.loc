<?php

namespace ContactUs\Form;

use Zend\InputFilter\InputFilter;

class ContactFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name' => 'name',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'utf-8',
                        'min' => 1,
                        'max' => 100,
                    ),
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'email',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'EmailAddress',
                    'messages' => array(
                        \Zend\Validator\EmailAddress::INVALID_FORMAT => 'Email Address format is not valid',
                    ),
                    'domain' => true,
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'subject',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'utf-8',
                        'min' => 1,
                        'max' => 100,
                    ),
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'message',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'utf-8',
                        'min' => 1,
                        'max' => 100,
                    ),
                ),
            ),
        ));   
    }    
}
