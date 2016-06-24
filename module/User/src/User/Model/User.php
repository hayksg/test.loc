<?php

namespace User\Model;

class User
{
    public $id;
    public $name;
    public $email;
    public $password;
    
    public function exchangeArray($data)
    {
        $this->id    = (isset($data['id'])) ? $data['id'] : null;
        $this->name  = (isset($data['name'])) ? $data['name'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        if (isset($data['password'])) {
            $this->encryptPassword($data['password']);
        }
    }
    
    public function encryptPassword($password)
    {
        $this->password = md5($password);
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
