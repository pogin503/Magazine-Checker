<?php
use GuzzleHttp\Client;

class ScraperController extends ApplicationController
{
    public $save_path;   //保存先パス
    private static $parse_data; //パースしたデータ

    public function __construct() {
        $this->save_path = realpath(dirname(__FILE__).'/../../tmp/magazines_cache/');
    }

    /*==============================
    / htmlをGETして保存
    ==============================*/
    public function get_html(){

        $scraper = new Scraper();
        $scrapes = $scraper->get_scrapers();

        foreach ($scrapes as $scrape){

            //GET
            $client = new Client();
            $response = $client->request('GET', $scrape['url']);
            $status   = $response->getStatusCode();

            //URLが存在する場合保存
            if($status === 200){
                file_put_contents($this->save_path.'/'.$scrape['name'].'.html',  $response->getBody());
                chmod($this->save_path.'/'.$scrape['name'].'.html', 0666);
            //URLが存在しない場合
            }else {
                echo '存在しないURL:'.$scrape['name']." ".$scrape['url'].PHP_EOL;
            }

        }

    }

    /*==============================
    / htmlをparseする
    ==============================*/
    public function parse_html(){

        $scraper = new Scraper();
        $scrapes = $scraper->get_scrapers();

        foreach ($scrapes as $scrape){

            //FILEからHTML文取得
            $html = @file_get_contents($this->save_path.'/'.$scrape['name'].'.html');

            //存在する場合
            if($html !== false){

                $doc         = phpQuery::newDocumentHTML($html);
                $selector    = $scrape['target'];
                //取得した文字列の英数字を半角に変換
                $target_text = mb_convert_kana($doc[$selector]->text(),'a');
                var_dump($target_text);
                //タイトルをパース
                $pattern     = '/'.$scrape['title_reg'].'/u';
                preg_match($pattern, $target_text, $array_result);
                $title = $array_result[1];

                //発売日をパース
                $pattern     = '/'.$scrape['release_reg'].'/u';
                preg_match($pattern, $target_text, $array_result);
                $release_date = $this->decide_release_date($array_result);

                self::$parse_data [] = ['title'=>$title,
                                        'release_date'=>$release_date,
                                        'magazine_id'=>$scrape['magazine_id'],
                                       ];

            //URLが存在しない場合
            }else {
                echo '存在しないファイル:'.$this->save_path.'/'.$scrape['name'].'.html'.PHP_EOL;
            }
        }
    }

    /*
     * 発売日の西暦を決定して発売日を返す
    */
    private function decide_release_date($array)
    {

        $get_month  = intval($array[1]);
        $get_day    = intval($array[2]);
        $this_year  = intval(date('Y'));
        $this_month = intval(date('m'));

        #発売月と今月の差の絶対値が10より大きい数値の場合来年とみなす
        #例 発売月:1月 今月:12月 |1-12|=11>10 ※発売日が年をまたぐケース
        #例 発売月:2月 今月:3月  |2-3| =1<10  ※次号の発売日が来月になっても更新されていないケース
        if (abs($this_month > $get_month) > 10) {
            $release_year = $this_year + 1;
        }else{
            $release_year = $this_year;
        }

        $release_date = new DateTime();
        $release_date->setDate($release_year , $get_month, $get_day);

        return $release_date;
    }

    /*
     * パースしたデータの判定
     */
    public function judge_parse_data(){

        $scraper = new Scraper();

        foreach (self::$parse_data as $a){

            echo "title:".$a['title']." id:".$a['magazine_id']." date:".$a['release_date']->format('Y-m-d').PHP_EOL;

            //すでに存在する場合処理中止
            if($scraper->issue_exist($a['title'], $a['magazine_id'], $a['release_date'])){
                echo '存在する'.PHP_EOL;
                continue;
            }

            //発売日が変わった場合、更新
            if($scraper->issue_changed($a['title'], $a['magazine_id'], $a['release_date'])) {
                //旧発売日を論理削除
                $result = $scraper->update_issue($a['title'], $a['magazine_id'], $a['release_date']);
                if ($result === true) {
                    echo 'UPDATE完了' . PHP_EOL;
                }
            }

            //新規追加
            $result = $scraper->insert_issue($a['title'], $a['magazine_id'], $a['release_date']);
            if($result === true){echo 'INSERT完了'.PHP_EOL;}
        }
    }

}
?>