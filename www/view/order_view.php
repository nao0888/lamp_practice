<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'cart.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入履歴</h1>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <?php if(count($orders) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
            <th>購入明細</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($orders as $order){ ?>
          <tr>
            <td><?php print htmlspecialchars($order['order_id'], ENT_QUOTES, "UTF-8");?> </td>
            <td><?php print htmlspecialchars($order['datetime'], ENT_QUOTES, "UTF-8"); ?></td>
            <td><?php print number_format($order['total_price']); ?>円</td>
            <td>
              <form method="post" action="detail.php">
                <input type="submit" value="表示" class="btn btn-secondary">
                <input type="hidden" name="post_token" value="<?php print $token; ?>">
                <input type="hidden" name="order_id" value="<?php print htmlspecialchars($order['order_id'], ENT_QUOTES, "UTF-8"); ?>">
              </form>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <form method="post" action="index.php">
        <input class="btn btn-block btn-primary" type="submit" value="商品一覧に戻る">
        <input type="hidden" name="post_token" value="<?php print $token; ?>">
      </form>
    <?php } else { ?>
      <p>購入履歴はございません。</p>
    <?php } ?> 
  </div>
</body>
</html>