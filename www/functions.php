<?php 
  $logger = TRUE;
  $host = '?';   // SQL Hostname. Change as necessary
  $data = '?';   // SQL DB Name. Change as necessary
  $user = '?';   // DB Username. Change as necessary
  $pass = '?';   // DB Password. Change as necessary
  $chrs = 'utf8mb4';
  $attr = "mysql:host=$host;dbname=$data;charset=$chrs";
  $opts =
  [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ];
  
  try{
    $pdo = new PDO($attr, $user, $pass, $opts);
  }catch (PDOException $e){
    throw new PDOException($e->getMessage(), (int)$e->getCode());
  }

  function checkBadWorld($word){
    $badwordlist = ["\\", '&quot', "&lt", "&gt", "&amp", "#", "/"];
    for($cnt = 0; $cnt < count($badwordlist); $cnt++){
      if(strpos($word, $badwordlist[$cnt]) !== false){
        return 1;
      }
    }
    return 0;
  }

  function createTable($name, $query){
    queryMysql("CREATE TABLE IF NOT EXISTS $name($query) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Table '$name' created or already exists.<br>";
  }

  function queryMysql($query){
    global $pdo;
    return $pdo->query($query);
  }

  function destroySession(){
    $_SESSION=array();

    if (session_id() != "" || isset($_COOKIE[session_name()]))
      setcookie(session_name(), '', time()-2592000, '/');

    session_destroy();
  }

  function sanitizeString($var){
    global $pdo;

    $var = strip_tags($var);
    $var = htmlentities($var);

    if (get_magic_quotes_gpc())
      $var = stripslashes($var);

    $result = $pdo->quote($var);          // This adds single quotes
    //return str_replace("'", "", $result); // So now remove them
    return substr($result,1,-1);          // Fix that only remove single quote on the strings' first and last quote.
  }

  function showProfile($user){
    global $pdo;

    if($result = $pdo->query("SELECT * FROM profiles WHERE user='$user'")){
      while ($row = $result->fetch()){
        $user_image = stripslashes(preg_replace('/\\\\+/','',$row['image']));
        if (file_exists("userdata/images/$user_image.jpg"))
          echo "<img src=\"userdata/images/$user_image.jpg\" style='float:left;'>";
      }
    }
    
    //if (file_exists("userdata/images/$user.jpg"))
    //  echo "<img src='userdata/images/$user.jpg' style='float:left;'>";

    if($result = $pdo->query("SELECT * FROM profiles WHERE user='$user'")){
      while ($row = $result->fetch()){
        //echo stripslashes($row['text']);
        echo showBBCodes($row['text']);
        echo "<br style='clear:left;'><br>";
      }
    } else {
      echo "<p>Nothing to see here, yet</p><br>";
    }
  }

  function resizePicture($img_path, $img_type, $save_to){
    $typeok = TRUE;

    switch($img_type){
      case "image/gif":   $src = imagecreatefromgif($img_path); break;
      case "image/jpeg":  // Both regular and progressive jpegs
      case "image/pjpeg": $src = imagecreatefromjpeg($img_path); break;
      case "image/png":   $src = imagecreatefrompng($img_path); break;
      default:            $typeok = FALSE; break;
    }

    if ($typeok){
      list($w, $h) = getimagesize($img_path);

      $max = 100;
      $tw  = $w;
      $th  = $h;

      if ($w > $h && $max < $w){
        $th = $max / $w * $h;
        $tw = $max;
      }elseif ($h > $w && $max < $h){
        $tw = $max / $h * $w;
        $th = $max;
      }elseif ($max < $w){
        $tw = $th = $max;
      }

      $tmp = imagecreatetruecolor($tw, $th);
      imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
      imageconvolution($tmp, array(array(-1, -1, -1),
        array(-1, 16, -1), array(-1, -1, -1)), 8, 0);
      imagejpeg($tmp, $save_to);
      imagedestroy($tmp);
      imagedestroy($src);
    }
  }

  function csrfGetToken(){
    if (isset($_SESSION['user'])){
      $_SESSION['token'] = md5(uniqid(mt_rand(), true));
      return $_SESSION['token'];
    }else
      return NULL;
  }
  
  function csrfValidate($post_token, $sess_token, $custom_url) {
    $comp_token = sanitizeString($post_token);
    $comp_token = preg_replace('/\s\s+/', ' ', $comp_token);
    if(!$comp_token || $comp_token !== $sess_token)
      if(isset($custom_url))
        die("<meta http-equiv=\"refresh\" content=\"0;url=$custom_url\">");
      else
        die("<meta http-equiv=\"refresh\" content=\"0;url=index.php\">");
  }

  /** 
  * A simple PHP BBCode Parser function
  *
  * @author Afsal Rahim
  * @link http://digitcodes.com/create-simple-php-bbcode-parser-function/
  * Hardened by: Me
  **/

  //BBCode Parser function

  function showBBcodes($text) {
    
    // NOTE : I had to update this sample code with below line to prevent obvious attacks as pointed out by many users.
    // Always ensure that user inputs are scanned and filtered properly.
    $text  = htmlspecialchars($text, ENT_QUOTES, $charset);

    // BBcode array
    $find = array(
      '~\[b\](.*?)\[/b\]~s',
      '~\[i\](.*?)\[/i\]~s',
      '~\[u\](.*?)\[/u\]~s',
      '~\[quote\](.*?)\[/quote\]~s',
      '~\[size=(.*?)\](.*?)\[/size\]~s',
      '~\[color=(.*?)\](.*?)\[/color\]~s',
      '~\[url\]((?:ftp|https?)://.*?)\[/url\]~s',
      '~\[url=(.*?)\](.*?)\[/url\]~s',
      '~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~s',
      '~\[br\]~s',
      '~\[dq\]~s'
    );

    // HTML tags to replace BBcode
    $replace = array(
      '<b>$1</b>',
      '<i>$1</i>',
      '<span style="text-decoration:underline;">$1</span>',
      '<pre>$1</'.'pre>',
      '<span style="font-size:$1px;">$2</span>',
      '<span style="color:$1;">$2</span>',
      '<a href="$1">$1</a>',
      '<a href="$1">$2</a>',
      '<img src="$1" alt="$1" />',
      '<br>',
      '"'
    );

    // Replacing the BBcodes with corresponding HTML tags
    return preg_replace($find,$replace,$text);
  }

  function dumpVar($dmp){
    ob_start();
    var_dump($dmp);
    error_log(ob_get_clean());
  }
?>
