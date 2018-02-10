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

        //定義済みコントローラ、アクション
        $controllers['index']     = ['index', 'set_tag', 'about'];
        $controllers['magazines'] = ['index'];

        //ドメイン直下の場合。indexController.index
        if (count($params) === 0){
            $indexController = new IndexController();
            $indexController->setControllerAction('index','index');
            $indexController->run();
        }
        //ドメイン以下の入力がある場合
        elseif (0 < count($params)) {
            /*---------------------------
             *   コントローラ名の決定
             ---------------------------*/
            // １番目のパラメーターをコントローラーとして取得
            $get_controller_name = $params[0];

            //定義済みコントローラと一致する場合
            if (array_key_exists($get_controller_name, $controllers)) {
                //先頭を大文字に変換
                $controller_name = ucfirst(strtolower($get_controller_name));
            }else{
                $controller_name = 'Index';
            }

            // パラメータより取得した値でコントローラー名作成
            $className = $controller_name . 'Controller';
            $controllerInstance = new $className;

            /*---------------------------
             *   アクション名の決定
             ---------------------------*/
            //コントローラ名
            $key = strtolower($controller_name);
            $action_name = null;
            $notview = null;
            //コントローラがIndexの場合、一番目のパラメータをアクションにする
            if ($controller_name === 'Index') {
                // １番目のパラメーターをアクションとして取得
                $get_action_name = $params[0];
                if (in_array($get_action_name, $controllers[$key])) {
                    $action_name = $get_action_name;
                }
            //その他の場合、二番目のパラメータをアクションにする
            }else{
                //もし２番目のパラメータが存在するなら
                if (1 < count($params)){
                    $get_action_name = $params[1];
                    if (in_array($get_action_name, $controllers[$key])) {
                        $action_name = $get_action_name;
                    }
                }else{
                    $action_name = $controllers[$key][0];
                }
            }

            //存在するアクションなら実行
            if ($action_name){
                if($action_name === "set_tag"){
                    $notview = true;
                }
                // アクションメソッドを実行
                $controllerInstance->setControllerAction($key,$action_name);
                $controllerInstance->run($notview);
            //ない場合no data found
            }else{
                echo "no data found";
            }

        }

    }
}
?>