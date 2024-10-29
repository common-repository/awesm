<?php

/**
 * Print out the awe.sm plugin configuration screen
 */
function awesm_options()
{
	// must be prefixed with "awesm_" in HTML
	$option_fields = array(
        
        'api_key',
		'facebook_app',
        
        // categories and tags
        'tag_for_tags',
        'tag_for_categories',
        
        // address bar tracking
        'bar_tracking',
        		
        // buttons general config
        'buttons_a',
		'buttons_a_custom',
		'buttons_a_placement',
		'buttons_a_context_post',
		'buttons_a_context_index',
		'buttons_a_context_page',
		'buttons_a_context_feed',
		'buttons_a_tool',
		'buttons_a_css',
		'buttons_b',
		'buttons_b_custom',
		'buttons_b_placement',
		'buttons_b_context_post',
		'buttons_b_context_index',
		'buttons_b_context_page',
		'buttons_b_context_feed',
		'buttons_b_tool',
		'buttons_b_css',
		
        // button-specific config
		'twitter_via_a',
		'twitter_via_b',
		'twitter_related_a',
		'twitter_related_b',
		'twitter_count_a',
		'twitter_count_b',
		'twitter_text_a',
		'twitter_text_b',
		'fblike_layout_a',
		'fblike_layout_b',
		'fblike_width_a',
		'fblike_width_b',
		'fblike_show_faces_a',
		'fblike_show_faces_b',
		'fblike_verb_a',
		'fblike_verb_b',
		'fblike_color_a',
		'fblike_color_b',
		'fblike_font_a',
		'fblike_font_b',
		'fbsend_font_a',
		'fbsend_font_b',
		'fbsend_color_a',
		'fbsend_color_b',
		'email_subject_a',
		'email_subject_b',
		'email_body_a',
		'email_body_b',
		'fbshare_size_a',
		'fbshare_size_b',
		'fbshare_title_a',
		'fbshare_title_b',
		'fbshare_color_a',
		'fbshare_color_b',
		'fbshare_bgcolor_a',
		'fbshare_bgcolor_b',
		
        // autoposting config
		'subscription_key',
		'facebook_autopost',
		'facebook_publisher_id',
		'facebook_message',
		'facebook_link_name',
		'facebook_link_description',
		'facebook_link_picture',
		'facebook_link_caption',
		'facebook_link_source',
		'twitter_autopost',
		'twitter_publisher_id',
		'twitter_message'
	);
	
	?>
	<div id="icon-options-general" class="icon32"><br></div>	
	<h2>awe.sm Social Buttons</h2>

	<!-- general settings -->
	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
		
		<table class="form-table" id="awesm-basic-options">
			<tbody>
			<tr><td colspan="2"><h3>Application Settings</h3></td></tr>
            <tr>
				<td class="label"><label for="awesm_api_key">API Key</label></td>
				<td>
                    <input type="text" name="awesm_api_key" value="<?= get_option('awesm_api_key') ?>" size="64">
                    <span>Get your API key from your <a target="_blank" href="https://so.awe.sm/">project settings page</a>.</span>
                </td>
			</tr>
			<tr>
				<td class="label"><label for="awesm_facebook_app">Facebook App ID</label></td>
				<td>
                    <input type="text" name="awesm_facebook_app" value="<?= get_option('awesm_facebook_app') ?>">
                    <span>Optional</span>
                </td>
			</tr>
			<tr>
                <td></td>
                <td class="checkbox">
                    <input type="checkbox" name="awesm_bar_tracking" id="awesm_bar_tracking" <?php if(get_option('awesm_bar_tracking')) echo 'checked="checked"'; ?>>
                    <label for="awesm_bar_tracking">Enable Address Bar Tracking</label>
                </td>
            </tr>
            </tbody>
		</table>
		
        <table class="form-table" id="awesm-button-settings">
            <tbody>
            <tr>
                <td colspan="2">
                    <h3>Button Settings</h3>
                    <p>How should WordPress categories and tags be maped to awe.sm?</p>
                </td>
            </tr>
            <tr>
                <td class="label"><label for="awesm_tag_for_categories">Categories</label></td>
                <td>
                    <?php
                    $category=get_option('awesm_tag_for_categories');
                    ?>
                    <select name="awesm_tag_for_categories">
                        <option value="tag" <?php if($category=='tag') echo 'selected="selected"';?>>Tag</option>
                        <option value="tag_2" <?php if($category=='tag_2') echo 'selected="selected"';?>>Tag 2</option>
                        <option value="tag_3" <?php if($category=='tag_3') echo 'selected="selected"';?>>Tag 3</option>
                        <option value="tag_4" <?php if($category=='tag_4') echo 'selected="selected"';?>>Tag 4</option>
                        <option value="tag_5" <?php if($category=='tag_5') echo 'selected="selected"';?>>Tag 5</option>
                        <option value="no_tag" <?php if($category=='no_tag') echo 'selected="selected"';?>>No Tag</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label"><label for="awesm_tag_for_tags">Tags</label></td>
                <td>
                    <?php
                    $tag=get_option('awesm_tag_for_tags');
                    ?>
                    <select name="awesm_tag_for_tags">
                        <option value="tag" <?php if($tag=='tag') echo 'selected="selected"';?>>Tag</option>
                        <option value="tag_2" <?php if($tag=='tag_2') echo 'selected="selected"';?>>Tag 2</option>
                        <option value="tag_3" <?php if($tag=='tag_3') echo 'selected="selected"';?>>Tag 3</option>
                        <option value="tag_4" <?php if($tag=='tag_4') echo 'selected="selected"';?>>Tag 4</option>
                        <option value="tag_5" <?php if($tag=='tag_5') echo 'selected="selected"';?>>Tag 5</option>
                        <option value="no_tag" <?php if($tag=='no_tag') echo 'selected="selected"';?>>No Tag</option>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>

		<?php awesm_button_set_config('a'); ?>
		<?php awesm_button_set_config('b'); ?>

		<input type="hidden" name="action" value="update">
		<input type="hidden" name="page_options" value="awesm_<?= implode(',awesm_',$option_fields) ?>">
		<p class="submit"><input type="submit" value="Save settings" >
	</form>
	
	<?php
}

/**
 * Wordpress's get_option seems to have flaky defaults. Hack to fix.
 * @param type $name
 * @param type $default
 * @return type 
 */
function awesm_get_option($name,$default='')
{
	$value = get_option($name);
	if (empty($value)) $value = $default;
	return $value;
}

/**
 * Format the popup auth link. Used in 4 places.
 * @param type $project_name
 * @param type $link_text
 * @return type 
 */
function awesm_auth_screen_link($project_name,$link_text)
{
	$href = 'http://so.awe.sm/settings/projects/' . $project_name . '/sharing/facebook';
	$onclick = "window.open('$href','awesm-auth','width=800,height=560'); return false;";
	return '<a href="'. $href .'" onclick="' . $onclick . '" target="_blank">' . $link_text . '</a>';
}

/**
 * Print out the configuration options for each button set (a or b)
 * @param type $set 
 */
function awesm_button_set_config($set)
{
	// removed: fbsend
    $all_services = array(
		'twitter',
		'fblike',
		'fbshare',
        'gplus',
        'linkedin',
		'pinterest',
		'email',
		'custom'
	);
	$enabled_services = explode(',',get_option('awesm_buttons_'.$set,''));
	if (empty($enabled_services[0])) unset($enabled_services[0]);
	$available_services = array_diff($all_services,$enabled_services);
	?>
		<fieldset class="awesm-button-set">
			
            <h3>Button Set <?= strtoupper($set) ?> <span>drag to reorder</span></h3>
			
            <div class="awesm-service-list">
				<input type="hidden" name="awesm_buttons_<?= $set ?>" id="awesm_buttons_<?= $set ?>" value="<?= implode(',',$enabled_services) ?>">
				<div class="awesm-drag-container">
					<ul id="sortable-<?= $set ?>-available" class="awesm-connectedSortable-<?= $set ?>">
						<?php 
						foreach($all_services as $service){ ?>
							<li id="awesm-service-<?php echo $service;?>">
                                <?php echo awesm_service_label($service); ?>
                                <input type="checkbox" id="<?php echo $set ?>-service-<?php echo $service;?>" name="<?php echo $set ?>-service-<?php echo $service;?>" <?php if(in_array($service,$enabled_services)){?> checked="checked" <?php } ?> />
                            </li>
						<?php } ?>
					</ul>
				</div>
                
                <div class="awesm-tabs-nav">
                    <div class="awesm-config-twitter-<?= $set ?>"><a href="#">Twitter</a></div>
                    <div class="awesm-config-fblike-<?= $set ?>"><a href="#">FB Like</a></div>
                    <div class="awesm-config-fbshare-<?= $set ?>"><a href="#">FB Share</a></div>
                    <div class="awesm-config-gplus-<?= $set ?>"><a href="#">G+</a></div>
                    <div class="awesm-config-linkedin-<?= $set ?>"><a href="#">Linkedin</a></div>
                    <div class="awesm-config-pinterest-<?= $set ?>"><a href="#">Pinterest</a></div>
                    <div class="awesm-config-email-<?= $set ?>"><a href="#">Email</a></div>
                    <div class="awesm-config-custom-<?= $set ?>"><a href="#">Custom</a></div>
                </div>
                <div class="awesm-tabs">
				    <div class="awesm-config-service" id="awesm-config-twitter-<?= $set ?>">
					    <table class="form-table">
					        <tr>
						        <td class="label"><label for="<?php echo 'awesm_twitter_count_'.$set;?>">Button type</label></td>
						        <td><?php
							        awesm_output_select(
								        'awesm_twitter_count_'.$set,
								        array(
									        'horizontal' => "Horizontal with count",
									        'vertical' => "Vertical with count",
									        'none' => "No count"									
								        ),
								        get_option('awesm_twitter_count_'.$set)
							        );
						        ?></td>
					        </tr>
					        <tr>
						        <td class="label"><label for="awesm_twitter_via_<?= $set ?>">"via" username</label></td>
						        <td><input type="text" name="awesm_twitter_via_<?= $set ?>" value="<?= get_option('awesm_twitter_via_'.$set) ?>"></td>
					        </tr>
					        <tr>
						        <td class="label"><label for="awesm_twitter_related_<?= $set ?>">"related" usernames</label></td>
						        <td>
                                    <input type="text" name="awesm_twitter_related_<?= $set ?>" value="<?= get_option('awesm_twitter_related_'.$set) ?>">
                                    <span>comma-separated</span>
                                </td>
					        </tr>
					        <tr>
						        <td class="label"><label for="awesm_twitter_text_<?= $set ?>">Pre-filled tweet message</label></td>
						        <td>
                                    <input type="text" name="awesm_twitter_text_<?= $set ?>" value="<?= get_option('awesm_twitter_text_'.$set) ?>">
                                    <span>blank for title</span>
                                </td>
					        </tr>
					    </table>
				    </div>
				    <div class="awesm-config-service" id="awesm-config-fblike-<?= $set ?>">
					    <table class="form-table">
					        <tr>
						        <td class="label"><label for="<?php echo 'awesm_fblike_layout_'.$set;?>">Layout</label></td>
						        <td><?php
							        awesm_output_select(
								        'awesm_fblike_layout_'.$set,
								        array(
									        'button_count' => 'Button count (small)',
									        'box_count' => 'Box count (large)',
									        'standard' => 'Standard'
								        ),
								        get_option('awesm_fblike_layout_'.$set)
							        );
						        ?></td>
					        </tr>
					        <tr>
						        <td class="label"><label for="awesm_fblike_show_faces_<?= $set ?>">Show faces</label></td>
						        <td>
                                    <input type="checkbox" value="true" name="awesm_fblike_show_faces_<?= $set ?>"> (Standard layout only)
                                </td>
					        </tr>
					        <tr>
						        <td class="label">Width</td>
						        <td>
                                    <input type="text" name="awesm_fblike_width_<?= $set ?>" value="<?= get_option('awesm_fblike_width_'.$set,'') ?>">
                                    <span>optional</span>
                                </td>
					        </tr>
                            <tr>
						        <td class="label"><label for="<?php echo 'awesm_fblike_verb_'.$set;?>">Verb</label></td>
						        <td><?php
							        awesm_output_select(
								        'awesm_fblike_verb_'.$set,
								        array(
									        'like' => "Like",
									        'recommend' => "Recommend"
								        ),
								        get_option('awesm_fblike_verb_'.$set)
							        );
						        ?></td>
					        </tr>
					        <tr>
						        <td class="label"><label for="<?php echo 'awesm_fblike_color_'.$set;?>">Color scheme</label></td>
						        <td><?php
							        awesm_output_select(
								        'awesm_fblike_color_'.$set,
								        array(
									        'light' => "Light",
									        'dark' => "Dark"
								        ),
								        get_option('awesm_fblike_color_'.$set)
							        );
						        ?></td>
					        </tr>
					        <tr>
						        <td class="label"><label for="<?php echo 'awesm_fblike_font_'.$set;?>">Font</label></td>
						        <td><?php
							        awesm_output_select(
								        'awesm_fblike_font_'.$set,
								        array(
									        'lucida grande' => "Lucida Grande",
									        'arial' => "Arial",
									        'segoe ui' => "Segoe UI",
									        'tahoma' => "Tahoma",
									        'trebuchet ms' => "Trebuchet MS",
									        'verdana' => "Verdana"
								        ),
								        get_option('awesm_fblike_font_'.$set)
							        );
                                    ?>
                                    <span>optional</span>
						        </td>
					        </tr>
					    </table>
				    </div>
				    <div class="awesm-config-service" id="awesm-config-fbshare-<?= $set ?>">
					    <table  class="form-table">
					        <tr>
						        <td class="label"><label for="<?php echo 'awesm_fbshare_size_'.$set;?>">Size</label></td>
						        <td><?php
							        awesm_output_select(
								        'awesm_fbshare_size_'.$set,
								        array(
									        'small' => 'small',
									        'large' => 'large'
								        ),
								        get_option('awesm_fbshare_size_'.$set)
							        );
						        ?></td>
					        </tr>
					        <tr>
						        <td class="label"><label for="awesm_fbshare_title_<?= $set ?>">Title</label></td>
						        <td>
                                    <input type="text" name="awesm_fbshare_title_<?= $set ?>" value="<?= get_option('awesm_fbshare_title_'.$set,'') ?>">
                                    <span>optional</span>
                                </td>
					        </tr>
					        <tr>
						        <td class="label"><label for="awesm_fbshare_color_<?= $set ?>">Text color</label></td>
						        <td>
                                    <input type="text" name="awesm_fbshare_color_<?= $set ?>" value="<?= get_option('awesm_fbshare_color_'.$set,'') ?>">
                                    <span>optional</span>
                                </td>
					        </tr>
					        <tr>
						        <td class="label"><label for="awesm_fbshare_bgcolor_<?= $set ?>">Background color</label></td>
						        <td>
                                    <input type="text" name="awesm_fbshare_bgcolor_<?= $set ?>" value="<?= get_option('awesm_fbshare_bgcolor_'.$set,'') ?>">
                                    <span>optional</span>
                                </td>
					        </tr>
					    </table>
				    </div>
                    <div class="awesm-config-service" id="awesm-config-gplus-<?= $set ?>">
                        <h4>There are no settings for Google+</h4>
                    </div>
				    <div class="awesm-config-service" id="awesm-config-linkedin-<?= $set ?>">
                        <h4>There are no settings for LinkedIn</h4>
                    </div>
                    <div class="awesm-config-service" id="awesm-config-pinterest-<?= $set ?>">
                        <h4>There are no settings for Pinterest</h4>
                    </div>
                    <div class="awesm-config-service" id="awesm-config-email-<?= $set ?>">
					    <table class="form-table">
					        <tr>
						        <td colspan="2">
							        Supported wildcards: %author%, %title%, %date%, %all_categories%, %first_category%, %all_tags%, %first_tag%
						        </td>
					        </tr>
					        <tr>
						        <td class="label"><label for="awesm_email_subject_<?= $set ?>">Subject</label></td>
						        <td><input type="text" name="awesm_email_subject_<?= $set ?>" value="<?= get_option('awesm_email_subject_'.$set,'') ?>"></td>
					        </tr>
					        <tr>
						        <td class="label"><label for="awesm_email_body_<?= $set ?>">Body</label></td>
						        <td><input type="text" name="awesm_email_body_<?= $set ?>" value="<?= get_option('awesm_email_body_'.$set,'') ?>"></td>
					        </tr>
					    </table>
				    </div>
				    <div class="awesm-config-service" id="awesm-config-custom-<?= $set ?>">
					    <table class="form-table">
					        <tr>
						        <td class="label"><label for="awesm_buttons_<?= $set ?>_custom">Hint: use the wildcard %post_url% for the "href=" value</label></td>
						        <td><textarea name="awesm_buttons_<?= $set ?>_custom"><?= get_option('awesm_buttons_'.$set.'_custom') ?></textarea></td>
					        </tr>
					    </table>
				    </div>                  
                </div><!-- awesm-tabs -->
			</div>
			
			<script>
			jQuery(function() {
				jQuery( "#sortable-<?= $set ?>-available, #sortable-<?= $set ?>-enabled" ).sortable({
					connectWith: ".awesm-connectedSortable-<?= $set ?>",
					stop: function(event,ui) {
						//awesm_update_services('<?= $set ?>');
					}
				}).disableSelection();
			});
			</script>
			
			<div class="awesm-button-layout">
				<?php
				$selected_placement = get_option('awesm_buttons_'.$set.'_placement');
				?>
				<div class="awesm-set-placement">
					<table class="form-table">
                        <tr>
                            <td class="label"><label for="<?php echo 'awesm_buttons_'.$set.'_placement';?>">Placement</label></td>
                            <td>
                                <?php awesm_output_select(
                                    'awesm_buttons_'.$set.'_placement',
                                    array(
                                        'top' => 'at top',
                                        'bottom' => 'at bottom',
                                        'both' => 'at top and bottom',
                                        'manual' => 'manually',
                                        'none' => 'do not show these buttons'
                                    ),
                                    $selected_placement
                                ); ?>
                                
                                <div class="manual_instructions">
                                    <h4>To manually place buttons in your template,  add the following code:</h4>
                                    <code><?php echo htmlentities("<?php echo awesm_button_content('a'); ?>"); ?></code>
                                    <br>
                                    <code><?php echo htmlentities("<?php echo awesm_button_content('b'); ?>"); ?></code>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><label for="awesm_buttons_<?php echo $set;?>_toolkit">Tool Key</label></td>
                            <td>
                                <input type="text" name="awesm_buttons_<?= $set ?>_tool" value="<?= get_option('awesm_buttons_'.$set.'_tool') ?>">
                                <span>optional</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><label for="awesm_buttons_<?= $set ?>_css">Custom CSS</label></td>
                            <td><input type="text" name="awesm_buttons_<?= $set ?>_css" value="<?= get_option('awesm_buttons_'.$set.'_css') ?>"></td>
                        </tr>
                    </table>
				</div>
				<div class="awesm-set-context">
					<?php
					$context_post = (get_option('awesm_buttons_'.$set.'_context_post') == 'yes') ? "checked" : "";
					$context_page = (get_option('awesm_buttons_'.$set.'_context_page') == 'yes') ? "checked" : "";
					$context_index = (get_option('awesm_buttons_'.$set.'_context_index') == 'yes') ? "checked" : "";
					$context_feed = (get_option('awesm_buttons_'.$set.'_context_feed') == 'yes') ? "checked" : "";
					?>
					<h4>On these sections:</h4>
					<ul>
						<li><label><input type="checkbox" <?= $context_index ?> value="yes" name="awesm_buttons_<?= $set ?>_context_index"> Index pages</label></li>
						<li><label><input type="checkbox" <?= $context_post ?> value="yes" name="awesm_buttons_<?= $set ?>_context_post"> Posts</label></li>
						<li><label><input type="checkbox" <?= $context_page ?> value="yes" name="awesm_buttons_<?= $set ?>_context_page"> Pages</label></li>
						<li><label><input type="checkbox" <?= $context_feed ?> value="yes" name="awesm_buttons_<?= $set ?>_context_feed"> Feed entries</label></li>
					</ul>
				</div>
			</div>
		</fieldset>
	<?php
}

function awesm_output_select($name,$options,$selected=null)
{
	echo "<select name=\"$name\">\n";
	foreach($options as $value => $label)
	{
		$isSelected = '';
		if ($value == $selected) {
			$isSelected = "selected";
		}
		echo "<option value=\"$value\" $isSelected>$label</option>\n";
	}
	echo "</select>\n";
}

/**
 * Return a friendly name for the service label
 * @param type $service
 * @return type 
 */
function awesm_service_label($service)
{
	switch($service)
	{
		case 'twitter': return "Twitter";
		case 'fblike': return "Facebook Like";
		case 'fbshare': return "Facebook Share";
        case 'fbsend': return "Facebook Send";
        case 'gplus': return "G+";
        case 'linkedin': return "LinkedIn";
		case 'pinterest': return "Pinterest";
		case 'email': return "Email";
		case 'custom': return "Custom";
	}
}

function awesm_get_project($api_key)
{
	$url = AWESM_WS_API . '/accounts/' . $api_key . '/show?subscription_key=' . get_option('awesm_subscription_key','') . '&application_key=' . AWESM_WP_PLUGIN_APP_KEY;
	
	$response = awesm_make_api_call($url);
	
	return $response['account'];
}

/**
 * Fetch the list of authorized publisher links for an account
 * Authorized by subscription key
 * @return type 
 */
function awesm_get_publisher_list()
{
	$subscription_key = get_option('awesm_subscription_key',false);
	$api_key = get_option('awesm_api_key',false);
	
	if (!$subscription_key || !$api_key) return false;
	
	$url = AWESM_WS_API . '/publishers/list/' . $api_key . '?subscription_key=' . $subscription_key;

	$response = awesm_make_api_call($url);
	
	if ($response)
	{
		// split into facebook and twitter
		$publishers = array(
			'twitter' => array(),
			'facebook' => array()
		);
		foreach($response['publishers'] as $publisher)
		{
			$publishers[$publisher['channel']][] = $publisher;
		}
		return $publishers;
	}
	return false;
}

/**
 * Call the awe.sm API and parse the response
 * @param type $url
 * @return type 
 */
function awesm_make_api_call($url,$response_key='response')
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_USERAGENT, 'awesm-wordpress-plugin');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt($ch, CURLOPT_MAXREDIRS, 3 );
	$response = curl_exec($ch);
	curl_close($ch);
	
	if (!empty($response))
	{
		$result = json_decode($response,true);
		if (is_array($result))
		{
			if ($response_key && array_key_exists($response_key,$result))
			{
				return $result[$response_key];
			}
			else
			{
				return $result;
			}
		}
		else
		{
			return false;
		}
	}
	
	return false;
}