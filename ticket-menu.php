<?php





/** Step 2 (from text above). */
add_action( 'admin_menu', 'my_plugin_ticket_menu' );

/** Step 1: define main menu */
function my_plugin_ticket_menu()
{
	add_menu_page( null, 'پشتیبانی ', 'export', 'my-ticket-menu' ,'my_ticket_menu_main','dashicons-sos',56);
	$my_admin_page_add     = add_submenu_page( 'my-ticket-menu', 'ایجاد تیکت', 'ایجاد', 'export', 'my-submenu-ticket_add', 'my_plugin_ticket_add');
	$my_admin_page_list    = add_submenu_page( 'my-ticket-menu', 'لیست تیکت', 'لیست تیکت ها', 'export', 'my-submenu-ticket-list', 'my_plugin_ticket_list');


    // Adds my_help_tab when my_admin_page loads
    add_action('load-'.$my_admin_page_add, 'my_plugin_ticket_add_help');
    add_action('load-'.$my_admin_page_list, 'my_plugin_ticket_list_help');
}






/** -----ADD */
function my_plugin_ticket_add()
{
  global $mainTicketSite;
	if ( !current_user_can( 'export' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

        $url        = get_home_url();
        $urlArray   = parse_url($url);
        $domain     = str_replace('www.','',$urlArray['host']);
        $userid     = wp_get_current_user();

        $url = 'ticketredirect?apikey='.SP_SITE_APIKEY.'&site_id='.SP_SITE_ID.'&uid='.$userid->ID.'&url=ثبت-تیکت';

    ?>

        <div class="wrap">
          <iframe style="width:100%;height:100%;" src="<?php echo($mainTicketSite.$url); ?>"></iframe>
        </div>

    <?php

}



/**----- LIST */
function my_plugin_ticket_list()
{
  global $mainTicketSite;

	if ( !current_user_can( 'export' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

    $url        = get_home_url();
    $urlArray   = parse_url($url);
    $domain     = str_replace('www.','',$urlArray['host']);
    $userid     = wp_get_current_user();
    $url = 'ticketredirect?apikey='.SP_SITE_APIKEY.'&site_id='.SP_SITE_ID.'&uid='.$userid->ID.'&url=ticket';




    ?>
      <div class="wrap">
        <iframe style="width:100%;height:100%;" src="<?php echo($mainTicketSite.$url.'/'); ?>"></iframe>
      </div>
    <?php

}




function my_plugin_ticket_add_help()
{
	// We are in the correct screen because we are taking advantage of the load-* action (below)

	$screen = get_current_screen();
	//$screen->remove_help_tabs();
	$screen->add_help_tab( array(
		'id'       => 'my-plugin-default2',
		'title'    => __( 'Add' ).' '. __( 'تیکت' ),
		'content'  => 'این بخش حاوی فرمی برای ایجاد یک  تیکت می باشد . در این فرم شما موضوع تیکت
          و در گام بعد توضیح درباره مشکل بوجود آمده و در نهایت اگر فایلی یا عکسی برای مشخص شدن بهتر مشکل دارید در بخش ضمیمه، وارد می کنید'
	));
	//add more help tabs as needed with unique id's

	// Help sidebars are optional
	$screen->set_help_sidebar(
		'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
		'<p><a href="http://wordpress.org/support/" target="_blank">' . _( 'Support Forums' ) . '</a></p>'
	);
}

function my_ticket_menu_main()
{
  my_plugin_ticket_list();
}


function my_plugin_ticket_list_help()
{
	// We are in the correct screen because we are taking advantage of the load-* action (below)

	$screen = get_current_screen();
	//$screen->remove_help_tabs();
	$screen->add_help_tab( array(
		'id'       => 'my-plugin-default2',
		'title'    => __( 'لیست' ).' '. __( 'تیکت' ) ,
		'content'  => 'در اینجا شما می توانید لیست تیکت های  خود را مشاهده کنید .'
	));
	//add more help tabs as needed with unique id'

	// Help sidebars are optional
	$screen->set_help_sidebar(
		'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
		'<p><a href="http://wordpress.org/support/" target="_blank">' . _( 'Support Forums' ) . '</a></p>'
	);
}












/** ------- -------------- --------- */
/** ------- on user Update --------- */
/** ------- -------------- --------- */
add_action( 'profile_update', 'my_profile_ticket_update', 10, 2 );

function my_profile_ticket_update( $user_id, $old_user_data )
{

 global $mainTicketSite;

  $user       = get_userdata($user_id);
  $uid        = $user->id;
  $fisrt_name = $user->first_name;
  $last_name  = $user->last_name;
  $mail       = $user->user_email;


  $url        = get_home_url();
  $urlArray   = parse_url($url);
  $domain     = str_replace('www.','',$urlArray['host']);

  $param = array(
          'apikey'  => SP_SITE_APIKEY,
          'site_id' => SP_SITE_ID,
          'domain'  => $domain,
          'uid'     => $uid,
          'first_name' => $fisrt_name,
          'last_name'  => $last_name,
          'mail'       => $mail,
      );



      $query_array  = array();
      foreach( $param as $key => $key_value )
          $query_array[] = urlencode( $key ) . '=' . urlencode( $key_value );
      $string = implode( '&', $query_array );

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $mainTicketSite.'ticketUpdateUser');
      curl_setopt($ch, CURLOPT_POSTFIELDS,$string);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      $res = curl_exec($ch);
        curl_close($ch);


}


/** ------- -------------- --------- */
/** ------- on user Added ---------- */
/** ------- -------------- --------- */
add_action( 'user_register', 'myplugin_registration_ticket_save', 10, 1 );

function myplugin_registration_ticket_save( $user_id )
{

    global $webserverUrl;
    
if( $_POST['role'] != 'shop_manager'  &&  $_POST['role'] != 'administrator')
  return false;

    $user       = get_userdata($user_id);
    $uid        = $user_id;
    $uname      = $_POST['user_login'];
    $pass       = $_POST['pass1'];
    $fisrt_name = $user->first_name;
    $last_name  = $user->last_name;
    $mail       = $user->user_email;
    $website    = $_POST['url'];




    $url        = get_home_url();
    $urlArray   = parse_url($url);
    $domain     = str_replace('www.','',$urlArray['host']);

    $param = array(
            'apikey'  => SP_SITE_APIKEY,
            'site_id' => SP_SITE_ID,
            'domain'  => $domain,
            'uid'     => $uid,
            'uname'   => $uname.'_'.$domain,
            'pass'    => mk_randomstring(20),
            'first_name' => $fisrt_name,
            'last_name'  => $last_name,
            'mail'       => $mail,
            'url'        => $website,
            'pkey'       => 'TMerbgk3TL765HGFBVM&6g3NGhl74R6m1DS4',
        );




        $query_array  = array();
        foreach( $param as $key => $key_value )
            $query_array[] = urlencode( $key ) . '=' . urlencode( $key_value );
        $string = implode( '&', $query_array );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webserverUrl.'/ticket/addUser');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);
          curl_close($ch);
    
}





function mk_randomstring($length = 20)
{
        $characters = '123456789abcdefghijklmnpqrstuvwxyz';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }




?>
