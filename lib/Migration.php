<?php

require_once(dirname(__FILE__) . '/../app/models/ApplicationRecord.php');

class Migration extends ApplicationRecord {

    public function __construct() {
        //親クラスのコンストラクタを呼び出す
        parent::__construct();
    }

    /*-------------------------------
     *  migrationsテーブルの有無を確認
    --------------------------------*/
    public function migrations_table_exist($table_name){
        $sql = "SHOW  TABLES 
                FROM  magazines 
                WHERE tables_in_magazines = ?
               ";

        //戻り値は配列
        $bindval [] = ['param'=>1, 'val'=>$table_name, 'type'=>PDO::PARAM_STR];
        $result = $this->prepare_fetch($sql, $bindval, PDO::FETCH_ASSOC);
        return $result;
    }

    /*-------------------------------
     *  migrationsテーブルの作成
     --------------------------------*/
    public function create_migrations_table(){
        //migrationsテーブルがあったら削除
        $sql = "DROP TABLE IF EXISTS `migrations`";

        if ($this->execute($sql) !== false ){ print $sql."\n"; }

        //migrationsテーブル作成
        $sql = "CREATE TABLE `migrations` (
              `id`         int(10) NOT NULL AUTO_INCREMENT,
              `finished`   varchar(100) COLLATE utf8_bin NOT NULL,
              `created_at` date NOT NULL,
              `updated_at` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

        if ($this->execute($sql) !== false ){ print $sql."\n"; }
    }

    /*-------------------------------
     *  migrationsテーブルから実行済みファイルを取得
    --------------------------------*/
    public function get_finished(){

        $sql = "SELECT finished
                FROM   migrations
                ";
        $result = $this->fetchAll($sql, PDO::FETCH_COLUMN);
        return $result;
    }

    /*-------------------------------
    *  migrationsテーブルから実行済みファイルを取得
    --------------------------------*/
    public function insert_finished($file){

        $sql = "INSERT INTO magazines.migrations (finished, created_at, updated_at)
                VALUES ('$file', now(), now())
                ";
        if ($this->execute($sql) !== false ){ print $sql."\n"; }
    }

    /*-------------------------------
    *  未実行ファイルを実行
    --------------------------------*/
    public function execute_migrate($file){
        echo exec("php72 $file")."\n";
    }
}
?>