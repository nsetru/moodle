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

    $turnitin_submissions = $DB->get_records('turnitintool_submissions', array('turnitintoolid' => $turnitinid));
    print_r(count($turnitin_submissions));
    echo '<br />';

    // get all stored files related to turnitin
    $turnitindata = turnitin_submissions_get_storefiles($turnitinid);

    if (!empty($turnitindata)) {
        $turnitindir_exists = 0;
        foreach ($turnitindata as $turnitin) {
            echo '<br />';
            print_r($turnitin);
            echo '<br />';
            echo 'course fullname:' . $turnitin->coursetitle . '<br />';
            echo 'filename:' . $turnitin->filename . '<br />';
            echo 'activity:' . $turnitin->activity . '<br />';

            // check if directory with course shortname already exists. If not make directory with course shortname
            $coursedir = $turnitin_archivepath . $turnitin->courseshort;
            $coursedir_exists = check_dir_exists($coursedir);

            // within course directory check if directory with turnitin name exists. If not create a directory
            if ($coursedir_exists) {
                $turnitindir_name = $turnitin->activityid . '_' . $turnitin->activity;
                $turnitindir = $coursedir . '/' . $turnitindir_name . '/';
                $turnitindir_exists = check_dir_exists($turnitindir);
            }

            if ($turnitindir_exists) {
                // get contenthash
                $filecontenthash = $turnitin->contenthash;

                // get first 4 characters
                $l1 = $filecontenthash[0] . $filecontenthash[1];
                $l2 = $filecontenthash[2] . $filecontenthash[3];
                // navigate to folder with hashed files
                $storedfilelocation = $CFG->dataroot . '/filedir/' . $l1 . '/' . $l2 . '/' . $filecontenthash;

                // check if hashed file exists under /filedir/
                if (file_exists($storedfilelocation)) {
                    // copy files from $CFG->dataroot/filedir/ to $CFG->dataroot/turnitinfilesbackup/
                    copy($storedfilelocation, $turnitindir . '/' . $turnitin->rawfilename);
                }
            }
        }
    }

    // copy files from $CFG->dataroot/filedir/ to $CFG->dataroot/turnitinfiles_backup/
    // Copy all turnitin assignments into directory
    // archive all files under $CFG->dataroot/turnitinfiles_backup/

}

function turnitin_submissions_get_storefiles($turnitintoolid) {
    global $DB, $CFG;

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

function turnitin_submissions_printtable($turnitinid) {

    $turnitin_results = turnitin_submissions_get_storefiles($turnitinid);

    $table = "<table border=\"1\" style=\"width:100%\"><tr>
                <th>Course Id</th>
                <th>Course Fullname</th>
                <th>Course Shortname</th>
                <th>User Id</th>
                <th>User Fulname</th>
                <th>Email</th>
                <th>Raw filename</th>
                <th>Filename</th>
                </tr>";
    foreach($turnitin_results as $turnitin){
        $table .= "<tr>
                <td>$turnitin->course</td>
                <td>$turnitin->coursetitle</td>
                <td>$turnitin->courseshort</td>
                <td>$turnitin->userid</td>
                <td>$turnitin->firstname.' '.$turnitin->lastname</td>
                <td>$turnitin->email</td>
                <td>$turnitin->rawfilename</td>
                <td>$turnitin->filename</td>
            </tr>";
    }
    $table .= "</table>";
    return $table;
}

