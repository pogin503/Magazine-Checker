<?php
require_once('../vendor/autoload.php');
require_once('../model/Magazine.php');

define('TAGS', ['week','month','boy','girl']);

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
    case 'set_tags':
        $true_case_flag = 0;   //正常ケースフラグ 初期値0
        setcookie('tags["all"]', "",time() - 60);   //チェックなしの状態解除
        //postされた値が存在する場合
        if (isset($_POST['magazine_tag']) &&
            is_array($_POST['magazine_tag'])) {
            //TAGSの定義と照らして存在する場合COOKIEにセット
            foreach (TAGS as $tag) {
                if (in_array($tag, $_POST['magazine_tag'])) {
                    setcookie("tags[$tag]", "checked");
                    $true_case_flag = 1;    //正常ケース
                } else {
                    setcookie("tags[$tag]", "", time() - 60);
                }
            }
        }
        //postされてるのに値がない、またはpostされた値が不正の場合
        if ($true_case_flag === 0){
            foreach (TAGS as $tag){
                //COOKIE全削除
                setcookie("tags[$tag]", "",time() - 60);
            }
            //チェックなしの状態をcookieに保存
            setcookie("tags['all']", "blank");
        }

        header( "Location: ./index" );
        break;

    case 'index':

        confirm_cookie($checked_lists);

        $template = $twig->load('index.html.twig');

        $magazine = new Magazine;
        $target_magazines      = $magazine->refine_by_tag($checked_lists);
        $magazine_last_update  = $magazine->get_magazine_last_update();
        $magazine_current_next = $magazine->get_magazine_current_next($target_magazines);

        $data = array(
            'page'                     => 'index',
            'magazine_last_update'     => $magazine_last_update,
            'magazine_current_next'    => $magazine_current_next,
            'checked_lists'            => $checked_lists,
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
        echo 'NotFound';
}

function confirm_cookie(&$checked_lists)
{
    //cookieがある場合（一度でも設定を行ったことがある）
    if ($_COOKIE) {
        //cookieのキーにtagsがある場合
        if (array_key_exists('tags', $_COOKIE)) {
            foreach (TAGS as $tag) {
                //cookieのkeyにTAGSの値が存在する場合
                if (array_key_exists($tag, $_COOKIE['tags'])) {
                    $checked_lists[$tag] = 'checked="checked"';
                }
            }
        }
        //cookieがあるのに一度もTAGSの値と一致していない場合
        if(empty($checked_lists)){
            setcookie('tags["all"]', "",time() - 60);   //チェックなしの状態解除
            $checked_lists['valid'] = 'NG';
            foreach (TAGS as $key) {
                $checked_lists[$key] = 'checked="checked"';
            }
        }
    //cookieがない（初回訪問、一度も設定を行ったことがない）場合、全てチェック
    }else{
        foreach (TAGS as $key) {
            $checked_lists[$key] = 'checked="checked"';
        }
    }
}