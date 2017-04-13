<?php
/*
Plugin Name: Pepo Campaigns wordpress woocommerce subscription
Description: Plugin for Acount Registration using Pepocampaigns API
Plugin URI: http://mrphpguru.com/
Author URI: http://mrphpguru.com/
Author: MrPhpGuru, Ranjeet Singh, Rajesh Kumar
License: Public Domaia
Version: 100.1
*/

include_once 'include/admin_menu.php';


function pepocampaigns_wordpress_woocommerce_subscription_admin_init() 
{
       /* Register our stylesheet. */
       wp_register_style('pepocampaigns_wordpress_woocommercePluginStylesheet', plugins_url('css/stylesheet.css', __FILE__) );
}
add_action( 'admin_init', 'pepocampaigns_wordpress_woocommerce_subscription_admin_init' );
function pepocampaigns_activate() {
add_option( 'pepocampaigns_apikey', '', '', 'yes' );
add_option( 'pepocampaigns_secretkey', '', '', 'yes' );
add_option( 'pepocampaigns_listid', '', '', 'yes');
}
register_activation_hook( __FILE__, 'pepocampaigns_activate');

add_action('register_form','add_newsletter_checkbox_in_registration');
add_action('user_register', 'update_newslatter_fields');

function add_newsletter_checkbox_in_registration()
{
?>
    <p>
    <label> <input id="newsletter" type="checkbox" tabindex="30" size="25" value="1" name="newsletter" checked />Subscribe to Newsletter
   
    </label>
    </p>
<?php
}

function update_newslatter_fields ( $user_id, $password = "", $meta = array() )
{
    update_user_meta( $user_id, 'newsletter', $_POST['newsletter'] );
}
add_action( 'user_register', 'after_user_registration', 10, 1 );
function after_user_registration($user_id){
	$user_info = get_userdata($user_id);
	update_user_meta( $user_id, 'newsletter', $user_info->newsletter);
	if($user_info->newsletter=='1'){
	if ( isset( $_POST['first_name'] ) ){
		$firstname=$_POST['first_name'];
	}
	else{
	$firstname='';	
	}
	if ( isset( $_POST['last_name'] ) ){
		$last_name=$_POST['last_name'];
	}
	else{
	$last_name='';	
	}
	$email=$user_info->user_email;
  
  $pepocampaigns_apikey=get_option( 'pepocampaigns_apikey');
  $pepocampaigns_secretkey=get_option( 'pepocampaigns_secretkey');
  $pepocampaigns_listid=get_option( 'pepocampaigns_listid');

  require ("include/PepoCampaigns.class.php");
  $pepo_campaigns = new PepoCampaigns(array(
  'key' => $pepocampaigns_apikey,
    'secret' => $pepocampaigns_secretkey
   ));
  $pepo_campaigns->add_contact_to_list($pepocampaigns_listid,
  array(
      'email' => $email, 
      'first_name' => $firstname, 
      'last_name' => $last_name
  )
 );

 }
  
	}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	/*
* Addding subscription  checkbox to the checkout page
*/
add_action('woocommerce_after_order_notes', 'newsletter_subscription_field');

function newsletter_subscription_field( $checkout ) {
$checked = $checkout->get_value( 'newsletter' ) ? $checkout->get_value( 'newsletter' ) : 1;
woocommerce_form_field('newsletter', array(
'type'          => 'checkbox',
'checked'=>'checked',
'class'         => array('checkbox_field'),
'label'         => __('Subscribe to Newsletter'),

), $checked);

}

add_action('woocommerce_checkout_update_order_meta', 'post_newslatter_fields');

function post_newslatter_fields( $order_id ) {
if ($_POST['newsletter']){
	$newsletter=$_POST['newsletter'];
}
else{
	$newsletter=0;
}	
 update_post_meta( $order_id, 'newsletter_subscription', $newsletter);
 
}

add_action('woocommerce_thankyou', 'Thankyouorder_And_Subscribe_Newlater');

function Thankyouorder_And_Subscribe_Newlater( $order_id ) {
  $order = new WC_Order( $order_id );
  global $wpdb;
 $order_id =  $order->id;

 $table = $wpdb->prefix . 'postmeta';
 $sql = 'SELECT * FROM `'. $table . '` WHERE post_id = '. $order_id; 
  $result = $wpdb->get_results($sql);
  foreach($result as $res) {
    if( $res->meta_key == '_billing_first_name'){
    $firstname = $res->meta_value;      
    }
    if( $res->meta_key == '_billing_last_name'){
     $last_name = $res->meta_value;  
    }
    if( $res->meta_key == '_billing_email'){
    $email = $res->meta_value;  
      }
    if( $res->meta_key == 'newsletter_subscription'){
    $newsletter = $res->meta_value;  
      }
    }
    if($newsletter=='1'):
    $pepocampaigns_apikey=get_option( 'pepocampaigns_apikey');
  $pepocampaigns_secretkey=get_option( 'pepocampaigns_secretkey');
  $pepocampaigns_listid=get_option( 'pepocampaigns_listid');

  require ("include/PepoCampaigns.class.php");
  $pepo_campaigns = new PepoCampaigns(array(
  'key' => $pepocampaigns_apikey,
    'secret' => $pepocampaigns_secretkey
   ));
  $pepo_campaigns->add_contact_to_list($pepocampaigns_listid,
  array(
      'email' => $email, 
      'first_name' =>  $firstname, 
      'last_name' => $last_name
  )
 );
    
    endif;
}
}
?>
