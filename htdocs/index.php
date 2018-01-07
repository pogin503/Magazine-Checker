<?php
require_once('../vendor/autoload.php');
require_once('../config/DBConfig.php');
require_once('../model/ModelBase.php');

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

//---------------------------------------
//        twigの設定
//---------------------------------------
//テンプレートファイルがあるディレクトリ
$loader = new Twig_Loader_Filesystem('../view');
$twig = new Twig_Environment($loader, array(
    //'cache' => './compilation_cache',
    'debug' => true,
));
$twig->addExtension(new Twig_Extension_Debug());

switch ($controller){
    case 'index':
        $template = $twig->load('index.html.twig');

        $magazine = new Magazine;
        $magazine_last_update  = $magazine->get_magazine_last_update();
        $magazine_current_next = $magazine->get_magazine_current_next();

        $data = array(
            'page' => 'index',
            'message' => $magazine_last_update,
            'array'   => $magazine_current_next,
        );

        echo $template->render($data);
        break;

    case 'about':
        $template = $twig->load('about.html.twig');

        $data = array(
            'page' => 'about',
        );

        echo $template->render($data);
        break;
    default:
        echo '誰だお前';
}
