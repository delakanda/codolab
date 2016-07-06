<?php

class System
{
    public static function getNextValue($sequence)
    {
        global $redirectedPackage;
        global $packageSchema;
        
        $name = $redirectedPackage != '' ? "$packageSchema.$sequence" : $sequence;
       
        $sequences = Model::load("system.sequences");
        return $sequences->nextval($name);
    }
    
    public static function getConfiguration($name)
    {
        global $redirectedPackage;
        global $productName;

        $key = $redirectedPackage != '' ? "$productName.$name" : $name;
        
        $configurations = Model::load("system.configurations");
        $value = $configurations->get(["fields" => "value","conditions" => "key = '$key'"]);
        
        return $value[0]['value'];
    }
}