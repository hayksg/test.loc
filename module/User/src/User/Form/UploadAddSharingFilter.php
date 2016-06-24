<?php

namespace User\Form;

use Zend\InputFilter\InputFilter;

class UploadAddSharingFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name' => 'uploadId',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
    }
}
