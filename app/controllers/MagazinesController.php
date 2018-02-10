<?php
require_once(dirname(__FILE__) . '/ApplicationController.php');

class MagazinesController extends ApplicationController
{
    /*==============================
    / トップページの表示
    ==============================*/
    public function index(){

        $magazine = new Magazine;
        $magazine_list = $magazine->get_magazines();

        $this->data['magazine_list'] = $magazine_list;

    }

    /*==============================
    / 雑誌の個別ページ
    ==============================*/
    public function show(){

        $magazine = new Magazine;
        $magazine_list = $magazine->get_magazines();

        $this->data = array(
            'magazine_list'     => $magazine_list,
        );

    }
}
?>