<?php
/**
 * Created by PhpStorm.
 * User: cceanse
 * Date: 02/02/2015
 * Time: 16:01
 */

function turnitin_submissions_process($turnitinid){
    global $DB;

    $turnitin_submissions = $DB->get_records('turnitintool_submissions', array('turnitintoolid' => $turnitinid));
    print_r(count($turnitin_submissions));
    echo '<br />';
    // get all stored files from filedir
    // make directory with turnitintool name
    // move files from $CFG->dataroot/filedir/ to $CFG->dataroot/turnitinfiles_backup/
    // archive all files under $CFG->dataroot/turnitinfiles_backup/

}


