<?php
namespace Libraries;

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

            $hash [] = ['method'  =>  $item[0],
                        'uri'     =>  addslashes($item[1]), //エスケープする
                        'controller_action'=>$item[2],
            ];
        }

        $this->patterns = $hash;
    }

}

?>