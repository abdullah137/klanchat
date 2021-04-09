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
    redirectTo("index.php");
  }


  // GET USER INFORMATION
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
          <li><a href="profile.php" class="active">About</a></li>
          <li><a href="profile-post.php">Posts</a></li>
          <li><a href="profile-comment.php">Comments</a></li>
          <li><a href="profile-edit.php" >Edit Profile</a></li>
        </ul>
      </div>
    </div>


    <div class="profile-display-content p-2">
      <div class="about-me">
        <div class="text-center">
          <h1>About Me</h1>
        </div>
        <div class="py-1">
          <p><?php echo '<pre>' . ucfirst($user['user_bio']) . '</pre>'; ?></p>
        </div>
      </div>
    </div>

  </div>
</main>


<?php

  include_once '../template/aside.php';

  include '../template/advert.php';

  include_once '../template/footer.php';

?>
