<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

//ユーザーのカート内商品情報読込処理関数
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  return fetch_all_query($db, $sql, array($user_id));
}

//ユーザーのカート内商品情報取得関数
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";

  return fetch_query($db, $sql, array($user_id, $item_id));

}

//ユーザーのカート内商品追加関数
function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

//ユーザーのカート内商品追加関数
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, ?)
  ";

  return execute_query($db, $sql, array($item_id, $user_id, $amount));
}

//ユーザーのカート内商品在庫数更新関数
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  return execute_query($db, $sql, array($amount, $cart_id));
}

//ユーザーのカート内商品削除関数
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  return execute_query($db, $sql, array($cart_id));
}

//ユーザーのカート内商品合計金額算出関数
function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  foreach($carts as $cart){
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  
  delete_user_carts($db, $carts[0]['user_id']);
}

//ユーザーのカート内商品情報の削除処理関数(トランザクション必要か)
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  execute_query($db, $sql, array($user_id));
}

//ユーザーのカート内商品個数の総計計算処理関数
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

//カート内商品購入確認関数
function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

//購入履歴追加関数(未実装)
function insert_order($db, $user_id){
  $sql = "
    INSERT INTO
      orders(
        datetime,
        user_id
      )
    VALUES(now(), ?)
  ";
  return execute_query($db, $sql, array($user_id));
}

//購入明細追加関数(未実装)トランザクション処理が必要
function insert_detail($db, $order_id, $item_id, $price, $amount){
  $sql = "
    INSERT INTO
      details(
        order_id,
        item_id,
        price,
        amount
      )
    VALUES(?, ?, ?, ?)
  ";
  return execute_query($db, $sql, array($order_id, $item_id, $price, $amount));
}

//購入履歴読込処理関数(複数)
function get_user_orders($db, $user_id){
  $sql = "
    SELECT
      orders.order_id,
      orders.datetime,
      sum(details.price * details.amount) AS total_price
    FROM
      orders
    JOIN
      details
    ON
      orders.order_id = details.order_id
    WHERE
      orders.user_id = ?
    GROUP BY
      order_id
    ";


  return fetch_all_query($db, $sql, array($user_id));
}

/*購入履歴読込処理関数(複数)
function get_all_orders($db){
  $sql = "
    SELECT
      orders.order_id,
      orders.datetime,
      sum(details.price * details.amount) AS total_price
    FROM
      orders
    JOIN
      details
    ON
      orders.order_id = details.order_id
    GROUP BY
      order_id
    ";
  return fetch_all_query($db, $sql);
}
*/

//購入履歴読込処理関数（単一）
function get_user_order($db, $order_id){
  $sql = "
    SELECT
      orders.order_id,
      orders.datetime,
      sum(details.price * details.amount) AS total_price
    FROM
      orders
    JOIN
      details
    ON
      orders.order_id = details.order_id
    WHERE
      orders.order_id = ?
    GROUP BY
      order_id
    ";

 return fetch_all_query($db, $sql, array($order_id));
}


//購入詳細読込処理関数
function get_user_details($db, $order_id){
  $sql = "
    SELECT
      items.name,
      details.item_id,
      details.price,
      details.amount,
      details.price * details.amount AS total_price
    FROM
      details
    JOIN
      items
    ON
      details.item_id = items.item_id
    WHERE
      details.order_id = ?
    ";
  return fetch_all_query($db, $sql, array($order_id));
}
