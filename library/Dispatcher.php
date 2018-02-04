<?php

class Dispatcher{

    public function dispatch(){

        //先頭のスラッシュを削除
        $req_uri = ltrim($_SERVER['REQUEST_URI'], '/');
        $params = array();
        if ('' != $req_uri) {
            // パラメーターを"/"で分割
            $params = explode('/', $req_uri);
        }
        // １番目のパラメーターをコントローラーとして取得
        $controller = 'index';
        if (0 < count($params)) {
            $controller = $params[0];
        }
        // 2番目のパラメーターをコントローラーとして取得
        $action= 'index';
        if (1 < count($params)) {
            $action= $params[1];
        }

        // パラメータより取得した値でコントローラー名作成
        $className = ucfirst(strtolower($controller)) . 'Controller';
        $controllerIns = new $className;

        // アクションメソッドを実行
        $actionMethod = $action . 'Action';
        $controllerIns->$actionMethod();
/*
        switch ($controller) {
            case 'set_tags':
                $indexController->setTagAction();
                break;
            case 'index':
                $indexController->indexAction();
                break;
            case 'about':
                $indexController->showAboutAction();
                break;
            default:
                echo "404 not found";
                exit;
                break;
        }*/
    }
}
?>