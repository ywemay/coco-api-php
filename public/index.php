<?php
session_start();
include('../config.php');

global $Req, $Resp;
$Req = new Request();
$Resp = new Response();

include(DIR_ROUTER . '/index.php');

if (!$Resp->done) {

  if ($Req->method == 'OPTIONS') {
    $Resp->send();
  }

  $Resp->json([
    'message'=>'Not found',
  ], 404);
}

?>
