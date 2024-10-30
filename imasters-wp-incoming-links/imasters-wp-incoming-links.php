<?php
/*
Plugin Name: iMasters WP Incoming Links
Plugin URI: http://code.imasters.com.br/wordpress/plugins/imasters-wp-incoming-links/
Description: Who links to you? What says about you? iMasters WP Incoming links track, in real time, all sources that is generating traffic to your WordPress based site.
Author: Apiki
Version: 0.1
Author URI: http://apiki.com/
*/

/*  Copyright 2009  Apiki (email : leandro@apiki.com)

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Create Class IMASTERS_WP_Incoming_Links
 *
 * Search in web all links that appear for your site
 */

class IMASTERS_WP_Incoming_Links {

    var $itens_per_page = 15;
    
    /**
     * Call this function always plugin initialized
     *
     * @global object $wpdb WordPress database object
     * @return void
     */
    function IMASTERS_WP_Incoming_Links()
    {
        global $wpdb;

        $wpdb->imasters_wp_incoming_links = $wpdb->prefix . 'imasters_wp_incoming_links';
        
        //when it be activated to call the function install
        add_action( 'activate_imasters-wp-incoming-links/imasters-wp-incoming-links.php', array( &$this, 'install' ) );
        //call the function that create menu in admin
        add_action( 'admin_menu', array( &$this, 'menu' ) );
        //call the function that insert in database the incoming links
        add_action( 'init', array( &$this, 'incoming_links' ) );
        //call the function that translate the language
        add_action( 'init', array( &$this, 'textdomain' ) );
        //call the function to insert the JavaScript for admin
        add_action( 'wp_print_scripts', array( &$this, 'scripts' ) );
        //insert the javascript in the end
        add_action( 'admin_footer', array( &$this, 'new_page' ) );

    }

    /**
     * Install the plugin and build table of database
     *
     * @global object $wpdb WordPress Database object
     */
    function install()
    {
        global $wpdb;

       $role = get_role( 'administrator' );
       if( !$role->has_cap( 'admin_incoming_links' ) ) :
            $role->add_cap( 'admin_incoming_links' );
        endif;
        
        require_once ABSPATH . 'wp-admin/upgrade-functions.php';

        if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->imasters_wp_incoming_links'" ) != $wpdb->imasters_wp_incoming_links ) :
            $sql_table_incoming_links = "CREATE TABLE " . $wpdb->imasters_wp_incoming_links . " (
                incoming_links_id INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                incoming_links_origin_page VARCHAR( 255 ) NOT NULL,
                incoming_links_target_page VARCHAR( 255 ) NOT NULL,
                incoming_links_ip INT( 4 ) UNSIGNED,
                incoming_links_registered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                )  ENGINE = MYISAM COMMENT = 'Table used by iMasters WP Incoming Links';
                );
            ";
        endif;

        dbDelta( $sql_table_incoming_links );

        add_option( 'imasters_wp_incominglinks_new_page', 1 );

        add_option( 'imasters_wp_incominglinks_itens_per_page', 15 );
    }

    /**
     * Create menu in Wordpress admin sidebar
     */
    function menu()
    {
        add_menu_page( 'iMasters WP Incoming Links', 'iMasters WP Incoming Links', 'admin_incoming_links', 'imasters-wp-incoming-links/imasters-wp-incoming-links-default.php', '' , plugins_url( 'imasters-wp-incoming-links/assets/images/imasters.png' ) );
        add_submenu_page( 'imasters-wp-incoming-links/imasters-wp-incoming-links-default.php', 'Google Blog Search', 'Google Blog Search', 'admin_incoming_links', 'imasters-wp-incoming-links/imasters-wp-incoming-links-googleblogsearch.php' );
        add_submenu_page( 'imasters-wp-incoming-links/imasters-wp-incoming-links-default.php', __('Settings','iwpil'), __('Settings','iwpil'), 'admin_incoming_links', 'imasters-wp-incoming-links/imasters-wp-incoming-links-settings.php' );
        add_submenu_page( 'imasters-wp-incoming-links/imasters-wp-incoming-links-default.php', __('iMasters WP Incoming Links Unistall','iwpil'), __('iMasters WP Incoming Links Unistall','iwpil'), 'admin_incoming_links', 'imasters-wp-incoming-links/imasters-wp-incoming-links-unistall.php' );
    }

    /**
     *
     * @global object $wpdb WordPress Database object
     */
    function incoming_links()
    {
        global $wpdb;
        
        $referer_page = $_SERVER['HTTP_REFERER'];
        $my_url = bloginfo( 'url' );
        if ( strpos( $referer_page, $my_url ) === false and !empty($referer_page)) :
            $wpdb->query( sprintf( "
                INSERT INTO $wpdb->imasters_wp_incoming_links
                (incoming_links_origin_page, incoming_links_target_page, incoming_links_ip)
                VALUES
                ( '%s', '%s', INET_ATON('%s') )
                ",
                $referer_page,
                $_SERVER['REQUEST_URI'],
                $_SERVER['REMOTE_ADDR']
            ) );
      endif;
        
    }

    /**
     *
     * @global object $wpdb WordPress DataBase Object
     * @param String $filter_time Filter for list from logs incoming links
     * @return Bool type for error in filters
     */
    function get_incoming_links( $filter_time = 'all', $limit )
    {
        global $wpdb;

        $interval = $this->_get_interval( $filter_time );

        if ( 'all' == $interval ) :
            $incoming_links = $wpdb->get_results( "
                SELECT SQL_CALC_FOUND_ROWS incoming_links_origin_page, incoming_links_target_page, INET_NTOA(incoming_links_ip) AS incoming_links_ip, incoming_links_registered_at
                FROM $wpdb->imasters_wp_incoming_links
                ORDER BY incoming_links_id DESC
                $limit
                "
             );
        else :
            $incoming_links = $wpdb->get_results( "
                SELECT SQL_CALC_FOUND_ROWS incoming_links_origin_page, incoming_links_target_page, COUNT(incoming_links_origin_page) AS total
                FROM $wpdb->imasters_wp_incoming_links
                WHERE incoming_links_registered_at > date_sub(now(), interval $interval day)
                GROUP BY incoming_links_origin_page
                ORDER BY incoming_links_id DESC
                $limit
                "
             );
        endif;

        if($incoming_links)
            return $incoming_links;

        return false;
    }

    /**
     *
     * @return bool type for showing all logs registers
     */
    function is_showing_all()
    {
        return ( !isset( $_GET['iwpil-filter-time'] ) or $_GET['iwpil-filter-time'] == 'all' ) ? true : false;
    }

    /**
     *
     * @param String $time This param determinate the filter for list of logs
     * @return <type>
     */
    function _get_interval( $time )
    {
        
        switch($time) :
            case '24h' :
                $time = '1';
            break;
            case '1w' :
                $time = '7';
            break;
            case '1m' :
                $time = '31';
            break;
            case 'all' :
            default:
                $time = 'all';
            break;
        endswitch;

      return $time;
    }


    /**
     * Remove prefixes from the url getting only the domain
     *
     * @return String Url radical domain
     */
    function get_siteurl()
    {
        $siteurl = get_option( 'siteurl' );

        $siteurl = preg_replace( '(http://|www.)', '', $siteurl );

       return $siteurl;
    }

    /**
     *
     *Create the textdomain for translation language
     */
    function textdomain()
    {
        load_plugin_textdomain('iwpil',false,'wp-content/plugins/imasters-wp-incoming-links/assets/languages');
    }

    /**
     *
     * @global object $wpdb WordPress DataBase Object
     * @return bool type for clear the logs registers
     */
    function truncate_logs()
    {

        global $wpdb;

	$clearlog = $wpdb->query("TRUNCATE TABLE $wpdb->imasters_wp_incoming_links");
        $wpdb->query("OPTIMIZE TABLE $wpdb->imasters_wp_incoming_links");
        delete_option('imasters_wp_incominglinks_option');

        //var_dump($clearlog);

        if ($clearlog)
            return true;

            return false;

    }

    /**
     * This function insert JS in admin plugin
     * To back end for exception treatment
    */
    function scripts()
    {
        if (! empty($_GET['page']))
            if ( strpos( $_GET['page'], 'imasters-wp-incoming-links' ) !== false ) :
                $iwpil_scripts_ver = filemtime( dirname( __FILE__ ) . '/assets/javascript/imasters-wp-incoming-links-backend-scripts.js' );
                wp_enqueue_script( 'iwpil.scripts', WP_PLUGIN_URL . '/imasters-wp-incoming-links/assets/javascript/imasters-wp-incoming-links-backend-scripts.js', array( 'jquery' ), $iwpil_scripts_ver );
                echo "\n<!-- START - Generated by iMasters WP Incoming Link -->";
                echo '<script type="text/javascript">' . "\n";
                echo '/* <![CDATA[ */';
                printf( 'var confirm_delete_message = "%s";', __( 'You are about to clear incomings links registered by iMasters WP incoming links? \\n Choose [Cancel] To Cancel, [OK] to Clear.', 'iwpil' ) );
                echo '/* ]]> */';
                echo '</script>';
                echo "\n<!-- END - Generated by iMasters WP Incoming Link -->\n";
            endif;
    }

    /**
     * Insert in the end of page the javascript
     * for manipule the option to choose the way
     * to open the incoming links
     */
    function new_page()
    {
        if ( strpos( $_GET['page'], 'imasters-wp-incoming-links' ) !== false ) :
            if ( 1 == get_option( 'imasters_wp_incominglinks_new_page' ) ) :
                echo '<script type="text/javascript">' . "\n";
                echo '/* <![CDATA[ */' . "\n";
                echo 'jQuery( function($) {';
                echo "$('table.widefat a').attr( 'target', '_blank' );\n";
                echo '});' . "\n";
                echo '/* ]]> */' . "\n";
                echo '</script>';
            endif;
        endif;
    }

    /**
     *
     * @param <type> $origin_page
     * @return <type>
     */
    function get_origin_page( $origin_page )
    {
        $str = preg_replace( '/^https?:\/\/(www.)?/', '', $origin_page );

        if( strlen($str) > 50 ):
            $str = substr($str, 0, 50);
            $str = $str.'...';
        endif;

        return $str;
    }

}

/*
 * Define the role for iMasters WP Incoming Links
 */
$role = get_role('administrator');
	if(!$role->has_cap('manage_incoming_links')) {
		$role->add_cap('manage_incoming_links');
        }

/**
 * Instance of the IMASTERS WP INCOMING LINKS to run in constructor method
 */
$imasters_wp_incoming_links = new IMASTERS_WP_Incoming_Links();

?>