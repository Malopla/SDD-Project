<?php

  //The database needs to be created in the virtual server first

  $dbname = 'test';
  $user = 'root';
  $pass = 'root1234';
  $dbconn = new PDO('mysql:host=localhost;dbname='.$dbname, $user, $pass);
  
?>
