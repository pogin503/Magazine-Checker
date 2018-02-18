<?php

class Dispatcher
{

    public function execute()
    {

        //Routesで定義されたURIとControllerActionを取得
        $req = new Request();
        //HTTPのメソッド取得
        $method = $req->get_request_method();
        //URI取得
        $uri = $req->get_request_uri();
        //Routesで定義されたMETHODとURIとControllerActionの組み合わせを取得
        $routes = new Routes();

        foreach ($routes->patterns as $pattern) {

            //URIの一致を確認
            $result = preg_match("#^${pattern['uri']}$#", $uri, $matches);

            //定義済みのHTTPメソッドとURIの組み合わせに一致する場合
            if (($pattern['method'] == $method) &&
                ($result === 1))
            {
                // #で分割
                $array = explode('#', $pattern['controller_action']);

                try {
                    $this->run_controller($array[0], $array[1], $matches[1]??'');
                //例外が発生した場合
                }catch(Exception $e){
                    //404
                    $this->run_controller('index', 'not_found');
                }finally{
                    return;
                }
            }
        }

        //定義されたものと合致しなかった場合404
        $this->run_controller('index', 'not_found');
        return;
    }

    /*---------------------------
     *   コントローラーのアクションを実行
     ---------------------------*/
    public function run_controller($controller, $action, $param='')
    {
        // すべて小文字にして先頭大文字+Controller
        $controller_name = ucfirst(strtolower($controller)) . 'Controller';

        $controller_instance = new $controller_name();
        $controller_instance->setControllerAction($controller, $action);
        $controller_instance->run($param);
    }
}
?>