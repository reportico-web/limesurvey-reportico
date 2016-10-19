<?php
class ReporticoAdminMenuItem extends \ls\menu\MenuItem {

    public function __construct($options) {
        parent::__construct($options);
 
    }

    function getLabel()
    {
        return "Admin";
    }

    function getHref()
    {
        return Yii::app()->getController()->createUrl('admin/pluginhelper', array('plugin' => 'Reportico', 'sa'=>'sidebody','method'=>'admin', 'surveyId'=>0));

    }
}
