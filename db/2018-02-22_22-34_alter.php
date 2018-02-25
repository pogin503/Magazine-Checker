<?php
require_once(dirname(__FILE__) . '/../lib/Requires.php');

/*====================================================
 *  カラム変更
 ====================================================*/
$model = new ApplicationRecord;

$model->begin();
try{
    /*
    * ファイルパス カラムを削除
    */
    $sql = "ALTER TABLE magazines.url_and_regex DROP COLUMN file_path;";

    if ($model->execute($sql) !== false ){
        $model->result_output($sql);
    }

    /*
    * 今号か次号かのフラグ追加
    */
    $sql = "ALTER TABLE magazines.url_and_regex ADD issue CHAR(4) AFTER magazine_id;";

    if ($model->execute($sql) !== false ){
        $model->result_output($sql);
    }

    /*
    * テーブル名称変更
    */
    $sql = "ALTER TABLE magazines.url_and_regex RENAME TO magazines.scrapers;";

    if ($model->execute($sql) !== false ){
        $model->result_output($sql);
    }

    if ($model->commit()) {print "コミット完了".PHP_EOL;}

}catch(Exception $e){
    print $e;
    $model->rollback();
}



?>