<?php
/**
 * @package SMCountDown_widget
 * @version 1.2
 */
/*
  Plugin Name: SM CountDown Widget
  Plugin URI: http://wordpress.org/plugins/smcountdown-widget
  Description: Displays a responsive JQuery countdown timer.
  Version: 1.2
  Author: Stéphane Moitry
  Author URI: http://stephane.moitry.fr
  License: GPLv2 or later
  Text Domain: smcountdown-widget
  Domain Path: /languages
 */

/*  Copyright 2014-2015  Stéphane Moitry (stephane.moitry@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * smcountdown_widget_Widget Class
 */
class smcountdown_widget_Widget extends WP_Widget {

	/** constructor */
	function smcountdown_widget_Widget() {
		parent::WP_Widget(false, 'SM CountDown Widget', array('description' => __('Displays a responsive JQuery countdown timer.', 'smcountdown-widget')));
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$timerdate = esc_attr($instance['timerdate']);

		echo $before_widget;

		if ($title) {
			echo $before_title . $title . $after_title;
		}

		echo $this->render($timerdate);

		echo $after_widget;
	}

	/* Render the content, used by widget and shortcode methods */
	public static function render($timerdate) {
		static $id = 0;
		$text = "";

		$text = $text . '<script type="text/javascript">';
		$text = $text . "jQuery(document).ready(function() {jQuery('#smcountdown" . $id . "').countdown('" . $timerdate . "', function(event) {";
		$text = $text . "jQuery('#smcountdowndays" . $id . "').html(event.strftime('%D'));";
		$text = $text . "jQuery('#smcountdownhours" . $id . "').html(event.strftime('%H'));";
		$text = $text . "jQuery('#smcountdownminutes" . $id . "').html(event.strftime('%M'));";
		$text = $text . "jQuery('#smcountdownseconds" . $id . "').html(event.strftime('%S'));";
		$text = $text . '})});';
		$text = $text . '</script>';
		$text = $text . '<section class="smcountdownblock" id="smcountdown' . $id . '">';
		$text = $text . '<div class="smcountdownelement days"><span class="number" id="smcountdowndays' . $id . '">200</span><span class="label">' . __('Days', 'smcountdown-widget') . '</span></div>';
		$text = $text . '<div class="smcountdownelement hours"><span class="number" id="smcountdownhours' . $id . '">10</span><span class="label">' . __('Hours', 'smcountdown-widget') . '</span></div>';
		$text = $text . '<div class="smcountdownelement minutes"><span class="number" id="smcountdownminutes' . $id . '">10</span><span class="label">' . __('Minutes', 'smcountdown-widget') . '</span></div>';
		$text = $text . '<div class="smcountdownelement seconds"><span class="number" id="smcountdownseconds' . $id . '">10</span><span class="label">' . __('Seconds', 'smcountdown-widget') . '</span></div>';
		$text = $text . '</section>';

		$id++;		

		return $text;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['timerdate'] = strip_tags($new_instance['timerdate']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = '';
		$timerdate = '2015/12/31 23:59:59';

		if (isset($instance['title'])) {
			$title = esc_attr($instance['title']);
		}
		
		if (isset($instance['timerdate'])) {
			$timerdate = esc_attr($instance['timerdate']);
		}
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','smcountdown-widget'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('timerdate'); ?>"><?php _e('Target Date:','smcountdown-widget'); ?> <input class="widefat" id="<?php echo $this->get_field_id('timerdate'); ?>" name="<?php echo $this->get_field_name('timerdate'); ?>" type="text" value="<?php echo $timerdate; ?>" /></label></p>
<?php
	}

}

/* ShortCode Handler */
function smcountdown_widget_shortcode( $atts ) {
	$attributes = shortcode_atts( array(
	    'timerdate' => '2015/12/31 23:59:59'
	), $atts );
	
	$text = "<div class='widget_smcountdown_widget'>".smcountdown_widget_Widget::render($attributes['timerdate'])."</div>";
	
	return $text;
}

/* Initialization Handler */
function smcountdown_widget_init() {
	load_plugin_textdomain( 'smcountdown-widget', false, dirname( plugin_basename( __FILE__ ) ).'/languages' );
	register_widget( 'smcountdown_widget_Widget' );
	wp_enqueue_style( 'smcountdown_widget_Widget', plugin_dir_url( __FILE__ ).'smcountdown-widget.css');
	wp_register_script('smcountdown-script', plugin_dir_url( __FILE__ ).'jquery.countdown.min.js', array ('jquery'), '2.0.2' );
	wp_enqueue_script('smcountdown-script');
}

// register Widget
add_action('widgets_init', 'smcountdown_widget_init');
// register ShortCode
add_shortcode( 'smcountdown', 'smcountdown_widget_shortcode' );