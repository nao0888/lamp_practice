<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'cart.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入明細</h1>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>
    <?php if(count($orders) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($orders as $order){ ?>
          <tr>
            <td><?php print htmlspecialchars($order['order_id'], ENT_QUOTES, "UTF-8");?> </td>
            <td><?php print htmlspecialchars($order['datetime'], ENT_QUOTES, "UTF-8"); ?></td>
            <td><?php print number_format($order['total_price']); ?>円</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>購入履歴はございません。</p>
    <?php } ?> 
  
    <?php if(count($details) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品名</th>
            <th>価格</th>
            <th>購入数</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($details as $detail){ ?>
          <tr>
            <td><?php print htmlspecialchars($detail['name'], ENT_QUOTES, "UTF-8"); ?></td>
            <td><?php print(number_format($detail['price'])); ?>円</td>
            <td><?php print(number_format($detail['amount'])); ?>個</td>
            <td><?php print(number_format($detail['total_price'])); ?>円</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <form method="post" action="order.php">
        <input class="btn btn-block btn-primary" type="submit" value="購入履歴一覧に戻る">
        <input type="hidden" name="post_token" value="<?php print $token; ?>">
      </form>
    <?php } else { ?>
      <p>購入履歴はございません。</p>
    <?php } ?> 
  </div>
</body>
</html>