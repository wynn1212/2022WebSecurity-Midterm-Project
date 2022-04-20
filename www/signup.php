<?php
  require_once 'header.php';

  if ($logger) dumpVar($_POST);

echo <<<_END
  <script>
    function checkUser(user)
    {
      if (user.value == '')
      {
        $('#used').html('&nbsp;')
        return
      }

      $.post
      (
        'checkuser.php',
        { user : user.value },
        function(data)
        {
          $('#used').html(data)
        }
      )
    }
  </script>  
_END;

  $error = $user = $pass = "";
  if (isset($_SESSION['user'])) destroySession();

  if (isset($_POST['user'])){
    $user = sanitizeString($_POST['user']);
    $pass = sanitizeString($_POST['pass']);
    $confpass = sanitizeString($_POST['confpass']);

    if ($user == "" || $pass == "" || $confpass == "")
      $error = 'Not all fields were entered<br><br>';
    else if (checkBadWorld($user))
      $error = 'That username already exists<br><br>';
    else if ($pass != $confpass)
      $error = 'Password and Confirm Password mismatch<br><br>';
    else{
      $result = queryMysql("SELECT * FROM members WHERE user='$user'");

      if ($result->rowCount())
        $error = 'That username already exists<br><br>';
      else{
        queryMysql("INSERT INTO members VALUES(NULL ,'$user', '$pass')");
        echo('<h4>Account created</h4>Please Log in.<br><br>Redirecting to login page in 3 seconds...</div></body></html>');
        die("<meta http-equiv=\"refresh\" content=\"3;url=login.php?r=$randstr\">");
      }
    }
  }

echo <<<_END
      <form method='post' action='signup.php?r=$randstr'>$error
      <div data-role='fieldcontain'>
        <label></label>
        Please enter your details to sign up
      </div>
      <div data-role='fieldcontain'>
        <label>Username</label>
        <input type='text' maxlength='1024' name='user' value='$user'
          onBlur='checkUser(this)'>
        <label></label><div id='used'>&nbsp;</div>
      </div>
      <div data-role='fieldcontain'>
        <label>Password</label>
        <input type='password' maxlength='1024' name='pass' value=''>
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
