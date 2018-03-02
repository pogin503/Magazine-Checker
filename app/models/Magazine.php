<?php
namespace Models;

use Libraries\ApplicationRecord;
use \PDO;
//---------------------------------------
//        雑誌モデル
//---------------------------------------
class Magazine extends ApplicationRecord {

    public function __construct() {
        //親クラスのコンストラクタを呼び出す
        parent::__construct();
    }

    //---------------------------------------
    //        雑誌リストを取得
    //---------------------------------------
    public function get_magazines(){
        $sql = "SELECT name
                FROM   magazines
                ORDER BY name
                ";
        $result = $this->fetchAll($sql, PDO::FETCH_COLUMN);
        return $result;
    }
    //---------------------------------------
    //        任意の雑誌の号と発売日を取得
    //---------------------------------------
    public function get_magazine_release_dates($param){
        $sql = "SELECT TR.title
                      ,TR.release_date
                FROM  titles_and_release_date  TR
                WHERE  TR.magazine_id = ?
                ORDER BY release_date DESC
                ";
        //戻り値は配列
        $bindval [] = ['param'=>1, 'val'=>$param, 'type'=>PDO::PARAM_INT];
        $result = $this->prepare_fetchAll($sql, $bindval, PDO::FETCH_ASSOC);
        return $result;
    }
    //---------------------------------------
    //        任意の雑誌情報を取得
    //---------------------------------------
    public function get_magazine_info($param){
        $sql = "SELECT M.id
                      ,M.name
                      ,M.url
                      ,GROUP_CONCAT(T.name) AS tags
                FROM  magazines M
                     INNER JOIN magazines_tags MT
                             ON M.id = MT.magazine_id
                     INNER JOIN tags T
                             ON T.id = MT.tag_id
                WHERE  M.name   = ?
                GROUP BY M.id
                ";

        //戻り値は配列
        $bindval [] = ['param'=>1, 'val'=>$param, 'type'=>PDO::PARAM_STR];
        $result = $this->prepare_fetch($sql, $bindval, PDO::FETCH_ASSOC);
        return $result;
    }

}
?>