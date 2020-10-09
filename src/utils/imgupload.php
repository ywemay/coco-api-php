<?php

class IMGUpload {

  var $input_name = 'file';
  var $accept_mime = ["image/jpeg", "image/gif", "image/png"];
  var $upload_dir = '';
  var $thumb_dir = false;
  var $thumb_size = '100x100';
  var $name, $bname, $tmp_name, $size, $mime, $ext, $error;

  function gotFile() {
    $is_set = isset($_FILES[$this->input_name]);
    $is_uploaded = is_uploaded_file($_FILES[$this->input_name]['tmp_name']);
    if (!$is_set) l('No file received for key: ' . $this->input_name);
    if (!$is_uploaded) l('No file uploaded for key: ' . $this->input_name);
    return $is_set && $is_uploaded;
  }

  function uploadFile() {
    $s = (Object)[
      'bname' => uniqid() . '__temp',
      'tmp_name' => $_FILES[$this->input_name]['tmp_name'],
      'size' => $_FILES[$this->input_name]['size'],
      'mime' => finfo_file(finfo_open(FILEINFO_MIME_TYPE),
        $_FILES[$this->input_name]['tmp_name']),
      'ext' => pathinfo($_FILES[$this->input_name]['name'], PATHINFO_EXTENSION),
      'error' => $_FILES[$this->input_name]['error'],
    ];
    $s->name = $s->bname . '.' .$s->ext;
    $s->dest = $this->upload_dir . '/' . $s->name;

    foreach ((array)$s as $k => $v) $this->$k = $v;

    if (!in_array($s->mime, $this->accept_mime)) {
      return false;
    }
    if(move_uploaded_file($s->tmp_name, $s->dest)) {
      $this->makeThumb($s->dest);
      return $s->name;
    }
  }

  function makeThumb($file_src) {
    if (!$this->thumb_dir) return false;
    if (!is_dir($this->thumb_dir)) {
      if (!mkdir($this->thumb_dir)) {
        l('Failed to make the thumbnails directory ' . $this->thumb_dir);
        return false;
      }
    }
    list($w_src, $h_src, $type) = getimagesize($file_src);     // create new dimensions, keeping aspect ratio
    preg_match_all("/\d+/", $this->thumb_size, $mt);
    list($w_dst, $h_dst) = $mt[0];

    $ratio = $w_src/$h_src;
    if ($w_dst/$h_dst > $ratio) {
      $w_dst = floor($h_dst*$ratio);
    }
    else {
      $h_dst = floor($w_dst/$ratio);
    }
    $new_img = $this->thumb_dir . '/' . basename($file_src);

    $img_dst = imagecreatetruecolor($w_dst, $h_dst);  //  resample
    switch ($type){
      case 1:   //   gif -> jpg
        $img_src = imagecreatefromgif($file_src);
        imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $w_dst, $h_dst, $w_src, $h_src);
        imagegif($img_dst, $new_img);    //  save new image
        break;
      case 2:   //   jpeg -> jpg
        $img_src = imagecreatefromjpeg($file_src);
        imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $w_dst, $h_dst, $w_src, $h_src);
        imagejpeg($img_dst, $new_img);    //  save new image
        break;
      case 3:  //   png -> jpg
        $img_src = imagecreatefrompng($file_src);
        imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $w_dst, $h_dst, $w_src, $h_src);
        imagepng($img_dst, $new_img);    //  save new image
        break;
     }

    imagedestroy($img_src);
    imagedestroy($img_dst);
  }
}
