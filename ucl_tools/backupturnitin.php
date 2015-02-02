<?php

require_once('../config.php');
require_once($CFG->dirroot . '/ucl_tools/lib.php');

global $DB, $PAGE, $OUTPUT;
$context = context_system::instance();
require_login();

$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);
//$PAGE->set_url($CFG->wwwroot . '/local/testf2fapi/f2fapi_test.php');
echo $OUTPUT->header();


//If you are passing the file names to thae array directly use the following method
$file_names = array('test2.pdf','test3.jpg','test4.pdf');

//---------------
/*
//if you are getting the file name from database means use the following method
//Include DB connection
require_once('db.php');
//Mysql query to fetch file names
$cqurfetch=mysql_query("select * from files");

//create an empty array
$file_names = array();
//fetch the names from database
while($row = mysql_fetch_array($cqurfetch, MYSQL_NUM))
{
    //Add the values to the array
    //Below 8 ,eams the the number of the mysql table column
    $file_names[] = $row[8];
}*/
//----------------------

//Archive name
$archive_file_name = $CFG->dataroot . '/turnitin1_submission/archive1.zip';

//Download Files path
//$file_path=$_SERVER['DOCUMENT_ROOT'].'/images/';
$file_path= $CFG->dataroot . '/nivtemp/';

//cal the function
//zip_filesanddownload($file_names,$archive_file_name,$file_path);

echo $OUTPUT->footeer();
