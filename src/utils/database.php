<?php

/**
 * examples
 * $stmt = Database :: prepare ( "SELECT 'something' ;" ) ;
 * $stmt -> execute ( ) ;
 * var_dump ( $stmt -> fetchAll ( ) ) ;
 * $stmt -> closeCursor ( ) ;
 */
class Database extends PDO{
  var $offset = 0;
  var $page_size = 50;
  var $pg = 1;

    function __construct() {
      $set = parse_ini_file(DIR_ROOT . '/config.ini');
      try {
        parent::__construct($set['dsn'], $set['user'], $set['pass']);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      }
      catch(PDOException $e) {
        global $Resp;
        $Resp->json($e->getMessage());
      }
    }

    function pagination($query) {
      $pg = isset($query['pagination']['pg']) ? intval($query['pagination']['pg']) : 1;
      $pg_size = isset($query['pagination']['page_size']) ? intval($query['pagination']['p_sizeg']) : 50;
      if ($pg< 1) $pg= 1;
      if ($pg_size < 1) $pg_size = 1;
      $rez = [
        'offset' => ($pg - 1) * $pg_size,
        'page_size' => $pg_size,
        'pg' => $pg,
      ];
      $this->offset = $rez['offset'];
      $this->page_size = $rez['page_size'];
      $this->pg = $rez['pg'];
      return $rez;
    }
}
?>
