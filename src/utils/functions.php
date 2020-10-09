<?php
function s($key, $value = false) {
  if ($value !== false) {
    $_SESSION[$key] = $value;
  }
  else return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
}

function g($key) {
  return isset($_GET[$key]) ? $_GET[$key] : false;
}

function p($key) {
  return isset($_POST[$key]) ? $_POST[$key] : false;
}

// logging while running in console
function l($str) {
  if (is_array($str)) $str = print_r($str, true);
  file_put_contents('php://stdout', $str . "\n");
}

function answerOptions() {
  global $Req, $Resp;
  if ($Req->method == 'OPTIONS') {
    //l('Anser options.....................');
    $Resp->ok();
    die();
  }
}

function user_roles_intersect($roles) {
  $user = s('user');
  if (!$user) return false;
  if (!is_array($roles)) $roles = [$roles];
  return array_intersect($user['roles'], $roles);
}

function check_access_by_roles($roles) {
  if (!user_roles_intersect($roles)) {
    global $Resp;
    $Resp->unauthorized();
    die();
  }
}
 ?>
