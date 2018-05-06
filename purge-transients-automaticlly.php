<?php
/**
 * Plugin Name: Purge transients automaticlly
 * Plugin URI: https://dengruo.com
 * Description: I hate the transients, which slow down the site speed.
 * Author: Ruo <mail@dengruo.com>
 * Version: 0.0.1
 * Text Domain: purge_transients_automaticlly
 */


define('PURGE_TRANSIENTS_AUTOMATICLLY_DIR', dirname(__FILE__));
define('PURGE_TRANSIENTS_AUTOMATICLLY_VERSION','0.0.1');

/* autoload init */
function purge_transients_automaticlly_autoload($class) {
    require_once(PURGE_TRANSIENTS_AUTOMATICLLY_DIR.'/inc/purge_transients_automaticlly.class.php');
    require_once(PURGE_TRANSIENTS_AUTOMATICLLY_DIR.'/inc/purge_transients_automaticlly_purge.class.php');

}
spl_autoload_register('purge_transients_automaticlly_autoload');
/* loader */
add_action(
    'plugins_loaded',
    [
        'purge_transients_automaticlly',
        'instance',
    ]
);




/**
 * Cron jobs
 */
// create a scheduled event (if it does not exist already)
function purge_transients_automaticlly_activation() {
    $options=purge_transients_automaticlly::get_options();
    if($options['enabled']&&!wp_next_scheduled( 'purge_transients_automaticlly_cronjob' )){
        wp_schedule_event( time(), $options['scheduled'], 'purge_transients_automaticlly_cronjob' );
    }
}
// and make sure it's called whenever WordPress loads
//add_action('wp', 'purge_transients_automaticlly_activation');
register_activation_hook(__FILE__, 'purge_transients_automaticlly_activation');

//Update cron job scheduled once the option changed


add_action('update_option', function( $option_name, $old_value, $value ) {
    if($option_name=='purge_transients_automaticlly'&&$old_value!=$value){
        if($old_value['enabled']==1&&$value['enabled']==0){
            //enable it
            wp_schedule_event( time(), $value['scheduled'], 'purge_transients_automaticlly_cronjob' );
        }else if($old_value['enabled']==1&&$value['enabled']==0){
            //disable it
            purge_transients_automaticlly_deactivate();
        }else if($old_value['enabled']==1&&$value['enabled']==1 ){
            //update it
            purge_transients_automaticlly_deactivate();
            wp_schedule_event( time(), $value['scheduled'], 'purge_transients_automaticlly_cronjob' );
        }
    }
}, 10, 3);

// unschedule event upon plugin deactivation
function purge_transients_automaticlly_deactivate() {
    // find out when the last event was scheduled
    $timestamp = wp_next_scheduled ('purge_transients_automaticlly_cronjob');
    // unschedule previous event if any
    wp_unschedule_event ($timestamp, 'purge_transients_automaticlly_cronjob');
}
register_deactivation_hook (__FILE__, 'purge_transients_automaticlly_deactivate');

// here's the function we'd like to call with our cron job
function purge_transients_automaticlly_cronjob_function() {
    purge_transients_automaticlly_purge::purge_all();
}
// hook that function onto our scheduled event:
add_action ('purge_transients_automaticlly_cronjob', 'purge_transients_automaticlly_cronjob_function');
