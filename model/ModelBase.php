<?php
//---------------------------------------
//        親モデル
//---------------------------------------
class ModelBase
{
    protected $db;

    public function __construct()
    {
        $this->initDB();
    }

    public function initDB()
    {
        $this->db = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME.';charset=utf8'
            ,DB_USER
            ,DB_PASSWD);
    }
}

//子クラスにコンストラクタがない場合、親クラスのコンストラクタが自動で呼ばれる
//---------------------------------------
//        雑誌モデル
//---------------------------------------
class Magazine extends ModelBase{

    private $week_jp = ["日", "月", "火", "水", "木", "金", "土"];

    //---------------------------------------
    //        雑誌チェックの最終更新日を取得
    //---------------------------------------
    public function get_magazine_last_update(){
        $sql = "SELECT max(updated_at) as max_date
                FROM   magazines
                ";
        $stmt = $this->db->query($sql);
        $stmt -> execute();
        $result   = $stmt->fetch(PDO::FETCH_ASSOC);
        $max_date = strtotime($result['max_date']);
        $week      = date("w", $max_date);
        $time      = date("H:i:s", $max_date);
        $date      = date("Y年m月d日", $max_date);
        $update_date = $date."(".$this->week_jp[$week].") ".$time;
        return $update_date;
    }

    //---------------------------------------
    //        雑誌のタイトルと日付を配列に格納
    //---------------------------------------
    public function get_magazine_current_next(){

        $array = [];
        $today = new DateTime('now');
        $today_format = date_format($today, 'Y/m/d H:i:s');

        $sql = "SELECT *
                FROM current_next_v
                ";
        $stmt = $this->db->query($sql);
        $stmt -> execute();

        while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //初期化
            $status = 1; //正常
            $message = "";
            $release_day = new DateTime($result['release_date']);

            $date      = date("n/d", strtotime($result['release_date']));
            $week      = date("w", strtotime($result['release_date']));

            $interval  = date_diff($today, $release_day);
            $diff_day  = (int)($interval->format('%a'));
            $diff_hour = (int)($interval->format('%h'));
            $diff_min  = (int)($interval->format('%i'));
            $diff_abs  = $interval->format('%r');

            // 日数のメッセージ設定
            if($diff_day > 0 && $diff_abs != '-'){
                $message = ($diff_day+1)."日後";
                $gou     = 'next';
            }
            elseif($diff_day == 0 && $diff_abs != '-'){
                $message = "明日発売";
                $gou     = 'next';
            }
            elseif($diff_day == 0 && $diff_abs == '-'){
                $message = "本日発売";
                $gou     = 'current';
            }
            elseif($diff_day > 0 && $diff_abs == '-'){
                $message = $diff_day."日前";
                $gou     = 'current';
            }

            // ステータスの設定
            if($result['status'] == 0){
                $status = 0;
            }

            $magazine = $result['name'];
            $array[$magazine]['status']             = $status;
            $array[$magazine][$gou]['title']        = $result['title'];
            $array[$magazine][$gou]['url']          = $result['url'];
            $array[$magazine][$gou]['release_date'] = "{$date}({$this->week_jp[$week]})発売";
            $array[$magazine][$gou]['what_day']     = "({$message})";
        }

        return $array;
        //var_dump($array);
    }

}