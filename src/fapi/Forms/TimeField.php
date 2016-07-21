<?php
/**
 *A field for entering time-of-day values
 */
class TimeField extends TextField
{
    /**
     * Creates a new TimeField
     * @param type $label The label for the time field
     * @param type $name The name of the field
     * @param type $description A brief description of the field
     */
    public function __construct($label="",$name="",$description="")
    {
        parent::__construct($label,$name,$description);
    }

    public function render()
    {
        $this->addAttribute("type", "text");
        $this->addAttribute("pattern", "([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?");
        $this->addAttribute("placeholder", "00:00 (24-hr format)");
        $this->addAttribute("id" , $this->getId());
        $this->addAttribute("name" , $this->getName());
        $this->addAttribute("value" , $this->getValue());
        return "<input ".$this->getAttributes()." />";
    }

   public function setValue($value)
    { 
        if(preg_match("/([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?/", $value) == 1)
        {
           parent::setValue($value);
           return $this;
        }
       else{
           parent::setValue(false);
           return $this;
       }
    }
}

