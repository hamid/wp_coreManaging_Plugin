<?php


/** Step 2 (from text above). */
add_action( 'admin_menu', 'my_plugin_menu' );

/** Step 1: define main menu */
function my_plugin_menu() {
	add_menu_page( null, 'مدیریت ایمیل', 'export', 'my-mail-menu' ,'my_mail_menu_main','dashicons-email',55);
	add_submenu_page( 'my-mail-menu', 'ایجاد ایمیل', 'ایجاد', 'export', 'my-submenu-handle_add', 'my_plugin_mail_add');
	add_submenu_page( 'my-mail-menu', 'لیست ایمیل', 'لیست ایمیل ها', 'export', 'my-submenu-handle_list', 'my_plugin_mail_list');
	add_submenu_page( 'my-mail-menu', 'ورود به ایمیل', 'ورود به ایمیل ها', 'export', 'my-submenu-handle_panel', 'my_plugin_mail_panel');
	add_submenu_page( null, 'ویرایش ایمیل', 'ویرایش', 'export', 'my-submenu-handle_edit', 'my_plugin_mail_edit');
	add_submenu_page( null, 'حذف ایمیل', 'حذف', 'export', 'my-submenu-handle_del', 'my_plugin_mail_del');
}

/** -----ADD */
function my_plugin_mail_add()
{
    
	if ( !current_user_can( 'export' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    
    
        $url        = get_home_url();
        $urlArray   = parse_url($url);
        $domain     = str_replace('www.','',$urlArray['host']);
    
    
    
    if($_POST['add'])
    {
        // csrf
        $nonce = $_REQUEST['_wpnonce'];
        if ( ! wp_verify_nonce( $nonce, 'add_mail' ) ) {
          exit; // Get out of here, the nonce is rotten!
        }
        

        $name   = $_POST['name'];
        $pass   = $_POST['pass'];
        $repass = $_POST['repass'];
        $disk   = $_POST['disk'];
        $status = array('status'=>true);
        
 
        
        if($pass != $repass)
            $status = array('status'=>false,'mes'=>'رمز های عبور یکسان نیستند.');  
        
        if( empty($disk) ||$disk == 0)
            $status = array('status'=>false,'mes'=>'مقدار حجم وارد شده معتبر نمی باشد');
        
        
        if($status['status'] == true){
            $input = array(
                'apikey'  => SP_SITE_APIKEY,
                'site_id' => SP_SITE_ID,
                'domain'  => $domain,
                'name'  => $name,
                'pass'  => $pass,
                'disk'  => $disk
            );
             $output = fetchDataFromServer('/mail/add',$input);
            
             if($output['status'] == false && $output['err'] == '1')
                $status = array('status'=>false,'mes'=>'خطا در احراز هویت سایت'); // apikey and site_id doesnt match

             if($output['status'] == false)
                $status = array('status'=>false,'mes'=>$output['data']); // apikey and site_id doesnt match

            if($output['status'] == true)
                $status = array('status'=>true,'mes'=>$output['data']); // apikey and site_id doesnt match

        }
        
       
        
    }
    
    
    
    
    ?>

        <div class="wrap">
          <h1 class="wp-heading-inline">ایجاد ایمیل </h1>
            <br>
            <br>
          <form action="<?php echo ($_SERVER['REQUEST_URI']); ?>" method="post">
              <?php if($_POST['add'] && $status['status']) { ?>
                  <div class="updated" style="color:#2ecc71">
                      <p> ایمیل 
                      <?php echo($name.'@'.$domain); ?>
                          باموفقیت ایجاد شد
                      </p>
                  </div>
              <?php } ?>
              
              
              <?php if($_POST['add'] && $status['status']==false) { ?>
                  <div class="notice notice-error" style="color:#e74c3c">
                      <p> خطا در ایجاد ایمیل : 
                      <?php echo($name.'@'.$domain); ?>
                          <br/>
                      خطا : <?php if(is_array($status['mes'])) print_r($status['mes']); else echo($status['mes']); ?>
                      </p>
                  </div>
              <?php } ?>
              
              <table class="wp-list-table" style="text-align:left">
                <tr> <td> آدرس ایمیل</td>  <td style="min-width:150px"><?php echo($domain); ?>@</td> <td><input type="text" value="" style="text-align:left" name="name" placeholder=""></td> </tr>
                <tr> <td> رمز عبور </td> <td></td> <td><input type="password" style="text-align:left" value="" name="pass" placeholder=""></td> </tr>
                <tr> <td> تکرار رمز عبور </td> <td></td> <td><input type="password"  style="text-align:left"value="" name="repass" placeholder=""></td> </tr>
                <tr> <td>  حجم  </td> <td >مگابایت</td> <td><input type="number" value="20" style="text-align:center" name="disk" placeholder=""></td> </tr>
                <tr> <td></td> <td></td> <td style="text-align:left"><input type="submit" value="ایجاد" class="button-primary" /></td> </tr>
              </table>
            <input name="add" type="hidden" value="1" />
            <?php wp_nonce_field( 'add_mail' ); ?>
          
          </form>
        </div>

        <?php   
        
}


/** -----edit */
function my_plugin_mail_edit() {
	if ( !current_user_can( 'export' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    
   
    
    
    $url        = get_home_url();
    $urlArray   = parse_url($url);
    $domain     = str_replace('www.','',$urlArray['host']);
    
    $quota = $_GET['quota'];
    $name  = $_GET['name'];
    
    
    if($_POST['edit'])
    {
         //-- csrf check
        $nonce = $_REQUEST['_wpnonce'];
        if ( ! wp_verify_nonce( $nonce, 'edit_mail' ) ) {
          exit; // Get out of here, the nonce is rotten!
        }

        $name   = $_POST['name'];
        $pass   = $_POST['pass'];
        $repass = $_POST['repass'];
        $disk   = $_POST['disk'];
        $status = array('status'=>true);
        
        if(empty($disk) || $disk == 0)
            $status = array('status'=>false,'mes'=>'حجم ایمیل نباید صفر یا خالی وارد شده باشد.');
        
        if($pass != $repass)
            $status = array('status'=>false,'mes'=>'رمز های عبور یکسان نیستند.');
        
        if($status['status'] == true){
            $input = array(
                'apikey'  => SP_SITE_APIKEY,
                'site_id' => SP_SITE_ID,
                'domain'  => $domain,
                'name'  => $name,
                'pass'  => $pass,
                'disk'  => $disk
            );
            $output = fetchDataFromServer('/mail/modify',$input);
            
            if($output['status'] == false && $output['err'] == '1')
                $status = array('status'=>false,'mes'=>'خطا در احراز هویت سایت'); // apikey and site_id doesnt match
        
             if($output['status'] == false)
                $status = array('status'=>false,'mes'=>$output['data']); // apikey and site_id doesnt match

            if($output['status'] == true){
                $status = array('status'=>true,'mes'=>$output['data']); // apikey and site_id doesnt match
                $quota = $disk;
            }
        }
        
        
        
    }
    
    
    
    
    ?>

        <div class="wrap">
          <h1>ویرایش ایمیل </h1>
            <br>
            <br>
          <form action="<?php echo ($_SERVER['REQUEST_URI']); ?>" method="post">
              <?php if($_POST['edit'] && $status['status']) { ?>
                  <div class="updated" style="color:#2ecc71">
                      <p> ایمیل 
                      <?php echo($name.'@'.$domain); ?>
                          باموفقیت ویرایش شد
                      </p>
                  </div>
              <?php } ?>
              
              
              <?php if($_POST['edit'] && $status['status']==false) { ?>
                  <div class="notice notice-error" style="color:#e74c3c">
                      <p> خطا در ویرایش ایمیل : 
                      <?php echo($name.'@'.$domain); ?>
                          <br/>
                      خطا : <?php if(is_array($status['mes'])) print_r($status['mes']); else echo($status['mes']); ?>
                      </p>
                  </div>
              <?php } ?>
              
              <table class="wp-list-table" style="text-align:left">
                <tr> <td> آدرس ایمیل</td> <td></td>  <td style="min-width:150px"><?php echo($name.'@'.$domain); ?></td> </tr>
                <tr> <td> رمز عبور جدید </td> <td></td> <td><input type="password" style="text-align:left" value="" name="pass" placeholder=""></td> </tr>
                <tr> <td> تکرار رمز عبور </td> <td></td> <td><input type="password"  style="text-align:left"value="" name="repass" placeholder=""></td> </tr>
                <tr> <td>  حجم  </td> <td >مگابایت</td> <td><input type="number" value="<?php echo($quota); ?>" style="text-align:center" name="disk" placeholder=""></td> </tr>
                <tr> <td></td> <td></td> <td style="text-align:left"><input type="submit" value="ویرایش" class="button-primary" /></td> </tr>
              </table>
            <input name="edit" type="hidden" value="1" />
            <input name="name" type="hidden" value="<?php echo($name); ?>" />
            <?php wp_nonce_field( 'edit_mail' ); ?>
          
          </form>
        </div>

        <?php   
        
}

/**----- LIST */
function my_plugin_mail_list()
{
	if ( !current_user_can( 'export' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    
    $url = get_home_url();
    $urlArray = parse_url($url);
    $domain = str_replace('www.','',$urlArray['host']);
    
    // get list from web service
    $fetchedData = fetchDataFromServer('/mail/list',array(
        'domain'    => $domain,
        'apikey'    => SP_SITE_APIKEY,
        'site_id'   => SP_SITE_ID,
    ));
    
    
    if($fetchedData['status'])
    {
        $list = array();
        $rawList = $fetchedData['data'];
        if(is_array($rawList))
        foreach($rawList as $key=>$item)
        {
            parse_str($item, $quote);
            $allquota   = round(intval($quote['quota']) / (1024*1024) );
            $usage      = round(intval($quote['usage']) / (1024*1024) ); 
            $precent    = round(($usage/$allquota)*100); 
            if($precent < 50)
                $color = '#2ecc71';
            elseif ($precent > 75)
                $color = '#e74c3c';
            else
                $color = '#f39c12';
            $del_url = wp_nonce_url( '?page=my-submenu-handle_del&name='.$key, 'delete_mail'.$key );
            
            $list[] = array('name'=>$key.'@'.$domain ,'key'=>$key,'quota'=>$allquota,'usage'=>$usage,'precent'=>$precent,'color'=>$color,'del_url'=>$del_url);
        }
            
    }
    else
        $list = false;
    
    
    $c = 1;
    
    
    ?>

        <div class="wrap">
            <h1 class="wp-heading-inline"> ایمیل های موجود </h1><a href="?page=my-submenu-handle_add" class="page-title-action">افزودن ایمیل</a>
            <br/>
           
            <table class="wp-list-table widefat" style="margin-top:7px">
                
                <?php if(is_array($list)) foreach($list as $item){ ?>
                    <tr> <td style="width:5%"><?php echo($c); ?></td>  <td class="row-title"><?php echo($item['name']); ?></td>
                        <td> <a  href="?page=my-submenu-handle_edit&quota=<?php echo($item['quota']); ?>&name=<?php echo($item['key']); ?>"> <span class=" dashicons-before dashicons-unlock" ></span> تغییر رمز/حجم</a>  </td>
                        <td> <span class=" dashicons-before dashicons-no-alt" style="color:red"></span> <a  onclick="onmaildeleted('<?php echo($item['name']); ?>','<?php echo($item['del_url']); ?>')"  class="submitdelete" style="color:red" href="#">حذف </a>  </td>
                        <td>
                            <div style="width:200px;height:20px;border:1px solid #555;position:relative">
                                <div style="background: <?php echo($item['color'])  ?>;position : absolute;top:0px;left:0px;height:20px;width:<?php echo($item['precent']); ?>%;"></div>
                                <span style="position:absolute;z-index:10;color:#666;left:38%;top:1px;">  <?php echo($item['usage'].'/'.$item['quota']); ?> مگابایت </span> 
                            </div>  
                        </td>  </tr>
                <?php $c++; } ?>
            </table>
            
            
        </div>
        <script type="text/javascript">
            onmaildeleted = function(name,delurl){
                var stat = confirm('ایا مطمعن به حذف این ایمیل با تمامی محتوای داخل ان هستید ؟');
                if(stat){
                    window.location.href = delurl;
                }
                return false;
            };
        </script>

        <?php
     
}


function my_plugin_mail_del()
{
    
    if ( !current_user_can( 'export' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    
    $url        = get_home_url();
    $urlArray   = parse_url($url);
    $domain     = str_replace('www.','',$urlArray['host']);
    $name       = $_GET['name'];
    $nonce      = $_REQUEST['_wpnonce'];
    
    if ( ! wp_verify_nonce( $nonce, 'delete_mail'.$name ) ) {
      exit; // Get out of here, the nonce is rotten!
    }

    $input = array(
        'apikey'  => SP_SITE_APIKEY,
        'site_id' => SP_SITE_ID,
        'domain'  => $domain,
        'name'  => $name,
    );
    $output = fetchDataFromServer('/mail/del',$input);

    if($output['status'] == false && $output['err'] == '1')
        $status = array('status'=>false,'mes'=>'خطا در احراز هویت سایت'); // apikey and site_id doesnt match

     if($output['status'] == false)
        $status = array('status'=>false,'mes'=>$output['data']); // apikey and site_id doesnt match

    if($output['status'] == true){
        $status = array('status'=>true,'mes'=>$output['data']); // apikey and site_id doesnt match
        $quota = $disk;
    }
    
    
    ?>

        <div class="wrap">
            <h1 class="wp-heading-inline"> حذف ایمیل </h1>
            <?php if( $status['status']) { ?>
                  <div class="updated" style="color:#2ecc71">
                      <p> ایمیل 
                      <?php echo($name.'@'.$domain); ?>
                          باموفقیت حذف شد
                      </p>
                  </div>
              <?php } ?>
              
              
              <?php if( $status['status']==false) { ?>
                  <div class="notice notice-error" style="color:#e74c3c">
                      <p> خطا در حذف ایمیل : 
                      <?php echo($name.'@'.$domain); ?>
                          <br/>
                      خطا : <?php if(is_array($status['mes'])) print_r($status['mes']); else echo($status['mes']); ?>
                      </p>
                  </div>
              <?php } ?>
        </div>

    <?php
    
}


function my_plugin_mail_panel()
{
    header('Location: '.get_home_url().'/roundcube/');
    exit;
}
function my_mail_menu_main()
{
    header('Location: ?page=my-submenu-handle_list ');
    exit;
}


?>