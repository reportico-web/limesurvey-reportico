<?php
class ReporticoAdminMenuItem extends LimeSurvey\Menu\MenuItem {

    public function __construct($options) {
        parent::__construct($options);
 
    }

    function getLabel()
    {
        return "Admin";
    }

    function getHref()
    {
        return Yii::app()->getController()->createUrl('admin/pluginhelper', array('plugin' => 'Reportico', 'sa'=>'fullpagewrapper','method'=>'admin', 'surveyId'=>0));
        //return Yii::app()->getController()->createUrl('admin/', array('plugin' => 'Reportico', 'sa'=>'fullpagewrapper','method'=>'admin', 'surveyId'=>0));

    }
}
