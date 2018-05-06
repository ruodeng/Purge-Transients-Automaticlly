<?php
class purge_transients_automaticlly_purge{
    public static function purge_all(){
        global $wpdb;
        $wpdb->query( "
			delete from ".$wpdb->prefix."options
			where option_name like '\_transient\_%'
			or    option_name like '\_site\_transient\_%' 
		");
    }
}