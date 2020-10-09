<?php

class Request {

  var $uri = '';
  var $path = '';
  var $query = '';
  var $method = '';
  var $pathKeys = [];
  var $headers = [];

  function Request() {
    $this->server= $_SERVER;
    $this->uri = $_SERVER['REQUEST_URI'];
    $this->method = $_SERVER['REQUEST_METHOD'];
    foreach(parse_url($this->uri) as $k => $v) {
      $this->{$k} = $v;
    }
    parse_str($this->query, $this->query);
    $this->pathKeys = explode('/', $this->path);
  }

  function body() {
    return file_get_contents("php://input");
  }

  function json() {
    return json_decode($this->body());
  }

  function pk($id) {
    return isset($this->pathKeys[$id]) ? $this->pathKeys[$id] : false;
  }
}
?>
