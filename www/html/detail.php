<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();

/*if(is_logined() === true){
  redirect_to(CART_URL);
}*/

$db = get_db_connect();
$user = get_login_user($db);
$order_id = get_post('order_id');

$orders = get_user_order($db, $order_id);

$details = get_user_details($db, $order_id);
//dd($order_id);
$token = get_csrf_token();

include_once VIEW_PATH . 'detail_view.php';