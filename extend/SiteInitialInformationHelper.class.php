<?php

class SiteInitialInformationHelper {

    public static function globalSeo() {
        //设置整站的seo['keywords,description']
        slot('page_keywords', SiteInfoPeer::getSiteSeoKeywords());
        slot('page_description', SiteInfoPeer::getSiteSeoDescription());
    }

}

?>
