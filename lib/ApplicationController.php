<?php
namespace Libraries;
use \Exception;

abstract class ApplicationController
{
    protected $view;
    protected $data;
    public    $controller='index';
    public    $action    ='index';

    /*==============================
    /  コントローラー名とアクション名を決定
    ==============================*/
    public function setControllerAction($controller, $action){
        $this->controller = $controller;
        $this->action     = $action;
    }

    /*==============================
    /  コントローラーのアクションを実行
    ==============================*/
    /**
     * @param null $param
     * @throws Exception
     */
    public function run($param=null){

        //viewの選定
        $viewpath = sprintf('/%s/%s'
            ,$this->controller
            ,$this->action);

        //twigファイルの絶対パス
        $view_absolute_path = realpath(dirname(__FILE__))."/../app/views".$viewpath.".twig";

        //twigファイルがあるならtwig初期化
        if (file_exists($view_absolute_path)){

            $this->initView();
            $template = $this->view->load("".$viewpath.".twig");
            // /controller/action を設定
            $this->data['page'] = $viewpath;
            // http://ドメイン名 を設定
            $this->data['host'] = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
        }

        //アクション実行
        $action_name = $this->action;
        try{
            $this->$action_name($param);
        }catch(Exception $e){
            throw $e;
        }

        //viewが必用なアクションの場合
        if (file_exists($view_absolute_path)){
            echo $template->render($this->data);
        }

    }

    /*==============================
    / twigのview初期化
    ==============================*/
    protected function initView(){
        //環境毎にviewの設定切り替え
        if (SERVER_ENV == "development"){
            $debug       = true;
            $auto_reload = true;
            $cache       = false;
        }elseif(SERVER_ENV == "staging"){
            $debug       = false;
            $auto_reload = false;
            $cache       = '../tmp/twig_cache';
        }

        //テンプレートファイルがあるディレクトリ
        $loader     = new \Twig_Loader_Filesystem('../app/views/');

        $this->view = new \Twig_Environment($loader, array(
            'debug'       => $debug,
            'auto_reload' => $auto_reload,
            'cache'       => $cache,
            'charset'     => 'utf-8',
        ));
        $this->view->addExtension(new \Twig_Extension_Debug());
    }
}

?>