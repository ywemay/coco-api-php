<?php

class Response {

  var $headers = [];
  var $status = 200;
  var $done = false;

  function Response() {
    $this->cors();
  }

  function cors() {
    $this->headers[] = 'Access-Control-Allow-Origin: http://localhost:9528';
    $this->headers[] = 'Access-Control-Allow-Headers: X-Requested-With, Content-Type, content-type, Accept, Origin, Authorization';
    $this->headers[] = 'Access-Control-Allow-Credentials: true';
    $this->headers[] = 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS';
  }

  function setStatus($status = false) {
    if ($status) {
      $this->status = $status;
    }
    http_response_code($this->status);
  }

  function setHeaders($params = [], $status = false) {
    $this->setStatus($status);
    if ($params) {
      if (is_string($params)) $params = [$params];
      foreach($params as $p) $this->headers[] = $p;
    }
    foreach($this->headers as $h) {
      header($h);
    }
  }

  function json($json, $status = false) {
    if ($this->done) return;
    $this->setHeaders('Content-Type: application/json', $status);
    print is_array($json) ? json_encode($json) : $json;
    $this->done = true;
  }

  function send($body = false, $status = false) {
    if ($this->done) return;
    $this->setHeaders('Content-Type: text/html', $status);
    print $body ? $body : '';
    $this->done = true;
  }

  function unauthorized() {
    $this->json(['message' => 'Unauthorized'], 401);
  }

  function ok() {
    $this->json(['message' => 'OK'], 200);
  }

  function bad() {
    $this->json(['message' => 'Error'], 500);
  }

  function notFound() {
    $this->json(['message' => 'Not found'], 404);
  }

  function sendItemsPaged($items, $pg = 0, $pages = 50) {
    $js = [];
    foreach ($data as $itm) {
      $js[] = json_encode($itm);
    }
    $pagination = json_encode([
      'pg' => $pg,
      'pages_count' => $pages,
    ]);
    $data = '{"items":[' . implode(',', $js) . '],"pagination":' . $pagination. '}';
    $this->json($data);
  }

}
