<?php

class Utils
{
    private static $singulars = array();
    private static $plurals = array();
    
    /**
     * Returns the sigular form of any plural english word which is passed to it.
     * 
     * @param string $word
     * @see Utils::plural
     */
    public static function singular($word)
    {
        $singular = array_search($word, Utils::$singulars);
        if($singular == false)
        {
            if(substr($word, -3) == "ses")
            {
                $singular = substr($word, 0, strlen($word) - 2);
            }
            elseif(substr($word, -3) == "ies")
            {
                $singular = substr($word, 0, strlen($word) - 3) . "y";
            }
            elseif(strtolower($word) == "indices")
            {
                $singular = "index";
            }
            else if(substr(strtolower($word), -4) == 'news')
            {
                $singular = $word;
            }
            else if(substr(strtolower($word), -8) == 'branches')
            {
                $singular = substr($word, 0, strlen($word) - 2);
            }
            else if(substr($word, -1) == "s")
            {
                $singular = substr($word, 0, strlen($word) - 1);
            }
            else
            {
                $singular = $word;
            }
            Utils::$singulars[$singular] = $word;
        }
        return $singular;
    }

    /**
     * Returns the plural form of any singular english word which is passed to it.
     * 
     * @param string $word
     */
    public static function plural($word)
    {
        $plural = array_search($word, Utils::$plurals);
        if($plural === false)
        {
            if(substr($word, -1) == "y")
            {
                $plural = substr($word, 0, strlen($word) - 1) . "ies";
            }
            elseif(strtolower($word) == "index")
            {
                $plural = "indices";
            }            
            elseif(substr($word, -2) == "us")
            {
                $plural = $word . "es";
            } 
            elseif(substr($word, -2) == "ss")
            {
                $plural = $word . "es";
            }
            elseif(substr($word, -1) != "s")
            {
                $plural = $word . "s";
            }
            else
            {
                throw new exceptions\UnknownPluralException("Could not determine the plural for $word");
            }
            Utils::$plurals[$plural] = $word;
        }
        return $plural;
    }    
    
    /**
     * Converts a string time representation of the format DD/MM/YYY [HH:MI:SS]
     * into a unix timestamp. The conversion is done with the strtotime()
     * function which comes as part of the php standard library.
     *
     * @param string $string The date
     * @param boolean $hasTime When specified, the time components are also added
     * @return int
     */
    public static function stringToTime($string, $hasTime = false)
    {
        if(preg_match("/(\d{2})\/(\d{2})\/(\d{4})(\w\d{2}:\d{2}:\d{2})?/", $string) == 0) return false;
        $dateComponents = explode(" ", $string);

        $decomposeDate = explode("/", $dateComponents[0]);
        $decomposeTime = array();

        if($hasTime === true)
        {
            $decomposeTime = explode(":", $dateComponents[1]);
        }

        return
        strtotime("{$decomposeDate[2]}-{$decomposeDate[1]}-{$decomposeDate[0]}") +
        ($hasTime === true ? ($decomposeTime[0] * 3600 + $decomposeTime[1] * 60 + $decomposeTime[2]) : 0);
    }

    /**
     * Converts a string time representation of the format DD/MM/YYY [HH:MI:SS]
     * into an oracle date format DD-MON-YY.
     *
     * @param string $string The date
     * @param boolean $hasTime When specified, the time components are also added
     * @todo Allow the returning of the time values too.
     * @return string
     */
    public static function stringToDatabaseDate($string, $hasTime = false)
    {
        $timestamp = self::stringToTime($string, $hasTime);
        return date("Y-m-d", $timestamp);
    }    
    
    public static function currency($number)
    {
        return number_format(self::round($number,2),2,'.',',');
    }

    public static function deCommalize($number)
    {
        return str_replace(',', '', $number);
    }
    
    public static function convert_number($number)
    {
        if (($number < 0) || ($number > 9999999999))
        {
            throw new Exception("Number is out of range");
        }

        $Bn = floor($number / 1000000000);  /* Billions (tera) */
        $number -= $Bn * 1000000000;
        $Gn = floor($number / 1000000);  /* Millions (giga) */
        $number -= $Gn * 1000000;
        $kn = floor($number / 1000);     /* Thousands (kilo) */
        $number -= $kn * 1000;
        $Hn = floor($number / 100);      /* Hundreds (hecto) */
        $number -= $Hn * 100;
        $Dn = floor($number / 10);       /* Tens (deca) */
        $n = $number % 10;               /* Ones */

        $res = "";

        if ($Bn)
        {
            $res .= self::convert_number($Bn) . " Billion";
        }

        if ($Gn)
        {
            $res .= self::convert_number($Gn) . " Million";
        }

        if ($kn)
        {
            $res .= (empty($res) ? "" : " ") .
            self::convert_number($kn) . " Thousand";
        }

        if ($Hn)
        {
            $res .= (empty($res) ? "" : " ") .
            self::convert_number($Hn) . " Hundred";
        }

        $ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
        "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
        "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen",
        "Nineteen");
        $tens = array("", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty",
        "Seventy", "Eighty", "Ninety");

        if ($Dn || $n)
        {
            if (!empty($res))
            {
                $res .= " and ";
            }

            if ($Dn < 2)
            {
                $res .= $ones[$Dn * 10 + $n];
            }
            else
            {
                $res .= $tens[$Dn];
                if ($n)
                {
                    $res .= "-" . $ones[$n];
                }
            }
        }

        if (empty($res))
        {
            $res = "zero";
        }

        return $res;
    }

    public static function time()
    {
    	global $forcedTime;
    	return $forcedTime != null ? $forcedTime : time();
    }

    function round( $value, $precision=0 )
    {
        if ($precision == 0) 
        {
            $precisionFactor = 1;
        }
        else 
        {
            $precisionFactor = pow( 10, $precision );
        }

        return round( $value * $precisionFactor )/$precisionFactor;
    }
    
    public function sentenceTime($time, $options = null)
    {
        $elapsed = time() - $time;

        if($elapsed < 10)
        {
            $englishDate = 'now';
        }
        elseif($elapsed >= 10 && $elapsed < 60)
        {
            $englishDate = "$elapsed seconds";
        }
        elseif($elapsed >= 60 && $elapsed < 3600)
        {
            $minutes = floor($elapsed / 60);
            $englishDate = "$minutes minutes";
        }
        elseif($elapsed >= 3600 && $elapsed < 86400)
        {
            $hours = floor($elapsed / 3600);
            $englishDate = "$hours hour" . ($hours > 1 ? 's' : '');
        }
        elseif($elapsed >= 86400 && $elapsed < 172800)
        {
            $englishDate = "yesterday";
        }
        elseif($elapsed >= 172800 && $elapsed < 604800)
        {
            $days = floor($elapsed / 86400);
            $englishDate = "$days days";
        }
        elseif($elapsed >= 604800 && $elapsed < 2419200)
        {
            $weeks = floor($elapsed / 604800);
            $englishDate = "$weeks weeks";
        }
        elseif($elapsed >= 2419200 && $elapsed < 31536000)
        {
            $months = floor($elapsed / 2419200);
            $englishDate = "$months months";
        }
        elseif($elapsed >= 31536000)
        {
            $years = floor($elapsed / 31536000);
            $englishDate = "$years years";
        }

        switch($options['elaborate_with'])
        {
            case 'ago':
                if($englishDate != 'now' && $englishDate != 'yesterday')
                {
                    $englishDate .= ' ago';
                }
                break;
        }

        return $englishDate;
    }
}
