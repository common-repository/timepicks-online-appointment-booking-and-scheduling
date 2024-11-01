<?php
/*
Plugin Name: TimePicks Online Appointment Booking And Scheduling
Plugin URI: http://www.timepicks.com
Version: 0.3
Description: This plugin extends the functionality of your WordPress site by giving you the ability to accept online appointment bookings in real-time . Website visitors simply click on the "Book Online Now" tab that appears on every page of your WordPress site, and prompts the visitor to pick a data & time for their appointment based on real-time availabilities.
Author: TimePicks
Author URI: http://www.timepicks.com

This script is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This script is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
define( 'TP_PLUGIN_DIR', dirname(__FILE__).'/' );
define('TP_PLUGIN_DIR_URL',plugin_dir_url( __FILE__ ));
define('TP_PLUGIN_PAGE','timePicks-online-appointment-booking-and-scheduling');
define('TP_PLUGIN_ADMIN',admin_url().'admin.php?page='.TP_PLUGIN_PAGE);

define('TP_PLUGIN_NAME','Time Picks');
define('TP_PLUGIN_USE_SSL',FALSE);

if(TP_PLUGIN_USE_SSL==TRUE){
	define('TP_PLUGIN_HTTP',"https");
}else{
	define('TP_PLUGIN_HTTP',"http");
}

define('TP_PLUGIN_DOMAIN',TP_PLUGIN_HTTP.'://www.timepicks.com/');
define('TP_PLUGIN_CREATE_ACCOUNT',TP_PLUGIN_HTTP.'://www.timepicks.com/Application/registration/createAccount.php');

//TimePicks Database Keys
define('TIME_PICKS_EMAIL','timepicks_auth_email');
define('TIME_PICKS_PASSWD','timepicks_auth_passwd');
define('TIME_PICKS_SUBDOMAIN','timepicks_auth_subdomain');

//Unlink Message
define('TP_ACCOUNT_DISCONN_MSG','Are you sure, do you want to Unlink from '.TP_PLUGIN_DOMAIN);

/* INIT Admin */
add_action( 'admin_init', 'wptp_plugin_admin_init' );

function wptp_plugin_admin_init(){
	/* Register our stylesheet. */
    wp_register_style( 'adminWpTpStyle',TP_PLUGIN_DIR_URL.'admin-wptp.css', array(), '0.1');
}

function wptp_plugin_admin_styles() {
	/* It will be called only on plugin admin page, enqueue stylesheet here */
	wp_enqueue_style( 'adminWpTpStyle' );
}

// if an admin is loading the admin menu then call the admin actions function
if( is_admin() ) add_action('admin_menu', 'wptimepickssettings');

// actions to perform when the admin menu is loaded
function wptimepickssettings(){
	$page_title=TP_PLUGIN_NAME;
	$menu_title="TimePicks";
	$capability="manage_options";
  	$menu_slug=TP_PLUGIN_PAGE;
  	$function="tp_admin_menu_function";
  	$icon_url=TP_PLUGIN_DIR_URL."images/plugin-icon.png";
  	$position="";
	
	//Add the menu
	// parameters - add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = NULL );
	$wptpPage = add_menu_page( $page_title, $menu_title ,$capability,$menu_slug ,$function,$icon_url ) ;
	
	/* Using registered $page handle to hook stylesheet loading */
      add_action( 'admin_print_styles-' . $wptpPage, 'wptp_plugin_admin_styles' );
}

//Check if admin
if(is_admin()){
	
	//Unlink Account 
	if(isset($_GET['tp_unlink']) && $_GET['tp_unlink']=='1'){
		// Clear All Account Settings
		update_option(TIME_PICKS_EMAIL, "");
		update_option(TIME_PICKS_PASSWD, "");
		update_option(TIME_PICKS_SUBDOMAIN,"");
		
		delete_option(TIME_PICKS_EMAIL);
		delete_option(TIME_PICKS_PASSWD);
		delete_option(TIME_PICKS_SUBDOMAIN);
	}
	
	//Ajax Call  Linking To TimePicks Account
	function my_action_javascript() {
		?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			jQuery('#tpcreateusersub').click(function(){
				jQuery(this).attr('disabled','disabled');
				//Show ajax Progress
				jQuery('#tpajaxProg').show('fast');
				var data = {
					action: 'authtpaccount_action',
					email: jQuery("#email").val(),
					passwd: jQuery("#passwd").val()
				};
				$.post(ajaxurl, data, function(response) {
					//alert(response);
					//Hide ajax Progress
					jQuery('#tpajaxProg').hide('fast');
					jQuery('#tpcreateusersub').removeAttr('disabled');
					try{
						var jobj=JSON.parse(response);
						//alert(jobj.tr==undefined);
						if(jobj.subdomain!=undefined && jobj.subdomain!="error"){
							//Success
							//alert(jobj.subdomain);
							jQuery("#tp-fieldset-1").hide();
							jQuery("#tpSubDomName").html(jobj.subdomain);
							jQuery("#timesPickLinkedInfoTitle p").html('Welcome to your timepicks account <b>14 day Free Trial</b>.<br/><b>Getting Started</b><br/>Please make sure that you have configured your services and availabilitities by going to the <b><a href="<?php echo TP_PLUGIN_HTTP."://";?>'+jobj.subdomain+'/myaccount" target="_blank" >settings of your timepicks account</a></b>.<br/> To access your settings Log in to your admin panel at '+jobj.subdomain+'.timepicks.com/myaccount and click settings in the top right.<br/>');
							jQuery("#timesPickLinkedInfoTitle h2").html('Admin Panel : <a href="<?php echo TP_PLUGIN_HTTP."://";?>'+jobj.subdomain+'" target="_blank">'+jobj.subdomain+'</a>');
							
							jQuery("#timesPickLinkedInfoTitle h3:first").html('Access Admin : <a href="<?php echo TP_PLUGIN_HTTP."://";?>'+jobj.subdomain+'/myaccount" target="_blank">'+jobj.subdomain+'/myaccount</a>');
							
							jQuery("#tpEmailAdd").html(jQuery("#email").val());
							jQuery("#tpLoginArea,#tp-login-options,#tp-side").fadeOut("fast",function(){
								jQuery("#timesPickLinkedInfo").fadeIn("slow");
							});
						}else{
							alert("Invalid username or password, Please try again!");
						}
					}catch(ex){
						//alert("JSON ERROR : "+ response + "\n Error details: "+ ex);
						alert("Missing username or password, Please try again!");
					}
					
				});
			}); //Click btn
			
		});
		</script>
		<?php
	}
				
	// Ajax Response  check TimePicks auth
	add_action('wp_ajax_authtpaccount_action', 'authtpaccount_action_callback');
	
	function authtpaccount_action_callback() {
		global $wpdb;
		
		//Sim.. Success 
		//echo json_encode(array('subdomain'=>'harman.timepicks.com'));
		
		//Sim.. Fail 
		//echo json_encode(array('subdomain'=>'error'));
		
		$email = "";
		$passwd = "";
		
		//Check the valid input
		if(isset($_POST['email']) && $_POST['email']!="" && isset($_POST['passwd']) && $_POST['passwd']!=""){
			$email = $_POST['email'];
			$passwd = $_POST['passwd'];
			
			//Call Auth function
			
			$retFromServer = tp_remote_auth($email,$passwd);
			
			if(isset($retFromServer) && $retFromServer!=""){
					$retArrFromJson = json_decode($retFromServer, true);

					if(strtolower($retArrFromJson['subdomain'])!='error'){
						//echo $retFromServer;
						
						//Save or update account Settings
						if((get_option(TIME_PICKS_PASSWD))!=FALSE || !get_option(TIME_PICKS_EMAIL)!=FALSE){
							update_option(TIME_PICKS_EMAIL, $email);
							update_option(TIME_PICKS_PASSWD, $passwd);
							update_option(TIME_PICKS_SUBDOMAIN,$retArrFromJson['subdomain']);
						}else{
							add_option(TIME_PICKS_EMAIL, $email, NULL, "");
							add_option(TIME_PICKS_PASSWD, $passwd, NULL, "");
							add_option(TIME_PICKS_SUBDOMAIN,$retArrFromJson['subdomain'], NULL, "");
						}
						echo $retFromServer;

					}else{
						echo $retFromServer;			
					}
					die();

			}
		}else{
			json_encode(array('subdomain'=>'error'));
		} 
		die();
		
	}
} // End is admin()

//Check valid JSON 
function tp_isJson($string) {
 	json_decode($string);
	return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;
}

//Auth function
function tp_remote_auth($email,$passwd){
	
		//Start Curl Call 
		$param="?email={$email}&password={$passwd}";
		$apiurl = TP_PLUGIN_DOMAIN."wordpressAuthenticate.php";
		$apiurl .= $param;
		
		// make call
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_URL, $apiurl);
		curl_setopt($ch,CURLOPT_TIMEOUT, 120);
		$pageresults = curl_exec($ch);
		curl_close($ch);
		//echo $apiurl;
		//echo  $pageresults;
		$retArr=json_decode($pageresults,true);
		if(isset($retArr) && is_array($retArr) && strtolower($retArr['subdomain'])!="error" && tp_isJson($pageresults)){
			//Return JSON from server if success :  JSON : {"subdomain":"theirsubdomain.timepicks.com"}
			return $pageresults;
		}else{
			//Return JSON from server if Failed :  JSON : {"subdomain":"error"}
			if(tp_isJson($pageresults)){
				return $pageresults;
			}
		}
		
}

function tp_admin_menu_function() {
	global $wpdb;

	//Check if admin
	if(is_admin()){
		$pgcmd="home";
		//Get Page by param.
		if(isset($_GET['pgcmd'])){
			//Assign page cmd
			$pgcmd=$_GET['pgcmd'];
		}else{
			//Set for default page
			//$pgcmd="";
		}
		if(isset($_POST['save_tp_advance'])){
			if(isset($_POST['tpjqlib'])){
				
				if((get_option(USE_TIME_PICKS_JQ))!=FALSE){
					update_option(USE_TIME_PICKS_JQ, 'yes');
				}else{
					add_option(USE_TIME_PICKS_JQ,"yes");
				}
				
			}else{
				if((get_option(USE_TIME_PICKS_JQ))!=FALSE){
					update_option(USE_TIME_PICKS_JQ, 'no');
				}else{
					add_option(USE_TIME_PICKS_JQ,"no");
				}
			}
		}
		if($pgcmd=='home'){
				include_once('plugin_home_tpl.php');
		}
		 add_action( 'admin_footer', 'my_action_javascript' );
	}
}
// Render button on left 
function timePicksButton_render() {
	//$imgtp_btn_url=TP_PLUGIN_DIR_URL."images/book-online-btn-red-2.png";
	$imgtp_btn_url=TP_PLUGIN_DIR_URL."images/book-online-btn-red-right.png";
	
	$subDomainUrl=TP_PLUGIN_CREATE_ACCOUNT;
	
	$isAuth = FALSE;
	
	if(get_option(TIME_PICKS_SUBDOMAIN)!=FALSE || get_option(TIME_PICKS_EMAIL)!=FALSE || get_option(TIME_PICKS_PASSWD)!=FALSE){
		$retFromServer = tp_remote_auth(get_option(TIME_PICKS_EMAIL),get_option(TIME_PICKS_PASSWD));
		if(isset($retFromServer) && $retFromServer!=""){
				$retArrFromJson = json_decode($retFromServer, true);
				if(strtolower($retArrFromJson['subdomain'])!='error'){
					$subDomainUrl=TP_PLUGIN_HTTP."://www.".$retArrFromJson['subdomain'];
					$isAuth = TRUE;
				}else{
					$isAuth = FALSE;
					//$subDomainUrl=TP_PLUGIN_CREATE_ACCOUNT;
				}
		}else{
			//$subDomainUrl=TP_PLUGIN_CREATE_ACCOUNT;
		}
	}
	if($subDomainUrl==""){
		$subDomainUrl=TP_PLUGIN_CREATE_ACCOUNT;
	}
	
	if($isAuth){ 
		echo '<div id="timesPicksBtnFloat">
					<a href="'.$subDomainUrl.'?&TB_iframe=true&width=830&height=500"  class="thickbox"><img src="'.$imgtp_btn_url.'" alt="Book Online Now"/> </a>
			 </div>';
	}else{
		echo '
			<style type="text/css">
				#timePicksNotConnected{display:none;}
				#TB_title {background-color:#FF0F10 !important;color: #FFFFFF;font-weight: bold;}
				#timePicksMsg{ border: 2px solid #CCCCCC;border-radius: 6px;float: left;font-size: 16px;line-height: 20px;padding: 10px;margin-top:10px;}
				#TB_ajaxWindowTitle{
					background-color: #FF0F10;
					border-radius: 0 0px 10px 0;
					font-weight: bold;
					width: 91%;
					padding:7px !important;
				}
				#timePicksCreateAccount{
					background-color: #222222;
					border: 1px solid #FF0F10;
					border-radius: 5px;
					color: #FFFFFF !important;
					float: left;
					font-size: 14px;
					font-weight: bold;
					margin-top: 5px;
					padding: 5px;
					
					background-image: -webkit-gradient(linear,left top,left bottom,color-stop(0.26, #F22727),color-stop(1, #FC6072));
					background-image: -o-linear-gradient(bottom, #F22727 26%, #FC6072 100%);
					background-image: -moz-linear-gradient(bottom, #F22727 26%, #FC6072 100%);
					background-image: -webkit-linear-gradient(bottom, #F22727 26%, #FC6072 100%);
					background-image: -ms-linear-gradient(bottom, #F22727 26%, #FC6072 100%);
					background-image: linear-gradient(to bottom, #F22727 26%, #FC6072 100%);
				}
				#timePicksCreateAccount:hover{
					opacity:0.8;
					text-decoration:none;
				}
			</style>
			<script type="text/javascript">
				(function($) {				
						$(function(){
							var backupTb = \'<div id="timePicksMsg"> Your plugin is not linked to a timepicks account. Please link to your timepicks account from the wordpress admin panel. If you do not have a timepicks account, you can create one by   <br style="clear:both;"/><a  href="'.$subDomainUrl.'"  id="timePicksCreateAccount" target="_blank">Clicking Here</a></div>\';
							$("#timePicksCreateAccount").live(\'click\',function(event){
								$("#TB_overlay").remove();
								$("#TB_window").remove();
								/*
								$("#TB_window").remove();
								$("#timePicksNotConnected").hide();
								var tbUrl = "'.$subDomainUrl.'?&TB_iframe=true&width=830&height=500";
								$("body").append("<div id=\'TB_window\'></div>");
								tb_show("Create Account", tbUrl, "");
								event.preventDefault();*/
							});
								
							$("#timesPicksBtnFloat a").click(function(event){
								$("#timePicksNotConnected").html(backupTb);
								var tbUrl = "TB_inline?width=400&height=400&inlineId=timePicksNotConnected";
								tb_show("Please Connect To TimePicks", tbUrl, "");
								event.preventDefault();
							});	
					})
					
				})( jQuery );
			</script>
			 <div id="timePicksNotConnected" style="display:none;"></div>
			 <div id="timesPicksBtnFloat">
					<a href="#;"  class="" title="Please Connect To TimePicks"><img src="'.$imgtp_btn_url.'" alt="Book Online Now" /></a>
			 </div>';
	}
}
//Thick box 
function wptp_thickbox_scripts(){
	wp_enqueue_script('thickbox');
}
function wptp_thickbox_styles() {
	wp_enqueue_style('thickbox');
}
function wptp_timepicks_styles(){
	wp_register_style( 'wp-timepicks-plugin.css', TP_PLUGIN_DIR_URL . 'wp-timepicks-plugin.css', array(), '0.1' );
	wp_enqueue_style('wp-timepicks-plugin.css');
}

add_action('wp_footer', 'wptp_thickbox_scripts');
add_action('wp_footer', 'wptp_thickbox_styles');
add_action('wp_footer', 'wptp_timepicks_styles');
add_action('wp_footer', 'timePicksButton_render');
?>