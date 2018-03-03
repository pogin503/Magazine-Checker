<?php

require_once(dirname(__FILE__). '/../vendor/autoload.php');

use Controllers\ScraperController;

$scraper = new ScraperController();
//実行日時をファイルに出力
$scraper->save_execute_time();
//HTMLをGET
$scraper->get_html();
//GETしたHTMLをパース
$scraper->parse_html();
//パースしたデータが新規か判定し登録
$scraper->judge_parse_data();

?>