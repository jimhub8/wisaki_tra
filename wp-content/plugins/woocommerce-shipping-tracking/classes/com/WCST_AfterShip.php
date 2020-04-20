<?php 
//https://github.com/AfterShip/aftership-sdk-php
require WCST_PLUGIN_ABS_PATH.'/classes/vendor/autoload.php';

class WCST_AfterShip
{
	var $api_key;
	var $courier_slugs = array();
	var $trackings = null;
	var $courier = null;
	var $last_check_point = null;
	public function __construct($key)
	{
		$this->api_key = $key;
		
	}
	private function init_connectors()
	{
		$this->trackings = new AfterShip\Trackings($this->api_key);
		$this->courier = new AfterShip\Couriers($this->api_key);
		$this->last_check_point = new AfterShip\LastCheckPoint($this->api_key);
	}
	public function get_tracking()
	{
		/* $couriers = new AfterShip\Couriers($key);
		$trackings = new AfterShip\Trackings($key);
		$last_check_point = new AfterShip\LastCheckPoint($key); */
	}
	public function detect_courier_slug_by_tracking_id($tracking_code, $slug = null)
	{
		$this->init_connectors();
		$courier = $this->courier;
		$slug_to_use_for_detection = $slug;
		if(empty($this->courier_slugs) && !isset($slug))
		{
			$complete_courier_list = $courier->all();
			foreach((array)$complete_courier_list["data"]["couriers"] as $courier_data)
				$this->courier_slugs[] = $courier_data["slug"];
				
			$slug_to_use_for_detection =  $this->courier_slugs;
		}
		//wcst_var_dump($slug_to_use_for_detection);
		$slug_to_use_for_detection = isset($slug_to_use_for_detection) && is_array($slug_to_use_for_detection) ?  implode(",", $slug_to_use_for_detection) : $slug_to_use_for_detection;
		$response = $courier->detect($tracking_code, array('slug' => $slug_to_use_for_detection));
		/*  wcst_var_dump($slug_to_use_for_detection);
		wcst_var_dump($response);  */ 
		return $response;
	}
	public function get_tracking_info($courier_slug, $tracking_code)
	{
		$this->init_connectors();
		$trackings = $this->trackings;
		$tracking_info = [
			'slug'    => $courier_slug,
			//'title'   => 'My Title',
		];
		
		//$result = $this->delete_all_tracking_id();
	
		try
		{
			$response = $trackings->create($tracking_code, $tracking_info);
			$id = $response["data"]["tracking"]["id"];
			$last_check_point = $this->last_check_point;
			$response = $last_check_point->getById($id);
			//$id = $response["data"]["id"];
			//wcst_var_dump("created");
		}catch(Exception $e){
			//$response = $trackings->retrack($courier_slug, $tracking_code);
			$last_check_point = $this->last_check_point;
			$response = $last_check_point->get($courier_slug, $tracking_code);
			//wcst_var_dump("recover");
			$id = $response["data"]["id"];
			//wcst_var_dump($id);
		}
		
		//wcst_var_dump($response);
		//$response = $trackings->get($courier_slug, $tracking_code/*,  array('title','order_id') */);
		//wcst_var_dump(substr(get_locale(), 0,2));
		$response = $trackings->getById($id, array('lang' => substr(get_locale(), 0,2)) );
		//$trackings->deleteById($id);
		//$trackings->delete($courier_slug, $tracking_code);
		
		return $response;
	}
	public function get_tracking_info_by_tracking_code($tracking_code, $tracking_company_slug = null)
	{
		$tracking_code = trim($tracking_code);
		if(strpos($tracking_code, "###") !== false)
		{
			$split_result = explode('###', $tracking_code);
			$tracking_company_slug = $split_result[0];
			$tracking_code = $split_result[1];
		}
		
		try
		{
			$courier_slug_detected = $this->detect_courier_slug_by_tracking_id($tracking_code, $tracking_company_slug);
		}catch(Exception $e){return false;}
		
		//wcst_var_dump($courier_slug_detected);
		//wcst_var_dump($tracking_code);
		$response = $error = false;
		if(isset($courier_slug_detected["data"]) && $courier_slug_detected["data"]["total"] > 0)
		{
			//wcst_var_dump(count($courier_slug_detected["data"]["couriers"]));
			foreach($courier_slug_detected["data"]["couriers"] as $current_courier)
			{
				try{
					$response = $this->get_tracking_info($current_courier["slug"], $tracking_code);
					//wcst_var_dump($response);
				}catch(Exception $e){$response = false; $error = true; /* wcst_var_dump($e); */}
				
				//wcst_var_dump($response);
				
				if(is_array($response) && !empty($response["data"]["tracking"]["checkpoints"]))
					return $response ;
				elseif(is_array($response) && 
					   isset($response["data"]) && 
					   isset($response["data"]["tracking"]) && 
					   isset($response["data"]["tracking"]["id"]) && 
					   strtolower($response["data"]["tracking"]["tag"] != 'pending')
					   )
				{
					//wcst_var_dump("delete");
					//$this->delete_shipping_tracking_by_id($response["data"]["tracking"]["id"]);
				}
			}
			
		}
		else 
			return "no_currier_detected";
		
		//wcst_var_dump($courier_slug);		 
		//$response = $this->get_tracking_info($courier_slug, $tracking_code);
		//wcst_var_dump($response);
		
		return $response;
	}
	public function delete_shipping_tracking_by_id($id)
	{
		//Completely avoid tracking code delete
		return; 
		
		try 
		{
			$this->init_connectors();
			$trackings = $this->trackings;
			$trackings->deleteById($id);
		}catch(Exception $e){$response = false; /* wcst_var_dump($e); */}
	}
	public function delete_all_tracking_id()
	{
		//Completely avoid tracking code delete
		return;
		
		$this->init_connectors();
		$trackings = $this->trackings;
		/* 		 
		$options = [
			'page'  => 1,
			'limit' => 1000
		];  */
		$result = $trackings->all(/* $options */);
		if(isset($result["data"]) && isset($result["data"]["trackings"]))
			foreach($result["data"]["trackings"] as $tracking)
				$trackings->deleteById($tracking["id"]); 
	}
	public function render_tracking_info_box($params)
	{
		global $wcst_time_model;
		if(!isset($params['tacking_code']) || $params['tacking_code'] == "")
			return "";
		
		/* $options_controller = new WCST_Option();
		$options = $options_controller->get_general_options();
		$aftership_api_key = isset($options['aftership_api_key']) && isset($options['aftership_api_key']) ? $options['aftership_api_key'] : ""; 
		$after_shipping_tracker = new WCST_AfterShip($aftership_api_key);*/
		$params['preselected_companies'] = isset($params['preselected_companies']) && !empty($params['preselected_companies']) ? $params['preselected_companies'] : null;
		/* wcuf_var_dump($params['tacking_code']);
		wcuf_var_dump($params['preselected_companies']); */
		$tracking_info = $this->get_tracking_info_by_tracking_code($params['tacking_code'], $params['preselected_companies']);
		
		//Errors
		if($tracking_info == false)
		{
			ob_start();
			echo "<h3>".__( 'Tracking service unavailable at the moment. Please try again later', 'woocommerce-shipping-tracking' )."</h3>";
			return ob_get_clean();
		}
		else if($tracking_info  == 'no_currier_detected')
		{
			ob_start();
			echo "<h3>".__( 'No currier detected for the selected tracking code.', 'woocommerce-shipping-tracking' )."</h3>";
			return ob_get_clean();
		}
		$company_slug = $tracking_info["data"]["tracking"]['slug'];
		$status =  strtolower( $tracking_info["data"]["tracking"]["tag"]);
		$tracking_checkpoints =  $tracking_info["data"]["tracking"]["checkpoints"];
		if(empty($tracking_checkpoints))
		{
			ob_start();
			//wcst_var_dump($tracking_info);
			echo "<h3>".__( 'No tracking info avaliable at the moment. Please try again later', 'woocommerce-shipping-tracking' )."</h3>";
			return ob_get_clean();
		}
		
		
		$counter = 1;
		ob_start();
		if($status == 'pending'):
			echo "<h3>".__( 'Please try again later, we are awaiting tracking info from the carrier.', 'woocommerce-shipping-tracking' )."</h3>";
		else:
			?>
			<ul class="timeline">
			<?php 
			foreach($tracking_checkpoints as $tracking_checkpoint):
					$current_status = strtolower($tracking_checkpoint["tag"]);
			?>
				
				<li class="<?php if($counter++ % 2 == 0) echo 'timeline-inverted'; ?>">
				  <div class="timeline-badge wcst_badge"><!--<i class="glyphicon glyphicon-check"></i> --><img class="wcst_shipping_badge" src="<?php echo WCST_PLUGIN_PATH; ?>/img/aftership/<?php echo $current_status;?>.svg"></img></div>
				  <div class="timeline-panel">
					<div class="timeline-heading">
					  <h4 class="timeline-title"><?php echo $counter - 1; ?>. <?php echo $tracking_checkpoint["message"]; ?></h4>
					  <p><small class="text-muted"><!--<i class="glyphicon glyphicon-time"></i> --><?php echo $wcst_time_model->format_data_according_wordpress_settings ( $tracking_checkpoint["checkpoint_time"] ); //2018-02-24T18:11:10 ?></small></p>
					</div>
					<!-- <div class="timeline-body">
					  <p>Mussum ipsum cacilds, vidis litro abertis.</p>
					</div> -->
				  </div>
				</li>
			<?php endforeach; ?>
			</ul>
			<?php 
		endif;
		return ob_get_clean();
	}
}
?>