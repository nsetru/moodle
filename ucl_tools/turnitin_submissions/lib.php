<?php
/**
 * Created by PhpStorm.
 * User: cceanse
 * Date: 02/02/2015
 * Time: 16:01
 */

function turnitin_submissions_process($turnitinid) {
    global $DB;

    $turnitin_submissions = $DB->get_records('turnitintool_submissions', array('turnitintoolid' => $turnitinid));
    print_r(count($turnitin_submissions));
    echo '<br />';
    turnitin_submissions_get_storefiles($turnitinid);
    // get all stored files from filedir
    // make directory with turnitintool name
    // move files from $CFG->dataroot/filedir/ to $CFG->dataroot/turnitinfiles_backup/
    // archive all files under $CFG->dataroot/turnitinfiles_backup/

}

function turnitin_submissions_get_storefiles($turnitintoolid) {
    global $DB, $CFG;

    echo 'turnitin_submissions_get_storefiles';
    $params = array('turnitintoolid' => $turnitintoolid);

    $sql = "SELECT
    fl.id AS id,
    cm.id AS cmid,
    tu.id AS activityid,
    tu.name AS activity,
    sb.submission_unanon AS unanon,
    us.firstname AS firstname,
    us.lastname AS lastname,
    us.email AS email,
    us.id AS userid,
    fl.mimetype AS mimetype,
    fl.filesize AS filesize,
    fl.timecreated AS created,
    fl.pathnamehash AS hash,
    fl.filename AS rawfilename,
    cs.fullname AS coursetitle,
    cs.shortname AS courseshort,
    cs.id AS course,
    sb.submission_filename AS filename,
    sb.submission_objectid AS objectid
FROM {files} fl
LEFT JOIN
    {turnitintool_submissions} sb ON fl.itemid = sb.id
LEFT JOIN
    {user} us ON fl.userid = us.id
LEFT JOIN
    {course_modules} cm ON fl.contextid = cm.id AND cm.module = 'turnitintool'
LEFT JOIN
    {turnitintool} tu ON cm.instance = tu.id
LEFT JOIN
    {course} cs ON tu.course = cs.id
WHERE
    fl.component = 'mod_turnitintool' AND fl.filesize != 0 AND sb.turnitintoolid= :turnitintoolid
ORDER BY sb.submission_filename DESC ";

    $result = $DB->get_records_sql($sql, $params);
    print_r($result);
}


