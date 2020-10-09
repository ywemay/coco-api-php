<?php
require_once(DIR_SRC . '/utils/functions.php');
require_once(DIR_SRC . '/controllers/avatar.php');
global $Req;
// global $Resp;

$more = $Req->pk(2);

if (!$more) {
    answerOptions();
    $avatar = new AvatarController();
    if ($Req->method == "POST" && $avatar->gotFile()) {
      $file = $avatar->uploadAvatar();
      if ($file) {
        $Resp->json($avatar->json());
      }
      else {
        $Resp->bad();
      }
    }
}
?>
