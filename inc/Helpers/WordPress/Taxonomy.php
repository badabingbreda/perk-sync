<?php

namespace PerkSync\Helpers\WordPress;

class Taxonomy {

    public function __construct() {
        add_action( 'init' , __CLASS__ . '::register_perk_category' );
    }

    public static function register_perk_category() {

        register_taxonomy( 
            'perk_category', 
            array( 'perk', ), 
            array(
                'labels' => array(
                    'name' => 'Categories',
                    'singular_name' => 'Category',
                    'menu_name' => 'Categories',
                    'all_items' => 'All Categories',
                    'edit_item' => 'Edit Category',
                    'view_item' => 'View Category',
                    'update_item' => 'Update Category',
                    'add_new_item' => 'Add New Category',
                    'new_item_name' => 'New Category Name',
                    'search_items' => 'Search Categories',
                    'popular_items' => 'Popular Categories',
                    'separate_items_with_commas' => 'Separate categories with commas',
                'add_or_remove_items' => 'Add or remove categories',
                'choose_from_most_used' => 'Choose from the most used categories',
                'not_found' => 'No categories found',
                'no_terms' => 'No categories',
                'items_list_navigation' => 'Categories list navigation',
                'items_list' => 'Categories list',
                'back_to_items' => '← Go to categories',
                'item_link' => 'Category Link',
                'item_link_description' => 'A link to a category',
            ),
            'public' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            ) 
        );
    }
    
}