<?php
  if(!isset($_GET["error"]))
  {
    $error = null;
  }
  else
  {
    $error = $_GET["error"];
  }

  $page_author = "";
  $page_description = "";
  $page_keywords = "";
  $page_title = "";

  include_once '../template/header.php';
  include_once '../template/hero.php';
  include '../template/advert.php';


  if($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    // LOG IN TO USER ACCOUNT
    if(isset($_POST['login_user']))
    {
      $user = new User();
      $user->login(
        $_POST['user_name'],
        $_POST['user_pass']
      );
    }
  }

?>


<main>
  <div class="login-page flex p-2">

    <form method="post" enctype="multipart/form-data">
      <div class="text-center">
        <h1>Login</h1>
      </div>

      <div class="form-control">
        <label>Enter Username</label>
        <input type="text" name="user_name" placeholder="Username" autocomplete>
      </div>

      <div class="form-control">
        <label>Enter Password</label>
        <input type="password" name="user_pass" placeholder="Enter Password" autocomplete>
      </div>

      <?php
        if(isset($error))
        {
          echo "
            <div class=\"error-box\">
              <h4>Error Message</h4>
              <p>{$error}</p>
            </div>
          ";
        };
      ?>

      <div class="form-control">
        <button type="submit" name="login_user">Login</button>
      </div>

      <div class="more text-center">
        <h3><a href="signup.php">Create A New Account? Click Here To (Signup)</a></h3>
      </div>
    </form>

  </div>
</main>


<?php

  include_once '../template/aside.php';
  include '../template/advert.php';
  include_once '../template/footer.php';

?>
