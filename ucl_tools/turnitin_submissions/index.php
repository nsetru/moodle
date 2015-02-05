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

$course_shortname = $_POST['courseshortname'];
//$turnitin_assignments = $_POST['turnitinfiles'];

//check course with given courseshortname exists
$course = $DB->get_record('course', array('shortname' => $course_shortname));

print_r($_POST);

if(isset($_POST['turnitinassignments'])){
    $turnitinassignments  = $_POST['turnitinassignments'];
    foreach($turnitinassignments as $turnitinassignment){
        //create a turnitin directory and copy files from filedir to turnitin directory location
    }
}
//
?>

<html>
<head>
    <title> Backup Turnitin Assignment -1 submissions</title>
</head>
<body>
<h1>
    Backup Turnitin Assignment submissions
</h1>
<hr>
<!-- enter shortname of a course -->
<form action="index.php" method="post">
    <lable>Enter Course Shortname : </lable>
    <input type="text" name="courseshortname">
    <input type="hidden" name="action" value="coursesubmit">
    <input type="submit" value="Submit">
</form>

<?php
if($_POST['courseshortname'] and $course) {
    ?>
    <br />
    <h2>
        Backup Turnitin Assignment submissions
    </h2>
    <hr>
    <?php
    // check if there are any turnitin assignment for course
    $turnitin_modules = $DB->get_records('turnitintool', array('course' => $course->id));
    ?>
    <form action="index.php" method="post">
    <?php
    foreach($turnitin_modules as $turnitin) {
        //$turnitin_submissions = $DB->get_records('turnitintool_submissions', array('turnitintoolid' => $turnitin->id));
        echo $turnitin->name;
        ?>
        <!--<br />-->
        <input type="checkbox" name="turnitinassignments[]" value=<?php echo $turnitin->id ?>  />  <br />
        <?php
    }
    ?>

        <!--<lable>Enter Course Shortname : </lable>
        <input type="text" name="courseshortname1">
        <input type="hidden" name="action" value="coursesubmit1">-->
        <br />
        <br />
        <input type="submit" value="Submit">
    </form>
<?php
}
?>
</body>
</html>