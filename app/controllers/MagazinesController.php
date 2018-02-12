<?php
require_once(dirname(__FILE__) . '/ApplicationController.php');

class MagazinesController extends ApplicationController
{
    /*==============================
    / トップページの表示
    ==============================*/
    public function index(){

        $magazine      = new Magazine;
        $magazine_list = $magazine->get_magazines();

        $this->data['magazine_list'] = $magazine_list;

    }

    /*==============================
    / 雑誌の個別ページ
    ==============================*/
    public function show($param){

        $magazine      = new Magazine;
        $magazine_info = $magazine->get_magazine_info($param);

        if ($magazine_info){
            $this->data['magazine_info'] = $magazine_info;

            $release_dates = $magazine->get_magazine_release_dates($magazine_info['id']);
            $this->data['release_dates'] = $release_dates;

        }else{
            //定義されたものと合致しなかった場合404
            header("HTTP/1.0 404 Not Found");
            // アクションメソッドを実行
            $indexController = new IndexController();
            $indexController->setControllerAction('index', 'notfound');
            $indexController->run();
            exit;
        }

    }
}
?>