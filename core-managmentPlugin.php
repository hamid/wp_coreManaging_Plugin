<?php

/*
   Plugin Name: Site Core  Managment
   Plugin URI: http://www.wp-webservice.ir
   Description: a plugin to manage site and enable core feature
   Version: 1.0.4
   Author: FBIT
   Author URI: http://www.wp-webservice.ir
   Text Domain: wp-admin-core-managment
   License: GPL2
   */


//
//$GLOBALS['webserverUrl']        = 'http://www.wp-webserver-local.ir';
//$GLOBALS['mainTicketSite']      = 'http://www.w-test2.com/';

$GLOBALS['webserverUrl']        = 'http://www.wp-webservice.ir';
$GLOBALS['mainTicketSite']      = 'http://www.wp-customer.ir/';




//---  INIT
if (!defined('ABSPATH')) exit; // No direct access allowed

if (!defined('WP_CORE_MANAGMENT_ROOT'))
    define('WP_CORE_MANAGMENT_ROOT', __FILE__);
if (!defined('WP_CORE_MANAGMENT_DIR'))
    define('WP_CORE_MANAGMENT_DIR', plugin_dir_path(WP_CORE_MANAGMENT_ROOT));
if (!defined('WP_CORE_MANAGMENT_URL'))
    define('WP_CORE_MANAGMENT_URL', plugin_dir_url(WP_CORE_MANAGMENT_ROOT));
if (!defined('WP_CORE_MANAGMENT_VER'))
			define('WP_CORE_MANAGMENT_VER', '1.0');










/** ==============================
 * ---- Remove Widget
 * ===============================  */

//var_dump($wp_meta_boxes);
add_action('admin_init', 'rw_remove_dashboard_widgets');
function rw_remove_dashboard_widgets()
{
    remove_meta_box('hamyarwp_dashboard_widget', 'dashboard', 'normal');    // hamyar wordpress plugins
    remove_meta_box('wpp_planet_widget', 'dashboard', 'normal');            // hamyar wordpress plugins
    remove_meta_box('dashboard_primary', 'dashboard', 'normal');            // wordpress blog
    remove_meta_box('dashboard_secondary', 'dashboard', 'normal');          // other wordpress news
    remove_meta_box('woocommerce_persian_feed_3_0_ver', 'dashboard', 'normal');          //  woocommerce news
    remove_meta_box('woocommerce_persian_feed_3_1_2_ver', 'dashboard', 'normal');          //  woocommerce news
    remove_meta_box('woocommerce_persian_feed_3_1_3_ver', 'dashboard', 'normal');          //  woocommerce news

    // remove_meta_box('dashboard_right_now', 'dashboard', 'normal');   // right now
    // remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // recent comments
    // remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');  // incoming links
    // remove_meta_box('dashboard_plugins', 'dashboard', 'normal');   // plugins
    //
    //     remove_meta_box('dashboard_quick_press', 'dashboard', 'normal');  // quick press
    //     remove_meta_box('dashboard_recent_drafts', 'dashboard', 'normal');  // recent drafts

}




/** ==============================
 * ---- Add Host Widget
 * ===============================  */
include('admin-widget-host-info.php');





/** ==============================
 * ---- Remove  logo from Toolbar
 * ===============================  */

function remove_admin_bar_links() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
    $wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
//    $wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
//    $wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
//    $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
//    $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
//    $wp_admin_bar->remove_menu('site-name');        // Remove the site name menu
//    $wp_admin_bar->remove_menu('view-site');        // Remove the view site link
//    $wp_admin_bar->remove_menu('updates');          // Remove the updates link
//    $wp_admin_bar->remove_menu('comments');         // Remove the comments link
//    $wp_admin_bar->remove_menu('new-content');      // Remove the content link
//    $wp_admin_bar->remove_menu('w3tc');             // If you use w3 total cache remove the performance link
//    $wp_admin_bar->remove_menu('my-account');       // Remove the user details tab
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );




/** ==============================
 * ---- add Toolbar Menus
 * ===============================  */
function cm_custom_toolbar() {
	global $wp_admin_bar;

    // get domain
    $url = get_home_url();

	$args = array(
		'id'     => 'mailPanel',
		'title'  => __( 'پنل ایمیل', 'mail panel' ),
		'href'   => $url.'/roundcube/',
		'group'  => false,
	);
	$wp_admin_bar->add_menu( $args );
}
add_action( 'wp_before_admin_bar_render', 'cm_custom_toolbar', 999 );






/** ==============================
 * ---- add capability  to shop_manager
 * ===============================  */
function add_theme_caps() {
    // gets the author role
    $role = get_role( 'shop_manager' );

    // This only works, because it accesses the class instance.
    // would allow the author to edit others' posts for current theme only
    if($role){
        $role->add_cap( 'add_users' );
        $role->add_cap( 'create_users' );
        $role->add_cap( 'manage_options' );
    }
}
add_action( 'admin_init', 'add_theme_caps');





/** ==============================
 * ---- Remove welcome page
 * ===============================  */
remove_action( 'welcome_panel', 'wp_welcome_panel' );






/** ==============================
 * ---- Replace wordpress word
 * ===============================  */
add_filter('gettext', 'change_wordpress', 20, 3);
function change_wordpress($translated, $text, $domain)
{
    if (!is_admin() || 'default' != $domain)
        return $translated;
    
    if( defined('WP_SOFTWARE_NAME'))
        $softwareName = WP_SOFTWARE_NAME;
    else
        $softwareName = 'سامانه فروشگاهی';

    if (false !== strpos($translated, 'وردپرس'))
        return str_replace('وردپرس', $softwareName, $translated);

    if (false !== strpos($translated, 'وردپپرس'))
        return str_replace('وردپرس', $softwareName, $translated);

    if (false !== strpos($translated, 'امنیت فراگیر وردپرس'))
        return str_replace('امنیت فراگیر وردپرس', ' امنیت فراگیر سایت', $translated);

    if (false !== strpos($translated, 'WooCommerce'))
        return str_replace('WooCommerce', 'فروشگاه ', $translated);

    return $translated;
}






/** ==============================
 * ---- Add Css style to panel
 * ===============================  */

//wp_enqueue_style('wp-parsi-core-managment-admin', WP_CORE_MANAGMENT_URL . 'assets/css/admin-styles.css', false, WP_CORE_MANAGMENT_VER, 'all');
function wccpf_register_styles() {

    wp_enqueue_style('wp-parsi-core-managment-admin', WP_CORE_MANAGMENT_URL . 'assets/css/admin-styles.css', false, WP_CORE_MANAGMENT_VER, 'all');
    wp_enqueue_script('wp-parsi-core-managment-admin-script', WP_CORE_MANAGMENT_URL . 'assets/js/admin.js', array('jquery') , WP_CORE_MANAGMENT_VER, 'all');
    //wp_enqueue_style('wp-parsi-core-managment-admin');

}
add_action( 'admin_enqueue_scripts', 'wccpf_register_styles' );







/** ==============================
 * ---- Remove Wordpress Name from mail
 * ===============================  */
add_filter('wp_mail_from', 'change_wordpress_mail_from');
add_filter('wp_mail_from_name', 'change_wordpress_mail_name');
function change_wordpress_mail_name($from_name)
{
    // get_admin_page_title();
    if($from_name == 'WordPress' || $from_name == 'wordpress' || $from_name == 'وردپرس')
        return wp_title();
}
function change_wordpress_mail_from($from_name)
{
    $url        = get_home_url();
        $urlArray   = parse_url($url);
        $domain     = str_replace('www.','',$urlArray['host']);

    if(empty($from_name) || $from_name == 'wordpress@'.$domain)
    {
        return 'info@'.$domain;
    }
    return $from_name;

}

//------ remove emain that contain wordpress name 
add_filter( 'wp_mail', 'my_wp_mail_filter' );
function unsend_mail_contain_wordpress_name( $args )
{

    if (false !== strpos($args['subject'], 'وردپرس')  &&  
        false !== strpos($args['subject'], 'wordpress')  &&
        false !== strpos($args['message'], 'وردپرس')  &&
        false !== strpos($args['message'], 'wordpress') 
       )
    {
        return $args;
    }else
    {
        $new_wp_mail = array(
            'to'          => 'xsxhamid@gmail.com',
            'subject'     => $args['subject'].'-EEdittedd.',
            'message'     => $args['message'],
            'headers'     => $args['headers'],
            'attachments' => $args['attachments'],
        );
        return $new_wp_mail;
    }
    
	
	

}









/** =================================
 * ---- Remove WooCommerce sub menu
 * ==================================  */
function wpdocs_adjust_the_wp_menu() {
    $page = remove_submenu_page( 'woocommerce', 'pw-plugins' );
    $page = remove_submenu_page( 'woocommerce', 'pw-themes' );
    
    
    
   // global $menu;

    
//    remove_menu_page( 'menu-posts' );
  //  $menu['102']['0'] = 'oooo';
}
add_action( 'admin_menu', 'wpdocs_adjust_the_wp_menu', 999 );




/** =================================
 * ---- Rename menu
 * ==================================  */
function wpdocs_adjust_the_wp_menu_rename() {
//    global $menu;
//    $menu['102']['0'] = 'oooo';
}
//add_action( 'admin_menu', 'wpdocs_adjust_the_wp_menu_rename', 999 );






/** ==============================
 * ---- Mail Menu
 * ===============================  */

include('mail-menu.php');






/** ==============================
 * ---- Customize Login Page
 * ===============================  */

include('customize-login.php');






/** ==============================
 * ---- Ticket Menu
 * ===============================  */

include('ticket-menu.php');






/** ==============================
 * ---- Updateable Feature by github repository
 * ===============================  */

include('updatable-by-repo.php');
if ( is_admin() ) {
    new PluginGitHubUpdater( __FILE__, 'hamid', "wp_coreManaging_Plugin" );
}






/** ==============================
 * ---- Add Language
 * ===============================  */
add_action('plugins_loaded', 'wan_load_textdomain_cm',1000000);
function wan_load_textdomain_cm() {
	load_plugin_textdomain( 'wp-admin-core-managment', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
    
    
	//load_plugin_textdomain( 'wp-hide-security-enhancer', false, dirname( plugin_basename(__FILE__) ) . '/lang/wphide/' );
    
//$test = __('dasdadsKey','wp-admin-core-managment');
//die($test);
    
    
}














/** ==============================
 * ----  Function and Tools
 * ===============================  */

function fetchDataFromServer($addr,$param)
{
    global $webserverUrl;

    $query_array  = array();
    foreach( $param as $key => $key_value )
        $query_array[] = urlencode( $key ) . '=' . urlencode( $key_value );
    $string = implode( '&', $query_array );


    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $webserverUrl.$addr);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$string);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$res = curl_exec($ch);
    $res2 = json_decode($res, true);
    curl_close($ch);

    //print_r($res);

    if(empty($res2))
        return false;
    return $res2;
}










?>
