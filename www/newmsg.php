<?php
  require_once 'header.php';
  $error = "";

  if (!$loggedin) die("</div></body></html>");

  if (isset($_POST['modify_id'])){
    $post_id = sanitizeString($_POST['modify_id']);
    $result = queryMysql("SELECT * FROM msgboard WHERE id='$post_id'");
    if ($result->rowCount()){
      $row  = $result->fetch();
      $post_user = $row['auth'];
      if($user != $post_user)
        die("<meta http-equiv=\"refresh\" content=\"0;url=newmsg.php?r=$randstr\">");
      
      if($_POST['posttitle']){
        $post_title = sanitizeString($_POST['posttitle']);
        $post_title = preg_replace('/\s\s+/', ' ', $post_title);
        $post_msg = sanitizeString($_POST['postmsg']);
        $post_time = time();
        $sql_update = "UPDATE msgboard SET time='$post_time'";
        if($post_title != "" && $post_title != " ")
          $sql_update .= ",title='$post_title'";
        if($post_msg != "")
          $sql_update .= ",message='$post_msg'";
          $sql_update .= " where id='$post_id'";
        queryMysql($sql_update);
      }

      if (isset($_FILES['postfile']['name']) && $_FILES['postfile']['tmp_name'] != ""){
        $filename = sanitizeString($_FILES['postfile']['name']);
        $filename = preg_replace('/\s\s+/', ' ', $filename);
        $rand_file_prefix = substr(md5(rand()), 0, 7);
        $user_file = "$rand_file_prefix-$filename";
        $saveto = stripslashes(preg_replace('/\\\\+/','',"/userdata/$user_file"));
        move_uploaded_file($_FILES['postfile']['tmp_name'], $saveto);

        if ($orig_user_file = $row['attachment']){
          // Delete old file.
          if(file_exists("/userdata/$orig_user_file"))
            unlink("/userdata/$orig_user_file");
          queryMysql("UPDATE msgboard SET attachment='$user_file' where id='$post_id'");
        }else 
          queryMysql("UPDATE msgboard SET attachment='$user_file' where id='$post_id'");

      }

      die("<meta http-equiv=\"refresh\" content=\"0;url=post.php?id=$post_id&r=$randstr\">");
    }else
      die("<meta http-equiv=\"refresh\" content=\"0;url=newmsg.php?r=$randstr\">");
  }else if (isset($_POST['posttitle'])){
    $post_title = sanitizeString($_POST['posttitle']);
    $post_title = preg_replace('/\s\s+/', ' ', $post_title);
    $post_msg = sanitizeString($_POST['postmsg']);
    $post_time = time();
    if($post_title == "" || $post_title == " ")
      $error = "You must enter the Title field!";
    else{
      $result = queryMysql("SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$data' AND TABLE_NAME='msgboard'");
      if ($result->rowCount()){
        $row = $result->fetch();
        $post_id = $row['AUTO_INCREMENT'];
      }else
        die("Error! SQL Exception Found at <b>AUTO_INCREMENT</b></div></body></html>");
      
      if (isset($_FILES['postfile']['name']) && $_FILES['postfile']['tmp_name'] != ""){
        $filename = sanitizeString($_FILES['postfile']['name']);
        $filename = preg_replace('/\s\s+/', ' ', $filename);
        $rand_file_prefix = substr(md5(rand()), 0, 7);
        $user_file = "$rand_file_prefix-$filename";
        $saveto = stripslashes(preg_replace('/\\\\+/','',"/userdata/$user_file"));
        move_uploaded_file($_FILES['postfile']['tmp_name'], $saveto); 
        queryMysql("INSERT INTO msgboard VALUES(NULL, '$user', '$post_time', '$post_title', '$post_msg', '$user_file')");

      }else
        queryMysql("INSERT INTO msgboard VALUES(NULL, '$user', '$post_time', '$post_title', '$post_msg', NULL)");
      
      die("<meta http-equiv=\"refresh\" content=\"0;url=post.php?id=$post_id&r=$randstr\">");

    }
  }else if (isset($_POST['action'])){
    $post_action = $_POST['action'];
    if($post_action == 'modify'){
      $post_id = sanitizeString($_POST['id']);
      $result = queryMysql("SELECT * FROM msgboard WHERE id='$post_id'");
      if ($result->rowCount()){
          $row = $result->fetch();
          $post_user = $row['auth'];
          if($user != $post_user)
            die("<meta http-equiv=\"refresh\" content=\"0;url=newmsg.php?r=$randstr\">");
          $post_title = $row['title'];
          $post_msg = $row['message'];
          echo "<a data-role='button' data-inline='true' data-transition='slide' href='post.php?id=$post_id&r=$randstr' data-icon='back'>Go Back</a>";
          echo "<h1>Modify Post</h1><br>Leave field empty to remain unchanged<br>";
      }else
          die("<meta http-equiv=\"refresh\" content=\"0;url=newmsg.php?r=$randstr\">");
    }
  }else{
    echo "<a data-role='button' data-inline='true' data-transition='slide' href='msgboard.php?r=$randstr' data-icon='back'>Go Back</a>";
    echo "<h1>New Post</h1><br>";
  }

  echo <<<_MID
    <span class='error'>$error</span>  
    <form data-ajax='false' method='post'
        action='newmsg.php?r=$randstr' enctype='multipart/form-data'>
_MID;
  if(isset($_POST['action']))
    echo "<input type='hidden' name='modify_id' value='$post_id' />";
  echo <<<_END
    <h3>Title:</h3>
    <input type='text' name='posttitle' placeholder='Enter your title here...' value='$post_title'>
    <h3>Messages: (Accept BBCode)</h3>
    <textarea name='postmsg' placeholder="Enter your message here...">$post_msg</textarea><br>
    Attachment File: <input type='file' name='postfile' size='14'>
    <input type='submit' value='Post Message'>
    </form>
    <h3>BBCode Usage:</h3>
    <ul>
    <li>Hello [br] World<br>
    Hello <br> World
    <li> Use this to print [dq]Double Quote[dq].<br>
    Use this to print "Double Quote".
    <li>[b]<b>bolded text</b>[/b]<br>
    <li>[i]<i>italicized text</i>[/i]<br>
    <li>[u]<ins>underlined text</ins>[/u]<br>
    <li>[url]<a href="https://en.wikipedia.org">https://en.wikipedia.org</a>[/url]<br>
    <li>[url=https://en.wikipedia.org]<a href="https://en.wikipedia.org">English Wikipedia</a>[/url]<br>
    <li>[img]https://upload.wikimedia.org/wikipedia/commons/7/70/Example.png[/img]<br>
    <img src="https://upload.wikimedia.org/wikipedia/commons/7/70/Example.png"/>
    </ul>
_END;
?>

    </div><br>
  </body>
</html>
