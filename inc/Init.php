<?php
namespace PerkSync;

use PerkSync\Helpers\GithubUpdater;
use PerkSync\Helpers\WordPress;
use PerkSync\Integration\BatchProcessor;
use PerkSync\Integration\ActionScheduler;

class Init {
    public function __construct() {

        //self::init_updater();

        // class to do WordPress related stuff like registering CPTs/Taxonomies
        new WordPress();
        //new BatchProcessor();
        new ActionScheduler();
    }

    /**
     * updater
     *
     * @return void
     */
    public static function init_updater() {
        $updater = new GithubUpdater( PERKSYNC_FILE );
        $updater->set_username( 'badabingbreda' );
        $updater->set_repository( 'perksync' );
        $updater->set_settings( array(
                    'requires'			=> '7.4',
                    'tested'			=> '6.3',
                    'rating'			=> '100.0',
                    'num_ratings'		=> '10',
                    'downloaded'		=> '10',
                    'added'				=> '2023-10-03',
                ) );
        $updater->initialize();
    }    
}