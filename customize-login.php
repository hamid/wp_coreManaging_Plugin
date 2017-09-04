<?php


function my_custom_loginPage() {
echo '<link rel="stylesheet" type="text/css" href="' . WP_CORE_MANAGMENT_URL  . 'assets/css/login-styles.css" />';
}
add_action('login_head', 'my_custom_loginPage');


?>