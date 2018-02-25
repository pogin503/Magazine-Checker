<?php
require_once(dirname(__FILE__) . '/../lib/Requires.php');

$model = new ApplicationRecord;

/* --------------------------
  タグテーブル作成、雑誌とタグの多対多テーブル作成
----------------------------- */
$model->begin();
try{
    $sql = "DROP TABLE IF EXISTS magazines.tags";

    if ($model->execute($sql) !== false ){ print $sql."\n"; }

    $sql = "DROP TABLE IF EXISTS magazines.magazines_tags";

    if ($model->execute($sql) !== false ){ print $sql."\n"; }

    //タグテーブル作成
    $sql = "CREATE TABLE magazines.tags(
             id         INT(10) NOT NULL AUTO_INCREMENT,
             name       VARCHAR(100) NOT NULL,
             created_at DATETIME,
             updated_at DATETIME,
             PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

    if ($model->execute($sql) !== false ){ print $sql."\n"; }

    //雑誌とタグの多対多テーブル作成
    $sql = "CREATE TABLE magazines.magazines_tags(
             magazine_id        INT(10) NOT NULL,
             tag_id             INT(10) NOT NULL,
             created_at DATETIME,
             updated_at DATETIME,
             PRIMARY KEY (magazine_id,tag_id)
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

    if ($model->execute($sql) !== false ){ print $sql."\n"; }

    if ($model->commit()) {
        print "コミット完了\n";
    }

}catch(Exception $e){
    print $e;
    $model->rollback();
}
?>