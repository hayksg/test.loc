<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class UploadEditForm extends Form
{
    public function __construct()
    {
        parent::__construct('ManageUpload');
        
        $this->setAttributes(array(
            'class' => 'form-horizontal',
        ));
        
        $id = new Element\Hidden('id');
        $this->add($id);
        
        $label = new Element\Text('label');
        $label->setLabel('File Description:');
        $label->setLabelAttributes(array(
            'class' => 'control-label',
        ));
        $label->setAttributes(array(
            'class' => 'form-control',
            'id'    => 'label',
        ));
        $this->add($label);
        
        $submit = new Element\Submit('submit');      
        $submit->setAttributes(array(
            'class' => 'btn btn-default',
            'value' => 'Update',
        ));
        $this->add($submit);
    }
}
