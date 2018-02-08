<?php
require_once('../config/DBConfig.php');
require_once('../models/ModelBase.php');

$model = new ModelBase;

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
             id                 INT(10) NOT NULL  AUTO_INCREMENT,
             magazine_id        INT(10) NOT NULL,
             tag_id             INT(10) NOT NULL,
             created_at DATETIME,
             updated_at DATETIME,
             PRIMARY KEY (id)
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

    if ($model->execute($sql) !== false ){ print $sql."\n"; }

    //初期のタグデータ挿入
    $sql = "INSERT INTO magazines.tags (name, created_at, updated_at)
            VALUES ('少年', now(), now())
                  ,('青年', now(), now())
                  ,('少女', now(), now())
                  ,('週間', now(), now())
                  ,('月刊', now(), now())
            ";

    if ($model->execute($sql) !== false ){ print $sql."\n"; }

    //初期の雑誌とタグ多対多データ挿入
    $sql = "INSERT INTO magazines.magazines_tags (magazine_id, tag_id, created_at, updated_at)
            VALUES (1, 1, now(), now())
                  ,(1, 4, now(), now())
                  ,(2, 1, now(), now())
                  ,(2, 4, now(), now())
                  ,(3, 1, now(), now())
                  ,(3, 5, now(), now())
                  ,(4, 3, now(), now())
                  ,(4, 5, now(), now())
                  ,(5, 1, now(), now())
                  ,(5, 4, now(), now())

            ";

    if ($model->execute($sql) !== false ){ print $sql."\n"; }

    if ($model->commit()) {
        print "コミット完了\n";
    }

}catch(Exception $e){
    print $e;
    $model->rollback();
}
?>