<?php
namespace PerkSync\Helpers;
use PerkSync\Helpers\WordPress\CPT;
use PerkSync\Helpers\WordPress\Taxonomy;

class WordPress {
    public function __construct() {
        new CPT();
        new Taxonomy();
    }
}

