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
    // SIGNUP - CREATE NEW USER
    if(isset($_POST['signup_user']))
    {
      $user = new User();
      $user->signup(
        $_POST['user_name'],
        $_POST['user_gender'],
        $_POST['user_dob'],
        $_POST['user_email'],
        $_POST['user_pass'],
        $_POST['confirm_pass']
      );
    }
  }

?>

<main>
  <div class="signup-page flex p-2">

    <form method="post" enctype="multipart/form-data">
      <div class="text-center">
        <h1>Create An Account</h1>
      </div>

      <div class="form-control">
        <label>Username</label>
        <input type="text" name="user_name" maxlength="30" required autocomplete>
      </div>

      <div class="flex">
        <div class="form-control">
          <label>Gender</label>
          <select name="user_gender" required>
            <option value="m">Male</option>
            <option value="f">Female</option>
          </select>
        </div>

        <div class="form-control">
          <label>Date Of Birth</label>
          <input type="date" name="user_dob" required>
        </div>
      </div>

      <div class="form-control">
        <label>Email</label>
        <input type="email" name="user_email" maxlength="255" required autocomplete>
      </div>

      <div class="form-control">
        <label>Password</label>
        <input type="password" name="user_pass" maxlength="255" required autocomplete>
      </div>

      <div class="form-control">
        <label>Confirm Password</label>
        <input type="password" name="confirm_pass" maxlength="255" required autocomplete>
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
        <button type="submit" name="signup_user">Create Account</button>
      </div>

      <div class="more text-center">
        <h3><a href="login.php">Already Signed Up? Click Here To (Login)</a></h3>
      </div>
    </form>

  </div>
</main>

<?php

  include_once '../template/aside.php';
  include '../template/advert.php';
  include_once '../template/footer.php';

?>
