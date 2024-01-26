<?php
/**
 * Perk Sync
 *
 * @package     perk-sync
 * @author      Badabingbreda
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Perk Sync
 * Plugin URI:  https://www.badabing.nl
 * Description: Connect to the perk API to fetch corporate perks
 * Version:     1.0.0
 * Author:      Badabingbreda
 * Author URI:  https://www.badabing.nl
 * Text Domain: perk-sync
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

 
 use PerkSync\Autoloader;
 use PerkSync\Init;
 
 if ( defined( 'ABSPATH' ) && ! defined( 'PERKSYNC_VERION' ) ) {
  register_activation_hook( __FILE__, 'PERKSYNC_check_php_version' );
 
  /**
   * Display notice for old PHP version.
   */
  function PERKSYNC_check_php_version() {
      if ( version_compare( phpversion(), '7.4', '<' ) ) {
         die( esc_html__( 'Perk Sync requires PHP version 7.4+. Please contact your host to upgrade.', 'perk-sync' ) );
     }
  }
 
   define( 'PERKSYNC_VERSION'   , '1.0.0' );
   define( 'PERKSYNC_DIR'     , plugin_dir_path( __FILE__ ) );
   define( 'PERKSYNC_FILE'    , __FILE__ );
   define( 'PERKSYNC_URL'     , plugins_url( '/', __FILE__ ) );
 
   define( 'CHECK_PERKSYNC_PLUGIN_FILE', __FILE__ );
 
 }
 
 if ( ! class_exists( 'PerkSync\Init' ) ) {

  // load our 3rd party libs
  require_once( PERKSYNC_DIR . 'vendor/autoload.php' );

 
  /**
   * The file where the Autoloader class is defined.
   */
   require_once PERKSYNC_DIR . 'inc/Autoloader.php';
   spl_autoload_register( array( new Autoloader(), 'autoload' ) );
 
  $perk_sync = new Init();
 
 }
 