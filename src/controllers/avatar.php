<?php
require_once(DIR_SRC . '/utils/imgupload.php');

class AvatarController extends IMGUpload {

  var $input_name = 'file';
  var $accept_mime = ["image/jpeg", "image/gif", "image/png"];
  var $upload_dir = DIR_AVATAR;
  var $thumb_dir = DIR_AVATAR . '/thumb';
  var $uri, $thumb_uri;

  function uploadAvatar() {
    $file = $this->uploadFile();
    if ($file) {
      $this->uri = PATH_AVATAR . '/' . $this->name;
      $this->thumb_uri = PATH_AVATAR . '/thumb/' . $this->name;
    }
    return $file;
  }

  function json() {
    return [
      'data' => [
        'file' => PATH_AVATAR_THUMB . '/' . $this->name,
        'bname' => $this->name,
        'uri' => $this->uri,
        'thumb' => $this->thumb_uri,
      ]
    ];
  }
}
?>
