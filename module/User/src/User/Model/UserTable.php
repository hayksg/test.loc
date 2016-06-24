<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

use User\Model\User;

class UserTable extends TableGateway
{
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function getAll($paginated = false)
    {
        if ($paginated) {
            $select = new Select('user');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new User());
            $paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function getUserByColumn($dataArray)
    {
        $rowSet = $this->tableGateway->select($dataArray);
        $row = $rowSet->current();
        
        if (!$row) {
            return false;
        }
        return $row;
    }
    
    public function saveUser(User $user)
    {
        $data = array(
            'name'     => $user->name,
            'email'    => $user->email,
            'password' => $user->password,
        );
        $id = (int)$user->id;
        
        if ($id == 0) {
            $this->tableGateway->insert($data);
            return $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getUserByColumn(array('id' => $id))) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Row id does not exist');
            }
        }
    }
    
    public function deleteUserByColumn($dataArray)
    {
        return $this->tableGateway->delete($dataArray);
    }
}
