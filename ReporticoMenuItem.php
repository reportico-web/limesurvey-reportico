<?php
class ReporticoMenuItem extends LimeSurvey\Menu\MenuItem {

    public function __construct($options) {
        parent::__construct($options);
 
    }

    function getLabel()
    {
        return "Report Menu";
    }
}
