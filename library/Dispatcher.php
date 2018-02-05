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

        //定義済みコントローラ
        define('CONTROLLER', ['index']);
        //定義済みアクション
        define('ACTION', ['index', 'set_tag', 'about']);

        //ドメイン直下の場合。indexController.index
        if (count($params) === 0){
            $indexController = new IndexController();
            $indexController->index();
        }
        //ドメイン以下の入力がある場合
        elseif (0 < count($params)) {
            // １番目のパラメーターをコントローラーとして取得
            $get_controller_name = $params[0];

            //デフォルトコントローラ:Index
            $controller_name = 'Index';
            foreach (CONTROLLER as $predefine) {
                //定義済みコントローラと一致する場合
                if ($predefine === $get_controller_name) {
                    //先頭を大文字に変換
                    $controller_name = ucfirst(strtolower($get_controller_name));
                    break;
                }
            }
            // パラメータより取得した値でコントローラー名作成
            $className = $controller_name . 'Controller';
            $controllerIns = new $className;

            /*---------------------------
             *   コントローラ名がIndexの場合
             ---------------------------*/
            if ($controller_name === 'Index') {
                // １番目のパラメーターをアクションとして取得
                $get_action_name = $params[0];

                //アクションはデフォルトnull
                $action_name = null;
                foreach (ACTION as $predefine) {
                    //定義済みアクションと一致する場合
                    if ($predefine === $get_action_name) {
                        //先頭を大文字に変換
                        $action_name = $get_action_name;
                        break;
                    }
                }
                //存在するアクションなら実行
                if ($action_name){
                    // アクションメソッドを実行
                    $actionMethod = $action_name;
                    $controllerIns->$actionMethod();
                //ない場合no data found
                }else{
                    echo "no data found";
                }

                /*---------------------------
                 *   コントローラ名がIndex以外の場合
                 ---------------------------*/
            } else {
                echo "no data found";
            }
        }

    }
}
?>