<?php
class ReporticoMainMenu extends \ls\menu\Menu {

    public function __construct($options) {
        parent::__construct($options);
 
    }

    function getLabel()
    {
        return "Reportico";
    }

    function isDropdown()
    {
        return true;
    }
}
