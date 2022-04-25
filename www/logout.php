<?php
  require_once 'header.php';

  if (isset($_SESSION['user']) && isset($_POST['logout'])){
    $logout_confirm = sanitizeString($_POST['logout']);
    $logout_confirm = preg_replace('/\s\s+/', ' ', $logout_confirm);
    csrfValidate($_POST['token'], $_SESSION['token']);
    if($logout_confirm == 'confirm'){
      destroySession();
      echo "<br><div class='center'>You have been logged out. Please
          <a data-transition='slide'
            href='index.php?r=$randstr'>click here</a>
            to refresh the screen.<br><br>Redirecting...</div>";
      echo("<meta http-equiv=\"refresh\" content=\"0;url=index.php?r=$randstr\">");
    }
  }

  $csrf_token = csrfGetToken();

  if (isset($_SESSION['user'])){
    echo <<<_END
      <div class='center'>
        <br>
        <h1>Really Logout?</h1>
        <form id='logout' method='post' action='logout.php'>
          <input type='hidden' name='logout' value='confirm' />
          <input type="hidden" name="token" value="$csrf_token">
        </form>
        <a data-role='button' data-inline='true' data-transition='slide' 
          href='members.php?view=$user&r=$randstr' data-icon='delete'>No! Back to home!</a>
        <a data-role='button' data-inline='true' data-transition='slide' 
          onclick="document.getElementById('logout').submit();" data-icon='check'>Yes! Logout!</a>
      </div>
_END;
  }
  else echo "<div class='center'>You cannot log out because
             you are not logged in</div>";
?>
    </div>
  </body>
</html>