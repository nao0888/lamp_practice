<?php

//変数$varの表示処理関数
function dd($var){
  var_dump($var);
  exit();
}


//リダイレクト処理関数
function redirect_to($url){
  header('Location: ' . $url);
  exit;
}

//GET方式でのデータ取得処理関数
function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}

//POST方式でのデータ取得処理関数
function get_post($name){
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}

//GET方式でのファイル取得処理関数
function get_file($name){
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  return array();
}

//セッション確認処理関数
function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  return '';
}

//セッション取得処理関数
function set_session($name, $value){
  $_SESSION[$name] = $value;
}

//エラー発生時のエラー表示処理
function set_error($error){
  $_SESSION['__errors'][] = $error;
}

//エラー発生時のエラー表示処理
function get_errors(){
  $errors = get_session('__errors');
  if($errors === ''){
    return array();
  }
  set_session('__errors',  array());
  return $errors;
}

//エラー確認関数
function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

//セッションへエラーメッセージ表記関数
function set_message($message){
  $_SESSION['__messages'][] = $message;
}

//エラーメッセージ表記関数
function get_messages(){
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}

//ログイン
function is_logined(){
  return get_session('user_id') !== '';
}

//画像取得関数
function get_upload_filename($file){
  if(is_valid_upload_image($file) === false){
    return '';
  }
  $mimetype = exif_imagetype($file['tmp_name']);
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  return get_random_string() . '.' . $ext;
}

//ランダムなパスワード配列(20文字)の作成
function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

//ファイルアップロード処理関数
function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}
//ファイル削除処理関数
function delete_image($filename){
  if(file_exists(IMAGE_DIR . $filename) === true){
    unlink(IMAGE_DIR . $filename);
    return true;
  }
  return false;
  
}


//有効文字数確認関数
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

//有効文字確認関数
function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

//正の整数確認関数
function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

//有効文字確認関数
function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
}


//有効画像アップロード処理関数
function is_valid_upload_image($image){
  //画像の有効確認処理を実行し、有効でない場合の処理
  if(is_uploaded_file($image['tmp_name']) === false){
    //エラーメッセージを送信する
    set_error('ファイル形式が不正です。');
    //値をfalseで返す
    return false;
  }
  //画像ファイル形式情報を読取
  $mimetype = exif_imagetype($image['tmp_name']);
  //画像の有効確認処理を実行し、有効でない場合の処理
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    //エラーメッセージを送信する
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    //値をfalseで返す
    return false;
  }
  //以上の有効確認処理が全てクリアした場合、trueを返す
  return true;
}

//XSS対策処理
function h($str){
  //特殊文字形式で値を引き渡す
  return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}

//トークン生成関数
function get_csrf_token(){
  //138行目ランダム配列を利用する
  $token = get_random_string();
  //セッションに埋め込む
  $_SESSION['token'] = $token;
  return $token;
}
//トークン確認関数
function check_token(){
  if ($_SESSION['token'] !== $_POST['post_token'] ){
  set_error('不正なセッションが発生しました');
  return false;
  }
}
