<footer>
  <div class="flex">
    <div class="foot-form flex py-2">
      <form method="post" enctype="multipart/form-data">
        <input type="search" name="search_text" placeholder="Search...">
        <button type="submit" name="search_btn">Search</button>
      </form>
    </div>

    <div class="foot-nav flex py-1">
      <div>
        <h4>Klanchat</h4>
        <p>Connecting the world</p>
      </div>
      <nav>
        <h4>Categories</h4>
        <div class="flex">
        <?php
          $catResults = new Category();
          $catResults = $catResults->getAllCategories();
          foreach ($catResults as $cat)
          {
        ?>
            <a href="<?php echo "./index.php?cat_id={$cat['cat_id']}&cat_name=" . strtolower($cat['cat_name']); ?>">
              <?php echo $cat['cat_name'] ?>
            </a>
        <?php
          }
        ?>
        </div>
      </nav>
      <nav>
        <h4>Pages</h4>
        <div class="flex">
          <a class="active" href="./index.php">Home</a>
          <a href="./">About</a>
          <a href="./">Adverts</a>
          <a href="./">Help</a>
          <?php
          // IF LOGGED IN ACCESS PROFILE PAGE
          if(isset($_SESSION['user_id']))
          {
          ?>
            <a href="./profile.php">Profile</a>
          <?php
          }
          ?>
        </div>
      </nav>
    </div>

    <div class="flex">
      <p>Copyright &copy; Klanchat <date>2020</date></p>
    </div>
  </div>
</footer>


<section class="sidebar">
  <div class="flex p-2">
    <div class="text-right">
      <i class="fa fa-times"></i>
    </div>

    <div class="sidebar-form py-2">
      <form method="post" enctype="multipart/form-data">
        <input type="search" name="search_text" placeholder="Search...">
        <button type="submit" name="search_btn">Search</button>
      </form>
    </div>

    <div class="sidebar-pages py-2">
      <h2>Pages</h2>
      <ul>
        <li><a class="active" href="./index.php">Home</a></li>
        <li><a href="./">About</a></li>
        <li><a href="./">Advert</a></li>
        <li><a href="./">Help</a></li>
        <?php
        // IF LOGGED IN ACCESS PROFILE PAGE
        if(isset($_SESSION['user_id']))
        {
        ?>
          <li><a href="./profile.php">Profile</a></li>
        <?php
        }
        ?>
      </ul>
    </div>

    <div class="sidebar-cats py-2">
      <h2>Categories</h2>
      <ul>
      <?php
        $catResults = new Category();
        $catResults = $catResults->getAllCategories();
        foreach ($catResults as $cat)
        {
      ?>
          <li>
            <a href="<?php echo "./index.php?cat_id={$cat['cat_id']}&cat_name=" . strtolower($cat['cat_name']); ?>">
              <?php echo $cat['cat_name'] ?>
            </a>
          </li>
      <?php
        }
      ?>
      </ul>
    </div>
  </div>
</section>
</div>

</body>
  <script src="../assets/plugins/ckeditor5-build-classic/ckeditor.js"></script>
  <script src="../assets/js/script.js"></script>
</html>
