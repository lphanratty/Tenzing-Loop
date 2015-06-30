<?php
//Set up our connection info
$DBServer = 'localhost'; // e.g 'localhost' or '192.168.1.100'
$DBUser   = 'trevorp3_recruit';
$DBPass   = '2014l00p!';
$DBName   = 'trevorp3_ten_loop';
$link = new mysqli($DBServer, $DBUser, $DBPass, $DBName);

if ($link->connect_error) {
  trigger_error('Database connection failed: '  . $link->connect_error, E_USER_ERROR);
}
?>