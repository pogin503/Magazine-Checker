<?php
define('TAGS', ['week','month','boy','girl']);

class IndexController extends ApplicationController {

    /*==============================
    / トップページの表示
    ==============================*/
    public function index(){

        $this->confirm_cookie($checked_lists);

        $index = new Index;
        $target_magazines      = $index->refine_by_tag($checked_lists);
        $magazine_last_update  = $index->get_magazine_last_update();
        $magazine_current_next = $index->get_magazine_current_next($target_magazines);

        $this->data = array_merge($this->data,[
                        'magazine_last_update'     => $magazine_last_update,
                        'magazine_current_next'    => $magazine_current_next,
                        'checked_lists'            => $checked_lists,
        ]);

    }

    /*==============================
    / ４０４not foundを表示する
    ==============================*/
    public function not_found(){
        header("HTTP/1.0 404 Not Found");
    }

    /*==============================
    / このサイトにについてのページを表示/
    ==============================*/
    public function about()
    {

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