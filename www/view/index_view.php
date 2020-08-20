<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  
  <title>商品一覧</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'index.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  

  <div class="container">
    <h1>商品一覧</h1>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <div class="card-deck">
      <div class="row">
      <?php foreach($items as $item){ ?>
        <div class="col-6 item">
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print htmlspecialchars($item['name'], ENT_QUOTES, "UTF-8"); ?>
            </div>
            <figure class="card-body">
              <img class="card-img" src="<?php print htmlspecialchars(IMAGE_PATH . $item['image'], ENT_QUOTES, "UTF-8"); ?>">
              <figcaption>
                <?php print(number_format($item['price'])); ?>円
                <?php if($item['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                    <input type="hidden" name="post_token" value="<?php print $token; ?>">
                    <input type="hidden" name="item_id" value="<?php print htmlspecialchars($item['item_id'], ENT_QUOTES, "UTF-8"); ?>">
                  </form>
                <?php } else { ?>
                  <p class="text-danger">現在売り切れです。</p>
                <?php } ?>
              </figcaption>
            </figure>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>

    <div class="ranking">
      <h1>人気ランキング</h1>
      <?php if(count($rankings) > 0){ ?>
        <table class="table table-bordered">
          <thead class="thead-light">
            <tr>
              <th>順位</th>
              <th>商品名</th>
              <th>商品画像</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rankings as $key => $ranking){ ?>
            <tr>
              <td><?php print ($key +1);?> </td>
              <td><?php print htmlspecialchars($ranking['name'], ENT_QUOTES, "UTF-8"); ?></td>
              <td><img src="<?php print htmlspecialchars(IMAGE_PATH . $ranking['image'], ENT_QUOTES, "UTF-8");?>" class="item_image"></td>
              
            </tr>
            <?php } ?>
          </tbody>
        </table>
        <form method="post" action="index.php">
          <input class="btn btn-block btn-primary" type="submit" value="商品一覧に戻る">
          <input type="hidden" name="post_token" value="<?php print $token; ?>">
        </form>
      <?php } else { ?>
        <p>ランキングはございません。</p>
      <?php } ?>
    </div>
  </div>
  
</body>
</html>