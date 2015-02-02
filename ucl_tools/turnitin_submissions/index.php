<?php
/**
 * Created by PhpStorm.
 * User: cceanse
 * Date: 02/02/2015
 * Time: 16:01
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/ucl_tools/turnitin_submissions/lib.php');

//get all courses

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
</body>
</html>