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

  /********* COMMENTS PAGINATION STARTS  ************/
  $sql = "SELECT COUNT(comment_id)
          FROM comments; ";
  $stmt = $dbconn->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_NUM);

  // Results Per Page
  $result_per_page = "50";
  // No Of Rows Per Page
  $no_of_result = $result[0];
  // Total Pages
  $total_pages = ceil($no_of_result / $result_per_page);

  if(!isset($_GET['page']))
  {
    $page = 1;
  }
  else
  {
    $page = $_GET['page'];

    if($page <= 1)
    {
      $page = 1;
    }
    else if($page >= $total_pages)
    {
      $page = $total_pages;
    }
  }

  // Offset The Pages
  $offset = ($page - 1) * $result_per_page;
  // Previous Page
  $prev_page = $page - 1;
  // Next Page
  $next_page = $page + 1;
  /********* PAGINATION ENDS  ************/

  // GET ALL LOGGED IN USER COMMENTS AND -- LIMIT BY 25 POST AT A TIME
  $sql = "SELECT *
          FROM comments
          WHERE comment_by = :comment_by
          ORDER BY comment_id DESC
          LIMIT {$offset}, {$result_per_page}; ";
  $stmt = $dbconn->prepare($sql);
  $stmt->bindValue(":comment_by", $_SESSION['user_id']);
  $stmt->execute();
  $commentResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
          <li><a href="profile-post.php">Posts</a></li>
          <li><a href="profile-comment.php" class="active">Comments</a></li>
          <li><a href="profile-edit.php" >Edit Profile</a></li>
        </ul>
      </div>
    </div>

    <div class="profile-display-content p-2">
      <div class="view-comments">
        <div>
          <h2 class="text-center">View Comments</h2>
        </div>
        <ul class="comment-list">
        <?php
          foreach ($commentResults as $comment)
          {
            // GET ALL COMMENT WRTER INFORMATION
            $user = new User();
            $user = $user->select($comment['comment_by']);
        ?>
          <li class="comment-card">
            <div class="comment-top flex">
              <p><?php echo $user['user_name']; ?></p>
              <div class="flex">
                <p><?php echo $user['user_gender']; ?></p>
                <button>
                  <?php echo getUserRankName($user['user_rank']); ?>
                </button>
              </div>
            </div>
            <div title="comment-text">
              <?php echo $comment['comment_content']; ?>
            </div>
            <div class="comment-info flex">
              <ul class="flex">
                <li>
                  <a href=""><i class="fas fa-arrow-up"></i></a>
                  0
                  <a href=""><i class="fas fa-arrow-down"></i></a>
                </li>
                <li><i class="fas fa-reply"></i>0</li>
              </ul>
              <ul class="flex">
                <li>
                  <date><?php echo get_time_ago($comment['comment_date']); ?></date>
                </li>
                <?php
                if(isset($_SESSION['user_id']))
                {
                  if($_SESSION['user_id'] == $comment['comment_by'])
                  {
                ?>
                <li>
                  <a href="<?php echo "post.php?post_id={$comment['comment_topic']}&comment_id={$comment['comment_id']}&edit_comment=1"; ?>">Edit</a>
                </li>
                <li>
                  <a href="<?php echo "post.php?post_id={$comment['comment_topic']}&comment_id={$comment['comment_id']}&delete_comment=1"; ?>">Delete</a>
                </li>
                <?php
                  }
                }
                ?>
              </ul>
            </div>

            <!-- REPLY SECTION DATABASE NOT DEVELOPED -->
            <!-- <div class="reply-sect">
              <ul class="reply-list">
                <li class="reply-card">
                  <div class="reply-top flex">
                    <p>Username</p>
                    <div class="flex">
                      <p>M</p>
                      <button>Rank</button>
                    </div>
                  </div>
                  <div title="reply-text">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed dotes eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    Ut enim ad minim veniam.
                  </div>
                  <div class="reply-info flex">
                    <ul class="flex">
                      <li><i class="fas fa-arrow-up"></i>0<i class="fas fa-arrow-down"></i></li>
                      <li><i class="far fa-eye"></i>0</li>
                    </ul>
                    <ul class="flex">
                      <li><date>2h</date></li>
                    </ul>
                  </div>
                </li>
              </ul>
            </div> -->
          </li>
        <?php
          }
        ?>
        </ul>

        <div class="home-btns flex py-2">
          <button class="<?php if($prev_page < 1) { echo 'hidden'; }?>">
            <a href="profile-comment.php?page=<?php echo $prev_page;?>">Prev</a>
          </button>
          <button class="<?php if($next_page > $total_pages) { echo 'hidden'; }?>">
            <a href="profile-comment.php?page=<?php echo $next_page;?>">Next</a>
          </button>
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
