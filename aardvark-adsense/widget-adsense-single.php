<?php
/*
Plugin Name: AardvarkSense - Google AdSense Widget
Plugin URI: http://mabboud.net
Description: A suite of tools for Google AdSense and other ads, currently only has a widget.
Author: Maddie Abboud
Version: 0.1
Author URI: http://mabboud.net
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');	
if (!defined('WP_CONTENT_URL'))
      define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if (!defined('WP_CONTENT_DIR'))
      define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if (!defined('WP_PLUGIN_URL'))
      define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR'))
      define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');

add_action( 'widgets_init', function(){
     register_widget( 'AardvarkSense_Single_Ad_Widget' );
});	


function admin_init_aardvark() {
  register_setting('aardvark-adsense', 'adsense_client_id');
}

function Admin_Menu_AardvarkAdSense() {
	add_options_page('Aardvark AdSense', 'Aardvark AdSense', 'manage_options', 'aardvark-adsense', 'Options_Menu_AardvarkAdSense');
}

function Options_Menu_AardvarkAdSense() {
	include(WP_PLUGIN_DIR.'/aardvark-adsense/options.php');  
}

function Aardvark_Mobile_Ads() {
	$adsense_client_id = get_option('adsense_client_id');

	?>
		<!-- Mobile ad overlay and fullscreen ads -->
		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<script>
		  (adsbygoogle = window.adsbygoogle || []).push({
		    google_ad_client: "<?php echo $adsense_client_id ?>",
		    enable_page_level_ads: true
		  });
		</script>  
	<?php
}

if (is_admin()) {
	add_action('admin_init', 'admin_init_aardvark');
	add_action('admin_menu', 'Admin_Menu_AardvarkAdSense');
}

add_action('wp_head', 'Aardvark_Mobile_Ads');

class AardvarkSense_Single_Ad_Widget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'AardvarkSense_Single_Ad_Widget',
			__('Aardvark AdSense Widget', 'text_domain'), 
			array( 'description' => __( 'AardvarkSense AdSense Single Ad Widget', 'text_domain' ), ) 
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
     	echo $args['before_widget'];

		// begin html content
		?>

		<style>
			.widget_responsive { 
				width: <?php echo $instance['max_width_mobile'] ?>px; 
				height: <?php echo $instance['max_height_mobile'] ?>px; 
			}

			@media(min-width: 400px) { .widget_responsive { 
				width: <?php echo $instance['max_width_desktop'] ?>px; 
				height: <?php echo $instance['max_height_desktop'] ?>px; 
			} }
		</style>
		
		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<div style="margin-top: 20px; margin-bottom: 20px;">
			<ins class="adsbygoogle widget_responsive"
			     style="display:block"
			     data-ad-client="<?php echo $instance['ad_client_id'] ?>"
			     data-ad-slot="<?php echo $instance['ad_slot_id'] ?>"
			     data-ad-format="auto"></ins>
			<script>
			(adsbygoogle = window.adsbygoogle || []).push({});
			</script>
		</div>

		<?php
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if(!isset($instance['max_width_desktop'])) $instance['max_width_desktop'] = 330;
		if(!isset($instance['max_height_desktop'])) $instance['max_height_desktop'] = 600;

		if(!isset($instance['max_width_mobile'])) $instance['max_width_mobile'] = 300;
		if(!isset($instance['max_height_mobile'])) $instance['max_height_mobile'] = 300;

		if ( isset( $instance[ 'ad_slot_id' ] ) )
			$ad_slot_id = $instance[ 'ad_slot_id' ];
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'ad_slot_id' ); ?>"><?php _e( 'Ad Slot ID:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'ad_slot_id' ); ?>" name="<?php echo $this->get_field_name( 'ad_slot_id' ); ?>" type="text" value="<?php echo esc_attr( $ad_slot_id ); ?>">
		</p>
		
		<?php 
		if ( isset( $instance[ 'ad_client_id' ] ) )
			$ad_client_id = $instance[ 'ad_client_id' ];
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'ad_client_id' ); ?>"><?php _e( 'Publisher/Client ID:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'ad_client_id' ); ?>" name="<?php echo $this->get_field_name( 'ad_client_id' ); ?>" type="text" value="<?php echo esc_attr( $ad_client_id ); ?>">
		</p>


		<p>
			<label for="<?php echo $this->get_field_id('max_width_desktop'); ?>"><?php esc_html_e('Desktop - Max Width', 'aardvark-adsense') ?></label>

			<input  type="number" value="<?php echo esc_html($instance['max_width_desktop']); ?>"
			      name="<?php echo $this->get_field_name('max_width_desktop'); ?>"
			      id="<?php $this->get_field_id('max_width_desktop'); ?>"
			      class="widefat" />
		<p>


		<p>
			<label for="<?php echo $this->get_field_id('max_height_desktop'); ?>"><?php esc_html_e('Desktop - Max Height', 'aardvark-adsense') ?></label>

			<input  type="number" value="<?php echo esc_html($instance['max_height_desktop']); ?>"
			      name="<?php echo $this->get_field_name('max_height_desktop'); ?>"
			      id="<?php $this->get_field_id('max_height_desktop'); ?>"
			      class="widefat" />
		<p>


		<p>
			<label for="<?php echo $this->get_field_id('max_width_mobile'); ?>"><?php esc_html_e('Mobile - Max Width', 'aardvark-adsense') ?></label>

			<input  type="number" value="<?php echo esc_html($instance['max_width_mobile']); ?>"
			      name="<?php echo $this->get_field_name('max_width_mobile'); ?>"
			      id="<?php $this->get_field_id('max_width_mobile'); ?>"
			      class="widefat" />
		<p>


		<p>
			<label for="<?php echo $this->get_field_id('max_height_mobile'); ?>"><?php esc_html_e('Mobile - Max Height', 'aardvark-adsense') ?></label>

			<input  type="number" value="<?php echo esc_html($instance['max_height_mobile']); ?>"
			      name="<?php echo $this->get_field_name('max_height_mobile'); ?>"
			      id="<?php $this->get_field_id('max_height_mobile'); ?>"
			      class="widefat" />
		<p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['ad_client_id'] = ( ! empty( $new_instance['ad_client_id'] ) ) ? strip_tags( $new_instance['ad_client_id'] ) : '';
		$instance['ad_slot_id'] = ( ! empty( $new_instance['ad_slot_id'] ) ) ? strip_tags( $new_instance['ad_slot_id'] ) : '';

		$instance['max_width_desktop'] = $this->numericFieldCheck($new_instance['max_width_desktop']);
		$instance['max_height_desktop'] = $this->numericFieldCheck($new_instance['max_height_desktop']);

		$instance['max_width_mobile'] = $this->numericFieldCheck($new_instance['max_width_mobile']);
		$instance['max_height_mobile'] = $this->numericFieldCheck($new_instance['max_height_mobile']);

		return $instance;
	}

	public function numericFieldCheck($field) {
		return (! empty( $field ) && is_numeric( $field )) ? esc_html( $field ) : 0;
	}
}