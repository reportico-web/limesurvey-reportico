# limesurvey-reportico

Reportico Plugin for Limesurvey.

Compatibility: Limesurvey 2.5.4 +

Provides 


See website www.reportico.org for documentation on using this tool.
Until Limesurvey specific documentation is written here is some basic instructions:-

1. git pull this under plugins folder.
2. Rename it from limesurvey-reportico to Reportico.
3. Enable the plugin in Limesurvey Plugin Manager
4. In the Limesurvey admin page, a Reportico menu should appear
5. Choose the drop Menu Reportico->Admin .. you should see the Reportico Admin Page
6. You can here create a project for reports.

To get going there are two useful report projects already setup and you will find them under the plugins/Reportico/components/projects under your Limesurvey installation. They are in folders named "tutorials" and "examples". You need to copy these to the tmp/runtime/reportico/projects under your Limesurvey installation.
After doing this, they will appear as available projects in your Reportico admin page within Limesurvey.

The "examples" project provides a few example reports on your survey database. Note that this project under tmp/runtime/reportico/projects  contains a file called reportico_defults.php, which sets styling defaults nad provided useful functions. Note also the xml files which are the file definitions. Notice that the answers.xml is accompanied by an answers.xml.php. This is a custom file which runs custom functions prior to report generation. This particular file calls a function called denormalize_survey_responses(). This function in reportico_defauts.php generates a table from the lime_survey_XXXX tables with one row per answer instead of one column. This table is called "t_answer" and when this function is called, this temp table can be used in the reports. The tables joins survey, group, question and actual responses together in the structure and can be selected with

SELECT sid, gid, qid, title, question, type, ch_title, code, count
FROM t_answers

count is set to 1 if the response code is se, which will Y/N foryes/no responses or a code for multi choice options.This mean you can sum the count column to get counts of particular answers given.

This format is useful for generating certain types of reports which count answers grouped by question/group.

The "tutorials" project allows you set up  sample dataset in your database which you can run through the standard set of reportico tutorials which will teach you all about the features of Reportico. You can run through this by following the instructions here.

http://www.reportico.org/documentation/4.6/doku.php?id=reporticotutorial

## Screenshots

![Administration Page](/components/images/lsadmin.png?raw=true "Administration Page")
![Menu Page](/components/images/lsmenu.png?raw=true "Menu Page")
![Criteria Page](/components/images/lscriteria.png?raw=true "Criteria Page")
![Edit Query Page](/components/images/lssql.png?raw=true "Edit Query Page")
![Report Output Page](/components/images/lsoutput.png?raw=true "Report Output Page")
