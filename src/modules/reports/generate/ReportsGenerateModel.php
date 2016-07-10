<?php

class ReportsGenerateModel extends ORMSQLDatabaseModel
{
    public $database = '.generation_view';
    
    public function save() 
    {
        $generateTable = Model::load(".tables.generate");
        $generateTable->setData($this->datastore->data);
        $generateTable->save();
    }
    
    public function update($field, $value) 
    {
        $generateTable = Model::load(".tables.generate");
        $generateTable->setData($this->datastore->data);
        $generateTable->update($field, $value);
    }
}