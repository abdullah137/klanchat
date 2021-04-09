<?php


class Comment
{

  public function count($post_id)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();

    $sql = "SELECT COUNT(comment_id)
            FROM comments
            WHERE comment_topic = :comment_topic; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":comment_topic" => $post_id
    ]);
    $db->close();

    return $stmt->fetch(PDO::FETCH_NUM)[0];
  }


  public function voteCount($comment_id)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();
    $sql = "SELECT SUM(comment_vote)
            FROM comment_votes
            WHERE comment_id = :comment_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ':comment_id' => $comment_id
    ]);
    $db->close();

    return $stmt->fetch(PDO::FETCH_NUM)[0] ?? 0;
  }


  public function create($comment_topic, $comment_content, $comment_by, $comment_to, $comment_date)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();
    $sql = "INSERT INTO comments (comment_topic, comment_content, comment_by, comment_to, comment_date)
            VALUES (:comment_topic, :comment_content, :comment_by, :comment_to, :comment_date); ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":comment_topic" => $comment_topic,
      ":comment_content" => $comment_content,
      ":comment_by" => $comment_by,
      ":comment_to" => $comment_to,
      ":comment_date" => $comment_date
    ]);
    $db->close();
  }


  public function select($comment_id)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();
    $sql = "SELECT *
            FROM comments
            WHERE comment_id = :comment_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":comment_id" => $comment_id
    ]);
    $db->close();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }


  public function update($comment_id, $comment_content)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();
    $sql = "UPDATE comments
            SET comment_content = :comment_content
            WHERE comment_id = :comment_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":comment_content" => $comment_content,
      ":comment_id" => $comment_id
    ]);
    $db->close();
  }


  public function delete($comment_id)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();

    // DELETE A COMMENT VOTES
    $sql = "DELETE
            FROM comment_votes
            WHERE comment_id = :comment_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":comment_id" => $comment_id
    ]);

    // DELETE a COMMENT
    $sql = "DELETE
            FROM comments
            WHERE comment_id = :comment_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":comment_id" => $comment_id
    ]);
    $db->close();
  }


  public function getAllPostComments($post_id)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();
    $sql = "SELECT *
            FROM comments
            WHERE comment_topic = :comment_topic
            ORDER BY comment_id DESC; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":comment_topic" => $post_id
    ]);
    $db->close();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }



  public function vote($comment_id, $user_id, $vote)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();
    $sql = "SELECT * FROM comment_votes
            WHERE comment_id = :comment_id AND vote_by = :vote_by; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ':comment_id' => $comment_id,
      ':vote_by' => $user_id
    ]);
    $voteCount = $stmt->rowCount();
    
    if($voteCount > 0)
    {
      // CHANGE YOUR VOTE
      $sql = "UPDATE comment_votes
              SET comment_vote = :comment_vote
              WHERE comment_id = :comment_id AND vote_by = :vote_by; ";
      $stmt = $dbconn->prepare($sql);
      $stmt->execute([
        ':comment_vote' => ($vote == 1 ? 1 : -1),
        ':comment_id' => $comment_id,
        ':vote_by' => $user_id
      ]);
    }
    else
    {
      // CREATE NEW VOTE
      $sql = "INSERT INTO comment_votes (comment_id, vote_by, comment_vote)
              VALUES (:comment_id, :vote_by, :comment_vote); ";
      $stmt = $dbconn->prepare($sql);
      $stmt->execute([
        ':comment_id' => $comment_id,
        ':vote_by' => $user_id,
        ':comment_vote' => ($vote == 1 ? 1 : -1)
      ]);
    }

    $db->close();
  }



}




?>
