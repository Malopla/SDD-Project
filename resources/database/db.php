<?php

  //The database needs to be created in the virtual server first

  $dbname = 'sdd';
  $user = 'root';
  $pass = 'Th1rty-F0ur-Th1rty';
  $dbconn = new PDO('mysql:host=localhost;dbname='.$dbname, $user, $pass);
  
?>