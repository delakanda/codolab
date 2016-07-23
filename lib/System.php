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
    
    public static function addSubDays($date, $days, $operator = "+", $return = 'd/m/Y' )
    {
        $operand = intval($days); 
        $timestamp = is_numeric($date) ? $date : Utils::stringToTime($date);
       
        if($return == 'timestamp')
        {
            return strtotime("$operator $operand days", $timestamp);
        }
        
        return date($return, strtotime("$operator $operand days", $timestamp));
    }
    
    public static function dateDifference($startDate, $endDate)
    {
        $start = is_numeric($startDate) ? $startDate : Utils::stringToTime($startDate);
        $end = is_numeric($endDate) ? $endDate : Utils::stringToTime($endDate);
        
        return floor(($end - $start) / 86400);
    }
    
    
}