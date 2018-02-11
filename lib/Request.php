<?php

class Request{

    private  $request_uri;
    private  $request_method;

    public function __construct() {
        $this->request_uri    = $_SERVER['REQUEST_URI'];
        $this->request_method = $_SERVER['REQUEST_METHOD'];
    }
    /*---------------------------
     *   URIのパスを'/'で分割
     ---------------------------*/
    public function get_split_uri(){

        //先頭と末尾のスラッシュを削除
        $req_uri = trim($this->request_uri, '/');

        //リクエストURIに値がある場合、'/'で分割し返す
        $params = $req_uri ? explode('/', $req_uri) : null;

        return $params;
    }

    /*---------------------------
     *   URIを加工して返す
     ---------------------------*/
    public function get_request_uri(){
        //両端の'/'を削り、先頭に'/'をつける
        return '/'.trim(urldecode($this->request_uri),'/');
    }

    /*---------------------------
    *    REQUEST_METHODをそのまま返す
    ---------------------------*/
    public function get_request_method(){
        return $this->request_method;
    }
}