<?php
namespace PerkSync\Integration;

class ActionScheduler {

    public function __construct() {

        add_action( 'init', __CLASS__ . '::eg_schedule_midnight_log' );

        // custom action hook name
        add_action( 'perksync_midnight_log', __CLASS__ . '::do_remote_import' );
        
        // custom hook that gets called and schedules perk update/imports
        add_action( 'perksync_single_brand_process', __CLASS__ . '::handle_single_brand_process', 10, 1 ); 
        // custom hook that gets called and schedules perk update/imports
        add_action( 'perksync_single_perk_process', __CLASS__ . '::handle_single_perk_process', 10, 1 ); 

        
    }

    /**
     * Schedule an action with the hook 'eg_midnight_log' to run at midnight each day
     * so that our callback is run then.
     */
    public static function eg_schedule_midnight_log() {

        if (!function_exists( 'as_has_scheduled_action' )) return;

        // add our scheduled action if it doesn't exist
        if ( false === \as_has_scheduled_action( 'perksync_midnight_log' ) ) {
            \as_schedule_recurring_action( 
                strtotime( 'tomorrow' ), 
                DAY_IN_SECONDS, 
                'perksync_midnight_log',  // the action hook name to run
                array(), 
                '', 
                true 
            );
        }
    }

    /**
     * A callback to run when the 'eg_midnight_log' scheduled action is run.
     */
    public static function do_remote_import() {

        if (!function_exists( 'as_has_scheduled_action' )) return;

        self::remote_brand_import();
        self::remote_perks_import();
        
        error_log( 'It is just after midnight on ' . date( 'Y-m-d' ) );
    }

    
    /**
     * remote_brand_import
     *
     * @return void
     */
    private static function remote_brand_import() {
        
        $wp_upload = wp_upload_dir( );
        $file = file_get_contents( 'https://wordpress-860301-2984324.cloudwaysapps.com/wp-json/wellzyperks/v1/brands?key=kxwS3UaneUAobV#&type=all' );
        $data = json_decode($file , true);
        
        // import first 10 for now, during testing
        $brands = array_slice($data[ 'brands' ],0,10);
        
        // run local brand cleanup first so that a new item's same slug can be taken
        self::local_brand_cleanup( $brands );
        
        foreach ( $brands as $brand ) {
            
            \as_schedule_single_action( 
                time(),
                'perksync_single_brand_process',
                [ 'brand' => $brand ]
            );
        }
        
    }
    
    /**
     * local_brand_cleanup
     * 
     * using the import brands, figure out what brands we have in our database that are orphaned.
     * If they are (not featured in the import) they can be deleted, including the attachment(s)
     *
     * @return void
     */
    private static function local_brand_cleanup( $brands ) {
        // get a list of post_ids with original_ids that do not match our list
        // of remote ids. These are obselete and can be removed
        
        $search_for_original_ids = [];

        // create a list of possibly imported brand original ids
        foreach( $brands as $brand ) {
            $search_for_original_ids[] = $brand['ID'];
        }

        $args = [
            'post_type' => 'brand',
            'numberposts' => -1,
            'meta_query' => [
                'relation' => 'AND',
                'orginal_ids_clause' => [
                    'key' => 'original_id',
                    'value' => $search_for_original_ids,
                    'type' => 'NUMERIC',
                    'compare' => 'NOT IN',
                ]
            ],
            'fields' => 'ids',
        ];

        $posts = \get_posts( $args );

        if ( !is_wp_error( $posts ) and sizeof( $posts ) > 0 ) {
            // delete the posts/brands
            foreach ($posts as $brand_id) {
                // get the attachment id
                $featured_image_id = \get_post_thumbnail_id( $brand_id );
                // delete, no takebacks
                wp_delete_attachment( $featured_image_id, true );
                // delete, no takebacks
                wp_delete_post( $brand_id , true);
            }
        }
    }

    /**
     * local_perk_cleanup
     * 
     * using the import brands, figure out what brands we have in our database that are orphaned.
     * If they are (not featured in the import) they can be deleted, including the attachment(s)
     *
     * @return void
     */
    private static function local_perk_cleanup( $perks ) {
        // get a list of post_ids with original_ids that do not match our list
        // of remote ids. These are obselete and can be removed
        
        $search_for_original_ids = [];

        // create a list of possibly imported brand original ids
        foreach( $perks as $perk ) {
            $search_for_original_ids[] = $perk['ID'];
        }

        $args = [
            'post_type' => 'perk',
            'numberposts' => -1,
            'meta_query' => [
                'relation' => 'AND',
                'orginal_ids_clause' => [
                    'key' => 'original_id',
                    'value' => $search_for_original_ids,
                    'type' => 'NUMERIC',
                    'compare' => 'NOT IN',
                ]
            ],
            'fields' => 'ids',
        ];

        $posts = \get_posts( $args );

        if ( !is_wp_error( $posts ) and sizeof( $posts ) > 0 ) {
            // delete the posts/brands
            foreach ($posts as $perk_id) {
                // get the attachment id
                $featured_image_id = \get_post_thumbnail_id( $brand_id );
                // delete, no takebacks
                wp_delete_attachment( $featured_image_id, true );

                // get the images from the images field as well
                $images = get_field( 'images' , $perk_id );
                foreach( $images as $image_id ) {
                    // delete the image
                    wp_delete_attachment( $image_id, true );
                }

                // delete, no takebacks
                wp_delete_post( $brand_id , true );
            }
        }
    }

    
    /**
     * remote_brand_import
     *
     * @return void
     */
    private static function remote_perks_import() {
        
        $wp_upload = wp_upload_dir( );
        $file = file_get_contents( 'https://wordpress-860301-2984324.cloudwaysapps.com/wp-json/wellzyperks/v1/perks?key=kxwS3UaneUAobV#&type=all' );
        $data = json_decode($file , true);

        // import first 10 for now, during testing
        $perks = array_slice($data[ 'perks' ],0,10);

        // run local brand cleanup first so that a new item's same slug can be taken
        self::local_perk_cleanup( $perks );


        foreach ( $perks as $perk ) {

            \as_schedule_single_action( 
                time(),
                'perksync_single_perk_process',
                [ 'perk' => $perk ]
            );
        }
    }
    
    /**
     * handle_single_brand_process
     *
     * @param  mixed $brand
     * @return void
     */
    public static function handle_single_brand_process( $brand ) {

        // needed for sideloading images
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');        


        // try to match the perk to a perk in the database
        $args = [
            'post_type' => 'brand',
            'meta_query' => [
                'relation' => 'AND',
                'original_id_clause' => [
                    'key' => 'original_id',
                    'value' => $brand[ 'ID' ],
                    'compare' => '='
                ]
            ],
            'fields' => 'ids',
        ];

        $original_posts = \get_posts( $args );

        $brand_post_data = array(
            'post_type' => 'brand',
            'post_title' => $brand[ 'title' ],
            'post_content' => $brand[ 'description' ],
            'post_status' => 'publish',


        );

        // figure out if we can update only by providing the local post_id
        if ( ! is_wp_error( $original_posts ) && sizeof( $original_posts ) == 1 ) {

            // return early if this post needs no update/insert
            if ( \get_field( 'modified_date' , $original_posts[0] ) <= $brand[ 'modified' ] ) return;

            $brand_post_data = array_merge( 
                $brand_post_data, 
                [ 
                    'ID' => $original_posts[0],
                ] 
            );

            // if we've come to here, we just need to import the perk
            $local_post_id = \wp_update_post( 
                $brand_post_data,
                false
            );

        } else {


            // if we've come to here, we just need to import the perk
            $local_post_id = \wp_insert_post( 
                $brand_post_data,
                false
            );
            
            // add/update the original ID
            update_field( 'original_id' , $brand[ 'ID' ] , $local_post_id );

        }

        // add/update the original ID
        update_field( 'modified_date' , $brand[ 'modified' ] , $local_post_id );

        // if there has been an error or 0 is returned, bail
        if ( !$local_post_id ) return;

        // update acf fields
        self::handle_brand_logo_image( $local_post_id , $brand );

    }
    
    /**
     * find_original_related_brand_id
     * 
     * search the brands and return the local related brand id that has our original id
     *
     * @param  mixed $original_id
     * @return void
     */
    private static function find_original_related_brand_id( $original_id ) {

        // try to match the perk to a perk in the database
        $args = [
            'post_type' => 'brand',
            'meta_query' => [
                'relation' => 'AND',
                'original_id_clause' => [
                    'key' => 'original_id',
                    'value' => $original_id,
                    'compare' => '='
                ]
            ],
            'fields' => 'ids',
        ];

        $original_posts = \get_posts( $args );

        // figure out if we can update only by providing the local post_id
        if ( ! is_wp_error( $original_posts ) && sizeof( $original_posts ) == 1 ) {
            return $original_posts[0];
        } else {
            return false;
        }
    }

    
    /**
     * handle_single_perk_process
     *
     * @param  mixed $perk
     * @return void
     */
    public static function handle_single_perk_process( $perk ) {

        // needed for sideloading images
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');        


        // try to match the perk to a perk in the database
        $args = [
            'post_type' => 'perk',
            'meta_query' => [
                'relation' => 'AND',
                'original_id_clause' => [
                    'key' => 'original_id',
                    'value' => $perk[ 'ID' ],
                    'compare' => '='
                ]
            ],
            'fields' => 'ids',
        ];

        $original_posts = \get_posts( $args );

        $perk_post_data = array(
            'post_title' => $perk[ 'title' ],
            'post_content' => $perk[ 'description' ],
            'post_type' => 'perk',
            'post_status' => 'publish',


        );

        // figure out if we can update only by providing the local post_id
        if ( ! is_wp_error( $original_posts ) && sizeof( $original_posts ) == 1 ) {

            // return early if this post needs no update/insert
            if ( \get_field( 'modified_date' , $original_posts[0] ) <= $perk[ 'modified' ] ) return;

            $perk_post_data = array_merge( 
                $perk_post_data, 
                [ 
                    'ID' => $original_posts[0],
                ] 
            );

            // if we've come to here, we just need to import the perk
            $local_post_id = \wp_update_post( 
                $perk_post_data,
                false
            );

        } else {


            // if we've come to here, we just need to import the perk
            $local_post_id = \wp_insert_post( 
                $perk_post_data,
                false
            );
            
            // add/update the original ID
            \update_field( 'original_id' , $perk[ 'ID' ] , $local_post_id );

        }

        // add/update the original ID
        \update_field( 'modified_date' , $perk[ 'modified' ] , $local_post_id );

        // if there has been an error or 0 is returned, bail
        if ( !$local_post_id ) return;

        // add the category
        \wp_set_object_terms( $local_post_id, self::get_perk_categories( $perk ), 'perk_category', false );

        // update acf fields
        self::handle_acf_fields( $local_post_id , $perk );
        self::handle_main_image( $local_post_id , $perk );
        self::handle_gallery_images( $local_post_id , $perk );

    }

    private static function get_perk_categories( $perk ) {
        if ( isset( $perk[ 'category'] ) && is_array($perk[ 'category' ]) ) {
            // Use the category name to create new terms
            $terms = array_map( function($category) { return $category[ 'name' ]; } , $perk[ 'category' ] );
        } else {
            $terms = [];
        }

        return $terms;
    }
    
    /**
     * handle_acf_fields
     * 
     * using the import acf fields, update the acf values for the local post
     *
     * @param  mixed $local_post_id
     * @param  mixed $perk
     * @return void
     */
    public static function handle_acf_fields( $local_post_id , $perk ) {

        if ( !function_exists( 'update_field' )) return;

        if ( isset( $perk[ 'brand_ID' ]) ) {
            \update_field( 'related_brand' , self::find_original_related_brand_id( $perk[ 'brand_ID' ] ) , $local_post_id );
        }

        if ( isset( $perk[ 'perk_type' ] )) {
            \update_field( 'perk_type' , $perk[ 'perk_type' ] , $local_post_id );
        }

        if ( isset( $perk[ 'mucodes' ])) {
            \update_field( 'mucodes' , $perk[ 'mucodes' ] , $local_post_id );
        }

        if ( isset( $perk[ 'affiliate_links' ])) {
            \update_field( 'affiliate_links' , $perk[ 'affiliate_links' ] , $local_post_id );
        }

        if ( isset( $perk[ 'benefits' ])) {
            \update_field( 'benefits_list' , $perk[ 'benefits' ] , $local_post_id );
        }

        if ( isset( $perk[ 'fine_print' ])) {
            \update_field( 'fine_print' , $perk[ 'fine_print' ] , $local_post_id );
        }

    }

    /**
     * handle_brand_logo_image
     *
     * @param  mixed $local_post_id
     * @param  mixed $brand
     * @return void
     */
    public static function handle_brand_logo_image( $local_post_id , $brand ) {

        if ( isset( $brand[ 'logo' ] ) && $brand[ 'logo' ] !== '' ) {
            // sideload main image and return attachment ID
            $attachment_id = \media_sideload_image( $brand[ 'logo' ] , $local_post_id , 'Logo Image for ' . esc_html( $brand[ 'title' ] ) , 'id' );
            // if there is an attachment ID, proceed to set this image as post thumbnail
            if ( $attachment_id ) {
                \set_post_thumbnail( $local_post_id , $attachment_id );
            }
        }
    }
    

    /**
     * handle_main_image
     *
     * @param  mixed $local_post_id
     * @param  mixed $perk
     * @return void
     */
    public static function handle_main_image( $local_post_id , $perk ) {

        if ( isset( $perk[ 'main_image' ] ) && $perk[ 'main_image' ] !== '' ) {
            // sideload main image and return attachment ID
            $attachment_id = \media_sideload_image( $perk[ 'main_image' ] , $local_post_id , 'Image for ' . esc_html( $perk[ 'title' ] ) , 'id' );
            // if there is an attachment ID, proceed to set this image as post thumbnail
            if ( $attachment_id ) {
                \set_post_thumbnail( $local_post_id , $attachment_id );
            }
        }
    }
    
    /**
     * handle_gallery_images
     *
     * @param  mixed $local_post_id
     * @param  mixed $perk
     * @return void
     */
    public static function handle_gallery_images( $local_post_id , $perk ) {

        $array_ids = [];

        if ( isset( $perk[ 'images' ] ) && $perk[ 'images' ] !== '' ) {
            // try to explode the images string
            $images = explode( ',' , $perk[ 'images' ] );

            if ( $images ) {
                foreach ($images as $image) {
                    // sideload main image and return attachment ID
                    $attachment_id = \media_sideload_image( $image , $local_post_id , 'Image for ' . esc_html( $perk[ 'title' ] ) , 'id' );

                    // Add the id to the array so we can set this as the gallery images
                    if ( $attachment_id ) $array_ids[] = $attachment_id;
                }
            }

            // update the gallery images
            \update_field( 'images' , $array_ids , $local_post_id );
        }
    }
}