<!DOCTYPE html> <!-- Example 03: setup.php -->
<html>
  <head>
    <title>Setting up database</title>
  </head>
  <body>
    <h3>Setting up...</h3>

<?php
  require_once 'functions.php';

  createTable('config',
              'uid INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              name VARCHAR(32),
              value VARCHAR(1024),
              INDEX(name(6))');
  
  createTable('members',
              'uid INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              user VARCHAR(1024),
              pass VARCHAR(1024),
              INDEX(user(6))');

  createTable('msgboard', 
              'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              auth VARCHAR(1024),
              time INT UNSIGNED,
              title VARCHAR(4096),
              message VARCHAR(4096),
              attachment VARCHAR(4096),
              INDEX(auth(6))');
  
  createTable('messages', 
              'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              auth VARCHAR(1024),
              recip VARCHAR(16),
              pm CHAR(1),
              time INT UNSIGNED,
              message VARCHAR(4096),
              INDEX(auth(6)),
              INDEX(recip(6))');

  createTable('friends',
              'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              user VARCHAR(1024),
              friend VARCHAR(1024),
              INDEX(user(6)),
              INDEX(friend(6))');

  createTable('profiles',
              'user VARCHAR(1024) PRIMARY KEY,
              text VARCHAR(4096),
              image VARCHAR(4096),
              INDEX(user(6))');
?>

    <br>...done.
  </body>
</html>
