<?php
require_once(DIR_SRC . '/repo/users.php');

class usersController extends Users {

  function cleanUser($uo) {
    unset($uo['password']);
    if (isset($uo['enabled'])) {
      $uo['enabled'] = $uo['enabled'] ? true : false;
    }
    return $uo;
  }

  function getOneCleanById($id) {
    $item = $this->fetchOne($id);
    if (!$item) return [];
    return $this->cleanUser($item);
  }

  function getOneCleanByName($username) {
    $item = $this->fetchOne($username);
    if (!$item) return [];
    return $this->cleanUser($item);
  }

  function createUser($user) {
    if(!($user = $this->validate($user))) {
      return false;
    }
    if (!isset($user['password'])) return false;
    $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);
    $this->processAvatarFiles($user);
    return $this->insertUser($user);
  }

  function validate($user) {
    if (!$user) return false;
    if (!is_array($user)) return $this->error_log('User is not an array...');
    if (!isset($user['username']) || strlen($user['username'])<2)
      return $this->error_log('Username is missing or shorter then 2 chars...');
    if (isset($user['password']) && strlen($user['password'])<5) {
      return $this->error_log('Password is set but shorter then 5');
    }
    $user['enabled'] = isset($user['enabled']) && $user['enabled'] ? 1 : 0;
    if (isset($user['avatar']) && $user['avatar']) {
      $fn = DIR_PUBLIC . '/' . $user['avatar'];
      if (!file_exists($fn)) {
        $user['avatar'] = '';
        return $this->error_log('Provided avatar file is missing.');
      }
    }
    return $user;
  }

  function processAvatarFiles(&$user) {
    if (!isset($user['avatar']) || !$user['avatar']) return;
    $fn = $user['avatar'] = basename($user['avatar']);
    if (preg_match("/^(.*?)__temp\.(jpg|jpeg|gif|png)$/", $fn, $mt)) {
      $newfn = $mt[1] . '.' . $mt[2];
      $d = DIR_PUBLIC . '/' . PATH_AVATAR . '/';
      rename( $d . $fn, $d . $newfn);
      $d = DIR_PUBLIC . '/' . PATH_AVATAR_THUMB. '/';
      rename( $d . $fn, $d . $newfn);
      $user['avatar'] = $newfn;
    }
  }

  function error_log($msg) {
    l($msg);
    return false;
  }

  function updUser($user){
    if (!isset($user['_id'])) return false;
    if (isset($user['password']) && $user['password']) {
      $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);
    }
    $this->processAvatarFiles($user);
    return $this->updateUser($user);
  }
}
?>
