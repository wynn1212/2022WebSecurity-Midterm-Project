<?php
  require_once 'header.php';

  if (isset($_SESSION['user'])){
    destroySession();
    echo "<br><div class='center'>You have been logged out. Please
         <a data-transition='slide'
           href='index.php?r=$randstr'>click here</a>
           to refresh the screen.<br><br>Redirecting...</div>";
    echo("<meta http-equiv=\"refresh\" content=\"0;url=index.php?r=$randstr\">");
  }
  else echo "<div class='center'>You cannot log out because
             you are not logged in</div>";
?>
    </div>
  </body>
</html>
