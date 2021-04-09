<?php
session_start();
require_once '../src/classes/user.class.php';
require_once '../src/functions/helper.php';

if(empty($_SESSION['user_id']))
{
  redirectTo("./index.php");
}
else
{
  $_SESSION['user_id'] = "";
  session_unset();
  session_destroy();
  redirectTo("./index.php");
}

?>
