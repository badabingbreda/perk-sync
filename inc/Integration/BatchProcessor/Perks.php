<?php
namespace PerkSync\Integration\BatchProcessor;
/**
 * Class MY_Example_Batch
 */
class Perks extends \WP_Batch {

    /**
     * Unique identifier of each batch
     * @var string
     */
    public $id = 'perksync_perks_import';

    /**
     * Describe the batch
     * @var string
     */
    public $title = 'Perks Sync';

    /**
     * To setup the batch data use the push() method to add WP_Batch_Item instances to the queue.
     *
     * Note: If the operation of obtaining data is expensive, cache it to avoid slowdowns.
     *
     * @return void
     */
    public function setup() {

        $wp_upload = wp_upload_dir( );
        $file = file_get_contents( 'https://wellzyperks.com/wp-json/wellzyperks/v1/perks?key=kxwS3UaneUAobV#&type=all' );
        $data = json_decode($file , true);

        // $users = get_users( array(
        //     'number' => '40',
        //     'role'   => 'administrator',
        // ) );

        // foreach ( $users as $user ) {
        //     $this->push( new \WP_Batch_Item( $user->ID, array( 'author_id' => $user->ID ) ) );
        // }

        foreach ($data['perks'] as $perk ) {
            $this->push( new \WP_Batch_Item( 
                $perk[ 'ID' ],
                array_merge( $perk , [  ] )
            ));
        }
    }

    /**
     * Handles processing of batch item. One at a time.
     *
     * In order to work it correctly you must return values as follows:
     *
     * - TRUE - If the item was processed successfully.
     * - WP_Error instance - If there was an error. Add message to display it in the admin area.
     *
     * @param WP_Batch_Item $item
     *
     * @return bool|\WP_Error
     */
    public function process( $item ) {



        // Return true if the item processing is successful.
        return true;
    }


    /**
     * Handles processing of batch item. One at a time.
     *
     * In order to work it correctly you must return values as follows:
     *
     * - TRUE - If the item was processed successfully.
     * - WP_Error instance - If there was an error. Add message to display it in the admin area.
     *
     * @param WP_Batch_Item $item
     *
     * @return bool|\WP_Error
     */
    public function __process( $item ) {

        // Retrieve the custom data
        $author_id = $item->get_value( 'author_id' );

        // Return WP_Error if the item processing failed (In our case we simply skip author with user id 5)
        if ( $author_id == 5 ) {
            return new \WP_Error( 302, "Author skipped" );
        }

        // Do the expensive processing here.
        // ...

        // Return true if the item processing is successful.
        return true;
    }
    
    /**
     * Called when specific process is finished (all items were processed).
     * This method can be overriden in the process class.
     * @return void
     */
    public function finish() {
        // Do something after process is finished.
        // You have $this->items, etc.
    }
}       
