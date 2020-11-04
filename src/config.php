<?php
define('DIR_ROOT', __DIR__);
define('DIR_ROUTER', DIR_ROOT . '/router');
define('DIR_SRC', DIR_ROOT);
define('DIR_PUBLIC', DIR_ROOT . '/../public');

define('PATH_AVATAR', 'img/avatar');
define('DIR_AVATAR', DIR_PUBLIC . '/' . PATH_AVATAR);
define('PATH_AVATAR_THUMB', 'img/avatar/thumb');

include(DIR_ROOT . '/request.php');
include(DIR_ROOT . '/response.php');

?>
