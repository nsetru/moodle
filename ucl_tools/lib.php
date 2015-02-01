<?php
/**
 * Created by PhpStorm.
 * User: Nivedita
 * Date: 01/02/2015
 * Time: 22:09
 */

//This script is developed by www.webinfopedia.com
//For more examples in php visit www.webinfopedia.com
function zip_filesanddownload($file_names,$archive_file_name,$file_path)
{
    $zip = new ZipArchive();

    echo 'filepath:'.$file_path;
    echo 'archive:'.$archive_file_name;

    //create the file and throw the error if unsuccessful
    if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE )!==TRUE) {
        exit("cannot open <$archive_file_name>\n");
    }
    //add each files of $file_name array to archive
    foreach($file_names as $files)
    {
        echo "file:".$files;
        $zip->addFile($file_path.$files,$files);
        //echo $file_path.$files,$files."<br>";
    }
    $zip->close();
    
    //then send the headers to foce download the zip file
    header("Content-type: application/zip");
    header("Content-Disposition: attachment; filename=$archive_file_name");
    header("Pragma: no-cache");
    header("Expires: 0");
    readfile("$archive_file_name");
    exit;
}