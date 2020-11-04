<?php
require_once(DIR_SRC . '/utils/functions.php');
require_once(DIR_SRC . '/controllers/users.php');

$more = Req()->pk(2);

if (!$more) {
  answerOptions();
  check_access_by_roles('admin');
  if (Req()->method == 'GET') {
    $u = new usersController();
    $data = $u->fetchAll(Req()->query);
    $js = [];
    foreach ($data as $u) {
      $js[] = json_encode($u);
    }
    $data = '{"items":[' . implode(',', $js) . '],"pagination":{"pg":0,"pages_cont":50}}';
    Resp()->json($data);
    exit;
  }
  elseif (Req()->method == 'POST') {
    $u = new usersController();
    $rez = $u->createUser((Array)Req()->json());
    $rez ? Resp()->ok() : Resp()->bad();
  }
}
elseif (preg_match("/^\d+$/", $more)) {
  answerOptions();
  check_access_by_roles('admin');
  if (Req()->method == 'GET') {
    $u = new usersController();
    $item = $u->getOneCleanById($more);
    $item ? Resp()->json($item) : Resp()->notFound();
  }
}
