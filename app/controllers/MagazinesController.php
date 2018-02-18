<?php

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
    /**
     * @param $param
     * @throws Exception
     */
    public function show($param){

        $magazine      = new Magazine;

        try{
            $magazine_info = $magazine->get_magazine_info($param);
            //定義されたものと合致しなかった場合404
            if ($magazine_info){
                $this->data['magazine_info'] = $magazine_info;
                $release_dates = $magazine->get_magazine_release_dates($magazine_info['id']);
                $this->data['release_dates'] = $release_dates;
            } else {
                throw new Exception();
            }

        }catch(Exception $e){
            //定義されたものと合致しなかった場合404
            throw $e;
        }

    }
}
?>