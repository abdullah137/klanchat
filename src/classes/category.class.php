<?php



class Category
{

  public function getCategoryName($cat_id)
  {
    $db = new DBHandle();
    $dbconn = $db->connect();

    $sql = "SELECT cat_id, cat_name
            FROM categories
            WHERE cat_id = :cat_id; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([
      ":cat_id" => $cat_id
    ]);
    $db->close();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getAllCategories()
  {
    $db = new DBHandle();
    $dbconn = $db->connect();
    $sql = "SELECT cat_id, cat_name
            FROM categories; ";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute();
    $db->close();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


}





?>
