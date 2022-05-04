<?php
  require_once 'header.php';

  if ($logger) dumpVar($_POST);

  if (!$loggedin) die("</div></body></html>");

  echo "<h3>Your Profile</h3>";

  $result = queryMysql("SELECT * FROM members WHERE user='$user'");
  if ($result->rowCount()){
    $row  = $result->fetch();
    //$uid = stripslashes($row['uid']);
    $uid = $row['uid'];
  }

  $result = queryMysql("SELECT * FROM config WHERE name='title'");

  if(isset($_POST['settitle'])){
    csrfValidate($_POST['token'], $_SESSION['token']);
    if($uid != 1)
      die("<meta http-equiv=\"refresh\" content=\"0;url=profile.php?r=$randstr\">");
    $set_title = sanitizeString($_POST['settitle']);
    $set_title = preg_replace('/\s\s+/', ' ', $set_title);
    if ($result->rowCount()){
      $row  = $result->fetch();
      queryMysql("UPDATE config SET value='$set_title' where name='title'");
    }else 
      queryMysql("INSERT INTO config VALUES(NULL, 'title', '$set_title')");
    echo("<meta http-equiv=\"refresh\" content=\"0;url=profile.php?r=$randstr\">");
  }

  $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");
  
  if (isset($_POST['text'])){
    csrfValidate($_POST['token'], $_SESSION['token']);
    $text = sanitizeString($_POST['text']);
    #$text = preg_replace('/\s\s+/', ' ', $text);

    if ($result->rowCount())
      queryMysql("UPDATE profiles SET text='$text' where user='$user'");
    else 
      queryMysql("INSERT INTO profiles VALUES('$user', '$text', '')");

    $text = $_POST['text'];
  }else{
    if ($result->rowCount()){
      $row  = $result->fetch();
      $text = $row['text'];
      #$text = stripslashes($row['text']);
    }
    else $text = "";
  }

  #$text = stripslashes(preg_replace('/\s\s+/', ' ', $text));
  

  $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");
  if (isset($_FILES['image']['name']) && $_FILES['image']['tmp_name'] != ""){
    csrfValidate($_POST['token'], $_SESSION['token']);
    $rand_image_prefix = substr(md5(rand()), 0, 7);
    $user_image = "$rand_image_prefix-$user";
    $saveto = stripslashes(preg_replace('/\\\\+/','',"userdata/images/$user_image.jpg"));
    //move_uploaded_file($_FILES['image']['tmp_name'], $saveto);
    resizePicture($_FILES['image']['tmp_name'], $_FILES['image']['type'], $saveto);
    unlink($_FILES['image']['tmp_name']);

    if ($result->rowCount()){
      $row  = $result->fetch();
      // Delete old image.
      $orig_user_image = $row['image'];
      if(file_exists("userdata/images/$orig_user_image.jpg"))
        unlink("userdata/images/$orig_user_image.jpg");
      queryMysql("UPDATE profiles SET image='$user_image' where user='$user'");
    }else 
      queryMysql("INSERT INTO profiles VALUES('$user', '', '$user_image')");

  }

  $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");
  if(isset($_POST['image_url']) && $_POST['image_url'] != ''){
    csrfValidate($_POST['token'], $_SESSION['token']);
    $rand_image_prefix = substr(md5(rand()), 0, 7);
    $user_image = "$rand_image_prefix-$user";
    $url=$_POST['image_url'];
    $saveto = stripslashes(preg_replace('/\\\\+/','',"userdata/images/$user_image.jpg"));
    $tmp_path = "/tmp/tmp-$user";
    $dl_image = file_get_contents($url);
    file_put_contents($tmp_path,$dl_image);
    //rename($tmp_path,$saveto);
    $tmp_type = mime_content_type($tmp_path);
    resizePicture($tmp_path, $tmp_type, $saveto);
    unlink($tmp_path);

    if ($result->rowCount()){
      $row  = $result->fetch();
      // Delete old image.
      $orig_user_image = ($row['image']);
      if(file_exists("userdata/images/$orig_user_image.jpg"))
        unlink("userdata/images/$orig_user_image.jpg");
      queryMysql("UPDATE profiles SET image='$user_image' where user='$user'");
    }else 
      queryMysql("INSERT INTO profiles VALUES('$user', '', '$user_image')");

  }

  $csrf_token = csrfGetToken();
  
  showProfile($user);

  $admin_panel = <<<_ADMIN
                  <form data-ajax='false' method='post'
                  action='profile.php?r=$randstr' enctype='multipart/form-data'>
                  <h3>Enter or edit your website's title.</h3>
                  <input type='text' name='settitle' value="$title" placeholder="Default Title"><br>
                  <input type='submit' value='Update Title'>
                  <input type="hidden" name="token" value="$csrf_token">
                  </form>
_ADMIN;
  
  if($uid == 1){
    echo $admin_panel;
  }  


echo <<<_END
      <form data-ajax='false' method='post'
        action='profile.php?r=$randstr' enctype='multipart/form-data'>
      <h3>Enter or edit your details and/or upload an image</h3>
      (Details Accept BBCode)
      <textarea name='text' placeholder="You did not enter your details! Click here to start editing.">$text</textarea><br>
      Image File: <input type='file' name='image' size='14'>
      Image URL: <input type='text' name='image_url' placeholder="https://"></input>
      <input type='submit' value='Save Profile'>
      <input type="hidden" name="token" value="$csrf_token">
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
    </div><br>
  </body>
</html>
_END;
?>
