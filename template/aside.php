<aside>
  <div class="flex">
    <div class="side-ad"></div>

    <div class="side-cats p-2">
      <h3 class="text-center">Categories</h3>
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
</aside>
