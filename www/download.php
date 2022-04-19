<?php
    session_start();
    require_once 'functions.php';

    if (isset($_SESSION['user']))
        $loggedin = TRUE;
    else 
        $loggedin = FALSE;
    
    if (!$loggedin) die("<meta http-equiv=\"refresh\" content=\"0;url=index.php\">");

    if(isset($_POST['id'])){
        $post_id = sanitizeString($_POST['id']);
        $result = queryMysql("SELECT * FROM msgboard WHERE id='$post_id'");
        if ($result->rowCount()){
            $row  = $result->fetch();
            //Read the filename
            $filename = "/userdata/" . $row['attachment'];
            //Check the file exists or not
            if(file_exists($filename)) {

                //Define header information
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Cache-Control: no-cache, must-revalidate");
                header("Expires: 0");
                header('Content-Disposition: attachment; filename="'.basename($filename).'"');
                header('Content-Length: ' . filesize($filename));
                header('Pragma: public');

                //Clear system output buffer
                flush();

                //Read the size of the file
                readfile($filename);

                //Terminate from the script
                die("");
            }else{
                die("<meta http-equiv=\"refresh\" content=\"0;url=index.php\">");
            }
        }
    }else
        die("<meta http-equiv=\"refresh\" content=\"0;url=index.php\">");
?>