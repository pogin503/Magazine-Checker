<?php

class MagazinesController
{
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
            $cache       = '../views/twig_cache';
        }

        //テンプレートファイルがあるディレクトリ
        $loader     = new Twig_Loader_Filesystem('../app/views/');

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
        $template = $this->view->load('./magazines/index.twig');

        $magazine = new Magazine;
        $magazine_list = $magazine->get_magazines();

        $data = array(
            'page'              => 'magazines',
            'magazine_list'     => $magazine_list,
        );

        echo $template->render($data);

    }

}
?>