<?php

class Dispatcher
{

    public function dispatch()
    {

        //Routesで定義されたURIとControllerActionを取得
        $req = new Request();
        //HTTPのメソッド取得
        $method = $req->get_request_method();
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
                // すべて小文字にして先頭大文字+Controller
                $controller_name = ucfirst(strtolower($array[0])) . 'Controller';
                $controller = new $controller_name();
                $controller->setControllerAction($array[0], $array[1]);
                $controller->run($matches[1] ?? '');
                exit;
            }
        }

        //定義されたものと合致しなかった場合404
        header("HTTP/1.0 404 Not Found");
        // アクションメソッドを実行
        $indexController = new IndexController();
        $indexController->setControllerAction('index', 'notfound');
        $indexController->run();
        exit;
    }
}
?>