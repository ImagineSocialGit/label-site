<?php

namespace App\Classes;

use App\Models\Label;
use App\Models\Favicon;
use App\Models\MetaData;
use App\Models\LegalPage;



class UniversalData
{

    public $favicon;
    public $metaData;
    public $legalPages;

    public $labels;

    public function __construct() {
        
        $this->favicon = Favicon::first();
        $this->metaData = MetaData::first();
        $this->legalPages = LegalPage::all();
        
        $this->labels = Label::all();

    }

}