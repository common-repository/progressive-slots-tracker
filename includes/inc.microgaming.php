<?php
	class ProgressiveSlotsTrackerMicrogaming {
		function ProgressiveSlotsTrackerMicrogaming() {
			$this->setup();
		}
		
		function my_name() {
			return 'microgaming';
		}
		
		function setup() {
			$currency = get_option('progressive_slots_tracker_currency[microgaming]');                                               
			if (!$currency) { $currency = 'EUR'; }
			echo "<script src='http://www.jackpotmadness.com/modules/getProgBlock/mandatory.php?currency=".$currency."'></script>\n";
		}
		
		function products_to_string($id) {
			$names = Array('15' => 'Mega Moolah', '10' => 'Major Millions', '5' => 'Fruit Fiesta', '8' => 'Jackpot Deuces', '12' => 'King Cashalot', '2' => 'Lotsaloot', '11' => 'Roulette Royal', '4' => 'Supajax', '9' => 'Triple Sevens', '13' => 'Tunzamunni', '6' => 'Treasure Nile', '7' => 'CyberStud', '1' => 'Cash Splash');
			return $names[$id];
		}
		
		function available_products() {                                            
			// Two more unknowns (Wow pot and Poker Ride)
			return Array('15','10','5','8','12','2','11','4','9','13','6','7','1');
		}
		
		function render_product($id,$display_type='text') {
			$affiliate_link = ProgressiveSlotsTracker::get_affiliate_link($this->my_name(), $id);
			if ($display_type == 'text') {
				echo "<span class='progressive-slots-tracker-product'><a href='{$affiliate_link}'>".$this->products_to_string($id)."</a></span>";
				echo "<form name='jpform{$id}' id='jpform{$id}'><input readonly name='progressive{$id}' id='progressive{$id}' class='progressive-slots-tracker-jackpot' size='15' value='Calculating...'><script>ScrollProgressiveCounters(".$id.");</script></form>\n";
			} else {
				echo "<span class='progressive-slots-tracker-product-graphic'><a href='{$affiliate_link}'><img src='".ProgressiveSlotsTracker::get_pluginpath()."images/".$this->my_name()."/".$id.".gif' width='164' height='90'></a></span>";      
				echo "<form name='jpform{$id}' id='jpform{$id}'><input readonly name='progressive{$id}' id='progressive{$id}' class='progressive-slots-tracker-jackpot-graphic' size='15' value='Calculating...'><script>ScrollProgressiveCounters(".$id.");</script></form>\n";
				
			}
		}
	}

?>
