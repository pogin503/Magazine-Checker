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
     * 設定を取得
     -------------------------*/
    public function get_scrapers(){
        $sql = "SELECT M.name
                      ,S.magazine_id
                      ,S.url
                      ,S.target
                      ,S.title_reg
                      ,S.release_reg
                FROM   scrapers S
                       INNER JOIN magazines M
                             ON M.id = S.magazine_id
                WHERE  S.issue = 'next'
                AND    S.getpicflg IS NULL    
                ";
        $result = $this->fetchAll($sql, PDO::FETCH_ASSOC);
        return $result;
    }

    /*-------------------------
     * 同一のタイトルと発売日がすでに存在するか
     -------------------------*/
    public function issue_exist($title, $magazine_id, $release_date){

        $sql = "SELECT COUNT(*)
                FROM   titles_and_release_date
                WHERE  release_date = STR_TO_DATE(:release_date ,'%Y-%m-%d')
                AND    title        = :title
                AND    magazine_id  = :magazine_id
                AND    del_flag     = 0
                ";

        $bindval [] = ['param'=>':title',       'val'=>$title,        'type'=>PDO::PARAM_STR];
        $bindval [] = ['param'=>':magazine_id', 'val'=>$magazine_id,  'type'=>PDO::PARAM_INT];
        $bindval [] = ['param'=>':release_date','val'=>$release_date->format('Y-m-d'), 'type'=>PDO::PARAM_STR];
        $result = $this->prepare_fetch($sql, $bindval, PDO::FETCH_COLUMN);
        return $result >= 1 ? true : false;
    }

    /*-------------------------
     * 雑誌の一番最新のデータとタイトルが一致するのに発売日が異なるか
     -------------------------*/
    public function issue_changed($title, $magazine_id, $release_date){

        $sql = "SELECT COUNT(*)
                FROM   titles_and_release_date TR
                WHERE  TR.title        = :title
                AND    TR.magazine_id  = :magazine_id
                AND    TR.del_flag     = 0
                AND    TR.release_date <> STR_TO_DATE(:release_date ,'%Y-%m-%d')
                AND    TR.release_date = (SELECT TMP.max_date
                                          FROM  (SELECT MAX(release_date) AS max_date
                                                 FROM   titles_and_release_date
                                                 WHERE  magazine_id = :magazine_id2
                                                ) TMP
                                         )                
                ";

        $bindval [] = ['param'=>':title',       'val'=>$title,        'type'=>PDO::PARAM_STR];
        $bindval [] = ['param'=>':magazine_id', 'val'=>$magazine_id,  'type'=>PDO::PARAM_INT];
        $bindval [] = ['param'=>':magazine_id2','val'=>$magazine_id,  'type'=>PDO::PARAM_INT];
        $bindval [] = ['param'=>':release_date','val'=>$release_date->format('Y-m-d'), 'type'=>PDO::PARAM_STR];
        $result = $this->prepare_fetch($sql, $bindval, PDO::FETCH_COLUMN);
        return $result >= 1 ? true : false;

    }

    /*----------------------
     * 旧発売日を論理削除
     ----------------------*/
    public function update_issue($title, $magazine_id, $release_date){

        $sql = "UPDATE titles_and_release_date
                SET del_flag   = 1
                   ,updated_at = now()
                WHERE  title        = :title
                AND    magazine_id  = :magazine_id
                AND    del_flag     = 0
                AND    release_date <> STR_TO_DATE(:release_date ,'%Y-%m-%d')
                AND    release_date = (SELECT TMP.max_date
                                       FROM  (SELECT MAX(release_date) AS max_date
                                              FROM   titles_and_release_date
                                              WHERE  magazine_id = :magazine_id2
                                             ) TMP
                                      )
                ";
        $bindval [] = ['param'=>':title',       'val'=>$title,        'type'=>PDO::PARAM_STR];
        $bindval [] = ['param'=>':magazine_id', 'val'=>$magazine_id,  'type'=>PDO::PARAM_INT];
        $bindval [] = ['param'=>':magazine_id2','val'=>$magazine_id,  'type'=>PDO::PARAM_INT];
        $bindval [] = ['param'=>':release_date','val'=>$release_date->format('Y-m-d'), 'type'=>PDO::PARAM_STR];
        $result = $this->prepare_exec($sql, $bindval);
        return $result !== false ? true : false;

    }

    #-------------------------
    # 週刊誌の発売日を登録
    #-------------------------
    public function insert_issue($title, $magazine_id, $release_date)
    {
        $sql = "INSERT INTO titles_and_release_date
                       (magazine_id, title, release_date, updated_at,created_at)
                VALUES (:magazine_id, :title, :release_date, now(), now())
                ";
        $bindval [] = ['param'=>':title',       'val'=>$title,        'type'=>PDO::PARAM_STR];
        $bindval [] = ['param'=>':magazine_id', 'val'=>$magazine_id,  'type'=>PDO::PARAM_INT];
        $bindval [] = ['param'=>':release_date','val'=>$release_date->format('Y-m-d'), 'type'=>PDO::PARAM_STR];
        $result = $this->prepare_exec($sql, $bindval);
        return $result !== false ? true : false;

    }
}