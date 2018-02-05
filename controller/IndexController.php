<?php
define('TAGS', ['week','month','boy','girl']);

class IndexController{

    private $view;

    public function __construct()
    {
        //環境毎に切り替え
        if (SERVER_ENV == "development"){
            $debug       = true;
            $auto_reload = true;
            $cache       = false;
        }elseif(SERVER_ENV == "staging"){
            $debug       = false;
            $auto_reload = false;
            $cache       = './twig_cache';
        }

        //テンプレートファイルがあるディレクトリ
        $loader     = new Twig_Loader_Filesystem('../view');

        $this->view = new Twig_Environment($loader, array(
            'debug'       => $debug,
            'auto_reload' => $auto_reload,
            'cache'       => $cache,
            'charset'     => 'utf-8',
        ));
        $this->view->addExtension(new Twig_Extension_Debug());

    }

    /*==============================
    / トップページの表示
    ==============================*/
    public function index(){
        $template = $this->view->load('index.html.twig');

        $this->confirm_cookie($checked_lists);

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

    }

    /*==============================
    / タグをクッキーに保存する
    ==============================*/
    public function set_tag()
    {
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
        //$this->indexAction("set_tag");
    }

    /*==============================
    / このサイトにについてのページを表示/
    ==============================*/
    public function about()
    {
        $template = $this->view->load('about.html.twig');

        $data = array(
            'page' => 'about',
        );

        echo $template->render($data);
    }

    /*==============================
    / クッキーがある場合とない場合の処理
    ==============================*/
    public function confirm_cookie(&$checked_lists)
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

}
?>