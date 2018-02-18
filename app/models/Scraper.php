<?php
/*-------------------------------------
 *     スクレイパーモデル
 --------------------------------------*/
class Scraper extends ApplicationRecord {

    public function __construct() {
        //親クラスのコンストラクタを呼び出す
        parent::__construct();
    }

    /*-------------------------
     * URLと正規表現の設定を取得
     -------------------------*/
    public function get_url_and_regex(){
        $sql = "SELECT *
                FROM   url_and_regex
                ";
        $result = $this->fetchAll($sql, PDO::FETCH_ASSOC);
        return $result;
    }

    /*-------------------------
     * 同一のタイトルと発売日がすでに存在するか
     -------------------------*/
    public function url_and_regex_exist($release_date, $title, $magazine_id){

        $sql = "SELECT *
                FROM   titles_and_release_date
                WHERE  release_date = ?
                AND    title        = ?
                AND    magazine_id  = ?
                ";

        $bindval [] = ['param'=>1, 'val'=>$release_date, 'type'=>PDO::PARAM_STR];
        $bindval [] = ['param'=>2, 'val'=>$title,        'type'=>PDO::PARAM_STR];
        $bindval [] = ['param'=>3, 'val'=>$magazine_id,  'type'=>PDO::PARAM_INT];
        $result = $this->prepare_fetchAll($sql, $bindval, PDO::FETCH_ASSOC);
        return $result;
    }

    /*-------------------------
     * 雑誌の一番最新のデータとタイトルが一致するのに発売日が異なるか
     -------------------------*/
    public function release_date_changed($title, $magazine_id, $release_date){

        $sql = "SELECT *
                FROM   titles_and_release_date 
                WHERE  title        = :title
                AND    magazine_id  = :magazine_id
                AND    release_date <> STR_TO_DATE(:release_date ,'%Y-%m-%d')
                AND    release_date = (SELECT MAX(release_date)
                                       FROM   titles_and_release_date
                                       WHERE  magazine_id = :magazine_id
                                      )
                ";

        $bindval [] = ['param'=>':title',       'val'=>$title,        'type'=>PDO::PARAM_STR];
        $bindval [] = ['param'=>':magazine_id', 'val'=>$magazine_id,  'type'=>PDO::PARAM_INT];
        $bindval [] = ['param'=>':release_date','val'=>$release_date->format('Y-m-d'), 'type'=>PDO::PARAM_STR];
        $result = $this->prepare_fetchAll($sql, $bindval, PDO::FETCH_ASSOC);
        return $result;

    }

    /*----------------------
     * 週刊誌の発売日を更新
     ----------------------*/
    public function change_release_date($title, $magazine_id, $release_date){

        $sql = "UPDATE titles_and_release_date
                SET del_flag   = 1
                   ,updated_at = now()
                WHERE  title        = :title
                AND    magazine_id  = :magazine_id
                AND    release_date <> STR_TO_DATE(:release_date ,'%Y-%m-%d')
                AND    release_date = (SELECT TMP.max_date
                                       FROM (SELECT MAX(release_date) max_date
                                             FROM   titles_and_release_date
                                             WHERE  magazine_id = :magazine_id
                                             ) TMP
                                        )
                ";
        $bindval [] = ['param'=>':title',       'val'=>$title,        'type'=>PDO::PARAM_STR];
        $bindval [] = ['param'=>':magazine_id', 'val'=>$magazine_id,  'type'=>PDO::PARAM_INT];
        $bindval [] = ['param'=>':release_date','val'=>$release_date->format('Y-m-d'), 'type'=>PDO::PARAM_STR];
        $result = $this->prepare_fetchAll($sql, $bindval, PDO::FETCH_ASSOC);
        return $result;

    }

    #-------------------------
    # 週刊誌の発売日を登録
    #-------------------------
    public function insert($title, $magazine_id, $release_date)
    {
        $sql = "INSERT INTO titles_and_release_date
                       (magazine_id, title, release_date, updated_at,created_at)
                VALUES ('$magazine_id', '$title', '$release_date', now(), now())
                ";
        if ($this->execute($sql) !== false ){ print $sql."\n"; }
    }
}

$html = file_get_contents("https://ja.wikipedia.org/wiki/%E3%82%A6%E3%82%A7%E3%83%96%E3%82%B9%E3%82%AF%E3%83%AC%E3%82%A4%E3%83%94%E3%83%B3%E3%82%B0");

echo phpQuery::newDocument($html)->find("h1")->text();