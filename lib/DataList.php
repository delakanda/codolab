<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class DataList extends SelectionList{
    public function __construct($model, $label, $nameField, $idField, $getParams=array()){
        parent::__construct($label, $idField);
        $dataModel=Model::load($model);
        $data=$dataModel->get($getParams);
        foreach($data as $item){
            $this->addOption($item[$nameField], $item[$idField]);
        }
    }
}

