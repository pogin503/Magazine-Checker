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

        $this->data = array(
            'page'              => 'magazines',
            'magazine_list'     => $magazine_list,
        );

    }

}
?>