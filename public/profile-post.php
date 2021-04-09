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
  // IF LOGGED IN
  if(!isset($_SESSION['user_id']))
  {
    redirectTo("./index.php");
  }

  /************ SERVER POST REQUEST STARTS ****************/
  if($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    // CREATE A NEW POST
    if(isset($_POST["create_post"]))
    {
      if(!empty($_POST['post_topic']) && !empty($_POST['post_content']) && !empty($_POST['post_cat']) && !empty($_SESSION['user_id']) && !empty(date('Y-m-d H:i:s')))
      {
        $post = new Post();
        $post->create(
          $_POST['post_topic'],
          $_POST['post_content'],
          $_POST['post_cat'],
          $_SESSION['user_id'],
          date('Y-m-d H:i:s')
        );
        redirectTo("profile-post.php");
      }
    }

    // UPDATE THE POST
    if(isset($_POST['update_post']))
    {
      if(!empty($_POST['post_topic']) && !empty($_POST['post_content']) && !empty($_POST['post_cat']))
      {
        $post = new Post();
        $post->update(
          $_POST['post_id'],
          $_POST['post_topic'],
          $_POST['post_content'],
          $_POST['post_cat']
        );
        redirectTo("profile-post.php");
      }
    }

  }
  /************ SERVER POST REQUEST ENDS ****************/


  /********* POSTS PAGINATION STARTS  ************/
  $sql = "SELECT COUNT(post_id) FROM posts; ";
  $stmt = $dbconn->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_NUM);

  // Results Per Page
  $result_per_page = "50";
  // No Of Rows Per Page
  $no_of_result = $result[0];
  // Total Pages
  $total_pages = ceil($no_of_result / $result_per_page);

  if(!isset($_GET['page'])) {
    $page = 1;
  }
  else {
    $page = $_GET['page'];

    if($page <= 1) {
      $page = 1;
    }
    else if($page >= $total_pages) {
      $page = $total_pages;
    }
  }
  // Offset The Pages
  $offset = ($page - 1) * $result_per_page;
  // Previous Page
  $prev_page = $page - 1;
  // Next Page
  $next_page = $page + 1;
  /********* PAGINATION END  ************/

  // GET ALL LOGGED IN USER POSTS AND -- LIMIT BY 25 POST AT A TIME
  $sql = "SELECT *
          FROM posts
          WHERE post_by = :post_by
          ORDER BY post_id DESC
          LIMIT {$offset}, {$result_per_page}; ";
  $stmt = $dbconn->prepare($sql);
  $stmt->bindValue(":post_by", $_SESSION['user_id']);
  $stmt->execute();
  $postResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // GET USER INFORMATION
  $user = new User();
  $user = $user->select($_SESSION['user_id']);

  // GET ALL CATEGORIES ID AND NAME
  $catResults = new Category();
  $catResults = $catResults->getAllCategories();

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
          <li><a href="profile-post.php" class="active">Posts</a></li>
          <li><a href="profile-comment.php">Comments</a></li>
          <li><a href="profile-edit.php" >Edit Profile</a></li>
        </ul>
      </div>
    </div>


    <div class="profile-display-content p-2">
      <!-- CREATE A NEW POST -->
      <div class="create-post">
        <button id="create-post-btn" class="create-post-btn">Create Post</button>

        <div id="create-post-box" class="create-post-box flex">
          <h2 class="text-center">Create Post</h2>
          <?php
            // IF POST ID ISSET - EDIT THE POST
            if(!empty($_GET['post_id']) && !empty($_GET['edit_post']))
            {
              // GET THE POST TO BE EDITTED
              $post = new Post();
              $post = $post->select($_GET['post_id'] ?? "");
              // GET POST CATEGORY NAME
              $editCat = new Category();
              $editCat = $editCat->getCategoryName($post['post_cat']);
            }
          ?>
          <form class="create-post-form flex" method="post">
            <?php
            if(isset($_GET['post_id']) && !empty($_GET['post_id']))
            {
            ?>
              <input class="hidden" type="text" name="post_id" value="<?php echo $_GET['post_id']; ?>">
            <?php
            }
            ?>

            <div class="form-control">
              <label for="">Topic</label>
              <input type="text" name="post_topic" placeholder="Enter Title Or Topic"
                     value="<?php if(!empty($_GET['post_id']) && !empty($_GET['edit_post'])) { echo $post['post_topic']; } ?>">
            </div>

            <div class="form-control">
              <label for="">Content</label>
              <textarea name="post_content" id="editor">
                <?php if(!empty($_GET['post_id']) && !empty($_GET['edit_post'])) { echo $post['post_content']; } ?>
              </textarea>
            </div>

            <div class="form-control">
              <label for="">Category</label>
              <select name="post_cat">
              <?php
              foreach ($catResults as $cat)
              {
              ?>
                <option value="<?php echo $cat['cat_id']; ?>"
                  <?php
                  if(isset($editCat['cat_id']))
                  {
                    if($cat['cat_id'] == $editCat['cat_id']) { echo "selected"; }
                  }
                  ?>
                >
                  <?php echo $cat['cat_name']; ?>
                </option>
              <?php
              }
              ?>
              </select>
            </div>

            <div class="create-post-btns flex">
              <button type="submit" name="create_post">Create Post</button>
              <?php
              if(!empty($_GET['post_id']))
              {
              ?>
              <button type="submit" name="update_post">Update</button>
              <?php
              }
              ?>
            </div>
          </form>
        </div>
      </div>


      <div class="view-post">
        <h2 class="text-center">View Posts</h2>
      </div>
      <ul class="topic-list flex">
      <?php
        foreach ($postResults as $post)
        {
          // GET USER INFORMATION
          $user = new User();
          $user = $user->select($post['post_by']);
          // GET POST CATEGORY NAME
          $cat = new Category();
          $cat = $cat->getCategoryName($post['post_cat']);
      ?>
        <li class="topic-card flex">
          <div class="topic-top flex">
            <p><?php echo $user['user_name']; ?></p>
            <div class="flex">
              <p><?php echo $user['user_gender']; ?></p>
              <button>
                <?php echo getUserRankName($user['user_rank']); ?>
              </button>
            </div>
          </div>
          <a class="topic-title" href="<?php echo "post.php?post_id={$post['post_id']}"; ?>">
            <?php echo $post['post_topic'] ?>
          </a>
          <div class="topic-info flex">
            <ul class="flex">
              <li><i class="fas fa-arrow-up"></i>0<i class="fas fa-arrow-down"></i></li>
              <li><i class="far fa-eye"></i>0</li>
              <li><i class="fas fa-reply"></i>0</li>
              <li>
                <date><?php echo $cat['cat_name']; ?></date>
              </li>
            </ul>
            <ul class="flex">
              <li>
                <date><?php echo get_time_ago($post['post_date']); ?></date>
              </li>
              <?php
              if(isset($_SESSION['user_id']))
              {
                if($_SESSION['user_id'] == $post['post_by'])
                {
              ?>
              <li>
                <a href="<?php echo "profile-post.php?post_id={$post['post_id']}&edit_post=1"; ?>">Edit</a>
              </li>
              <li>
                <a href="<?php echo "post.php?post_id={$post['post_id']}&delete_post=1"; ?>">Delete</a>
              </li>
              <?php
                }
              }
              ?>
            </ul>
          </div>
        </li>
      <?php
        }
      ?>
      </ul>

      <div class="home-btns flex py-2">
        <button class="<?php if($prev_page < 1) { echo 'hidden'; }?>">
          <a href="profile-post.php?page=<?php echo $prev_page;?>">Prev</a>
        </button>
        <button class="<?php if($next_page > $total_pages) { echo 'hidden'; }?>">
          <a href="profile-post.php?page=<?php echo $next_page;?>">Next</a>
        </button>
      </div>
    </div>

  </div>
</main>


<?php

  include_once '../template/aside.php';

  include '../template/advert.php';

  include_once '../template/footer.php';

?>
