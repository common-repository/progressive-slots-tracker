<?php
/*
Plugin Name: Progressive Slots Tracker
Plugin URI: http://www.onlinegambling.eu/slots/progressive-slots/tracker
Description: Free Progressive Slots Tracker / JackPot Tracker with affiliate link placement capabilities
Version: 1.0
Author: Gary-adam Shannon
Author URI: http://www.garyadamshannon.com
*/
/*  Copyright 2009 Web Marketing Solutions/Gary-adam Shannon  (gary@garyadamshannon.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
	class ProgressiveSlotsTracker {
		function ProgressiveSlotsTracker() {
			ProgressiveSlotsTracker::do_includes();
		}
		
		function do_includes() {
			$pathinfo = pathinfo(__FILE__);
			$include_path = $pathinfo['dirname'].'/includes';
			if ($handle = opendir($include_path)) {
			    while (false !== ($file = readdir($handle))) {
			        if (preg_match('/^inc\.(.*?)\.php/', $file)) {
						require_once($include_path.'/'.$file);
			        }
			    }
			    closedir($handle);
			}
		}
	
		function do_display($type='in_content') {
			switch ($type) {
				case 'widget':
					return ProgressiveSlotsTracker::do_display_widget();
					break;
				case 'in_content':
					return ProgressiveSlotsTracker::do_display_content();
					break;
			}	
		}

		function do_display_widget($args) {
			extract($args);
	     	echo $before_widget;
			$title = get_option('progressive_slots_tracker_widget_title');
			if (!$title) {
				$title = 'Progressive Slots';
			}
	
    		echo $before_title . $title . $after_title;
		echo '<div class="progressive-slots-tracker">';
			$this->test_microgaming();
			echo '<div style="clear: both"></div>';
		echo '</div>';
	        echo $after_widget;		
		}
		
		function do_display_content() {
			
		}       
		
		function get_affiliate_link($key, $id) {                                                         
			$affiliate_link = get_option('progressive_slots_tracker_affiliate_link_'.$key.'['.$id.']');
			if (!$affiliate_link) {
				$affiliate_link = 'http://www.onlinegambling.eu/redirect/progressiveslots/'.$key.'/'.$id;
			}
			return $affiliate_link;
		}

		function content_filter($text) {
			$scanwords = Array('<!-- ProgressiveSlotsTracker -->', '<ProgressiveSlotsTracker>', '[ProgressiveSlotsTracker]');
			foreach ($scanwords as $needle) {
				$text = str_replace($needle, ProgressiveSlotsTracker::do_display(), $text);	
			}
			return $text;
		}
		
		function test_microgaming() {
			if ($this->is_graphics()) {
				$display_type = 'graphics';
			} else {
				$display_type = 'text';
			}
			$microgaming = new ProgressiveSlotsTrackerMicrogaming();
			foreach($microgaming->available_products() as $id) {
				if ($this->has_some_enabled()) {
					if ($this->is_affiliate_enabled('microgaming',$id)) {
						$microgaming->render_product($id, $display_type);
						echo "<br>";					
					}
				} else {
					$microgaming->render_product($id, $display_type);
					echo "<br>";					
				}
			}
			ProgressiveSlotsTracker::advertise_widget(0);
		}
	
		function advertise($return=1) {
			$link = '<a href="http://www.onlinegambling.eu/slots/progressive-slots/tracker" alt="Progressive Slots">Progressive Slots Tracker</a> is an <a href="http://www.onlinegambling.eu" alt="Online Gambling">Online Gambling</a> product.<br/>';
			if ($return) {
				return $link;
			} else {
				echo $link;
			}
		}

		function advertise_widget($return=1) {
			$link = 'by <a href="http://www.onlinegambling.eu/slots/progressive-slots/tracker" alt="Progressive Slots">Progressive Slots Tracker</a><br/>';
			if ($return) {
				return $link;
			} else {
				echo $link;
			}
		}

		
		function do_widget_init() {
			register_sidebar_widget('Progressive Slots Tracker', array($this, do_display_widget));
		}
		
		function do_option_menu() {
			add_options_page('Progressive Slots Tracker Options', 'Progressive Slots Tracker', 8, __FILE__, array($this, 'do_options'));
		}

		function do_options() {
			echo '<div class="wrap"><h2>Progressive Slots Tracker Options</h2>';
			if ($_REQUEST['do_update']) {
				$link_in_footer = $_REQUEST['link_in_footer'];
				if ($link_in_footer == 'on') {
					$link_in_footer = 1;
				} else {
					$link_in_footer = 0;
				}
				update_option('progressive_slots_tracker_link_in_footer', $link_in_footer);
				foreach ($_REQUEST['progressive_slots_tracker_affiliate_link']['microgaming'] as $key => $value) {
					update_option('progressive_slots_tracker_affiliate_link_microgaming['.$key.']', $value);					
				}
				$microgaming = new ProgressiveSlotsTrackerMicrogaming();
				
				foreach ($microgaming->available_products() as $key) {
					update_option('progressive_slots_tracker_affiliate_microgaming['.$key.']', 'off');
				}
				
				foreach ($_REQUEST['progressive_slots_tracker_affiliate']['microgaming'] as $key => $value) {
					update_option('progressive_slots_tracker_affiliate_microgaming['.$key.']', $value);					
				}
				
				foreach ($_REQUEST['progressive_slots_tracker_currency'] as $key => $value) {
					update_option('progressive_slots_tracker_currency['.$key.']', $value);
				}
				
				if ($_REQUEST['graphical_display'] == 'on') {
					update_option('progressive_slots_tracker_display', 'graphic');
				} else {
					update_option('progressive_slots_tracker_display', 'text');					
				}

				echo '<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><strong>Settings saved.</strong></p></div>';
			}
			
			$link_in_footer = get_option('progressive_slots_tracker_link_in_footer');

			if ($link_in_footer == '') {
				$link_in_footer_cb = 'checked';
			}
			if ($link_in_footer == '1') {
				$link_in_footer_cb = 'checked';
			}
			if ($link_in_footer == '0') {
				$link_in_footer = '';
			}
			echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="POST" class="form-table">';
			echo '<input type="hidden" name="do_update" value="Y">';
			echo '<h3>Progressive Slots Tracker Options</h3>';
                      
			echo '<h4>Microgaming Slots</h4>';
			echo '<table width="700">';
			echo '<tr><th width="75">Active</th><th width="150">Product</th><th>Affiliate URL</th></tr>';
			foreach (ProgressiveSlotsTrackerMicrogaming::available_products() as $index) {
				$product_name = ProgressiveSlotsTrackerMicrogaming::products_to_string($index);
				$affiliate_link = $this->get_affiliate_link('microgaming', $index);
				$affiliate_enabled = $this->is_affiliate_enabled('microgaming', $index);
				if ($affiliate_enabled){
					$is_enabled = 'checked';
				} else {
					$is_enabled = '';
				}
				echo '<tr><td width="75"><input type="checkbox" name="progressive_slots_tracker_affiliate[microgaming]['.$index.']"'.$is_enabled.'></td><td>'.$product_name.'</td><td width="150"><input class="regular-text code" type="text" name="progressive_slots_tracker_affiliate_link[microgaming]['.$index.']" size="75" value="'.$affiliate_link.'"/></td></tr>';
			}
			
			echo '</table>';
			
			echo 'Currency: <select name="progressive_slots_tracker_currency[microgaming]">';
			
			foreach (Array('USD','GBP','EUR') as $currency) {
				if (get_option('progressive_slots_tracker_currency[microgaming]') == $currency) {
					$is_selected = ' selected';
				} else {
					$is_selected = '';
				}
				echo '<option'.$is_selected.'>'.$currency.'</option>';
			}
			
			echo '</select>';
			
			echo '<h3>Display</h3>';
			if ($this->is_graphics()) {
				$is_graphics = 'checked';
			}                             else {
				$is_graphics= '';
			}
			
			echo '<input type="checkbox" name="graphical_display" '.$is_graphics.'> Use graphics instead of text display<br/>';

			echo '<h3>Help me help you</h3>';
			echo '<input type="checkbox" name="link_in_footer" '.$link_in_footer_cb.'> Display link back to our website in your themes footer<br/>';
			echo '<hr/>';
			echo '<p class="submit">';
			echo '<input type="submit" name="Submit" value="'.__('Update Options').'" /></p>';
			echo '</form>';
			echo '</div>';
		}      
		
		function is_graphics() {
			$option = get_option('progressive_slots_tracker_display');
			if ($option == 'text' || $option == '' || !$option) {
				return false;
			} else {
				return true;
			}
		}  
		
		function has_some_enabled() {
			foreach (ProgressiveSlotsTrackerMicrogaming::available_products() as $index) {
				if ($this->is_affiliate_enabled('microgaming',$index)) {
					return true;
				}
			}
			return false;
		}
		
		function is_affiliate_enabled($key, $id) {
			$option_result = get_option('progressive_slots_tracker_affiliate_'.$key.'['.$id.']');
			if ($option_result == '' || $option_result == 'off') {
				return false;
			} else {
				return true;
			}
			
		}

		function get_pluginpath() {
			$plugin_path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
			return $plugin_path;
		}

		function do_css() {
			$plugin_path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
			echo '<link rel="stylesheet" type="text/css" href="'.$plugin_path.'progressive-tracker.css" />';
		}
	}

        if (class_exists("ProgressiveSlotsTracker")) {
			$cl_progressiveslotstracker = new ProgressiveSlotsTracker();
			add_filter('the_content', array(&$cl_progressiveslotstracker, 'content_filter'), 1);
			$link_in_footer = get_option('progressive_slots_tracker_link_in_footer');
			if ($link_in_footer == '1' || $link_in_footer == '') {
				add_action('wp_footer', array(&$cl_progressiveslotstracker, 'advertise'), 0);
			}
			add_action('wp_head', array(&$cl_progressiveslotstracker, 'do_css'), 0);
			add_action('admin_menu', array(&$cl_progressiveslotstracker, 'do_option_menu'), 1);
			add_action('plugins_loaded', array(&$cl_progressiveslotstracker, 'do_widget_init'));
        }
?>
