<?php
/**
 * Created by PhpStorm.
 * User: cceanse
 * Date: 02/02/2015
 * Time: 16:01
 */

function turnitin_submissions_process($turnitinid) {
    global $DB, $CFG;

    $turnitin_archivepath = $CFG->dataroot . '/turnitinfilesbackup/';
    echo 'turnitin archive path:'.$turnitin_archivepath.'<br />';

    $turnitin_submissions = $DB->get_records('turnitintool_submissions', array('turnitintoolid' => $turnitinid));
    print_r(count($turnitin_submissions));
    echo '<br />';

    // get all stored files from filedir
    $turnitindata = turnitin_submissions_get_storefiles($turnitinid);
    //print_r($turnitindata);

    if (!empty($turnitindata)) {
        $turnitindir_exists = 0;
        foreach ($turnitindata as $turnitin) {
            print_r($turnitin);
            echo '<br />';
            echo 'course fullname:'.$turnitin->coursetitle.'<br />';
            echo 'filename:'.$turnitin->filename.'<br />';
            // check if directory with course shortname already exists. If not make directory with course shortname
            $coursedir = $turnitin_archivepath.$turnitin->courseshort;
            echo 'coursedir:'.$coursedir.'<br />';

            $coursedir_exists = check_dir_exists($coursedir);
            echo 'coursedirexists:'.$coursedir_exists.'<br />';
            // within course directory check if directory with turnitin name exists. If not create a directory
            if($coursedir_exists){
                $turnitindir_name = $turnitin->activityid.'_'.$turnitin->activity;
                $turnitindir = $coursedir.'/'.$turnitindir_name;
                echo 'turnitindir:'.$turnitindir.'<br />';
                $turnitindir_exists = check_dir_exists($turnitindir);
                echo 'turnitindir_exists:'.$turnitindir_exists.'<br />';
            }

            if($turnitindir_exists){
                // copy files from $CFG->dataroot/filedir/ to $CFG->dataroot/turnitinfiles_backup/
            }
        }
    }

    // copy files from $CFG->dataroot/filedir/ to $CFG->dataroot/turnitinfiles_backup/
    // Copy all turnitin assignments into directory
    // archive all files under $CFG->dataroot/turnitinfiles_backup/

}

function turnitin_submissions_get_storefiles($turnitintoolid) {
    global $DB, $CFG;

    echo 'turnitin_submissions_get_storefiles';
    $params = array('turnitintoolid' => $turnitintoolid);

    $sql = "SELECT
    fl.id AS id,
    -- cm.id AS cmid,
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
    fl.pathnamehash AS pathnamehash,
    fl.contenthash AS contenthash,
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
--     {course_modules} cm ON fl.contextid = cm.id AND cm.module = 'turnitintool'
-- LEFT JOIN
--    {turnitintool} tu ON cm.instance = tu.id
    {turnitintool} tu ON sb.turnitintoolid = tu.id
LEFT JOIN
    {course} cs ON tu.course = cs.id
WHERE
    fl.component = 'mod_turnitintool' AND fl.filesize != 0 AND sb.turnitintoolid= :turnitintoolid
ORDER BY sb.submission_filename DESC ";

    $result = $DB->get_records_sql($sql, $params);
    return $result;
}


