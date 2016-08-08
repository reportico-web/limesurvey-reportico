<?php
class ReporticoMenuItem extends \ls\menu\MenuItem {

    public function __construct($options) {
        parent::__construct($options);
 
    }

    function getLabel()
    {
        return "Report Menu";
    }
}
