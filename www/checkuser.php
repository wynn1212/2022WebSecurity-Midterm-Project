<?php
  require_once 'functions.php';

  if ($logger) dumpVar($_POST);

  if (isset($_POST['user'])){
    $user   = sanitizeString($_POST['user']);
    $result = queryMysql("SELECT * FROM members WHERE user='$user'");

    if ($result->rowCount())
      echo  "<span class='taken'>&nbsp;&#x2718; " .
            "The username '$user' is taken</span>";
    else if (checkBadWorld($user))
      echo  "<span class='taken'>&nbsp;&#x2718; " .
            "The username '$user' is taken</span>";
    else
      echo "<span class='available'>&nbsp;&#x2714; " .
           "The username '$user' is available</span>";
  }
?>
