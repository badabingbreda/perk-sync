<?php
namespace PerkSync\Integration;

use PerkSync\Integration\BatchProcessor\Perks;

class BatchProcessor {

    public function __construct() {
        // Manually define the path constants to eliminate
        // possible errors when resolving the paths and also
        // include trailing slash at the end.

        if ( ! defined('WP_BP_PATH')) {
            define('WP_BP_PATH', PERKSYNC_DIR . 'vendor/gdarko/wp-batch-processing/');
        }

        if ( ! defined('WP_BP_URL')) {
            define('WP_BP_URL', PERKSYNC_DIR . 'vendor/gdarko/wp-batch-processing/');
        }

        \WP_Batch_Processor::boot(); 
        
        add_action( 'wp_batch_processing_init', __CLASS__ . '::wp_batch_processing_init', 15, 1 );

    }

    
    /**
     * wp_batch_processing_init
     * 
     * Initialize the batches
     *
     * @return void
     */
    public static function wp_batch_processing_init() {
        $batch = new Perks();
        \WP_Batch_Processor::get_instance()->register( $batch );
    }
}