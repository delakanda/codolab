<?php

class System
{
    public static function getNextValue($sequence, $redirect = false)
    {
        if($redirect)
        {
            global $packageSchema;
            $sequence = "$packageSchema.$sequence";
        }
       
        $sequences = Model::load("system.sequences");
        return $sequences->nextval($sequence);
    }
    
    public static function getConfiguration($name, $redirect = false)
    {
        if($redirect)
        {
            global $productName;
            $name = "$productName.$name";
        }
        
        $configurations = Model::load("system.configurations");
        $value = $configurations->get(["fields" => "value","conditions" => "key = $name"]);
        return reset($value);
    }
}