<?php

class User
{
  public function signup($user_name, $user_gender, $user_dob, $user_email, $user_pass, $confirm_pass)
  {
    if (!preg_match("/^[A-Za-z0-9\s]{1,30}$/", $user_name)) {
      $error = "Enter A Valid Username!";
      redirectTo("./signup.php?error={$error}");
    }
    else {
      $db = new DBHandle();
      $dbconn = $db->connect();

      $sql = "SELECT user_name FROM users WHERE user_name = :user_name";
      $stmt = $dbconn->prepare($sql);
      $stmt->bindValue(":user_name", $user_name);
      $stmt->execute();
      $user_exists = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user_exists) {
        $error = "Username Already Exists!";
        redirectTo("./signup.php?error={$error}");
      }
      else {
        if (!preg_match("/(m|f)/", $user_gender)) {
          $error = "Entered An Invalid Gender!";
          redirectTo("./signup.php?error={$error}");
        }
        else {
          if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $user_dob)) {
            $error = "Entered An Invalid Age!";
            redirectTo("./signup.php?error={$error}");
          }
          else {
            if (!preg_match("/^.+\@.+\.com$/", $user_email)) {
              $error = "You Entered An Invalid Email!";
              redirectTo("./signup.php?error={$error}");
            }
            else {
              if (!preg_match("/{$confirm_pass}/", $user_pass)) {
                $error = "Password Not Confirmed!";
                redirectTo("./signup.php?error={$error}");
              }
              else {
                $sql = "INSERT INTO users (user_name, hashed_password, user_email, user_gender, user_dob, user_rank, user_created)
                        VALUES (:user_name, :hashed_password, :user_email, :user_gender, :user_dob, :user_rank, :user_created)";

                $stmt = $dbconn->prepare($sql);
                $stmt->execute([
                  ':user_name' => $user_name,
                  ':hashed_password' => password_hash($user_pass, PASSWORD_DEFAULT),
                  ':user_email' => $user_email,
                  ':user_gender' => $user_gender,
                  ':user_dob' => $user_dob,
                  ':user_rank' => 1,
                  ':user_created' => date("Y-m-d")
                ]);
                $db->close();

                redirectTo("./login.php");
              }
            }
          }
        }
      }
    }
  }


  public function login($user_name, $user_pass)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();
    if (!preg_match("/^[A-Za-z0-9\s]{1,30}$/", $user_name)) {
      $error = "Entered Invalid Username";
      redirectTo("./login.php?error={$error}");
    }
    else
    {
      $sql = "SELECT * FROM users WHERE user_name = :user_name; ";
      $stmt = $dbconn->prepare($sql);
      $stmt->execute([
        ":user_name" => $user_name
      ]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      $db->close();

      if(empty($user)) {
        $error = "User Doesn't Exist";
        redirectTo("./login.php?error={$error}");
      }
      else {
        if(!password_verify($user_pass, $user["hashed_password"])) {
          $error = "Entered Invalid Password";
          redirectTo("./login.php?error={$error}");
        }
        else {
          $_SESSION['user_id'] = $user['user_id'];
          redirectTo("./index.php");
        }
      }
    }
  }


  public function select($user_id)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();

    $sql = "SELECT *
            FROM users
            WHERE user_id = :user_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":user_id" => $user_id
    ]);
    $db->close();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }


  public function selectAll()
  {
    $db = new DBHandle();
    $dbconn = $db->connect();

    $sql = "SELECT *
            FROM users ; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute();
    $db->close();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


  public function update($user_id, $user_img, $user_name, $user_email, $user_bio, $old_pass, $new_pass)
  {
    // UPLOAD AN IMAGE
    if(!empty($user_img['name']))
    {
      $file_path = "../assets/images/uploads/";
      $file_target = $file_path . basename($user_img['name']);
      $file_type = pathinfo($file_target, PATHINFO_EXTENSION);
      $allowedImageTypes = [ 'jpg', 'jpeg', 'png', 'gif' ];

      if(in_array($file_type, $allowedImageTypes))
      {
        foreach ($allowedImageTypes as $allowedType)
        {
          if($file_type == $allowedType)
          {
            $user_img['name'] = "{$user_id}.{$file_type}";
            $file_target = "{$file_path}{$user_img['name']}";
          }
        }

        if(move_uploaded_file($user_img['tmp_name'], $file_target))
        {
          $user_img = $user_img['name'];
        }
      }
    }
    else
    {
      $user_img = "";
    }

    $db = new DBHandle();
    $dbconn = $db->connect();
    $sql = "UPDATE users
            SET ";
    if(!empty($user_img)) {
      $sql .= "user_img = :user_img, ";
    }
    if(!empty($user_name)) {
      $sql .= "user_name = :user_name, ";
    }
    if (preg_match("/^.+\@.+\.com$/", $user_email)) {
      $sql .= "user_email = :user_email, ";
    }
    if(!empty($new_pass)) {
      if(password_verify($old_pass, $user['hashed_password'])) {
        $sql .= "hashed_password = :hashed_password, ";
      }
    }
    $sql .= "user_bio = :user_bio
             WHERE user_id = :user_id; ";

    $stmt = $dbconn->prepare($sql);
    if(!empty($user_img)) {
      $stmt->bindValue(':user_img', $user_img);
    }
    if(!empty($user_name)) {
      $stmt->bindValue(':user_name', $user_name);
    }
    if (preg_match("/^.+\@.+\.com$/", $user_email)) {
      $stmt->bindValue(':user_email', $user_email);
    }
    if(!empty($new_pass)) {
      if(password_verify($old_pass, $user['hashed_password'])) {
        $new_pass_hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt->bindValue(':hashed_password', $new_pass_hashed);
      }
    }
    $stmt->bindValue(':user_bio', $user_bio);
    $stmt->bindValue(':user_id', $user_id);
    $stmt->execute();

    $db->close();
  }


  public function delete($user_id)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();

    $image_path = "../assets/images/uploads/";
    $sql = "SELECT user_img
            FROM users
            WHERE user_id = :user_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":user_id" => $user_id
    ]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC)['user_img'];

    $image = "{$image_path}{$image}";
    if($file_exists($image))
    {
      unlink($image);
    }


    // DELETE ALL POST COMMENT VOTES
    $sql = "DELETE
            FROM comment_votes
            WHERE vote_by = :vote_by; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":vote_by" => $user_id
    ]);

    // DELETE ALL USER COMMENTS
    $sql = "DELETE
            FROM comments
            WHERE comment_by = :comment_by; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":comment_by" => $user_id
    ]);

    // DELETE POST VOTES
    $sql = "DELETE
            FROM post_votes
            WHERE vote_by = :vote_by; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":vote_by" => $user_id
    ]);

    // DELETE ALL USER POSTS
    $sql = "DELETE
            FROM posts
            WHERE post_by = :post_by; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":post_by" => $user_id
    ]);

    // DELETE USER INFO
    $sql = "DELETE
            FROM users
            WHERE user_id = :user_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":user_id" => $user_id
    ]);

    $db->close();
  }


}


?>
