<?php

class ViewsDatabaseModel extends ORMSQLDatabaseModel
{
    public $dataModel;
    public $dataModelName;
    
    public function postInitHook()
    {
        $this->dataModel = Model::load($this->dataModelName);
    }
    
    public function __wakeup()
    {
        $this->dataModel = Model::load($this->dataModelName);
    }
    
    public function save()
    {
        return $this->dataModel->save();
    }
    
    public function update($field, $value)
    {
        $this->dataModel->update($field, $value);
    }
    
    public function delete($field, $value)
    {
        $this->dataModel->delete($field, $value);
    }
    
    public function setData($data, $primaryKeyField = null, $primaryKeyValue = null)
    {
        return $this->dataModel->setData($data, $primaryKeyField, $primaryKeyValue);
    }
    
    public function validate()
    {
        return $this->dataModel->validate();
    } 
}