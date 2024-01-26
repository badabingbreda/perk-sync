<?php
namespace PerkSync\Helpers\Wordpress;

class CPT {

    public function __construct() {

        add_action( 'init' , __CLASS__ . '::register_perk'  );
        add_action( 'init' , __CLASS__ . '::register_brand'  );

    }

    public static function register_perk() {

        register_post_type( 
            'perk', 
            array(
                'labels' => array(
                    'name' => 'Perks',
                    'singular_name' => 'Perk',
                    'menu_name' => 'Perks',
                    'all_items' => 'All Perks',
                    'edit_item' => 'Edit Perk',
                    'view_item' => 'View Perk',
                    'view_items' => 'View Perks',
                    'add_new_item' => 'Add New Perk',
                    'new_item' => 'New Perk',
                    'parent_item_colon' => 'Parent Perk:',
                    'search_items' => 'Search Perks',
                    'not_found' => 'No perks found',
                    'not_found_in_trash' => 'No perks found in Trash',
                    'archives' => 'Perk Archives',
                    'attributes' => 'Perk Attributes',
                    'insert_into_item' => 'Insert into perk',
                    'uploaded_to_this_item' => 'Uploaded to this perk',
                    'filter_items_list' => 'Filter perks list',
                    'filter_by_date' => 'Filter perks by date',
                    'items_list_navigation' => 'Perks list navigation',
                    'items_list' => 'Perks list',
                    'item_published' => 'Perk published.',
                    'item_published_privately' => 'Perk published privately.',
                    'item_reverted_to_draft' => 'Perk reverted to draft.',
                    'item_scheduled' => 'Perk scheduled.',
                    'item_updated' => 'Perk updated.',
                    'item_link' => 'Perk Link',
                    'item_link_description' => 'A link to a perk.',
                ),
                'public' => true,
                'show_in_rest' => true,
                'supports' => array(
                    0 => 'title',
                    1 => 'editor',
                    2 => 'thumbnail',
                ),
                'has_archive' => 'perks',
                'rewrite' => array(
                    'feeds' => false,
                ),
                'delete_with_user' => false,
            )
        );        
    
    }

    public static function register_brand() {

        register_post_type( 
            'brand', 
            array(
                'labels' => array(
                    'name' => 'Brands',
                    'singular_name' => 'Brand',
                    'menu_name' => 'Brands',
                    'all_items' => 'All Brands',
                    'edit_item' => 'Edit Brand',
                    'view_item' => 'View Brand',
                    'view_items' => 'View Brands',
                    'add_new_item' => 'Add New Brand',
                    'new_item' => 'New Brand',
                    'parent_item_colon' => 'Parent Brand:',
                    'search_items' => 'Search Brands',
                    'not_found' => 'No brands found',
                    'not_found_in_trash' => 'No brands found in Trash',
                    'archives' => 'Brand Archives',
                    'attributes' => 'Brand Attributes',
                    'insert_into_item' => 'Insert into brand',
                    'uploaded_to_this_item' => 'Uploaded to this brand',
                    'filter_items_list' => 'Filter brands list',
                    'filter_by_date' => 'Filter brands by date',
                    'items_list_navigation' => 'Brands list navigation',
                    'items_list' => 'Brands list',
                    'item_published' => 'Brand published.',
                    'item_published_privately' => 'Brand published privately.',
                    'item_reverted_to_draft' => 'Brand reverted to draft.',
                    'item_scheduled' => 'Brand scheduled.',
                    'item_updated' => 'Brand updated.',
                    'item_link' => 'Brand Link',
                    'item_link_description' => 'A link to a brand.',
                ),
                'public' => true,
                'show_in_rest' => true,
                'supports' => array(
                    0 => 'title',
                    1 => 'editor',
                    2 => 'thumbnail',
                ),
                'delete_with_user' => false,
                ) 
        );

    }

}


