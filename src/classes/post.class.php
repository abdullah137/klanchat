<?php


class Post
{

  public function create($post_topic, $post_content, $post_category, $post_by, $post_date)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();

    $sql = "INSERT INTO posts (post_topic, post_content, post_cat, post_by, post_date)
            VALUES (:post_topic, :post_content, :post_cat, :post_by, :post_date); ";

    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":post_topic" => $post_topic,
      ":post_content" => $post_content,
      ":post_cat" => $post_category,
      ":post_by" => $post_by,
      ":post_date" => $post_date
    ]);

    $db->close();
  }


  public function select($post_id)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();

    $sql = "SELECT * FROM posts
            WHERE post_id = :post_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":post_id" => $post_id
    ]);
    $db->close();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }


  public function update($post_id, $post_topic, $post_content, $post_category)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();

    $sql = "UPDATE posts
            SET post_topic = :post_topic,
                post_content = :post_content,
                post_cat = :post_cat
            WHERE post_id = :post_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":post_topic" => $post_topic,
      ":post_content" => $post_content,
      ":post_cat" => $post_category,
      ":post_id" => $post_id
    ]);
    $db->close();
  }


  public function delete($post_id)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();

    // GET ALL COMMENTS FOR THE POST
    $sql = "SELECT comment_id
            FROM comments
            WHERE comment_topic = :comment_topic; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":comment_topic" => $post_id
    ]);
    $comment_id = $stmt->fetch(PDO::FETCH_ASSOC);

    // DELETE ALL POST COMMENT VOTES
    $sql = "DELETE
            FROM comment_votes
            WHERE comment_id = :comment_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":comment_id" => $comment_id
    ]);

    // DELETE ALL POST COMMENTS
    $sql = "DELETE
            FROM comments
            WHERE comment_topic = :comment_topic; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":comment_topic" => $post_id
    ]);

    // DELETE POST VOTES
    $sql = "DELETE
            FROM post_votes
            WHERE post_id = :post_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":post_id" => $post_id
    ]);

    // DELETE POST
    $sql = "DELETE
            FROM posts
            WHERE post_id = :post_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":post_id" => $post_id
    ]);

    $db->close();
  }


  public function increaseView($post_id, $post_view)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();

    $post_view = $post_view + 1;
    $sql = "UPDATE posts
            SET post_view = :post_view
            WHERE post_id = :post_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":post_view" => $post_view,
      ":post_id" => $post_id
    ]);

    $db->close();
  }



  public function vote($post_id, $user_id, $vote)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();

    $sql = "SELECT *
            FROM post_votes
            WHERE post_id = :post_id AND vote_by = :vote_by; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ':post_id' => $post_id,
      ':vote_by' => $user_id
    ]);
    $voteCount = $stmt->rowCount();

    if($voteCount > 0)
    {
      // CHANGE YOUR VOTE
      $sql = "UPDATE post_votes
              SET post_vote = :post_vote
              WHERE post_id = :post_id AND vote_by = :vote_by; ";
      $stmt = $dbconn->prepare($sql);
      $stmt->execute([
        ':post_vote' => ($vote == 1 ? 1 : -1),
        ':post_id' => $post_id,
        ':vote_by' => $user_id
      ]);
    }
    else
    {
      // CREATE NEW VOTE
      $sql = "INSERT INTO post_votes (post_id, vote_by, post_vote)
              VALUES (:post_id, :vote_by, :post_vote); ";

      $stmt = $dbconn->prepare($sql);
      $stmt->execute([
        ':post_id' => $post_id,
        ':vote_by' => $user_id,
        ':post_vote' => ($vote == 1 ? 1 : -1)
      ]);
    }

    $db->close();
  }



  public function voteCount($post_id)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();

    $sql = "SELECT SUM(post_vote)
            FROM post_votes
            WHERE post_id = :post_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ':post_id' => $post_id
    ]);
    $db->close();

    return $stmt->fetch(PDO::FETCH_NUM)[0] ?? 0;
  }






}


?>
