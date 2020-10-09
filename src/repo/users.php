<?php
require_once(DIR_SRC . '/utils/database.php');

class Users extends Database {

  function fetchOne($_id) {
    $stmt = $this->prepare("SELECT * FROM users WHERE _id=?");
    $stmt->execute([$_id]);
    $data = $stmt->fetch();
    $stmt->closeCursor();
    if ($data) {
      $data['roles'] = current($this->getRolesByIds($data['_id']));
    }
    return $data;
  }

  function fetchOneByName($username) {
    $stmt = $this->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$username]);
    $data = $stmt->fetch();
    if ($data) {
      $data['roles'] = current($this->getRolesByIds($data['_id']));
    }
    $stmt->closeCursor();
    return $data;
  }

  function fetchAll($query = []) {
    $where = [];
    $toBind = [];
    if(isset($query['t'])) {
      $where[] ="username LIKE :username";
      $toBind[] = [':username', '%' . $query['t'] . '%', PDO::PARAM_STR];
    }
    if(isset($query['enabled'])) {
      $where[] = "enabled=:enabled";
      $toBind[] = [':enabled', $query['enabled'] ? 1 : 0, PDO::PARAM_INT];
    }

    $where = $where ? ' WHERE ' . implode(' AND ', $where) . ' ' : ' ';
    $sql = "SELECT * FROM users${where}LIMIT :offset, :count";
    $stmt = $this->prepare($sql);// LIMIT :start, :count;");
    $this->pagination($query);
    foreach ($toBind as $set) {
      l($set);
      $stmt->bindValue($set[0], $set[1], $set[2]);
    }
    $stmt->bindValue(':offset', $this->offset, PDO::PARAM_INT);
    $stmt->bindValue(':count', $this->page_size, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll();
    $newData = [];
    $ids = [];
    foreach($data as $v) {
      $id = $v['_id'];
      $ids[] = $id;
      $newData[$id] = $v;
      $newData[$id]['roles'] = [];
      $newData[$id]['email'] = $v['username'] . '@some.com';
      $newData[$id]['enabled'] = $v['enabled'] ? true : false;
      $newData[$id]['avatar'] = PATH_AVATAR_THUMB . '/' . $v['avatar'];
    }
    foreach(array_chunk($ids, 15) as $chunk) {
      $roles = $this->getRolesByIds($chunk);
      foreach($roles as $uid => $rs){
        $newData[$uid]['roles'] = $rs;
      }
    }
    return $newData;
  }

  function getRolesByIds($ids) {
    if (!is_array($ids))  $ids = [$ids];
    $inQuery = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $this->prepare("SELECT * FROM user_roles WHERE uid in ($inQuery)");
    $stmt->execute($ids);
    $data = $stmt->fetchAll();
    $rez = [];
    foreach ($ids as $id) $res[$id] = [];
    foreach($data as $v) {
      $rez[$v['uid']][] = $v['role'];
    }
    return $rez;
  }

  function insertUser($user) {
    $stmt = $this->prepare("INSERT INTO users SET username=:username,
      password=:hash, enabled=:enabled, avatar=:avatar");
    $stmt->bindValue(':username', $user['username'], PDO::PARAM_STR);
    $stmt->bindValue(':hash', $user['password'], PDO::PARAM_STR);
    $stmt->bindValue(':enabled', $user['enabled'], PDO::PARAM_INT);
    $stmt->bindValue(':avatar', $user['avatar'], PDO::PARAM_STR);
    $uid = false;
    try {
      $stmt->execute();
      $uid = $this->lastInsertId();
    } catch(PDOException $e) {
      l($e->message());
    }
    if ($uid) {
      $stmt = $this->prepare("INSERT INTO user_roles SET uid=:uid, role=:role");
      $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
      $stmt->bindParam(':role', $role, PDO::PARAM_STR);
      foreach($user['roles'] as $role) {
        $stmt->execute();
      }
    }
    return true;
  }

  function updateUser($user) {
    if (!isset($user['_id'])) return false;
    $keys = ['username', 'enabled', 'avatar'];
    if (isset($user['password'])) $keys[] = 'password';
    $inQuery = [];
    foreach ($keys as $k => $v) {
      $inQuery[$k] = "$v=:$v";
    }
    $inQuery = implode(', ', $inQuery);
    $stmt = $this->prepare("UPDATE users SET $inQuery WHERE _id=:id");
    foreach($keys as $k) {
      $stmt->bindValue(':' . $k, $user[$k]);
    }
    $stmt->bindValue(':id', $user['_id']);
    try {
      $res = $stmt->execute();
    } catch(PDOException $e) {
      l($e->message());
    }
    $stmt = $this->prepare("DELETE FROM user_roles WHERE uid=:uid");
    $stmt->execute([':uid' => $user['_id']]);

    $stmt = $this->prepare("INSERT INTO user_roles SET uid=:uid, role=:role");
    $stmt->bindParam(':uid', $user['_id'], PDO::PARAM_INT);
    $stmt->bindParam(':role', $role, PDO::PARAM_STR);
    foreach($user['roles'] as $role) {
      $stmt->execute();
    }
    return $res;
  }
}
