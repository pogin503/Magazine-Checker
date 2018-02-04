<?php
require_once('../config/EnvConfig.php');
require_once('../vendor/autoload.php');
//require_once('../model/Magazine.php');
require_once('../controller/IndexController.php');

//先頭のスラッシュを削除
$req_uri = ltrim($_SERVER['REQUEST_URI'], '/');

$params = array();
if ('' != $req_uri) {
    // パラメーターを"/"で分割
    $params = explode('/', $req_uri);
}
// １番目のパラメーターをコントローラーとして取得
$controller = 'index';
if (0 < count($params)) {
    $controller = $params[0];
}

$indexController = new IndexController;
switch ($controller) {
    case 'set_tags':
        $indexController->setTagAction();
        break;
    case 'index':
        $indexController->indexAction();
        break;
    case 'about':
        $indexController->showAboutAction();
        break;
    default:
        echo 'NotFound';
}