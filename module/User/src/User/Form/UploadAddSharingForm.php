<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class UploadAddSharingForm extends Form
{
    public function __construct()
    {
        parent::__construct('ManageUpload');
        
        $this->setAttributes(array(
            'class' => 'form-horizontal',
        ));
        
        $uploadId = new Element\Hidden('uploadId');
        $this->add($uploadId);
        
        $userId = new Element\Select('userId');
        $userId->setLabel('Choose User:');
        $userId->setLabelAttributes(array(
            'class' => 'control-label',
        ));
        $userId->setAttributes(array(
            'class' => 'form-control',
        ));
        $this->add($userId);
        
        $submit = new Element\Submit('submit');        
        $submit->setAttributes(array(
            'class' => 'btn btn-default',
            'value' => 'Add Sharing',
        ));
        $this->add($submit);
    }
}
