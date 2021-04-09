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


  /********* POSTS PAGINATION STARTS  ************/
  $sql = "SELECT COUNT(post_id) FROM posts; ";
  $stmt = $dbconn->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_NUM);

  // Results Per Page
  $result_per_page = "200";
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

  // GET ALL USER POSTS AND -- OFFSET BY 50 POST AT A TIME --
  $sql = "SELECT * FROM posts ";
  // GET A PARTICULAR POST CATEGORY
  if( (isset($_GET['cat_id']) && !empty($_GET['cat_id']) && is_numeric($_GET['cat_id'])) &&
      (isset($_GET['cat_name']) && !empty($_GET['cat_name']))
    )
  { $sql .= "WHERE post_cat = :post_cat "; }
  $sql .= "ORDER BY post_id DESC
           LIMIT {$offset}, {$result_per_page}; ";

  $stmt = $dbconn->prepare($sql);
  // GET A PARTICULAR POST CATEGORY
  if( (isset($_GET['cat_id']) && !empty($_GET['cat_id']) && is_numeric($_GET['cat_id'])) &&
      (isset($_GET['cat_name']) && !empty($_GET['cat_name']))
    )
  { $stmt->bindValue(":post_cat", $_GET['cat_id']); }
  $stmt->execute();

  $postResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main>
  <div class="home-page p-2">
    <div class="flex p-2">
      <?php
      echo "Page {$page} of {$total_pages} <br>";

      if( (isset($_GET['cat_id']) && !empty($_GET['cat_id']) && is_numeric($_GET['cat_id'])) &&
          (isset($_GET['cat_name']) && !empty($_GET['cat_name']))
        )
      {
      ?>
        <h1 class="text-center"><?php echo ucfirst($_GET['cat_name']); ?></h1>
      <?php
      }
      else
      {
      ?>
        <h1 class="text-center">Latest Topics</h1>
      <?php
      }
      ?>

      <ul class="topic-list flex">
      <?php
        foreach ($postResults as $post)
        {
          // GET ALL USER INFORMATION
          $sql = "SELECT user_name, user_gender, user_rank
                  FROM users
                  WHERE user_id = :user_id; ";
          $stmt = $dbconn->prepare($sql);
          $stmt->execute([
            ":user_id" => $post['post_by']
          ]);
          $user = $stmt->fetch(PDO::FETCH_ASSOC);

          // GET ALL USER POST CATEGORY NAME
          $sql = "SELECT cat_name FROM categories
                  WHERE cat_id = :cat_id; ";
          $stmt = $dbconn->prepare($sql);
          $stmt->execute([
            ":cat_id" => $post['post_cat']
          ]);
          $cat = $stmt->fetch(PDO::FETCH_ASSOC);

          // COUNT ALL THE COMMENTS FOR EACH POST TOPIC
          $sql = "SELECT COUNT(comment_id)
                  FROM comments
                  WHERE comment_topic = :comment_topic; ";
          $stmt = $dbconn->prepare($sql);
          $stmt->execute([
            ":comment_topic" => $post['post_id']
          ]);
          $result = $stmt->fetch(PDO::FETCH_NUM);
          $commentCount = $result[0];
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
              <li>
                <i class="fas fa-arrow-up"></i>
                <?php
                  $sql = "SELECT SUM(post_vote)
                          FROM post_votes
                          WHERE post_id = :post_id; ";
                  $stmt = $dbconn->prepare($sql);
                  $stmt->execute([
                    ':post_id' => $post['post_id']
                  ]);
                  echo $stmt->fetch(PDO::FETCH_NUM)[0] ?? 0;
                ?>
                <i class="fas fa-arrow-down"></i>
              </li>
              <li><i class="far fa-eye"></i><?php echo $post['post_view']; ?></li>
              <li><i class="fas fa-reply"></i><?php echo $commentCount; ?></li>
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
          <a href="index.php?page=<?php echo $prev_page;?>">Prev</a>
        </button>
        <button class="<?php if($next_page > $total_pages) { echo 'hidden'; }?>">
          <a href="index.php?page=<?php echo $next_page;?>">Next</a>
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
