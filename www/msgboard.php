<?php
  require_once 'header.php';
  
  if (!$loggedin) die("</div></body></html>");
  echo "<a data-role='button' data-inline='true' data-transition='slide' href='newmsg.php?r=$randstr' data-icon='plus'>New Post</a><br>";
  
  if(isset($_GET['view'])){
    $view = sanitizeString($_GET['view']);
    
    if ($view == $user) $name1 = $name2 = "Your";
    else{
      $name1 = "<a href='members.php?view=$view&r=$randstr'>$view</a>'s";
      $name2 = "$view's";
    }

    echo "<h3>$name1 Posts</h3>";
    showProfile($view);
    $result = queryMysql("SELECT * FROM msgboard WHERE auth='$view'");
    $i = 0;
    if ($result->rowCount()){
      while ($row = $result->fetch()){
          $posts_id[$i] = $row['id'];
          $posts_title[$i] = $row['title'];
          $posts_user[$i] = $row['auth'];
          $posts_time[$i] = $row['time'];
          $i++;
      }
      echo "<ul>";
      for($j=0; $j<$i; $j++){
        echo "<li>";  
        echo date('M jS Y g:ia: ', $posts_time[$j]);
        echo " Post: ";
        echo "<a data-transition='slide' href='post.php?id=" . $posts_id[$j] . "&r=$randstr'><b>$posts_title[$j]</b></a><br>";
      }
      echo "</ul>";
      
    }else
      echo "<h1>No post yet</h1>";

  }else{
    //Title from config db
    $result = queryMysql("SELECT * FROM msgboard");
    $i = 0;
    if ($result->rowCount()){
      while ($row = $result->fetch()){
          $posts_id[$i] = $row['id'];
          $posts_title[$i] = $row['title'];
          $posts_user[$i] = $row['auth'];
          $posts_time[$i] = $row['time'];
          $i++;
      }
      echo "<ul>";
      for($j=0; $j<$i; $j++){
        echo "<li>";  
        echo date('M jS Y g:ia: ', $posts_time[$j]);
        echo "<a data-transition='slide' href='members.php?view=" . $posts_user[$j] . "&r=$randstr'>$posts_user[$j]</a> Post: <br>";
        echo "<a data-transition='slide' href='post.php?id=" . $posts_id[$j] . "&r=$randstr'><b>$posts_title[$j]</b></a><br>";
      }
      echo "</ul>";
      
    }else
      echo "<h1>No one post message yet! Be the first one to post the message!</h1>";
  }

  

?>

    </div><br>
  </body>
</html>
