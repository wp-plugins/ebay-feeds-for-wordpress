<?php
/*
Plugin Name:  Ebay Feeds for WordPress
Plugin URI:   http://bloggingdojo.com/wordpress-plugins/ebay-feeds-for-wordpress/
Description:  Parser of ebay RSS feeds to display on Wordpress posts, widgets and pages.
Version:      0.6.1
Author:       Rhys Wynne
Author URI:   http://bloggingdojo.com/

Copyright (C) 2011, Rhys Wynne
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
Neither the name of Rhys Wynne nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

Credit goes to Magpie RSS for RSS to PHP integration: http://magpierss.sourceforge.net/

*/

register_activation_hook(__FILE__,'ebay_feeds_for_wordpress_install');

function ebay_feeds_for_wordpress($url = "", $num = "") {
$link = get_option("ebay-feeds-for-wordpress-link");

if ($url == "")
{
$url = get_option('ebay-feeds-for-wordpress-default');
}

if ($num == "")
{
$num = get_option('ebay-feeds-for-wordpress-default-number');
}

include_once(ABSPATH . WPINC . '/rss.php');
$rss = fetch_feed($url);
$rss_items = $rss->get_items(0, $num);

echo "<div class='ebayfeed'>";
foreach ($rss_items as $item ) {
echo "<h4 class='ebayfeedtitle'><a href='".$item->get_permalink()."'  class='ebayfeedlink'>".$item->get_title()."</a></h4>";
echo $item->get_description();
}
echo "</div>";
if ($link == 1)
{
	echo "<a href='http://bloggingdojo.com/wordpress-plugins/ebay-feeds-for-wordpress/'>eBay Feeds for WordPress</a> by <a href='http://www.bloggingdojo.com'>The Blogging Dojo</a><br/><br/>";
}
}

if ( is_admin() ){ // admin actions

  add_action('admin_menu', 'ebay_feeds_for_wordpress_menus');
  add_action( 'admin_init', 'ebay_feeds_for_wordpress_options_process' );
  
}

function ebay_feeds_for_wordpress_menus() {

  add_options_page('eBay Feeds Options', 'ebay Feeds For Wordpress', 8, 'ebayfeedforwordpressoptions', 'ebay_feeds_for_wordpress_options');

}

function ebay_feeds_for_wordpress_options() {

  echo '<div class="wrap">';
  echo '<h2>eBay Feeds For Wordpress Options</h2>'; ?>
  
 <form method="post" action="options.php">

  <?php wp_nonce_field('update-options'); ?>

  <?php settings_fields( 'ebay-feeds-for-wordpress-group' ); ?>
  
  <table class="form-table">

  <tbody>

<tr valign="top">

  <th scope="row" style="width:400px">Default eBay Feed:</th>

  <td><input type="text" name="ebay-feeds-for-wordpress-default" class="regular-text code" value="<?php echo get_option('ebay-feeds-for-wordpress-default'); ?>" /></td>

</tr>

<tr valign="top">

  <th scope="row" style="width:400px">Default Number of Items To Show:</th>

  <td><input type="text" name="ebay-feeds-for-wordpress-default-number" class="regular-text code" value="<?php echo get_option('ebay-feeds-for-wordpress-default-number'); ?>" /></td>

</tr>

<tr valign="top">

<th scope="row" style="width:400px"><label>Link to us (optional, but appreciated)</label></th>

<td><input type="checkbox" name="ebay-feeds-for-wordpress-link" value="1"

<?php 

if (get_option('ebay-feeds-for-wordpress-link') == 1) { echo "checked"; } ?>

></td>

</tr>


  </tbody>

</table>



<input type="hidden" name="action" value="update" />

<input type="hidden" name="page_options" value="ebay-feeds-for-wordpress-default" />

<p class="submit">

<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />

</p>

</form>

<h3>Donate</h3>
<p>I am quite lovely, and provide this software for free. If you fancy being equally, if not more lovely, then please donate to the upkeep of the plugin. I treat all that like us equally, but those who donate we <em>may</em> answer their questions quicker and we <em>may</em> take their feature requests more seriously. Just saying.</p>
<div style="text-align: center;">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick" />
<input type="hidden" name="hosted_button_id" value="5T2Z52C3R6DD4" />
<input type="image" name="submit" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" alt="PayPal � The safer, easier way to pay online." /> <img src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" alt="" width="1" height="1" border="0" /></form>
</div>

  
  <?php echo '</div>';

}


function ebay_feeds_for_wordpress_options_process() { // whitelist options

  register_setting( 'ebay-feeds-for-wordpress-group', 'ebay-feeds-for-wordpress-default' );
  register_setting( 'ebay-feeds-for-wordpress-group', 'ebay-feeds-for-wordpress-default-number' );
  register_setting( 'ebay-feeds-for-wordpress-group', 'ebay-feeds-for-wordpress-link' );

}


	// Check to see required Widget API functions are defined...

	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )

		return; // ...and if not, exit gracefully from the script.



	// This function prints the sidebar widget--the cool stuff!
class ebay_feeds_for_wordpress_Widget_class extends WP_Widget {
	
	function ebay_feeds_for_wordpress_Widget_class() {
		parent::WP_Widget('ebay_feeds_for_wordpress_widget', 'eBay Feeds For Wordpress', array('description' => 'Widget for an eBay Feed'));	
	}

	
	function widget($args, $instance) {



		// $args is an array of strings which help your widget
		// conform to the active theme: before_widget, before_title,
		// after_widget, and after_title are the array keys.

		extract($args);
		extract($args, EXTR_SKIP);

		$title = empty($instance['widget_title']) ? 'eBay Items' : apply_filters('widget_title', $instance['widget_title']);
		$text = empty($instance['widget_text']) ? 'Here are our eBay auctions' : $instance['widget_text'];
		if (empty($instance['widget_num']) && !is_numeric($instance['widget_num']))
		{
			$num = 3;
		}
		else
		{
		 $num = $instance['widget_num'];
		}
		$feed = empty($instance['widget_feed']) ? get_option('ebay-feeds-for-wordpress-default') : $instance['widget_feed'];
		
		echo $before_widget;

		echo $before_title . $title . $after_title;

		echo $text;

		ebay_feeds_for_wordpress($feed, $num);

		echo $after_widget;


	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['widget_title'] = strip_tags($new_instance['widget_title']);
		$instance['widget_text'] = strip_tags($new_instance['widget_text']);
		$instance['widget_num'] = strip_tags($new_instance['widget_num']);
		$instance['widget_feed'] = strip_tags($new_instance['widget_feed']);
		return $instance;
	}
 
	/**
	 *	admin control form
	 */	 	
	function form($instance) {
		$default = 	array( 'title' => __('eBay Items') );
		$instance = wp_parse_args( (array) $instance, $default );
 
		$title_id = $this->get_field_id('widget_title');
		$title_name = $this->get_field_name('widget_title');
		$text_id = $this->get_field_id('widget_text');
		$text_name = $this->get_field_name('widget_text');
		$num_id = $this->get_field_id('widget_num');
		$num_name = $this->get_field_name('widget_num');
		$feed_id = $this->get_field_id('widget_feed');
		$feed_name = $this->get_field_name('widget_feed');
		echo "\r\n".'<p><label for="'.$title_id.'">'.__('Title').': <input type="text" class="widefat" id="'.$title_id.'" name="'.$title_name.'" value="'.attribute_escape( $instance['widget_title'] ).'" /><label></p>';
		echo "\r\n".'<p><label for="'.$text_id.'">'.__('Text').': <input type="text" class="widefat" id="'.$text_id.'" name="'.$text_name .'" value="'.attribute_escape( $instance['widget_text'] ).'" /><label></p>';
		echo "\r\n".'<p><label for="'.$num_id.'">'.__('Number of Items').': <input type="text" class="widefat" id="'.$num_id.'" name="'.$num_name .'" value="'.attribute_escape( $instance['widget_num'] ).'" /><label></p>';
				echo "\r\n".'<p><label for="'.$feed_id.'">'.__('eBay Feed').': <input type="text" class="widefat" id="'.$feed_id.'" name="'.$feed_name .'" value="'.attribute_escape( $instance['widget_feed'] ).'" /><label></p>';
	}

}

add_action('widgets_init', ebay_feeds_for_wordpress_Widget);

function ebay_feeds_for_wordpress_Widget(){
	// curl need to be installed
	register_widget('ebay_feeds_for_wordpress_Widget_class');
}

// Delays plugin execution until Dynamic Sidebar has loaded first.

//add_action('plugins_loaded', 'ebay_feeds_for_wordpress_init');


function ebay_feeds_for_wordpress_addbuttons() {
 // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "add_ebay_feeds_for_wordpress_tinymce_plugin");
     add_filter('mce_buttons', 'ebay_feeds_for_wordpress_button');
   }
}

function ebay_feeds_for_wordpress_button($buttons) {
   array_push($buttons, "separator", "ebffwplugin");
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_ebay_feeds_for_wordpress_tinymce_plugin($plugin_array) {
	$url = trim(get_bloginfo('url'), "/")."/wp-content/plugins/ebay-feeds-for-wordpress/editor_plugin.js";
   $plugin_array['ebffwplugin'] = $url;
   return $plugin_array;
}
 
// init process for button control
add_action('init', 'ebay_feeds_for_wordpress_addbuttons');

add_shortcode( 'ebayfeedsforwordpress', 'ebayfeedsforwordpress_shortcode' );

function ebayfeedsforwordpress_shortcode( $atts ) {
	$url = get_option('ebay-feeds-for-wordpress-default');
	$num = get_option('ebay-feeds-for-wordpress-default-number');
   extract( shortcode_atts( array(
      'feed' => $url, 'items' => $num
      ), $atts ) );
 
 	$feeddisplay = ebay_feeds_for_wordpress_notecho(esc_attr($feed),esc_attr($items));
 
   return $feeddisplay;
}

function ebay_feeds_for_wordpress_notecho($dispurl = "", $dispnum = "") {
include_once(ABSPATH . WPINC . '/rss.php');
$link = get_option("ebay-feeds-for-wordpress-link");

if ($dispnum == "" || $dispnum == "null")
{
$dispnum = get_option('ebay-feeds-for-wordpress-default-number');
}

if ($dispurl == "" || $dispurl == "null")
{
$dispurl = get_option('ebay-feeds-for-wordpress-default');
$disprss = fetch_feed($dispurl);
$disprss_items = $disprss->get_items(0, $dispnum);

} else {
$dispurl = str_replace("&amp;", "&", $dispurl);
$disprss = fetch_feed($dispurl);
$disprss_items = $disprss->get_items(0, $dispnum);
}


$display .=  "<div class='ebayfeed'>";
foreach ($disprss_items as $dispitem ) {
$display .= "<h4 class='ebayfeedtitle'><a class='ebayfeedlink' href='".$dispitem->get_permalink()."'>".$dispitem->get_title()."</a></h4>";
$display .= $dispitem->get_description();
}
$display .= "</div>";
if ($link == 1)
{
	$display .= "<a href='http://bloggingdojo.com/wordpress-plugins/ebay-feeds-for-wordpress/'>eBay Feeds for WordPress</a> by <a href='http://www.bloggingdojo.com'>The Blogging Dojo</a><br/><br/>";
}


return $display;

}

function ebay_feeds_for_wordpress_install() {
		add_option('ebay-feeds-for-wordpress-default', 'http://rest.ebay.com/epn/v1/find/item.rss?keyword=Ferrari&categoryId1=18180&sortOrder=BestMatch&programid=15&campaignid=5336886189&toolid=10039&listingType1=All&lgeo=1&descriptionSearch=true&feedType=rss');
		add_option('ebay-feeds-for-wordpress-default-number', 3);
		add_option('ebay-feeds-for-wordpress-link', 0);
}

?>