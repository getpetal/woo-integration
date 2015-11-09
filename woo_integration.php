<?php
/*
Plugin Name: Woo Integration
Description: Basic integration
Author: Emiliano Jankowski
Version: 0.1
*/

###########################################################################
# PLEASE DO NOT USE THIS CODE INTO ANY PRODUCTION PROJECT (NOT EVEN DEV!) #
###########################################################################
class WooIntegration
{
  public static function generate_key(){
      global $wpdb;

      $was_generated = get_option('secret-api-key-generated', false);
      if ( $was_generated ) { return; }

      $status          = 2;
      $consumer_key    = 'ck_' . wc_rand_hash();
      $consumer_secret = 'cs_' . wc_rand_hash();
      $user = get_user_by('email', 'super_admin@gmail.com');
      $data = array(
        'user_id'         => $user->ID,
        'description'     => 'Secret API Key',
        'permissions'     => 'read_write',
        'consumer_key'    => wc_api_hash( $consumer_key ),
        'consumer_secret' => $consumer_secret,
        'truncated_key'   => substr( $consumer_key, -7 )
      );
      $wpdb->insert(
        $wpdb->prefix . 'woocommerce_api_keys',
        $data,
        array(
          '%d',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s'
        )
      );

      add_option('secret-api-key-generated', $wpdb->insert_id);
      // This is bad!!!
      add_option('secret-api-key-generated-consumer', $consumer_key);


      \WC_Install::create_pages();
    }

    public static function callback_handler(){
      global $wpdb;

      $keys = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_api_keys WHERE key_id = %d", get_option('secret-api-key-generated') ) );
      $keys->consumer_key = get_option('secret-api-key-generated-consumer');
      return $keys;
    }

}

add_action('init', ['WooIntegration','generate_key']);
add_action( 'woocommerce_api_callback', ['WooIntegration','callback_handler'] );


add_action( 'rest_api_init', function () {
    register_rest_route( 'petal', '/woo', array(
        'methods' => 'GET',
        'callback' => ['WooIntegration','callback_handler']
    ) );
} );
