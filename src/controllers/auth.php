<?php
require_once(DIR_SRC . '/repo/users.php');

class authController extends Users {
  var $errors = [];

  function logIn($data) {
    list($username, $password) = $data;
    $u = $this->fetchOne($username);
    if (!$u) {
      return $this->error('Unknow username.');
    }
    elseif (!$u->enabled) {
      return $this->error('User account disabled.');
    }
    elseif (!password_verify($password, $u->password)) {
      return $this->error('Wrong password.');
    }
    $u = $this->clearUser($u);
    s('user', $u);
    return true;
  }

  function getInfo() {
    return s('user');
  }

  function err($msg) {
    $this->errors[] = $msg;
    return false;
  }
}
