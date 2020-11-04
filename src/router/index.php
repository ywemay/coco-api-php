<?php
require_once(DIR_SRC . '/utils/functions.php');

$more = Req()->pathKeys[1];
$user = s('user');
if (Req()->method == 'OPTIONS' && $more) {
 //allow continue
}
elseif (!$user) {
    if ($more == 'user' && Req()->pathKeys[2] == 'login') {
      if (Req()->method == 'OPTIONS') {
        Resp()->send();
      }
      elseif (Req()->method == 'POST') {
        $v = json_decode(Req()->body());
        $pass_hash = "$2y$10$7Ldh5VMTbzPWNa0GLrUreOtQSFNh4H1jOv4.Qdf.eeN622BWr.gCC";
        if ($v->username == 'admin' && password_verify($v->password, $pass_hash)) {
          Resp()->json(['message' => 'Logged In', 'token' => 'OK']);
          s('user', ['username' => 'admin', 'roles' => ['admin']]);
        }
      }
    }
    else {
      Resp()->json(['message' => 'Unauthorized'], 401);
    }
    $more = false;
}
elseif (Req()->path == '/user/logout') {
  s('user', []);
  Resp()->json(['message' => 'OK']);
  $more = false;
}
elseif (Req()->path == '/user/info') {
  Resp()->json(s('user'));
  $more = false;
}

if ($more && preg_match("/^\w+$/", $more)) {
  if (file_exists(DIR_ROUTER . '/' . $more . '.php')) {
    include(DIR_ROUTER . '/' . $more . '.php');
  }
}
?>
