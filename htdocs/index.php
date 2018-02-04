<?php
require_once('../config/EnvConfig.php');  //環境設定
require_once('../vendor/autoload.php'); //twigをどこからでも呼び出せるよう

// ライブラリの絶対パス
define('LIB_PATH', realpath(dirname(__FILE__) . '/../library'));
// モデルの絶対パス
define('MODEL_PATH', realpath(dirname(__FILE__) . '/../model'));
// コントローラの絶対パス
define('CONTROLLER_PATH', realpath(dirname(__FILE__) . '/../controller'));
// ライブラリとモデルのディレクトリをinclude_pathに追加
$incPath = implode(PATH_SEPARATOR, array(LIB_PATH, MODEL_PATH, CONTROLLER_PATH));
// ライブラリの絶対パスをinclude_pathに追加
set_include_path(get_include_path() . PATH_SEPARATOR . $incPath);

// クラスのオートロード（ライブラリ配下のクラスをいきなりnewで呼び出せる）
function myClassLoader($className){
    require_once ($className.'.php');
}
spl_autoload_register('myClassLoader');

$dispatch = new Dispatcher();
$dispatch->dispatch();

?>