<?php
/**
 * Created by PhpStorm.
 * User: cceanse
 * Date: 02/02/2015
 * Time: 16:01
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/ucl_tools/turnitin_submissions/lib.php');

require_login();
?>

<html>

<head>
    <title> Backup Turnitin Assignment submissions</title>
</head>

<body>
<h1>
    Backup Turnitin Assignment submissions
</h1>
<hr>

<?php
if (isset($_POST['courseshortname'])) {
    //check course with given courseshortname exists
    $course = $DB->get_record('course', array('shortname' => $_POST['courseshortname']));
}

if (isset($_POST['turnitinassignments'])) {
    $turnitinassignments = $_POST['turnitinassignments'];
    foreach ($turnitinassignments as $turnitinassignment) {

        //create a turnitin directory and copy files from filedir to turnitin directory location
        turnitin_submissions_process($turnitinassignment);
        $table = turnitin_submissions_printtable($turnitinassignment);
        echo $table;
    }
}

if(isset($_POST['courseid'])) {
    $courseid = $_POST['courseid'];
    // archive all files under $CFG->dataroot/turnitinfiles_backup/
    turnitin_submissions_archive($courseid);
}

// TODO: Implement download archive functionality
/*if(isset($_POST['download'])) {
    echo 'courseid:'.$_POST['courseid'];
    echo 'download:'.$_POST['download'];
}*/
?>


<?php
if (!isset($_POST['courseshortname']) and !isset($_POST['turnitinassignments'])) {
    ?>
    <!-- enter shortname of a course -->
    <form action="index.php" method="post">
        <lable>Enter Course Shortname :</lable>
        <input type="text" name="courseshortname">
        <input type="hidden" name="action" value="coursesubmit">
        <input type="submit" value="Submit">
    </form>

<?php
}
if (isset($_POST['courseshortname']) and $course) {

    // check if there are any turnitin assignment for course
    $turnitin_modules = $DB->get_records('turnitintool', array('course' => $course->id));
    ?>
    <br />
    <form action="index.php" method="post">
        <?php
        foreach ($turnitin_modules as $turnitin) {
            echo $turnitin->name;
            ?>
            <input type="checkbox" name="turnitinassignments[]" value=<?php echo $turnitin->id ?>/>  <br/>
        <?php
        }
        ?>
        <br/>
        <br/>
        <input type="hidden" name="courseid" value="<?php echo $course->id?>" >
        <input type="submit" value="Submit">
    </form>
<?php
}
if (isset($_POST['turnitinassignments'])) {
    ?>
    <!-- TODO: Implement download archive functionality -->
    <!--<br />
    <form action="index.php" method="post">
        <input type="hidden" name="courseid" value="<?php //echo $course->id?>" >
        <input type="submit" name="download" value="Download Files">-->
    </form>
<?php
}
?>
</body>
</html>