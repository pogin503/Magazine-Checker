<?php

class Routes{
    //定義済みコントローラ、アクション
    public $patterns;

    public function __construct() {
        $this->set_routes();
    }

    public function set_routes(){
                //['Method', 'URI',  'Controller#Action']
        $a[] = ['GET',  '/',                 'index#index'];
        $a[] = ['GET',  '/index',            'index#index'];
        $a[] = ['GET',  '/about',            'index#about'];
        $a[] = ['POST', '/set_tag',          'index#set_tag'];
        $a[] = ['GET',  '/magazines',        'magazines#index'];
        $a[] = ['GET',  '/magazines/(.+)',   'magazines#show'];

        $hash = null;

        foreach ($a as $item) {

            //もし両端の'/'を除いたとき値がない場合
            //$uri = (trim($item[1], '/') !== '') ? trim($item[1], '/') : null;

            $hash [] = ['method'  =>  $item[0],
                        'uri'     =>  addslashes($item[1]), //エスケープする
                        'controller_action'=>$item[2],
            ];
        }

        $this->patterns = $hash;
    }

}

?>