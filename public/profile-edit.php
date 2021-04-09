<?php
  $page_author = "";
  $page_description = "";
  $page_keywords = "";
  $page_title = "";
?>

<?php

  include_once '../template/header.php';

  include_once '../template/hero.php';

  include '../template/advert.php';

?>

<?php

  if(!isset($_SESSION['user_id']))
  {
    redirectTo("./index.php");
  }


  /************ SERVER POST REQUEST STARTS ****************/
  if($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    // UPDATE USER ACCOUNT
    if(isset($_POST['update_user']))
    {
      $user = new User();
      $user->update(
        $_SESSION['user_id'],
        $_FILES['user_img'],
        $_POST['user_name'],
        $_POST['user_email'],
        $_POST['user_bio'],
        $_POST['old_pass'],
        $_POST['new_pass']
      );
      redirectTo('./profile-edit.php');
    }

    // DELETE USER ACCOUNT
    if(isset($_POST['delete_user']))
    {
      $user = new User();
      $user->delete($_SESSION['user_id']);
      redirectTo('./logout.php');
    }

  }
  /************ SERVER POST REQUEST ENDS ****************/

  // GET LOGIN USER INFORMATION
  $user = new User();
  $user = $user->select($_SESSION['user_id']);

  $image_path = "../assets/images/uploads/";

?>


<main>
  <div class="profile-page p-2">
    <div class="flex p-2">
      <div class="profile-card flex">
        <div class="user-img-box">
          <img class="user-img" src="<?php echo "{$image_path}{$user['user_img']}"; ?>" alt="profile image">
        </div>
        <div class="text-center">
          <p class="md"><?php echo "{$user['user_name']}"; ?></p>
          <p><?php echo "{$user['user_gender']}"; ?></p>
          <p><?php echo "{$user['user_email']}"; ?></p>
          <p><?php echo "{$user['user_dob']}"; ?></p>
          <button>
            <?php
              echo getUserRankName($user['user_rank']);
            ?>
          </button>
        </div>
      </div>

      <div class="profile-control py-2">
        <ul class="flex">
          <li><a href="profile.php">About</a></li>
          <li><a href="profile-post.php">Posts</a></li>
          <li><a href="profile-comment.php">Comments</a></li>
          <li><a href="profile-edit.php" class="active">Edit Profile</a></li>
        </ul>
      </div>
    </div>


    <div class="profile-display-content p-2">
      <div class="edit-profile">
        <h2 class="text-center">Edit Profile</h2>

        <form class="edit-form" method="post" enctype="multipart/form-data">
          <div>
            <div class="user-img-box">
              <img class="user-img" src="<?php echo "{$image_path}{$user['user_img']}"; ?>" alt="">
            </div>
            <div class="form-control">
              <label for="">User Image</label>
              <input type="file" name="user_img" value="">
            </div>
          </div>
          <div class="form-control">
            <label for="">Username</label>
            <input type="text" name="user_name" value="<?php echo $user['user_name'] ?>" >
          </div>
          <div class="form-control">
            <label for="">Email</label>
            <input type="email" name="user_email" value="<?php echo $user['user_email'] ?>" >
          </div>
          <div class="form-control">
            <label for="">Bio</label>
            <textarea name="user_bio"><?php echo $user['user_bio'] ?></textarea>
          </div>
          <div class="form-control">
            <label for="">Old Password</label>
            <input type="password" name="old_pass">
          </div>
          <div class="form-control">
            <label for="">New Password</label>
            <input type="password" name="new_pass">
          </div>
          <div class="home-btns flex">
            <button class="submit-btn" type="submit" name="update_user">Save Changes</button>
            <button class="submit-btn" type="submit" name="delete_user">Delete</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</main>


<?php

  include_once '../template/aside.php';

  include '../template/advert.php';

  include_once '../template/footer.php';

?>
