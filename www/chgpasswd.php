<?php
  require_once 'header.php';

  if (!$loggedin) die("</div></body></html>");

  $error = $curpass = $newpass = $confpass = "";

  if (isset($_POST['curpass'])){
    $result = queryMySQL("SELECT pass FROM members WHERE user='$user'");
    $row = $result->fetch();

    $curpass = sanitizeString($_POST['curpass']);
    $newpass = sanitizeString($_POST['newpass']);
    $confpass = sanitizeString($_POST['confpass']);

    if ($curpass == "" || $newpass == "" || $confpass == "")
      $error = 'Not all fields were entered<br><br>';
    else if ($curpass != $row['pass'])
      $error = 'Invalid Current Password<br><br>';
    else if ($newpass != $confpass)
      $error = 'New Password and Confirm Password mismatch<br><br>';
    else{
        queryMysql("UPDATE members SET pass='$newpass' WHERE user='$user'");
        echo('<h4>Password Changed</h4><br><br>Redirecting to home page in 3 seconds...</div></body></html>');
        die("<meta http-equiv=\"refresh\" content=\"3;url=members.php?view=$user&r=$randstr\">");
    }
  }

echo <<<_END
        <form method='post' action='chgpasswd.php?r=$randstr'>$error
        <div data-role='fieldcontain'>
            <label></label>
            Please enter your details to change password
        </div>
        <div data-role='fieldcontain'>
            <label>Current Password</label>
            <input type='password' maxlength='1024' name='curpass' value=''
            onBlur='checkUser(this)'>
            <label></label><div id='used'>&nbsp;</div>
        </div>
        <div data-role='fieldcontain'>
            <label>New Password</label>
            <input type='password' maxlength='1024' name='newpass' value=''>
        </div>
        <div data-role='fieldcontain'>
            <label>Confirm Password</label>
            <input type='password' maxlength='1024' name='confpass' value=''>
        </div>
        <div data-role='fieldcontain'>
            <label></label>
            <input data-transition='slide' type='submit' value='Sign Up'>
        </div>
        </div>
    </body>
</html>
_END;
?>