<?php 
  require_once 'header.php';
  
  if (!$loggedin) die("</div></body></html>");
  echo "<a data-role='button' data-inline='true' data-transition='slide' href='msgboard.php?r=$randstr' data-icon='back'>Go Back</a>";

  if(isset($_GET['id'])){
    $post_id = sanitizeString($_GET['id']);
    $result = queryMysql("SELECT * FROM msgboard WHERE id='$post_id'");
    if ($result->rowCount()){
        $row  = $result->fetch();
        $post_title = $row['title'];
        $post_user = $row['auth'];
        $post_time = $row['time'];
        $post_msg = showBBcodes($row['message']);
        $post_attach = $row['attachment'];
        if($user == $post_user){
          if(isset($_POST['action'])){
            if($_POST['action'] == 'delete'){
              echo("Deleting post...<br>");
              queryMysql("DELETE FROM msgboard WHERE id='$post_id'");
              echo("Done");
              die("<meta http-equiv=\"refresh\" content=\"0;url=msgboard.php?r=$randstr\">");
            }else if($_POST['action'] == 'delete_attach'){
              echo("Deleting Attachment...<br>");
              if(file_exists("/userdata/$post_attach"))
                unlink("/userdata/$post_attach");
              queryMysql("UPDATE msgboard SET attachment=NULL where id='$post_id'");
              echo("Done");
              die("<meta http-equiv=\"refresh\" content=\"0;url=\">");
            }
          }
          if($post_attach)
            echo "<form id='delete' method='post' action='post.php?id=$post_id'>
                <input type='hidden' name='action' value='delete' />
                </form>";
          echo "<form id='delete_attach' method='post' action='post.php?id=$post_id'>
                <input type='hidden' name='action' value='delete_attach' />
                </form>";
          echo "<form id='modify' method='post' action='newmsg.php'>
                <input type='hidden' name='action' value='modify' />
                <input type='hidden' name='id' value='$post_id' />
                </form>";
          echo "<a data-role='button' data-inline='true' data-transition='slide' 
                onclick=\"document.getElementById('delete').submit();\" data-icon='delete'>Delete Post</a>";
          echo "<a data-role='button' data-inline='true' data-transition='slide' 
                onclick=\"document.getElementById('modify').submit();\" data-icon='edit'>Modify Post</a>";
          if($post_attach)
            echo "<a data-role='button' data-inline='true' data-transition='slide' 
                onclick=\"document.getElementById('delete_attach').submit();\" data-icon='delete'>Delete Attachment</a>";
        }
        echo("<br><h1><b>$post_title</b></h1><br>");
        echo("<b>Posted By: </b><a data-transition='slide' href='members.php?view=" . $post_user . "&r=$randstr'>$post_user</a><br>");
        echo("<b>Post Time: </b>");
        echo date('M jS Y g:ia: ', $post_time);
        echo("<br><br>$post_msg<br>");
        if($post_attach){
          echo "<form id='download' method='post' action='download.php'>
                <input type='hidden' name='id' value='$post_id' />
                </form>"; 
          echo("<br><b>Attachment: </b>$post_attach<br> <a data-role='button' data-inline='true' 
          onclick=\"document.getElementById('download').submit();\" data-icon='arrow-d'>Download</a>");
        }
    }else
        die("<meta http-equiv=\"refresh\" content=\"0;url=msgboard.php?r=$randstr\">");


  }else
    die("<meta http-equiv=\"refresh\" content=\"0;url=msgboard.php?r=$randstr\">");

?>

    </div><br>
  </body>
</html>
