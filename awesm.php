<?php
/**
* Plugin Name: awe.sm for WordPress
* Plugin URI: http://totally.awe.sm/
* Description: Enable sharing on your blog, awe.sm-style!
* Version: 0.6
*
* Author: awe.sm
* Author URI: http://totally.awe.sm/
*/

// We use curl. If you don't have php5-curl installed, this pretends to be it.
require_once('libcurlemu.inc.php');

define('AWESM_WS_API','http://api.awe.sm');
define('AWESM_CREATE_API','http://api.awe.sm');
define('AWESM_WP_PLUGIN_APP_KEY','app-ZR8g9N');
define('AWESM_WP_TOOL_AUTOPOST','ahU28x');
define('AWESM_WP_TOOL_A','bmuLX5');
define('AWESM_WP_TOOL_B','Dmopck');

/**
 * Immediately renders the awe.sm buttons for the specified position.
 * If you're editing your own template, call this function.
 * By default the buttons selected for the "top" position are rendered.
 * @param type $position 
 */
function awesm_buttons($position='a')
{
	echo awesm_button_content($position);	
}

/**
 * Activate plugin by setting our default options
 */
register_activation_hook( __FILE__, 'awesm_activation_hook' );
function awesm_activation_hook(){
	// this re-sets options to default on insert, so don't do it for everything
	//add_option('awesm_buttons_a','twitter,fblike,email');
	//add_option('awesm_buttons_a_placement','top');
	add_option('awesm_buttons_a_context_post','yes');
	add_option('awesm_buttons_a_context_index','yes');
	add_option('awesm_buttons_a_context_feed','no');
}

/**
 * Initialize our plugin, no matter what context it's in.
 */
add_action('init', 'awesm_init');
function awesm_init()
{
	// In admin mode, register our options screen and any extra stuff that has to happen
	if (is_admin()) {
		add_action('admin_init', 'awesm_admin_init');		
		add_action('admin_menu', 'awesm_admin_menu');
	}

	// Bring in our scripts and stylesheets
	add_action('wp_print_styles', 'awesm_styles');
	
	// add our filter if automatic insertion is enabled
	if (awesm_automatically_insert_buttons()) {
		add_filter('the_content', 'awesm_filter_content');
		add_filter('the_content_feed', 'awesm_filter_content_feed');
		// do not filter excerpts
		add_filter('get_the_excerpt', 'awesm_remove_content_filter', 9);
	}
	
	// add our publishers if autoposting is enabled
	if (get_option('awesm_twitter_autopost') == 'yes') {
		add_action('publish_post', 'awesm_twitter_autopost');
	}
	if (get_option('awesm_facebook_autopost') == 'yes') {
		add_action('publish_post', 'awesm_facebook_autopost');
	}
	
}

/**
 * Determines whether or not we should be automatically inserting buttons
 */
function awesm_automatically_insert_buttons()
{
	$a = get_option('awesm_buttons_a_placement');
	$b = get_option('awesm_buttons_b_placement');
	
	$enabled_states = array('top','bottom','both');
	
	if (in_array($a,$enabled_states) || in_array($b,$enabled_states)) {
		return true;
	}
	
	return false;
}

/**
 * WP-friendly way of loading our script once per page
 */
function awesm_scripts() {
    $api_key = get_option('awesm_api_key');
    if($api_key!=''){
        $address_bar_tracking=get_option('awesm_bar_tracking');
        
        ?>
        <script type="text/javascript">
            var AWESM = AWESM || {};
            AWESM.api_key = '<?php echo $api_key; ?>';
            
            <?php if($address_bar_tracking!=''){ ?>
                AWESM.addressbar = true;
            <?php } ?>
            
            AWESM.shares = {
                <?php if(is_user_logged_in()){ global $current_user; get_currentuserinfo();?>
                    
                    'user_id' : '<?php echo $current_user->ID;?>',
                    'user_id_username' : '<?php echo $current_user->user_login;?>'
                    
                <?php } ?>    
            };
        </script>
        <script src="//widgets.awe.sm/v3/widgets.js?key=<?php echo $api_key; ?>"></script>
        <?php 
    }
}
add_action('wp_head', 'awesm_scripts'); 

/**
 * Bring in our style sheets
 */
function awesm_styles()
{
	$url = plugins_url('css/awesm.css', __FILE__);
	wp_register_style('awesm-style', $url);
	wp_enqueue_style( 'awesm-style');
}

/**
 * Initialize extra data needed by administrators
 */
function awesm_admin_init()
{
	wp_register_style( 'awesm-admin-style', plugins_url('css/awesm-admin.css', __FILE__) );
	wp_enqueue_style( 'awesm-admin-style' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_register_script( 'awesm', plugins_url('js/awesm-admin.js', __FILE__ ) );
	wp_enqueue_script('awesm');
	
}

/**
 * Register our options screen and menu item
 */
function awesm_admin_menu()
{
	// admin screen
	add_menu_page('awe.sm Sharing', 'awe.sm Sharing', 'manage_options', __FILE__, 'awesm_options');
}

/**
 * Pseudo-filter that actually just prevents our normal filter from running on excerpts
 */
function awesm_remove_content_filter($content) {
	remove_action('the_content', 'awesm_filter_content');
	return $content;
}

/**
 * Send the current post to Twitter using awe.sm's APIs
 * @param type $post_id 
 */
function awesm_twitter_autopost($post_id)
{
	error_log("Twitter autopost triggered for post $post_id");
	$post = get_post($post_id);
	$href = get_permalink($post_id);
	$api_key = get_option('awesm_api_key','');
	
	// create the link
	$awesm_url = awesm_create($api_key,$href,'twitter');
	
	// publish our formatted message including our link
	$text = get_option('awesm_twitter_message','');
	$publish_query = awesm_http_build_query(array(
		'subscription_key' => get_option('awesm_subscription_key',''),
		'publisher_id' => get_option('awesm_twitter_publisher_id',''),
		'status' => awesm_replace_wildcards($text,$post) . ' ' . $awesm_url['awesm_url']
	));
	$publish_url = AWESM_WS_API . '/publishers/publish/' . $api_key . '?' . $publish_query;
	
	$response = awesm_make_api_call($publish_url);
	$twitter_data = $response['response'];
	
	// update the awesm_url with reach metadata
	awesm_update($api_key,$awesm_url,$twitter_data['id_str'],$twitter_data['created_at'],$twitter_data['user']['followers_count']);
	
}

/**
 * Assemble metadata and send the current post to Facebook using awe.sm's APIs
 * @param type $post_id 
 */
function awesm_facebook_autopost($post_id)
{
	error_log("Facebook autopost triggered for post $post_id");
	$post = get_post($post_id);
	$href = get_permalink($post_id);
	$api_key = get_option('awesm_api_key','');
	
	// create the link
	$awesm_url = awesm_create($api_key,$href,'facebook');
	
	// this gets sent as the destination URL
	$message = get_option('awesm_facebook_message','');
	$link_name = get_option('awesm_facebook_link_name','');
	$link_description = get_option('awesm_facebook_link_description','');
	$link_picture = get_option('awesm_facebook_link_picture');
	$link_caption = get_option('awesm_facebook_link_caption');
	$link_source = get_option('awesm_facebook_link_source');
	$publish_query = awesm_http_build_query(array(
		'subscription_key' => get_option('awesm_subscription_key',''),
		'publisher_id' => get_option('awesm_facebook_publisher_id',''),
		'status' => awesm_replace_wildcards($message,$post),
		'link' => $awesm_url['awesm_url'],
		'link_name' => awesm_replace_wildcards($link_name,$post),
		'link_description' => awesm_replace_wildcards($link_description,$post),
		'link_picture' => awesm_replace_wildcards($link_picture,$post),
		'link_caption' => awesm_replace_wildcards($link_caption,$post),
		'link_source' => awesm_replace_wildcards($link_source,$post)
	));
	$publish_url = AWESM_WS_API . '/publishers/publish/' . $api_key . '?' . $publish_query;
	
	$response = awesm_make_api_call($publish_url);
	$facebook_data = $response['response'];
	
	// update the awesm_url with reach metadata
	awesm_update($api_key,$awesm_url,$facebook_data['id'],$awesm_url['created_at'],$facebook_data['friends']);
	
}

/**
 * Create an awe.sm URL with minimal metadata
 * @param type $api_key
 * @param type $href
 * @param type $channel
 * @return type 
 */
function awesm_create($api_key,$href,$channel)
{
	// share URL
	$query = awesm_http_build_query(array(
		'v' => 3,
		'key' => $api_key,
		'url' => $href,
		'channel' => $channel,
		'tool' => AWESM_WP_TOOL_AUTOPOST
	));
	$url = AWESM_CREATE_API . '/url.json?' . $query;
	
	error_log("$channel create url: $url");
	
	$response = awesm_make_api_call($url,false);

	return $response;
}

/**
 * Update an awe.sm URL with post metadata
 * @param type $api_key
 * @param type $awesm_url
 * @param type $service_postid
 * @param type $service_postid_shared_at
 * @param type $service_postid_reach
 * @return type 
 */
function awesm_update($api_key,$awesm_url,$service_postid,$service_postid_shared_at,$service_postid_reach)
{
	$update_query = awesm_http_build_query(array(
		'v' => 3,
		'key' => $api_key,
		'tool' => AWESM_WP_TOOL_AUTOPOST,
		'service_postid' => $service_postid,
		'service_postid_shared_at' => $service_postid_shared_at,
		'service_postid_reach' => $service_postid_reach
	));	
	$update_url = AWESM_CREATE_API . '/url/update/' . $awesm_url['awesm_id'] . '.json?' . $update_query;
	
	error_log("{$awesm_url['channel']} update url: $update_url");
	
	$response = awesm_make_api_call($update_url);
	
	return $response;
	
}

// exactly like PHP 5.3's http_build_query except it encodes stuff with RFC3986
// this becomes a parameter in php 5.4
function awesm_http_build_query($data, $prefix='', $sep='', $key='') { 
	$ret = array(); 
	foreach ((array)$data as $k => $v) { 
		if (is_int($k) && $prefix != null) { 
			$k = urlencode($prefix . $k); 
		} 
		if ((!empty($key)) || ($key === 0))  $k = $key.'['.rawurlencode($k).']'; 
		if (is_array($v) || is_object($v)) { 
			array_push($ret, awesm_http_build_query($v, '', $sep, $k)); 
		} else { 
			array_push($ret, $k.'='.rawurlencode($v)); 
		} 
	} 
	if (empty($sep)) $sep = ini_get('arg_separator.output'); 
	return implode($sep, $ret); 
}// http_build_query 

require_once('awesm-button-html.php');
require_once('awesm-options.php');