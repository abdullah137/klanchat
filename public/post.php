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

  if(!isset($_GET['post_id']) || empty($_GET['post_id']))
  {
    redirectTo("./index.php");
  }

  $post = new Post();
  $post = $post->select($_GET['post_id']);

  $cat = new Category();
  $cat = $cat->getCategoryName($post['post_cat']);

  $user = new User();
  $user = $user->select($post['post_by']);

  $post_view = new Post();
  $post_view->increaseView($_GET['post_id'], $post['post_view']);

  $comment = new Comment();
  $commentCount = $comment->count($post['post_id']);

  $post_vote = new Post();
  $post_vote = $post_vote->voteCount($post['post_id']);


  if(isset($_SESSION['user_id']))
  {
    /************ SERVER GET REQUEST STARTS ****************/
    if($_SERVER['REQUEST_METHOD'] == 'GET')
    {

      // VOTING FOR POSTS
      if (isset($_GET['vote_post']))
      {
        if(!empty($_GET['post_id']) && is_numeric($_GET['vote_post']))
        {
          $post = new Post();
          $post->vote($_GET['post_id'], $_SESSION['user_id'], $_GET['vote_post']);
          redirectTo("./post.php?post_id={$_GET['post_id']}");
        }
      }


      // VOTING FOR COMMENTS
      if (isset($_GET['vote_comment']))
      {
        if(!empty($_GET['post_id']) && !empty($_GET['comment_id']) && is_numeric($_GET['vote_comment']))
        {
          $comment = new Comment();
          $comment->vote($_GET['comment_id'], $_SESSION['user_id'], $_GET['vote_comment']);
          redirectTo("./post.php?post_id={$_GET['post_id']}");
        }
      }


      // DELETE A POST
      if(!empty($_GET['post_id']) && !empty($_GET['delete_post']))
      {
        $post = new Post();
        $post->delete($_GET['post_id']);
        redirectTo("./profile-post.php");
      }


      // DELETE A COMMENT
      if(!empty($_GET['post_id']) && !empty($_GET['comment_id']) && !empty($_GET['delete_comment']))
      {
        $comment = new Comment();
        $comment->delete($_GET['comment_id']);
        redirectTo("./post.php?post_id={$_GET['post_id']}");
      }

    }
    /************ SERVER GET REQUEST ENDS ****************/


    /************ SERVER POST REQUEST STARTS ****************/
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
      // CREATE A COMMENT
      if(isset($_POST["create_comment"]))
      {
        if(!empty($post['post_id']) && !empty($_POST['comment_content']) && !empty($_SESSION['user_id']) && !empty($user['user_id']))
        {
          $comment = new Comment();
          $comment->create(
            $post['post_id'],
            $_POST['comment_content'] ?? "",
            $_SESSION['user_id'],
            $user['user_id'],
            date('Y-m-d H:i:s')
          );
          redirectTo("./post.php?post_id={$post['post_id']}");
        }
      }

      // UPDATE COMMENT
      if(isset($_POST["update_comment"]))
      {
        if(!empty($_POST['comment_id']) && !empty($_POST['comment_content']))
        {
          $comment = new Comment();
          $comment->update($_POST['comment_id'], $_POST['comment_content']);
          redirectTo("./post.php?post_id={$post['post_id']}");
        }
      }

    }
    /************ SERVER POST REQUEST ENDS ****************/

  }

?>


<main>
  <div class="post p-2">
    <div class="p-1">
      <h1 class="text-center"><?php echo $cat['cat_name']; ?></h1>

      <!-- POST SECTION -->
      <div class="post-card flex">
        <div class="post-top flex">
          <p><?php echo $user['user_name']; ?></p>
          <div class="flex">
            <p><?php echo $user['user_gender']; ?></p>
            <button>
              <?php echo getUserRankName($user['user_rank']); ?>
            </button>
          </div>
        </div>
        <div class="post-title"><?php echo $post['post_topic']; ?></div>
        <div class="post-content py-2">
          <?php echo $post['post_content']; ?>
        </div>
        <div class="post-info flex">
          <ul class="flex">
            <li>
              <a href="<?php echo "post.php?post_id={$post['post_id']}&vote_post=1"; ?>"><i class="fas fa-arrow-up"></i></a>
              <?php echo $post_vote; ?>
              <a href="<?php echo "post.php?post_id={$post['post_id']}&vote_post=0"; ?>"><i class="fas fa-arrow-down"></i></a>
            </li>
            <li><i class="far fa-eye"></i><?php echo $post['post_view']; ?></li>
            <li><i class="fas fa-reply"></i><?php echo $commentCount; ?></li>
          </ul>
          <ul class="flex">
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
            <li>
              <date><?php echo get_time_ago($post['post_date']); ?></date>
            </li>
          </ul>
        </div>
      </div>


      <!-- COMMENT SECTION -->
      <div class="comment-sect p-2">
      <?php
        if(isset($_SESSION['user_id']))
        {
          // EDIT COMMENT
          if(!empty($_GET['post_id']) && !empty($_GET['comment_id']) && isset($_GET['edit_comment']))
          {
            $comment = new Comment();
            $comment = $comment->select($_GET['comment_id']);
          }
      ?>
        <div>
          <button id="make-comment">Make A Comment</button>

          <form class="comment-form" method="post">
            <?php
            if(!empty($_GET['post_id']) && !empty($_GET['comment_id']))
            {
            ?>
              <input class="hidden" type="text" name="comment_id" value="<?php echo $_GET['comment_id']; ?>">
            <?php
            }
            ?>
            <div class="form-control py-1">
              <textarea name="comment_content" id="editor">
              <?php
              if(!empty($_GET['post_id']) && !empty($_GET['comment_id']) && isset($_GET['edit_comment'])) { echo $comment['comment_content']; }
              ?>
              </textarea>
            </div>
            <div class="flex">
              <button type="submit" name="create_comment">Comment</button>
              <?php
              if(!empty($_GET['post_id']) && !empty($_GET['comment_id']))
              {
              ?>
              <button type="submit" name="update_comment">Update</button>
              <?php
              }
              ?>
            </div>
          </form>
        </div>
      <?php
        }
      ?>

        <ul class="comment-list">
        <?php
          // GET ALL THE COMMENTS FOR POST
          $comment = new Comment();
          $commentResults = $comment->getAllPostComments($_GET['post_id']);
          foreach ($commentResults as $comment)
          {
            // GET THE COMMENT WRITER INFORMATION
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
                  <a href="<?php echo "post.php?post_id={$post['post_id']}&comment_id={$comment['comment_id']}&vote_comment=1"; ?>"><i class="fas fa-arrow-up"></i></a>
                  <?php
                    $commentCount = new Comment ();
                    echo $commentCount->voteCount($comment['comment_id']);
                  ?>
                  <a href="<?php echo "post.php?post_id={$post['post_id']}&comment_id={$comment['comment_id']}&vote_comment=0"; ?>"><i class="fas fa-arrow-down"></i></a>
                </li>
                <!-- <li><i class="fas fa-reply"></i>0</li> -->
              </ul>
              <ul class="flex">
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
                <li>
                  <date><?php echo get_time_ago($comment['comment_date']); ?></date>
                </li>
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
                      <li><i class="fas fa-reply"></i>0</li>
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
      </div>

      <div class="text-left py-2">
        <button class="more-btn">More Comments</button>
      </div>
    </div>
  </div>
</main>

<?php

  include_once '../template/aside.php';

  include '../template/advert.php';

  include_once '../template/footer.php';

?>
