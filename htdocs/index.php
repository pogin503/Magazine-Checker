<?php
require_once('../vendor/autoload.php');
require_once('../config/DBConfig.php');
require_once('../model/ModelBase.php');

$magazine = new Magazine;
$magazine_last_update  = $magazine->get_magazine_last_update();
$magazine_current_next = $magazine->get_magazine_current_next();

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
$template = $twig->load('index.html.twig');

$data = array(
    'message' => $magazine_last_update,
    'array'   => $magazine_current_next,
);

echo $template->render($data);