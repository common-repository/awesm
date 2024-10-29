<?php

/**
 * Filters post content, inserting our buttons before, after or both per settings.
 * Only called if automatic insertion is enabled.
 * @param type $content 
 */
function awesm_filter_content($content,$context=null)
{
	// get post. Gross.
	global $post;
	$post_type = $post->post_type;
	if (!is_singular()) $context = 'index';
	
	$a = get_option('awesm_buttons_a_placement');
	$b = get_option('awesm_buttons_b_placement');
	
	// skip insertion if not enabled for this type
	if ($context == 'feed')
	{
		// feed only
		if (get_option('awesm_buttons_a_context_feed') != 'yes') $a = '_skip_';
		if (get_option('awesm_buttons_b_context_feed') != 'yes') $b = '_skip_';
	}
	else if ($context == 'index')
	{
		// index pages
		if (get_option('awesm_buttons_a_context_index') != 'yes') $a = '_skip_';
		if (get_option('awesm_buttons_b_context_index') != 'yes') $b = '_skip_';
	}
	else
	{
		// single page: posts, or Pages
		if ($post_type == 'post')
		{
			if (get_option('awesm_buttons_a_context_post') != 'yes') $a = '_skip_';
			if (get_option('awesm_buttons_b_context_post') != 'yes') $b = '_skip_';
		}
		elseif ($post_type == 'page')
		{
			if (get_option('awesm_buttons_a_context_page') != 'yes') $a = '_skip_';
			if (get_option('awesm_buttons_b_context_page') != 'yes') $b = '_skip_';
		}
	}
	
	// top of content
	// TODO: extra classes for left-right alignment, big/small sizing
	$top_state = array('top','both');
	$top_buttons = '';
	if (in_array($a,$top_state)) $top_buttons .= awesm_button_content('a');
	if (in_array($b,$top_state)) $top_buttons .= awesm_button_content('b');
	if (!empty($top_buttons)) {
		$styles_a = get_option('awesm_buttons_a_css','');
		$content = '<div id="awesm-top" class="awesm-buttons '.$styles_a.'">'. $top_buttons .'</div>' . $content;
	}
	
	// bottom of content
	$bottom_state = array('bottom','both');
	$bottom_buttons = '';
	if (in_array($a,$bottom_state)) $bottom_buttons .= awesm_button_content('a');
	if (in_array($b,$bottom_state)) $bottom_buttons .= awesm_button_content('b');
	if (!empty($bottom_buttons)) {
		$styles_b = get_option('awesm_buttons_b_css','');
		$content .= '<div id="awesm-bottom" class="awesm-buttons '.$styles_b.'">'. $bottom_buttons .'</div>';		
	}
	
	return $content;
}

/**
 * Filters feed content, following the same rules as above, just called at different time.
 */
function awesm_filter_content_feed($content)
{
	return awesm_filter_content($content,'feed');
}

/**
 * Generate code for the buttons for the specified position
 */
function awesm_button_content($set)
{
	// get the post (gross)
	global $post;
	$href = get_permalink($post->ID);
	error_log("post: " . print_r($post,true));
	
	// which buttons are we serving?
	$services = explode(',',get_option('awesm_buttons_'.$set,''));
	
	// supplementary settings
	$api_key = get_option('awesm_api_key','');
	$campaign = awesm_replace_wildcards( get_option('awesm_campaign','') );
	$notes = awesm_replace_wildcards( get_option('awesm_notes','') );
	$tool = get_option('awesm_buttons_'.$set.'_tool','');
	if (empty($tool)) {
		if ($set == 'a') {
			$tool = AWESM_WP_TOOL_A;
		} else {
			$tool = AWESM_WP_TOOL_B;
		}
	}

	// both like and send use this
	$facebook_app_id = get_option('awesm_facebook_app','');
	
    // building the category and tag JSON arrays if necessary
    $tag_for_tags=get_option('awesm_tag_for_tags');
    $tag_for_categories=get_option('awesm_tag_for_categories');
    if($tag_for_tags!='' and $tag_for_tags!='no_tag' and $tag_for_categories!='' and $tag_for_categories!='no_tag'){
        $categories = get_the_category(get_the_ID());
        if ($categories) {
            $categories_list = ', "awesm_'.$tag_for_categories.'" : [';
            for ($i = 0, $iMax = count($categories); $i < $iMax; $i++) {
                if($i == 0)
                    $categories_list .= '"'.html_entity_decode($categories[$i]->name).'"';
                else
                    $categories_list .= ', "'.html_entity_decode($categories[$i]->name).'"';
            }
            $categories_list .= ']';
        } else {
            $categories_list = '';    
        }

        $tags = get_the_tags();
        $tags = array_values($tags);    // WordPress inconveniently returns a non-sequential indexed array, so fix it
        if ($tags) {
            $tags_list = ', "awesm_'.$tag_for_tags.'" : [';
            for ($i = 0, $iMax = count($tags); $i < $iMax; $i++) {
                if($i == 0)
                    $tags_list .= '"'.html_entity_decode($tags[$i]->name).'"';
                else
                    $tags_list .= ', "'.html_entity_decode($tags[$i]->name).'"';
            }
            $tags_list .= ']';
        } else {
            $tags_list = null;    
        }
    }
    
	// start outputting buttons, as requested	
	$button_code = '';
	// this foreach preserves ordering
	foreach($services as $service)
	{
		switch($service)
		{
			case 'twitter':
				$twitter_count = awesm_get_option('awesm_twitter_count_'.$set,'horizontal');
				$twitter_via = get_option('awesm_twitter_via_'.$set,'');
				$twitter_related = get_option('awesm_twitter_related_'.$set,'');
				$twitter_text = awesm_replace_wildcards( awesm_get_option('awesm_twitter_text_'.$set,"%title%:") );
				$twitter_width = awesm_twitter_width($twitter_count);
				$twitter_height = awesm_twitter_height($twitter_count);
                $button_code .= '
				<div class="awesm-button-item awesm-button-twitter-tweet">
                    <div id="awesm_tweetbutton_'.$post->ID.'"></div>
                    <script  type="text/javascript">
                        AWESM.tweet({
                            "data-url": "'.$href.'",
                            "awesm_buttonid" : "awesm_tweetbutton_'.$post->ID.'",
                            "data-text" : "'.$twitter_text.'",
                            "data-count" : "'.$twitter_count.'",
                            "data-via" : "'.$twitter_via.'",
                            "data-related" : "'.$twitter_related.'",
                            "awesm_tool" : "'.$tool.'",
                            "awesm_campaign" : "'.$campaign.'",
                            "awesm_notes" : "'.$notes.'"
                            '.$tags_list.'
                            '.$categories_list.'
                        });
                    </script>
                </div>
				';
				break;
			case 'fblike':
				$fblike_layout = awesm_get_option('awesm_fblike_layout_'.$set,'button_count');
				$fblike_show_faces = awesm_get_option('awesm_fblike_show_faces_'.$set,'false');
				$fblike_width = awesm_fblike_width($fblike_layout,$set);
				$fblike_verb = awesm_get_option('awesm_fblike_verb_'.$set,'like');
				$fblike_color = awesm_get_option('awesm_fblike_color_'.$set,'');
				$fblike_font = awesm_get_option('awesm_fblike_font_'.$set,'lucida grande');
				$fblike_height = awesm_fblike_height($fblike_layout,$fblike_show_faces);
                $button_code .= '
				<div class="awesm-button-item awesm-button-facebook-like">
                    <div id="awesm_fblike_'.$post->ID.'"></div>
                    <script  type="text/javascript">
                        AWESM.fblike({
                            "href" : "'.$href.'",
                            "layout" : "'.$fblike_layout.'",
                            "width" : '.$fblike_width.',
                            "font" : "'.$fblike_font.'",
                            "show_faces" : "'.$fblike_show_faces.'",
                            "action" : "'.$fblike_verb.'",
                            "colorscheme" : "'.$fblike_color.'",
                            "app_id" : "'.$facebook_app_id.'",
                            "awesm_campaign" : "'.$campaign.'",
                            "awesm_tool" : "'.$tool.'",
                            "awesm_notes" : "'.$notes.'",
                            "awesm_buttonid" : "awesm_fblike_'.$post->ID.'"
                            '.$tags_list.'
                            '.$categories_list.'
                        });
                    </script>
                </div>
				';
				break;
			case 'fbshare':
				$fbshare_size = awesm_get_option('awesm_fbshare_size_'.$set,'small');
				$fbshare_title = awesm_replace_wildcards( awesm_get_option('awesm_fbshare_title_'.$set,'%title%') );
				$fbshare_color = awesm_get_option('awesm_fbshare_color_'.$set,'');
				$fbshare_bgcolor = awesm_get_option('awesm_fbshare_bgcolor_'.$set,'');
				$fbshare_width = awesm_fbshare_width($fbshare_size);
				$fbshare_height = awesm_fbshare_height($fbshare_size);
                $button_code .= '
				<div class="awesm-button-item awesm-button-facebook-share">
                    <div id="awesm_fbshare_'.$post->ID.'"></div>
                    <script  type="text/javascript">
                    AWESM.fbshare({
                        "href": "'.$href.'",
                        "size" : "'.$fbshare_size.'",
                        "title" : "'.$fbshare_title.'",
                        "color" : "'.$fbshare_color.'",
                        "awesm_buttonid" : "awesm_fbshare_'.$post->ID.'",
                        "bgcolor" : "'.$fbshare_bgcolor.'",
                        "awesm_key" : "'.$api_key.'",
                        "awesm_tool" : "'.$tool.'",
                        "awesm_campaign" : "'.$campaign.'",
                        "awesm_notes" : "'.$notes.'"
                        '.$tags_list.'
                        '.$categories_list.'
                    });
                    </script>
                </div>
				';
				break;
			case 'fbsend':
				if (!empty($facebook_app_id)) {
					$withApp = "app_id=\"$facebook_app_id\" ";
				} else {
					$withApp = '';
				}
				$fbsend_color = awesm_get_option('awesm_fbsend_color_'.$set,'light');
				$fbsend_font = awesm_get_option('awesm_fbsend_font_'.$set,'lucida grande');
				$button_code .= '
				<div class="awesm-button-item awesm-button-facebook-send">
                    <awesm:fbsend 
					    colorscheme="'.$fbsend_color.'" 
					    font="'.$fbsend_font.'" 
					    '.$withApp.'href="'.$href.'"></awesm:fbsend>
                </div>
				';	
				break;
            case 'gplus':
                $button_code .= '
                    <div class="awesm-button-item awesm-button-gplus">
                        <script type="text/javascript">
                            function awesm_gplus_share_'.$post->ID.'() {
                                AWESM.share.googleplus({
                                    "url":"'.$href.'"
                                    '.$tags_list.'
                                    '.$categories_list.'
                                });
                                return false;
                            }
                        </script>
                        <a class="awesm-service-gplus awesm-size-medium" href="'.$href.'" onclick="awesm_gplus_share_'.$post->ID.'();return false;"></a>
                    </div>
                ';
                break;
			case 'linkedin':
                $button_code .= '
                    <div class="awesm-button-item awesm-button-linkedin">
                        <script type="text/javascript">
                            function awesm_linkedin_share_'.$post->ID.'() {
                                AWESM.share.linkedin({
                                    "url":"'.$href.'"
                                    '.$tags_list.'
                                    '.$categories_list.'
                                });
                                return false;
                            }
                        </script>
                        <a class="awesm-service-linkedin awesm-size-medium" href="'.$href.'" onclick="awesm_linkedin_share_'.$post->ID.'();return false;"></a>
                    </div>
                ';
                break;
            case 'pinterest':
                // looking for an image
                $img_url=null;
                if(has_post_thumbnail($post->ID)){
                    $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
                    $img_url = $image[0];
                }else{
                    $img_url=awesm_catch_that_image($post);
                }
                if($img_url){
                    
                    $button_code .= '
                        <div class="awesm-button-item awesm-button-pinterest">
                            <script type="text/javascript">
                                function awesm_pinterest_share_'.$post->ID.'() {
                                    AWESM.share.pinterest({
                                        "image" : "'.$img_url.'",
                                        "url":"'.$href.'",
                                        "description" : "'.$post->post_title.'"
                                        '.$tags_list.'
                                        '.$categories_list.'
                                    });
                                    return false;
                                }
                            </script>
                            <a class="awesm-service-pinterest awesm-size-medium" href="'.$href.'" onclick="awesm_pinterest_share_'.$post->ID.'();return false;"></a>
                        </div>
                    ';
                }
                break;
            case 'email':
				$email_subject = awesm_replace_wildcards( awesm_get_option('awesm_email_subject_'.$set,'%title%') );
				$email_body = awesm_replace_wildcards( awesm_get_option('awesm_email_body_'.$set,"I thought you'd like to see this: ") . ' AWESM_URL');
				$button_code .= '
					<div class="awesm-button-item awesm-button-email">
                        <script type="text/javascript">
                            function awesm_email_share_'.$post->ID.'() {
                                AWESM.share.email({
                                    "url":"'.$href.'",
                                    "subject" : "'.$email_subject.'",
                                    "body" : "'.$email_body.'"
                                    '.$tags_list.'
                                    '.$categories_list.'
                                });
                                return false;
                            }
                        </script>
                        <a class="awesm-service-email" title="Share by email" onclick="awesm_email_share_'.$post->ID.'();return false;" href="#"></a>						
                    </div>
				';
				break;
			case 'custom':
				$raw_code = get_option('awesm_buttons_'.$set.'_custom');
				$button_code .= '<div class="awesm-button-item awesm-button-custom">'.awesm_replace_wildcards( $raw_code ).'</div>';
				break;
		}
	}
	
	return $button_code;
}

function awesm_get_plugin_path($subdir)
{
	return plugins_url(dirname(plugin_basename(__FILE__)).'/'.$subdir);
}

function awesm_replace_wildcards($text,$post=null)
{
	if(is_null($post)) {
		global $post;
	}
	
	$tags = get_the_tags($post->ID);
	$tagList = array();
	if (is_array($tags)) {
		foreach($tags as $t) {
			$tagList[] = $t->name;
		}
	}
	
	$categories = get_the_category($post->ID);
	$categoryList = array();
	if (is_array($categories)) {
		foreach(@$categories as $c) {
			$categoryList[] = $c->name;
		}
	}
	
	$author = get_the_author();	
	
	$href = get_permalink($post_id);
	
	$wildcards = array(
		'all_tags' => implode(',',$tagList),
		'first_tag' => @$tagList[0],
		'all_categories' => implode(',',$categoryList),
		'first_category' => @$categoryList[0],
		'author' => $author,
		'title' => $post->post_title,
		'date' => $post->post_date,
		'excerpt' => $post->post_excerpt,
		'first_image' => awesm_catch_that_image($post),
		'post_url' => $href		
	);
	
	// inefficient but say what
	foreach($wildcards as $search => $replace)
	{
		$text = str_replace('%'.$search.'%',$replace,$text);
	}
	
	return $text;
}

function awesm_twitter_width($layout)
{
	switch($layout)
	{
		case 'vertical':
		case 'none':
			return 55;
		case 'horizontal':
			return 110;
	}
}

function awesm_twitter_height($layout)
{
	switch($layout)
	{
		case 'vertical':
			return 62;
		case 'horizontal':
		case 'none':
			return 20;
	}
}

function awesm_fblike_width($layout,$set)
{
	$requested = awesm_get_option('awesm_fblike_width_'.$set);
	switch($layout)
	{
		case 'standard':
			$default = 450;
			$min = 255;
			break;
		case 'button_count':
			$default = 90;
			$min = 90;
			break;
		case 'box_count':
			$default = 55;
			$min = 55;
			break;
	}
	if (!empty($requested) && $requested > $min)
	{
		return $requested;
	}
	return $default;
}

function awesm_fblike_height($layout,$faces)
{
	switch($layout)
	{
		case 'standard':
			if ($faces == 'true') {
				return 80;
			} else {
				return 35;
			}
		case 'button_count':
			return 20;
		case 'box_count':
			return 65;
	}
}

function awesm_fbshare_width($layout)
{   
	if ($layout == 'small') { 
		return 53;  
	} else {
		return 80;
	}
}

function awesm_fbshare_height($layout)
{
	if ($layout == 'small') {
		return 18;
	} else {
		return 69;
	}
}

/**
 * Gets the first image found in the body of a post.
 * Modified from http://www.wprecipes.com/how-to-get-the-first-image-from-the-post-and-display-it
 * @param type $post
 * @return type 
 */
function awesm_catch_that_image($post)
{
  $first_img = '';
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  $first_img = $matches[1][0];
  return $first_img;
}

function awesm_get_excerpt($post){
    $excerpt = $post->post_content;
    $excerpt_length = 15; 
    $excerpt = strip_tags(strip_shortcodes($excerpt)); 
    $words = explode(' ', $excerpt, $excerpt_length + 1);
    if(count($words) > $excerpt_length){
        array_pop($words);
        array_push($words, '...');
        $excerpt = implode(' ', $words);
    }
    return $excerpt;
}