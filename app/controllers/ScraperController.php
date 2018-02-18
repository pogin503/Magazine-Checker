<?php

class ScraperController extends ApplicationController
{
    /*==============================
    / マイグレーション実行
    ==============================*/
    public function execute(){

        $scraper = new Scraper();
        $result = $scraper->get_url_and_regex();
        var_dump($result);

    }
}
?>