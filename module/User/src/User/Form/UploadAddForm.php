<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class UploadAddForm extends Form
{
    public function __construct()
    {
        parent::__construct('ManageUpload');
        
        $this->setAttributes(array(
            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data',
        ));
        
        $label = new Element\Text('label');
        $label->setLabel('File Description:');
        $label->setLabelAttributes(array(
            'class' => 'control-label',
        ));
        $label->setAttributes(array(
            'class'    => 'form-control',
            'required' => 'required',
            'id'       => 'label',
        ));
        $this->add($label);
        
        $filename = new Element\File('filename');
        $filename->setLabel('Upload File:');
        $filename->setLabelAttributes(array(
            'class' => 'control-label',
        ));
        $filename->setAttributes(array(
            'class'    => 'jfilestyle',
            'required' => 'required',
            'id'       => 'filename',
        ));
        $this->add($filename);
        
        $submit = new Element\Submit('submit');      
        $submit->setAttributes(array(
            'class' => 'btn btn-default',
            'value' => 'Add Upload',
        ));
        $this->add($submit);
    }
}
