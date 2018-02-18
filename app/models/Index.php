<?php
//---------------------------------------
//        雑誌モデル
//---------------------------------------
class Index extends ApplicationRecord {

    public function __construct() {
        //親クラスのコンストラクタを呼び出す
        parent::__construct();
    }

    private $week_jp = ["日", "月", "火", "水", "木", "金", "土"];

    //---------------------------------------
    //        雑誌チェックの最終更新日を取得
    //---------------------------------------
    public function get_magazine_last_update(){
        $sql = "SELECT max(updated_at) as max_date
                FROM   magazines
                ";
        $result = $this->fetch($sql);

        $max_date  = strtotime($result['max_date']);
        $week      = date("w", $max_date);
        $time      = date("H:i:s", $max_date);
        $date      = date("Y年m月d日", $max_date);
        $update_date = $date."(".$this->week_jp[$week].") ".$time;
        return $update_date;
    }
    //---------------------------------------
    //        タグによる雑誌の絞込
    //---------------------------------------
    public function refine_by_tag($checked_lists)
    {
        //チェックボックスの値とタグIDのマッピング
        define('TAG_MAP',   ['week' => 4,
            'month'=> 5,
            'boy'  => 1,
            'girl' => 3]);

        //バインドする値の２次元配列生成
        $index = 1;
        foreach (TAG_MAP as $key => $val) {
            if ( array_key_exists($key, $checked_lists) ) {
                $bindval [] = ['param'=>$index++, 'val'=>$val, 'type'=>PDO::PARAM_INT];
            }
        }
        //プリペアードステイトメント用？の生成
        $questions = substr(str_repeat(',?', count($bindval)), 1);

        $sql = "SELECT  MAG.id
                FROM    magazines  MAG
                        INNER JOIN magazines_tags  MTAGS
                              ON   MAG.id = MTAGS.magazine_id
                WHERE   MTAGS.tag_id  IN (${questions})
                GROUP BY  MAG.id
                ";

        //戻り値は配列
        $results = $this->prepare_fetchAll($sql, $bindval, PDO::FETCH_COLUMN);

        return $results;
    }
    //---------------------------------------
    //        雑誌のタイトルと日付を配列に格納
    //---------------------------------------
    public function get_magazine_current_next($target_magazines){

        //バインドする値の２次元配列生成
        $index = 1;
        foreach ($target_magazines as $val) {
            $bindval [] = ['param'=>$index++, 'val'=>$val, 'type'=>PDO::PARAM_INT];
        }
        //プリペアードステイトメント用？の生成
        $questions = substr(str_repeat(',?', count($bindval)), 1);

        $array = [];
        $today = new DateTime('now');

        $today_YMD = date("Y-m-d", time());
        $tomorrow_YMD = date('Y-m-d', strtotime('+1 day'));

        //クエリをキャッシュするためにViewをやめる
        $sql = "SELECT  MN.name
                       ,MN.url
                       ,MN.status
                       ,UNI.title
                       ,UNI.release_date
                FROM   (
                       -- 次号
                       SELECT   TAR.magazine_id
                               ,TAR.title
                               ,TAR.release_date
                       FROM     titles_and_release_date TAR
                                INNER JOIN (SELECT   magazine_id
                                                    ,MIN(release_date) AS min_date
                                            FROM     titles_and_release_date
                                            WHERE    release_date >= str_to_date('${tomorrow_YMD}','%Y-%m-%d')
                                            GROUP BY magazine_id
                                          ) AS MIN_TABLE
                                      ON  TAR.magazine_id  = MIN_TABLE.magazine_id
                                      AND TAR.release_date = MIN_TABLE.min_date
                       UNION ALL
                       -- 今号
                       SELECT  TAR.magazine_id
                              ,TAR.title
                              ,TAR.release_date
                       FROM   titles_and_release_date TAR
                              INNER JOIN (SELECT   magazine_id
                                                  ,MAX(release_date) AS max_date
                                          FROM     titles_and_release_date
                                          WHERE    release_date <= str_to_date('${today_YMD}','%Y-%m-%d')
                                          GROUP BY magazine_id
                                         ) AS MAX_TABLE
                                    ON  TAR.magazine_id  = MAX_TABLE.magazine_id
                                    AND TAR.release_date = MAX_TABLE.max_date
                       ) AS UNI
                       INNER JOIN magazines MN
                             ON   UNI.magazine_id = MN.id
                WHERE  MN.id IN  (${questions})
                ORDER BY  MN.name
                        ,UNI.release_date DESC
               ";

        //戻り値は連想配列
        $results = $this->prepare_fetchAll($sql, $bindval, PDO::FETCH_ASSOC);

        foreach ($results as $magazine){

            //初期化
            $status = 1; //正常
            $message = "";
            $release_day = new DateTime($magazine['release_date']);

            $date      = date("n/d", strtotime($magazine['release_date']));
            $week      = date("w", strtotime($magazine['release_date']));

            $interval  = date_diff($today, $release_day);
            $diff_day  = (int)($interval->format('%a'));
            $diff_abs  = $interval->format('%r');

            // 日数のメッセージ設定
            if($diff_day > 0 && $diff_abs != '-'){
                $message = ($diff_day+1)."日後";
                $gou     = 'next';
            }
            elseif($diff_day == 0 && $diff_abs != '-'){
                $message = "明日";
                $gou     = 'next';
            }
            elseif($diff_day == 0 && $diff_abs == '-'){
                $message = "今日";
                $gou     = 'current';
            }
            elseif($diff_day > 0 && $diff_abs == '-'){
                $message = $diff_day."日前";
                $gou     = 'current';
            }

            // ステータスの設定
            if($magazine['status'] == 0){
                $status = 0;
            }

            $magazine_name = $magazine['name'];
            $array[$magazine_name]['status']             = $status;
            $array[$magazine_name][$gou]['title']        = $magazine['title'];
            $array[$magazine_name][$gou]['url']          = $magazine['url'];
            $array[$magazine_name][$gou]['release_date'] = "{$date}({$this->week_jp[$week]})";
            $array[$magazine_name][$gou]['what_day']     = "({$message})";
        }

        return $array;

    }

}
?>