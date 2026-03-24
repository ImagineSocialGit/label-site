<?php

namespace App\Classes;

use App\Models\Favicon;
use App\Models\Label;
use App\Models\LegalPage;
use App\Models\MetaData;
use App\Models\SiteSettings;



class UniversalData
{

    public $favicon;
    public $metaData;
    public $legalPages;

    public $labels;

    public $showLivePosts = false;

    public function __construct() {
        
        $this->favicon = Favicon::first();
        $this->metaData = MetaData::first();
        $this->legalPages = LegalPage::all();
        
        $this->labels = Label::all();

        $siteSettings = SiteSettings::first();
        $showLivePosts = $siteSettings->display_live_posts_on_staging;

    }

}