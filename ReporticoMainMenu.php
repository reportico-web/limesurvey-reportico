<?php
class ReporticoMainMenu extends LimeSurvey\Menu\Menu {

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
