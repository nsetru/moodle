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

//check course with given courseshortname exists
$course = $DB->get_record('course', array('shortname' => $course_shortname));


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
if($course) {
    ?>
    <br />
    <h2>
        Backup Turnitin Assignment submissions
    </h2>
    <hr>
    <?php
    // check if there are any turnitin assignment submissions for course
    ?>
    <!-- enter shortname of a course -->
    <form action="index.php" method="post">
        <lable>Enter Course Shortname : </lable>
        <input type="text" name="courseshortname1">
        <input type="hidden" name="action" value="coursesubmit1">
        <input type="submit" value="Submit">
    </form>
<?php
}
?>
?>
</body>
</html>