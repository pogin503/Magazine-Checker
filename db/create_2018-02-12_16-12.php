<?php
require_once(dirname(__FILE__) . '/../app/models/ApplicationRecord.php');
require_once(dirname(__FILE__) . '/../config/DBConfig.php');

$model = new ApplicationRecord;

$model->begin();
try{

    /*======================================
    *  magazinesテーブル
    ======================================*/
    //magazinesテーブルがあったら削除
    $sql = "DROP TABLE IF EXISTS `magazines`";

    if ($model->execute($sql) !== false ){ print $sql."\n"; }

    //magazinesテーブル作成
    $sql = "CREATE TABLE `magazines` (
              `id`         int(10) NOT NULL AUTO_INCREMENT,
              `name`       varchar(100) COLLATE utf8_bin NOT NULL,
              `url`        varchar(300) COLLATE utf8_bin NOT NULL,
              `status`     tinyint(1) NOT NULL,
              `created_at` datetime NOT NULL,
              `updated_at` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

    if ($model->execute($sql) !== false ){ print $sql."\n"; }

    /*======================================
     *  titles_and_release_dateテーブル
     ======================================*/

    //titles_and_release_dateテーブルがあったら削除
    $sql = "DROP TABLE IF EXISTS `titles_and_release_date`";

    if ($model->execute($sql) !== false ){ print $sql."\n"; }

    //titles_and_release_dateテーブル作成
    $sql = "CREATE TABLE `titles_and_release_date` (
              `id`            int(10) NOT NULL AUTO_INCREMENT,
              `magazine_id`   int(11) NOT NULL,
              `title`         varchar(100) COLLATE utf8_bin NOT NULL,
              `del_flag`      tinyint(1) NOT NULL DEFAULT '0',
              `release_date`  date NOT NULL,
              `created_at`    datetime NOT NULL,
              `updated_at`    datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

    if ($model->execute($sql) !== false ){ print $sql."\n"; }

    /*======================================
    *  url_and_regexテーブル
    ======================================*/
    //url_and_regexテーブルがあったら削除
    $sql = "DROP TABLE IF EXISTS `url_and_regex`";

    if ($model->execute($sql) !== false ){ print $sql."\n"; }

    //magazinesテーブル作成
    $sql = "CREATE TABLE `url_and_regex` (
              `id`          int(11) NOT NULL AUTO_INCREMENT,
              `magazine_id` int(10) NOT NULL,
              `url`         varchar(200) COLLATE utf8_bin NOT NULL,
              `file_path`   varchar(100) COLLATE utf8_bin NOT NULL,
              `target`      varchar(100) COLLATE utf8_bin NOT NULL,
              `getpicflg`   tinyint(1) DEFAULT NULL,
              `title_reg`   varchar(100) COLLATE utf8_bin NOT NULL,
              `release_reg` varchar(100) COLLATE utf8_bin NOT NULL,
              `created_at`  datetime NOT NULL,
              `updated_at`  datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

    if ($model->execute($sql) !== false ){ print $sql."\n"; }



    if ($model->commit()) {
        print "コミット完了\n";
    }

}catch(Exception $e){
    print $e;
    $model->rollback();
}
?>