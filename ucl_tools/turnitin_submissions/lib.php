<?php

/**
 * Script to process turnitin submissions
 *  1) Copy turnitin submisisons from $CFG->dataroot/filedir/ to $CFG->dataroot/turnitinfilesbackup given a courseid
 *  2) Archive all copied turnitin submissions from $CFG->dataroot/turnitinfilesbackup/<course_shortname>
 *
 * User: cceanse
 * Date: 02/02/2015
 * Time: 16:01
 */

/**
 * Process turnitin submissions
 *
 * @param $turnitinid
 */
function turnitin_submissions_process($turnitinid)
{
    global $DB, $CFG;

    $turnitin_archivepath = $CFG->dataroot . '/turnitinfilesbackup/';

    $turnitin_submissions = $DB->get_records('turnitintool_submissions', array('turnitintoolid' => $turnitinid));
    /*print_r(count($turnitin_submissions));*/

    // get all stored files related to turnitin
    $turnitindata = turnitin_submissions_get_storefiles($turnitinid);

    if (!empty($turnitindata)) {
        $turnitindir_exists = 0;
        foreach ($turnitindata as $turnitin) {

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

                // turnitin destination filename
                $turnitin_filename = $turnitindir . '/' . $turnitin->rawfilename;
                // check if hashed file exists under /filedir/
                if (file_exists($storedfilelocation)) {
                    if(is_file($turnitin_filename)) {
                        unlink($turnitin_filename);
                    }

                    // copy files from $CFG->dataroot/filedir/ to $CFG->dataroot/turnitinfilesbackup/
                    // Copy all turnitin assignments into directory
                    copy($storedfilelocation, $turnitin_filename);
                }
            }
        }
    }

}

/**
 * Get stored turnitin submissions
 *
 * @param $turnitintoolid
 * @return array
 */
function turnitin_submissions_get_storefiles($turnitintoolid)
{
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

/**
 * Print table with turnitin submission records
 *
 * @param $turnitinid
 * @return string
 */
function turnitin_submissions_printtable($turnitinid)
{

    $turnitin_results = turnitin_submissions_get_storefiles($turnitinid);

    $th = "<table border=\"1\" style=\"width:100%\"><tr>
                <th>Course Id</th>
                <th>Course Fullname</th>
                <th>Course Shortname</th>
                <th>Turnitin Id</th>
                <th>Turnitin Name</th>
                <th>User Id</th>
                <th>User Fulname</th>
                <th>Email</th>
                <th>Raw filename</th>
                <th>Filename</th>
                </tr>";

    $table = '';
    if (!empty($turnitin_results)) {
        $table .= "<br/>";
        $table .= $th;
    }

    foreach ($turnitin_results as $turnitin) {
        $table .= "<tr>
                <td>$turnitin->course</td>
                <td>$turnitin->coursetitle</td>
                <td>$turnitin->courseshort</td>
                <td>$turnitin->activityid</td>
                <td>$turnitin->activity</td>
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

/**
 * Archive all turnitin submissions related to course
 *
 * @param $courseid
 */
function turnitin_submissions_archive($courseid){
    global $DB, $CFG;

    $turnitin_archivepath = $CFG->dataroot . '/turnitinfilesbackup/';

    // get course shortname
    $course = $DB->get_record('course', array('id' => $courseid));

    $source_dir = $turnitin_archivepath . $course->shortname .'/';
    $zip_file = $turnitin_archivepath . $course->shortname .'.zip';
    $file_list = create_zip::listDirectory($source_dir);

    $zip = new ZipArchive();
    if ($zip->open($zip_file, ZIPARCHIVE::CREATE) === true) {
        foreach ($file_list as $file) {
            if ($file !== $zip_file) {
                $zip->addFile($file, substr($file, strlen($source_dir)));
            }
        }
        $zip->close();
    }
}


/**
 * Create Archive of all Turnitin submissions for a course
**/
class create_zip
{
    public static function listDirectory($dir)
    {
        $result = array();
        $root = scandir($dir);
        foreach($root as $value) {
            if($value === '.' || $value === '..') {
                continue;
            }
            if(is_file("$dir$value")) {
                $result[] = "$dir$value";
                continue;
            }
            if(is_dir("$dir$value")) {
                $result[] = "$dir$value/";
            }
            foreach(self::listDirectory("$dir$value/") as $value)
            {
                $result[] = $value;
            }
        }
        return $result;
    }
}