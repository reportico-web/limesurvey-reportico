<?php 
class Reportico extends PluginBase {


    protected $storage = 'DbStorage';    
    static protected $description = 'Reportico plugin';

    public $engine;
    private $_assetsUrl;
    
    public function __construct(PluginManager $manager, $id) {
        parent::__construct($manager, $id);

        /**
         * Here you should handle subscribing to the events your plugin will handle
         */
        //$this->subscribe('afterPluginLoad', 'adminPage');
        //$this->subscribe('afterPluginLoad', 'ajax');
        $this->subscribe('afterAdminMenuLoad');
        //$this->subscribe('beforeAdminMenuRender');
        $this->subscribe('newDirectRequest');
    }

    public function afterAdminMenuLoad()
    {
        $event = $this->event;
        $menu = $event->get('menu', array());
        $menu['items']['left'][]=array(
                'href' => "plugins/direct/reportico?function=adminPage&plugin=Reportico",
                'alt' => gT('Reportico'),
                'image' => 'bounce.png'
            );

        $event->set('menu', $menu);

    }
    
    
    /*
     * Below are the actual methods that handle events
     */
    public function beforeAdminMenuRender()
    {
        $event = $this->getEvent();
        $event->set('extraMenus', array(
        new ReporticoMainMenu(array(
            'isDropDown' => true,
            'menuItems' => array(
                //new ReporticoMenuItem(null),
                new ReporticoAdminMenuItem(null),
            )
        ))
        ));
    }

    public function adminPage() 
    {

        //return "HelloWorld";
        $this->engine = $this->getReporticoEngine();
        $this->partialRender = Yii::app()->request->getQuery("partialReportico", false);

        // Run reportico in admin mode
        $this->engine->access_mode = "FULL";
        $this->engine->initial_execute_mode = "ADMIN";
        $this->engine->initial_project = "admin";
        $this->engine->initial_report = false;
        $this->engine->clear_reportico_session = true;

        return $this->engine->execute();
    }
    
    public function ajax() 
    {
        $this->engine = $this->getReporticoEngine();

        $this->partialRender = Yii::app()->request->getQuery("partialReportico", false);

        // Run reportico in admin mode
        //$this->engine->access_mode = "FULL";
        //$this->engine->initial_execute_mode = "ADMIN";
        //$this->engine->initial_project = "admin";
        //$this->engine->initial_report = false;
        //$this->engine->clear_reportico_session = true;
            $this->engine->reportico_ajax_script_url = $_SERVER["SCRIPT_NAME"]."/plugins/direct?plugin=Reportico&function=ajax&dummy";
//http://127.0.0.1/limesurvey/index.php/plugins/direct?plugin=Reportico&function=adminPage
        echo $this->engine->execute();
        die;
    }
    
    
    function getAssetsUrl()
    {
        if ($this->_assetsUrl === null)
        {
            //Yii::app()->getAssetManager()->forceCopy = true;
            $path = __DIR__."/assets";
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
                //Yii::getPathOfAlias('plugins.Reportico.assets')
                $path 
                //false,
                //-1,
                //true
                 );
            //copy ("./protected/modules/reportico/assets/css/reportico_bootstrap.css", "./assets/3bdc0adc/css/reportico_bootstrap.css");
            //copy ("./protected/modules/reportico/assets/js/reportico.js", "./assets/3bdc0adc/js/reportico.js");
        }

        return $this->_assetsUrl;
    }

    public function init()
    {
        // In certain scenarios .. a yii session can be in placed but not started
        // until it is used .. by accessing the session it will be invoked and then reportico
        // can make use of this. otherwise no session is found and reportico will statrt its
        // own one. This has occurred with a CDBHttpSession 
        if ( !Yii::app()->session )
            $_session_not_started = true;

        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        //$this->setImport(array(
            //'reportico.models.*',
            //'reportico.components.*',
            //'reportico.components.reportico',
        //));

        //$component=Yii::createComponent(
            //array(
            //component details here
                //'class' => 'reportico',
                //'peter' => "peter"
            //));
        //$this->setComponent("reportico", $component) ;
    }

    public function getReporticoEngine()
    {
        include_once(__DIR__."/config.php");
        require_once("components/reportico.php");

        reportico\reportico\components\set_up_reportico_session();
        $this->engine = new reportico\reportico\components\reportico();

        if ( Yii::app()->getUrlManager()->getUrlFormat() == "get" )
        {
            $this->engine->reportico_ajax_script_url = $_SERVER["SCRIPT_NAME"]."/reportico/reportico/ajax";
            $this->engine->reportico_ajax_script_url = $_SERVER["SCRIPT_NAME"]."/plugins/direct?plugin=Reportico&function=ajax";
            $this->engine->forward_url_get_parameters = false;
            $this->engine->forward_url_get_parameters_graph = "r=reportico/reportico/graph";
            $this->engine->forward_url_get_parameters_dbimage = "r=reportico/reportico/dbimage";
            $this->engine->reportico_ajax_mode = 2;
        }
        else
        {
            $this->engine->reportico_ajax_script_url = $_SERVER["SCRIPT_NAME"]."/admin/pluginhelper?plugin=Reportico&sa=sidebody&method=ajax&surveyId=0";
            $this->engine->reportico_ajax_script_url = $_SERVER["SCRIPT_NAME"]."/plugins/direct?plugin=Reportico&function=ajax";
            $this->engine->forward_url_get_parameters = false;
            $this->engine->forward_url_get_parameters_graph = "r=reportico/reportico/graph";
            $this->engine->forward_url_get_parameters_dbimage = "r=reportico/reportico/dbimage";
            $this->engine->reportico_ajax_mode = 2;
        }
        $this->engine->embedded_report = true;
        $this->engine->allow_debug = true;

        $this->engine->forward_url_get_parameters_graph = "reportico/graph";
        $this->engine->forward_url_get_parameters_dbimage = "reportico/dbimage";

        $this->engine->framework_parent = $this->configGet("framework_type");

        if ( Yii::app()->user->id )
            $this->engine->external_user = Yii::app()->user->id;
        else
            $this->engine->external_user = "guest";

        $this->engine->url_path_to_assets = $this->getAssetsUrl();

        // Where to store reportco projects
        $this->engine->projects_folder = $this->configGet("path_to_projects");
        if ( !is_dir($this->engine->projects_folder) )
        {
            $status = @mkdir($this->engine->projects_folder, 0755, true);
            if ( !$status )
            {
            if ( !$status )
                echo "Error cant create project area ".$this->engine->projects_folder."<BR>";
                die;
            }
        }
        $this->engine->admin_projects_folder = $this->configGet("path_to_admin");

        // Indicates whether report output should include a refresh button
        $this->engine->show_refresh_button = $this->configGet("show_refresh_button");

        // Jquery already included?
        $this->engine->jquery_preloaded = $this->configGet("jquery_preloaded");

        // Bootstrap Features
        // Set bootstrap_styles to false for reportico classic styles, or "3" for bootstrap 3 look and feel and 2 for bootstrap 2
        // If you are embedding reportico and you have already loaded bootstrap then set bootstrap_preloaded equals true so reportico
        // doestnt load it again.
        $this->engine->bootstrap_styles = $this->configGet("bootstrap_styles");
        $this->engine->bootstrap_preloaded = $this->configGet("bootstrap_preloaded");

        // In bootstrap enable pages, the bootstrap modal is by default used for the quick edit buttons
        // but they can be ignored and reportico's own modal invoked by setting this to true
        $this->engine->force_reportico_mini_maintains = $this->configGet("force_reportico_maintain_modals");

        // Pull in Yii CSRF request token
        $this->engine->csrfToken = Yii::app()->request->csrfToken;

        // Engine to use for charts .. 
        // HTML reports can use javascript charting, PDF reports must use PCHART
        $this->engine->charting_engine = $this->configGet("charting_engine");
        $this->engine->charting_engine_html = $this->configGet("charting_engine_html");

        // Engine to use for PDF generation
        $this->engine->pdf_engine = $this->configGet("pdf_engine");

        // Whether to turn on dynamic grids to provide searchable/sortable reports
        $this->engine->dynamic_grids = $this->configGet("dynamic_grids");
        $this->engine->dynamic_grids_sortable = $this->configGet("dynamic_grids_sortable");
        $this->engine->dynamic_grids_searchable = $this->configGet("dynamic_grids_searchable");
        $this->engine->dynamic_grids_paging = $this->configGet("dynamic_grids_paging");
        $this->engine->dynamic_grids_page_size = $this->configGet("dynamic_grids_page_size");

        // Show or hide various report elements
        $this->engine->output_template_parameters["show_hide_navigation_menu"] = $this->configGet("show_hide_navigation_menu");
        $this->engine->output_template_parameters["show_hide_dropdown_menu"] = $this->configGet("show_hide_dropdown_menu");
        $this->engine->output_template_parameters["show_hide_report_output_title"] = $this->configGet("show_hide_report_output_title");
        $this->engine->output_template_parameters["show_hide_prepare_section_boxes"] = $this->configGet("show_hide_prepare_section_boxes");
        $this->engine->output_template_parameters["show_hide_prepare_pdf_button"] = $this->configGet("show_hide_prepare_pdf_button");
        $this->engine->output_template_parameters["show_hide_prepare_html_button"] = $this->configGet("show_hide_prepare_html_button");
        $this->engine->output_template_parameters["show_hide_prepare_print_html_button"] = $this->configGet("show_hide_prepare_print_html_button");
        $this->engine->output_template_parameters["show_hide_prepare_csv_button"] = $this->configGet("show_hide_prepare_csv_button");
        $this->engine->output_template_parameters["show_hide_prepare_page_style"] = $this->configGet("show_hide_prepare_page_style");

        // Static Menu definition
        // ======================
        $this->engine->static_menu = $this->configGet("static_menu");

        // Dropdown Menu definition
        // ========================
        $this->engine->dropdown_menu = $this->configGet("dropdown_menu");

/*
        $defaultconnection = $this->configGet("database.default");
        $useConnection = false;
        if ( $defaultconnection )
            $useConnection = $this->configGet("database.connections.$defaultconnection");
        else
            $useConnection = array(
                    "driver" => "unknown",
                    "dbname" => "unknown",
                    "user" => "unknown",
                    "password" => "unknown",
                    );
        $this->engine->available_connections = $this->configGet("database.connections");
*/
        $this->engine->external_connection = Yii::app()->db->getPDOInstance();

        // Set Yii Database Access Config from configuration
        if ( !defined("SW_FRAMEWORK_DB_DRIVER") )
        {
            // Extract Yii database elements from connection string 
            $driver = "mysql";
            $host = "127.0.0.1";
            $dbname = "unnknown";
            if ( Yii::app()->db->connectionString )
            {
                $dbelements  = explode(':', Yii::app()->db->connectionString );
                if ( count($dbelements) > 1 )
                {
                    $driver = $dbelements[0];
                    $dbconbits = explode(";", $dbelements[1]);
                    if ( preg_match("/mysql/", $driver ) )
                        $driver = "pdo_mysql";

                    foreach ( $dbconbits as $value )
                    {
                        $after = substr(strstr($value, "="), 1);
                        $pos = strpos($value, "=");
                        if ( $pos )
                        {
                            $k = substr($value, 0, $pos);
                            $v = substr($value, $pos + 1);
                            if ( $k == "host" || $k == "hostname" ) 
                                $host = $v;
                            if ( $k == "dbname" || $k == "database" ) 
                                $dbname = $v;
                        }
                    }
                }
            }
            define('SW_FRAMEWORK_DB_DRIVER', $driver);
            define('SW_FRAMEWORK_DB_USER',Yii::app()->db->username);
            define('SW_FRAMEWORK_DB_PASSWORD',Yii::app()->db->password);

            define('SW_FRAMEWORK_DB_HOST',$host);
            define('SW_FRAMEWORK_DB_DATABASE',$dbname);
        }

        return $this->engine;
    }    

    // Create an instance of a reportico generator for Yii
    public function getReporticoEngine2()
    {
        require_once("components/reportico.php");

        reportico\reportico\components\set_up_reportico_session();
        $this->engine = new reportico\reportico\components\reportico();

        if ( Yii::app()->getUrlManager()->getUrlFormat() == "get" )
        {
            $this->engine->reportico_ajax_script_url = $_SERVER["SCRIPT_NAME"];
            $this->engine->forward_url_get_parameters = "r=reportico/reportico/ajax";
            $this->engine->forward_url_get_parameters_graph = "r=reportico/reportico/graph";
            $this->engine->forward_url_get_parameters_dbimage = "r=reportico/reportico/dbimage";
            $this->engine->reportico_ajax_mode = 1;
        }
        else
        {
            $this->engine->reportico_ajax_script_url = $_SERVER["SCRIPT_NAME"]."/admin/pluginhelper?plugin=Reportico&sa=sidebody&method=ajax&surveyId=0";
            $this->engine->forward_url_get_parameters = false;
            $this->engine->forward_url_get_parameters_graph = "r=reportico/reportico/graph";
            $this->engine->forward_url_get_parameters_dbimage = "r=reportico/reportico/dbimage";
            $this->engine->reportico_ajax_mode = 2;
        }
        $this->engine->embedded_report = true;
        $this->engine->allow_debug = true;
        $this->engine->framework_parent = "yii";
        $this->engine->external_user = Yii::app()->user->id;
        $this->engine->url_path_to_assets = $this->getAssetsUrl();

        // Indicates whether report output should include a refresh button
        //$this->engine->show_refresh_button = false;


        // Jquery already included?
        $this->engine->jquery_preloaded = true;

        // Bootstrap Features
        // Set bootstrap_styles to false for reportico classic styles, or "3" for bootstrap 3 look and feel and 2 for bootstrap 2
        // If you are embedding reportico and you have already loaded bootstrap then set bootstrap_preloaded equals true so reportico
        // doestnt load it again.
        $this->engine->bootstrap_styles = "3";
        $this->engine->bootstrap_preloaded = true;

        // In bootstrap enable pages, the bootstrap modal is by default used for the quick edit buttons
        // but they can be ignored and reportico's own modal invoked by setting this to true
        $this->engine->force_reportico_mini_maintains = false;

        // Pull in Yii CSRF request token
        $this->engine->csrfToken = Yii::app()->request->csrfToken;

        // Engine to use for charts .. 
        // HTML reports can use javascript charting, PDF reports must use PCHART
        //$this->engine->charting_engine = "PCHART";
        //$this->engine->charting_engine_html = "NVD3";

        // Whether to turn on dynamic grids to provide searchable/sortable reports
        // $this->engine->dynamic_grids = true;
        // $this->engine->dynamic_grids_sortable = true;
        // $this->engine->dynamic_grids_searchable = true;
        // $this->engine->dynamic_grids_paging = false;
        // $this->engine->dynamic_grids_page_size = 10;

        // Show or hide various report elements
        //$this->engine->output_template_parameters["show_hide_navigation_menu"] = "show";
        //$this->engine->output_template_parameters["show_hide_dropdown_menu"] = "show";
        //$this->engine->output_template_parameters["show_hide_report_output_title"] = "show";
        //$this->engine->output_template_parameters["show_hide_prepare_section_boxes"] = "show";
        //$this->engine->output_template_parameters["show_hide_prepare_pdf_button"] = "show";
        //$this->engine->output_template_parameters["show_hide_prepare_html_button"] = "show";
        //$this->engine->output_template_parameters["show_hide_prepare_print_html_button"] = "show";
        //$this->engine->output_template_parameters["show_hide_prepare_csv_button"] = "show";
        $this->engine->output_template_parameters["show_hide_prepare_page_style"] = "hide";

        // Static Menu definition
        // ======================
        // identifies the items that will show in the middle of the project menu page.
        // If not set will use the project level menu definitions in project/projectname/menu.php
        // To have no static menu ( for example if you just want to use a drop down then set to empty array )
        // To define a static menu, follow the example here.
        // report can be a valid report file ( without the xml suffix ).
        // If title is left as AUTO then the title will be taken form the report definition
        // Use title of BLANKLINE to separate items and LINE to draw a horizontal line separator

        //$this->engine->static_menu = array (
	        //array ( "report" => "an_xml_reportfile1", "title" => "<AUTO>" ),
	        //array ( "report" => "another_reportfile", "title" => "<AUTO>" ),
	        //array ( "report" => "", "title" => "BLANKLINE" ),
	        //array ( "report" => "anotherfreportfile", "title" => "Custom Title" ),
	        //array ( "report" => "", "title" => "BLANKLINE" ),
	        //array ( "report" => "andanother", "title" => "Another Custom Title" ),
	    //);
    
        // To auto generate a static menu from all the xml report files in the project use
        //$this->engine->static_menu = array ( array ( "report" => ".*\.xml", "title" => "<AUTO>" ) );
    
        // To hide the static report menu
        //$this->engine->static_menu = array ();

        // Dropdown Menu definition
        // ========================
        // Menu items for the drop down menu
        // Enter definition for the the dropdown menu options across the top of the page
        // Each array element represents a dropdown menu across the page and sub array items for each drop down
        // You must specifiy a project folder for each project entry and the reportfile definitions must point to a valid xml report file
        // within the specified project
        //$this->engine->dropdown_menu = array(
        //                array ( 
        //                    "project" => "projectname",
        //                    "title" => "dropdown menu 1 title",
        //                    "items" => array (
        //                        array ( "reportfile" => "report" ),
        //                        array ( "reportfile" => "anotherreport" ),
        //                        )
        //                    ),
        //                array ( 
        //                    "project" => "projectname",
        //                    "title" => "dropdown menu 2 title",
        //                    "items" => array (
        //                        array ( "reportfile" => "report" ),
        //                        array ( "reportfile" => "anotherreport" ),
        //                        )
        //                    ),
        //            );


        // Set Joomla Database Access Config from configuration
        if ( !defined("SW_FRAMEWORK_DB_DRIVER") )
        {
            // Extract Yii database elements from connection string 
            $driver = "mysql";
            $host = "127.0.0.1";
            $dbname = "unnknown";
            if ( Yii::app()->db->connectionString )
            {
                $dbelements  = explode(':', Yii::app()->db->connectionString);
                if ( count($dbelements) > 1 )
                {
                    $driver = $dbelements[0];
                    $dbconbits = explode(";", $dbelements[1]);
                    if ( preg_match("/mysql/", $driver ) )
                        $driver = "pdo_mysql";

                    foreach ( $dbconbits as $value )
                    {
                        $after = substr(strstr($value, "="), 1);
                        $pos = strpos($value, "=");
                        if ( $pos )
                        {
                            $k = substr($value, 0, $pos);
                            $v = substr($value, $pos + 1);
                            if ( $k == "host" || $k == "hostname" ) 
                                $host = $v;
                            if ( $k == "dbname" || $k == "database" ) 
                                $dbname = $v;
                        }
                    }
                }
            }
            define('SW_FRAMEWORK_DB_DRIVER', $driver);
            define('SW_FRAMEWORK_DB_USER',Yii::app()->db->username);
            define('SW_FRAMEWORK_DB_PASSWORD',Yii::app()->db->password);

            define('SW_FRAMEWORK_DB_HOST',$host);
            define('SW_FRAMEWORK_DB_DATABASE',$dbname);
        }

        return $this->engine;
    }    

    // Generate output
    public function generate()
    {
        $this->engine->execute();
    }

    public function newDirectRequest(){
       $event = $this->event;

       // you can get other params from the request object
       $request = $event->get('request');
       
       //get the function name to call and use the method call_user_func
       $functionToCall = $event->get('function');        

       $content = call_user_func(array($this,$functionToCall)); 

       //set the content on the event      
       $event->setContent($this, $content);        
   }

    public function configGet($setting)
    {
//echo "$setting = ".$this->settings[$setting]."<BR>";
        return $this->settings[$setting];
    }
}
