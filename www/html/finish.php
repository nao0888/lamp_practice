<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();
check_token();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);
$carts = get_user_carts($db, $user['user_id']);
$db->beginTransaction();
//履歴情報書込処理
insert_order($db, $user['user_id']);
$order_id = $db->lastInsertId();

foreach($carts as $cart){
  if(insert_detail($db, $order_id, $cart['item_id'], $cart['price'], $cart['amount']
  ) === false){
    set_error($cart['name'] . 'の購入履歴追加に失敗しました。');
  }
}

if(purchase_carts($db, $carts) === false){
  set_error('商品が購入できませんでした。');
  redirect_to(CART_URL);
} 
//エラーが無ければコミット処理
if(($_SESSION['__errors']) === NULL){
  $db->commit();
}else{
  $db->rollback();
}


$total_price = sum_carts($carts);


include_once '../view/finish_view.php';