<?php

class MigrationController extends ApplicationController
{
    /*==============================
    / マイグレーション実行
    ==============================*/
    public function execute(){

        define('DB_PATH',dirname(__FILE__).'/../../db/');

        echo "マイグレーション実行\n";

        /*-------------------------------
         *  migrationsテーブルから実行済みのファイル名を取得
         --------------------------------*/
        $migration = new Migration;
        //migrationsテーブルの存在を確認
        $result = $migration->migrations_table_exist('migrations');
        //migrationsテーブルが無い場合作成
        if ($result === false) {
            $migration->create_migrations_table();
        }
        //実行済みファイル名取得
        $finished_list = $migration->get_finished();

        //db配下のファイル名とパスを取得
        $file_array = $this->getFileList(DB_PATH);
        //ファイル名順にソート
        asort($file_array);
        /*-------------------------------
         *  db配下のファイルで未実行ファイルを実行
         *  実行順序
         *  1.db配下のalter、create文
         *  2.data配下のinsert、delete文
         --------------------------------*/
        foreach ($file_array as $file){
            if (array_search($file['name'], $finished_list) !== false){
                echo "実行済み:".$file['name']."\n";
            }else{
                echo "---------------------------\n";
                echo "未実行ファイル:".$file['name']."\n";
                //未実行ファイル実行
                exec("php72 {$file['path']}", $output);
                foreach($output as $a){
                    echo $a."\n";
                }
                $migration->insert_finished($file['name']);
                echo "---------------------------\n";
            }
        }

    }

    /*==============================
    / ディレクトリ配下のファイル名とパスを取得
    ==============================*/
    public function getFileList($dir) {

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        $list = [];
        foreach ($iterator as $file_info) {
            if ($file_info->isFile()){
                $list[] = ['path'=> $file_info->getPathname(),
                           'name'=> $file_info->getFilename()
                ];
            }
        }
        return $list;

    }
}
?>