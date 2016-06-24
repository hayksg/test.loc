<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class UploadTable extends TableGateway
{
    protected $tableGateway;
    protected $uploadSharingTableGateway;
    
    public function __construct(TableGateway $tableGateway, TableGateway $uploadSharingTableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->uploadSharingTableGateway = $uploadSharingTableGateway;
    }
    
    public function getAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function getUploadByColumns($dataArray)
    {
        $rowSet = $this->tableGateway->select($dataArray);
        $row = $rowSet->current();
        
        if (!$row) {
            return false;
        }
        return $row;
    }
    
    public function getUploadsByColumns($dataArray)
    {
        $rowSet = $this->tableGateway->select($dataArray);        
    
        if (!$rowSet) {
            return false;
        }
        return $rowSet;
    }
    
    public function saveUpload(Upload $upload)
    {
        $data = array(
            'filename' => $upload->filename,
            'label'    => $upload->label,
            'user_id'  => $upload->user_id,
        );
        $id = (int)$upload->id;
        
        if ($id == 0) {
            $this->tableGateway->insert($data);
            return $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getUploadByColumns(array('id' => $id))) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Row id does not exist');
            }
        } 
    }
    
    public function editUpload(Upload $upload)
    {
        $data = array(            
            'label' => $upload->label,            
        );
        $id = (int)$upload->id;
        
        if ($this->getUploadByColumns(array('id' => $id))) {
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('Row id does not exist');
        } 
    }
    
    public function deleteUploadByColumns($dataArray)
    {
        return $this->tableGateway->delete($dataArray); 
    }
    
    public function addSharing($uploadId, $userId)
    {
        $data = array(
            'upload_id' => (int)$uploadId,
            'user_id'   => (int)$userId,
        );
        
        $this->uploadSharingTableGateway->insert($data);
        return $this->uploadSharingTableGateway->getLastInsertValue();
    }
    
    public function removeSharing($uploadId, $userId)
    {
        $data = array(
            'upload_id' => (int)$uploadId,
            'user_id'   => (int)$userId,
        );
        
        $this->uploadSharingTableGateway->delete($data);   
    }
    
    public function getSharedUploadByColumns($dataArray)
    {
        $rowSet = $this->uploadSharingTableGateway->select($dataArray);
        $row = $rowSet->current();
    
        if (!$row) {
            return false;
        }
        return $row;
    }
    
    public function getSharedUploads($uploadId)
    {
        $uploadId = (int)$uploadId;
        $rowSet = $this->uploadSharingTableGateway->select(array('upload_id' => $uploadId));
        
        if (!$rowSet) {
            return false;
        }
        return $rowSet;
    }
    
    public function sharedUploadsForUser($userId)
    {
        $userId = (int)$userId;
        $rowSet = $this->uploadSharingTableGateway->select(
                      function (Select $select) use ($userId) {
                          $select->columns(array())
                                 ->join('upload', 'upload_sharing.upload_id = upload.id')
                                 ->where(array('upload_sharing.user_id' => $userId));
                      }
                  );
        
        if (!$rowSet) {
            return false;
        }
        return $rowSet;
    }
}
