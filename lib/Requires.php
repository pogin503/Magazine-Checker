<?php

require_once(dirname(__FILE__) . '/../config/DBConfig.php');  //DB設定
require_once(dirname(__FILE__). '/../config/EnvConfig.php');  //環境設定
require_once(dirname(__FILE__). '/../vendor/autoload.php');   //vendor配下をどこからでも呼べるように

/*-------------------------------------
 * ライブラリ、モデル、コントローラーの絶対パスをINCLUDE_PATHに追加
 -------------------------------------*/
define('LIB_PATH',        realpath(dirname(__FILE__)));
define('MODEL_PATH',      realpath(dirname(__FILE__) . '/../app/models'));
define('CONTROLLER_PATH', realpath(dirname(__FILE__) . '/../app/controllers'));
$incPath = implode(PATH_SEPARATOR, array(LIB_PATH, MODEL_PATH, CONTROLLER_PATH));
set_include_path(get_include_path() . PATH_SEPARATOR . $incPath);

/*--------------------------------------
 *クラスのオートロード（ライブラリ配下のクラスをいきなりnewで呼び出せる）
 --------------------------------------*/
function myClassLoader($className){
    require_once ($className.'.php');
}
spl_autoload_register('myClassLoader');

?>