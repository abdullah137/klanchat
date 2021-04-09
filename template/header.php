<?php
session_start();

require_once '../src/classes/dbhandle.class.php';
require_once '../src/classes/user.class.php';
require_once '../src/classes/post.class.php';
require_once '../src/classes/comment.class.php';
require_once '../src/classes/category.class.php';

require_once '../src/functions/helper.php';

// Connect To Database
$db = new DBHandle();
$dbconn = $db->connect();

?>

<!DOCTYPE html>
<html>
<head>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="author" content="<?php echo $page_author; ?>">
  <meta name="description" content="<?php echo $page_description; ?>">
  <meta name="keywords" content="<?php echo $page_keywords; ?>">
  <meta charset="utf-8">

  <!-- <link rel="shortcut icon" href=".ico"> -->
  <link rel="stylesheet" href="../assets/css/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/master.css">

  <title><?php echo $page_title; ?></title>
</head>
<body>

<div class="wrapper">
  <header>
    <div class="flex">
      <nav class="top-nav flex">
        <div class="flex">
          <a href="#"><i class="fab fa-facebook"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-youtube"></i></a>
          <a href="#"><i class="fab fa-github"></i></a>
        </div>
        <div class="flex">
          <?php
          if(isset($_SESSION['user_id']))
          {
            echo '<a href="logout.php">Logout</a>';
          }
          else
          {
            echo '<a href="login.php">Login</a>';
          }
          ?>
          <a href="signup.php">Signup</a>
        </div>
      </nav>

      <nav class="page-nav flex py-1">
        <ul class="flex py-1">
          <li id="toggle-btn"><i class="fa fa-bars"></i></li>
          <li><a href="index.php">Khatbox</a></li>
        </ul>
        <form class="top-search flex" action="" method="post">
          <input type="search" name="search_text" placeholder="Search...">
          <button type="submit" name="search_btn">Search</button>
        </form>
        <div class="flex">
          <a href="index.php">Home</a>
          <a href="./">About</a>
          <a href="./">Adverts</a>
          <a href="./">Help</a>
          <?php
          if(isset($_SESSION['user_id']))
          {
          ?>
            <a href="profile.php">Profile</a>
          <?php
          }
          ?>
        </div>
      </nav>

      <nav class="top-cat-nav flex py-1">
        <div class="flex">
        <?php
          $cat = new Category();
          $catResults = $cat->getAllCategories();
          foreach ($catResults as $cat)
          {
        ?>
          <a href="<?php echo "./index.php?cat_id={$cat['cat_id']}&cat_name=" . strtolower($cat['cat_name']); ?>">
            <?php echo $cat['cat_name'] ?>
          </a>
        <?php
          }
        ?>
        </div>
      </nav>
    </div>
  </header>
