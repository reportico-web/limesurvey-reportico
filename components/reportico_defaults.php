<?php
    /*
     * Global reportico customization .. 
     * Set styles :-
     * Styles can be applied to the report body, details page, group headers, trailers, 
     * cell styles and are specified as a series of CSS like parameters.
     * Styles are applied to HTML and PDF formats
     * 
     * Add page headers, footers with styles to apply to every report page
     *
    */
    function reportico_defaults($reportico)
    {
            // Set up styles
            // Use 
            // $styles = array ( "styleproperty" => "value", .... );
            // $reportico->apply_styleset("REPORTSECTION", $styles, "columnname", WHENTOAPPLY );
            // Where REPORTSECTION is one of ALLCELLS ROW CELL PAGE BODY COLUMNHEADERS GROUPHEADER GROUPHEADERLABEL GROUPHEADERVALUE GROUPTRAILER
            // and WHENTOAPPLY can be PDF or HTML of leave unsepcified/false for both

            // Don't apply  apply body styles to pdf docuement when using fpdf engine
            if (  $reportico->pdf_engine != "fpdf" )
            {
                // REPORT BODY STYLES
                $styles = array(
                    //"background-color" => "#cccccc",
                    "border-width" => "1px 1px 1px 1px",
                    "border-style" => "solid",
                    "border-color" => "#333333",
                    //"background-color" => "#eeeeee",
                    //"padding" => "0 20 0 20",
                    //"margin" => "0 0 0 5",
                    "font-family" => "freesans",
                    );
                $reportico->apply_styleset("BODY", $styles, false, "PDF");

                // CRITERIA BOX STYLES
                $styles = array(
                    "background-color" => "#eeeeee",
                    "border-style" => "solid",
                    "border-width" => "1px 1px 1px 1px",
                    "border-color" => "#888888",
                    //"display" => "none",
                    //"margin" => "0px 5px 10px 5px",
                    //"padding" => "0px 5px 0px 5px",
                    );
                $reportico->apply_styleset("CRITERIA", $styles, false, false);
            }

            // PAGE DETAIL BOX STYLES
            $styles = array(
                "margin" => "0 2 0 2",
                );
            $reportico->apply_styleset("PAGE", $styles, false, "PDF");

            // DETAIL ROW BOX STYLES
            $styles = array(
                "background-color" => "#fdfdfd",
                "margin" => "0 5 0 5",
                );
            $reportico->apply_styleset("ROW", $styles, false, "PDF");

            $styles = array(
                "background-color" => "#eeeeee",
                );
            $reportico->apply_styleset("ALLCELLS", $styles, false, "PDF", "lineno() % 2 == 0");


            //GROUP HEADER LABEL STYLES
            $styles = array(
                "border" => "none",
                );
            $reportico->apply_styleset("GROUPHEADERLABEL", $styles, false, "HTML");

            //GROUP HEADER LABEL STYLES
            $styles = array(
                "margin" => "0 0 0 5",
                "width" => "3cm",
                "requires-before" => "8cm",
                );
            $reportico->apply_styleset("GROUPHEADERLABEL", $styles, false, "PDF");

            // GROUP HEADER VALUE STYLES
            $styles = array(
                "margin" => "0 20 0 0",
                );
            $reportico->apply_styleset("GROUPHEADERVALUE", $styles, false, "PDF");

            // ALL CELL STYLES
            /*
            $styles = array(
                "font-family" => "times",
                "border-width" => "1px 1px 1px 1px",
                "border-style" => "solid",
                "border-color" => "#888888",
                );
            $reportico->apply_styleset("ALLCELLS", $styles, false, "PDF");
            */

            // Specific named cell styles
            /*
            $styles = array(
                "color" => "#880000",
                "font-weight" => "bold",
                "font-style" => "italic",
                );
            $reportico->apply_styleset("CELL", $styles, "id", "PDF");
            */

            // Column header styles
            $styles = array(
                "color" => "#ffffff",
                "background-color" => "#999999",
                "font-weight" => "bold",
                );
            $reportico->apply_styleset("COLUMNHEADERS", $styles, false, "PDF");

            // Page Headers for TCPDF driver ( this is the default )
            if ( $reportico->pdf_engine == "tcpdf" )
            {
                // Create Report Title Page Header on every page of PDF
                $reportico->create_page_header("H1", 1, "{REPORT_TITLE}{STYLE border-width: 1 0 1 0; margin: 15px 0px 0px 0px; border-color: #000000; font-size: 18; border-style: solid;padding:5px 0px 5px 0px; height:1cm; background-color: #000000; color: #ffffff; text-align: center}" );
                $reportico->set_page_header_attribute("H1", "ShowInHTML", "no" );
                $reportico->set_page_header_attribute("H1", "ShowInPDF", "yes" );
                $reportico->set_page_header_attribute("H1", "justify", "center" );

                // Create Image on every page of PDF
                /*
                $reportico->create_page_header("H2", 1, "{STYLE width: 100; height: 50; margin: 0 0 0 0; background-color: #003333; background-image:images/reportico100.png;}" );
                $reportico->set_page_header_attribute("H2", "ShowInHTML", "no" );
                */

                // Create Image on every page of PDF
                $reportico->create_page_header("H3", 1, "Time: date('Y-m-d H:i:s'){STYLE font-size: 10; text-align: right; font-style: italic;}" );
                $reportico->set_page_header_attribute("H3", "ShowInHTML", "no" );
                $reportico->set_page_header_attribute("H3", "justify", "right" );

                // Create Page No on bottom of PDF page
                $reportico->create_page_footer("F1", 2, "Page: {PAGE}{STYLE border-width: 1 0 0 0; margin: 40 0 0 0; font-style: italic; }" );
            }
            else   // FPDF page headers
            {
                // Create Report Title Page Header on every page of PDF
                $reportico->create_page_header("H1", 2, "{REPORT_TITLE}{STYLE border-width: 1 0 1 0; margin: 15px 0px 0px 0px; border-color: #000000; font-size: 18; border-style: solid;padding:5px 0px 5px 0px; height:1cm; background-color: #000000; color: #ffffff}" );
                $reportico->set_page_header_attribute("H1", "ShowInHTML", "no" );
                $reportico->set_page_header_attribute("H1", "ShowInPDF", "yes" );
                $reportico->set_page_header_attribute("H1", "justify", "center" );

                // Create Image on every page of PDF
                $reportico->create_page_header("H3", 1, "Time: date('Y-m-d H:i:s'){STYLE font-size: 10; text-align: right; font-style: italic;}" );
                $reportico->set_page_header_attribute("H3", "ShowInHTML", "no" );
                $reportico->set_page_header_attribute("H3", "justify", "right" );

                // Create Page No on bottom of PDF page
                $reportico->create_page_footer("F1", 2, "Page: {PAGE}{STYLE border-width: 1 0 0 0; margin: 40 0 0 0; font-style: italic; }" );
            }
    }   

    /*
    * Creates a denormalized representation of lime_survey_xxxxx table
    * for easy joining row by row questions, answers and actual responses
    * Creates a temporary table
    *
    * Parameters
    * engine - reference to the reportico process in order yo 
    */
    
function denormalize_survey_responses($engine)
{
$survey = $engine->get_criteria_value("survey");
$group = $engine->get_criteria_value("group");

    $swhere = "";
    $gwhere = "";
    if ( $survey ) $swhere = " AND s.sid in ( $survey )";
    if ( $group ) $gwhere = " AND g.gid in ( $group )";
    $sql = "select s.sid, g.gid, q.qid,
        g.group_name,
        q.title,
        q.question,
        q.type,
        cq.title ch_title,
        CASE
            WHEN cq.title IS NOT NULL THEN concat(s.sid, 'X', g.gid, 'X', q.qid, cq.title )
            ELSE concat(s.sid, 'X', g.gid, 'X', q.qid )
        END id
            from lime_surveys s
            join lime_groups g ON g.sid = s.sid $swhere $gwhere
            join lime_questions q ON q.gid = g.gid AND q.parent_qid  = 0
            left join lime_questions cq ON cq.parent_qid = q.qid
        where 1 = 1
    ";

    $stmt = Yii::app()->db->createCommand($sql)->queryAll();
    //echo "<PRE>";
    $unions = [];
    foreach ( $stmt as $row )
    {
        $unions[]  = sprintf("SELECT '%s' group_name, \n
                              '%s' title, \n
                              '%s' question, \n
                              '%s' type, \n
                              '%s' id, \n
                              '%s' qid, \n
                              '%s' gid, \n
                              '%s' ch_title, \n
                              s.%s code, \n
                                0 count
                        FROM lime_survey_%s AS s \n
                        LEFT JOIN lime_answers AS a \n
                          ON a.code = s.%s
                          AND a.qid = '%s' \n",
        $row["group_name"],
        $row["title"],
        preg_replace("/'/", "/\'/", $row["question"]),
        $row["type"],
        $row["id"],
        $row["qid"],
        $row["gid"],
        $row["ch_title"],
        $row["id"],
        $row["sid"],
        $row["id"],
        $row["qid"]);
    }

    $union = implode( "\nUNION ALL\n", $unions);

    $union = "CREATE TEMPORARY TABLE t_answers AS $union";
    $stmt = Yii::app()->db->createCommand($union)->execute();

    //echo "<PRE>";
    //echo $the_union;
    //echo "</PRE>";

    $sql = "UPDATE t_answers SET count = 1 WHERE code IS NOT NULL";
    return Yii::app()->db->createCommand($sql)->execute();
}
?>
