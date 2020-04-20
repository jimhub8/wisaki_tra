<?php 
class WCST_Time
{
	public function __construct()
	{
	}
	/* public function get_available_date($estimation_rule)
	{
		if(!isset($estimation_rule))
			return __('N/A','woocommerce-shipping-tracking');
		
		$wcst_option_model = new WCST_Option();
		$miutes_offeset = $wcst_option_model->get_estimations_options('hour_offset', 0);
		$date_format = $wcst_option_model->get_option('wcst_general_options', 'date_format', "dd/mm/yyyy");
		
		//$now =  date($date_format,strtotime($miutes_offeset.' minutes'));
		$hour =  date('G',strtotime($miutes_offeset.' minutes'));
		//Offset in minute
		$start_day_of_the_week =  date('w',strtotime($miutes_offeset+($estimation_rule['day_cut_off_hour']*60).' minutes')); //0 (for Sunday) through 6 (for Saturday)
		$start_day_of_the_month =  date('j',strtotime($miutes_offeset+($estimation_rule['day_cut_off_hour']*60).' minutes'));
		$start_month =  date('n',strtotime($miutes_offeset+($estimation_rule['day_cut_off_hour']*60).' minutes'));
		$start_year =  date('Y',strtotime($miutes_offeset+($estimation_rule['day_cut_off_hour']*60).' minutes'));
		//it is like placing an order in the 24h ( 1440m) before
		if($hour < $estimation_rule['day_cut_off_hour'])
		{
			$start_day_of_the_week =  date('w',strtotime($miutes_offeset-1440+($estimation_rule['day_cut_off_hour']*60).' minutes')); //0 (for Sunday) through 6 (for Saturday)
			$start_day_of_the_month =  date('j',strtotime($miutes_offeset-1440+($estimation_rule['day_cut_off_hour']*60).' minutes'));
			//wcst_var_dump($start_day_of_the_week);
			$start_month =  date('n',strtotime($miutes_offeset-1440+($estimation_rule['day_cut_off_hour']*60).' minutes'));
			$start_year =  date('Y',strtotime($miutes_offeset-1440+($estimation_rule['day_cut_off_hour']*60).' minutes'));
		}
		//First avaiable day by which starting shipment date could take place. It could be today or tomorrow.
		$starting_date = array('day_of_the_week' => $start_day_of_the_week, 'day_of_the_month' => $start_day_of_the_month, 'month'=>$start_month, 'year' => $start_year);
		//wcst_var_dump($starting_date);
		
		$first_run = true;
		do
		{
			$starting_date = $this->get_next_available_date($starting_date,$estimation_rule, $first_run);
			$first_run = false;
			$is_non_working_day = $this->check_if_date_is_a_non_working_day($starting_date,$estimation_rule);
		}while($is_non_working_day);
		
		//wcst_var_dump($starting_date);
		$date = new DateTime($starting_date['year']."-".$starting_date['month']."-".$starting_date['day_of_the_month']);
		
		if( $date_format == "dd/mm/yyyy" )
			return $date->format("d/m/Y");
		else if( $date_format == "dd.mm.yyyy" )
			return $date->format("d.m.Y");
		else if( $date_format == "mm.dd.yyyy" )
			return $date->format("m.d.Y");
		else if( $date_format == "dd-mm-yyyy" )
			return $date->format("d-m-Y");
		else if( $date_format == "mm-dd-yyyy" )
			return $date->format("m-d-Y");
		else if( $date_format == "mmmm dd, yyyy" )
			return $date->format('F j, Y');
		
		return $date->format("m/d/Y");
	} */
	public function get_available_date($estimation_rule, $return_object = false)
	{
		if(!isset($estimation_rule))
			return !$return_object ? __('N/A','woocommerce-shipping-tracking') : null;
		
		$wcst_option_model = new WCST_Option();
		$miutes_offeset = $wcst_option_model->get_estimations_options('hour_offset', 0);
		$date_format = $wcst_option_model->get_option('wcst_general_options', 'date_format', "dd/mm/yyyy");
		
		//$now =  date($date_format,strtotime($miutes_offeset.' minutes'));
		$hour =  date('G',strtotime($miutes_offeset.' minutes'));
		//Offset in minute
		$start_day_of_the_week =  date('w',strtotime($miutes_offeset.' minutes')); //0 (for Sunday) through 6 (for Saturday)
		$start_day_of_the_month =  date('j',strtotime($miutes_offeset.' minutes'));
		$start_month =  date('n',strtotime($miutes_offeset.' minutes'));
		$start_year =  date('Y',strtotime($miutes_offeset.' minutes'));
		/* 
		* Format:
		["day_cut_off_hour"]=>
			  string(1) "0"
			  ["days_delay"]=>
			  string(1) "0"
			}
		*/
		$apply_cutoff_additional_day = false;
		//it is like placing an order in the 24h ( 1440m) after
		if($hour >= $estimation_rule['day_cut_off_hour'])
		{
			$apply_cutoff_additional_day = true;
			$start_day_of_the_week =  date('w',strtotime(($miutes_offeset+1440).' minutes')); //0 (for Sunday) through 6 (for Saturday)
			$start_day_of_the_month =  date('j',strtotime(($miutes_offeset+1440).' minutes'));
			//wcst_var_dump($start_day_of_the_week);
			$start_month =  date('n',strtotime(($miutes_offeset+1440).' minutes'));
			$start_year =  date('Y',strtotime(($miutes_offeset+1440).' minutes'));
		}
		//First avaiable day by which starting shipment date could take place. It could be today or tomorrow.
		$starting_date = array('day_of_the_week' => $start_day_of_the_week, 'day_of_the_month' => $start_day_of_the_month, 'month'=>$start_month, 'year' => $start_year);
		//wcst_var_dump($starting_date);
		
		$first_run = true;
		//do
		//{
			$starting_date = $this->get_next_available_date($starting_date,$estimation_rule, $first_run, $apply_cutoff_additional_day);
			//$first_run = false;
			//$is_non_working_day = $this->check_if_date_is_a_non_working_day($starting_date,$estimation_rule);
		//}while($is_non_working_day);
		
		//wcst_var_dump($starting_date);
		$date = new DateTime($starting_date['year']."-".$starting_date['month']."-".$starting_date['day_of_the_month']);
		
		if($return_object)
			return $date;
		
		//Elastic date
		$end_date = null;
		if($estimation_rule['elastic_date'])
		{
			//Offset in minute
			$additional_days_in_minutes = $estimation_rule['elastic_date_day_offset'] * 1440;
			$miutes_offeset += $additional_days_in_minutes;
			$end_day_of_the_week =  date('w',strtotime($miutes_offeset.' minutes')); //0 (for Sunday) through 6 (for Saturday)
			$end_day_of_the_month =  date('j',strtotime($miutes_offeset.' minutes'));
			$end_month =  date('n',strtotime($miutes_offeset.' minutes'));
			$end_year =  date('Y',strtotime($miutes_offeset.' minutes'));
			$apply_cutoff_additional_day = false;
			if($hour >= $estimation_rule['day_cut_off_hour'])
			{
				$apply_cutoff_additional_day = true;
				$end_day_of_the_week =  date('w',strtotime(($miutes_offeset+1440).' minutes')); //0 (for Sunday) through 6 (for Saturday)
				$end_day_of_the_month =  date('j',strtotime(($miutes_offeset+1440).' minutes'));
				$end_month =  date('n',strtotime(($miutes_offeset+1440).' minutes'));
				$end_year =  date('Y',strtotime(($miutes_offeset+1440).' minutes'));
			}
			$ending_date = array('day_of_the_week' => $end_day_of_the_week, 'day_of_the_month' => $end_day_of_the_month, 'month'=>$end_month, 'year' => $end_year);
			//wcst_var_dump($ending_date);
			$end_date = $this->get_next_available_date($ending_date,$estimation_rule, true, $apply_cutoff_additional_day);
			$end_date = new DateTime($end_date['year']."-".$end_date['month']."-".$end_date['day_of_the_month']);
		}
		
		/* if( $date_format == "dd/mm/yyyy" )
			return $date->format("d/m/Y");
		else if( $date_format == "mm/dd/yyyy" )
			return $date->format("m/d/Y");
		else if( $date_format == "yyyy/mm/dd" )
			return $date->format("Y/m/d");
		else if( $date_format == "dd.mm.yyyy" )
			return $date->format("d.m.Y");
		else if( $date_format == "mm.dd.yyyy" )
			return $date->format("m.d.Y");
		else if( $date_format == "yyyy.mm.dd" )
			return $date->format("Y.m.d");
		else if( $date_format == "dd-mm-yyyy" )
			return $date->format("d-m-Y");
		else if( $date_format == "mm-dd-yyyy" )
			return $date->format("m-d-Y");
		else if( $date_format == "yyyy-dd-mm" )
			return $date->format("Y-m-d");
		else if( $date_format == "mmmm dd, yyyy" )
			return $date->format('F j, Y');
		
		return !$return_object ? $date->format("m/d/Y") : $date; */
		
		return isset($end_date) ? $this->get_data_formatted($date, $date_format, "m/d/Y").__( ' - ', 'woocommerce-shipping-tracking' ).$this->get_data_formatted($end_date, $date_format, "m/d/Y"): $this->get_data_formatted($date, $date_format, "m/d/Y");
	}
	public function format_data($date)
	{
		if($date == "")
			return "";
		
		$wcst_option_model = new WCST_Option();
		$date_format = $wcst_option_model->get_option('wcst_general_options', 'date_format', "dd/mm/yyyy");
		/* wcst_var_dump($date);
		wcst_var_dump($date_format); */
		try{
			$date = new DateTime($date); //yyyy-mm-dd
		}catch(Exception $e){return $date;}
		
		return $this->get_data_formatted($date, $date_format, 'Y-m-d');
	}
	public function get_data_formatted($date, $date_format, $default_format = 'Y-m-d' )
	{
		$wpml_helper = new WCST_Wpml();
		/* wcst_var_dump($wpml_helper->get_current_locale_code());
		wcst_var_dump(get_locale()); */
		setlocale(LC_ALL, $wpml_helper->get_current_locale_code()); 
		
		if( $date_format == "dd/mm/yyyy" )
			return $date->format("d/m/Y");
		else if( $date_format == "mm/dd/yyyy" )
			return $date->format("m/d/Y");
		else if( $date_format == "yyyy/mm/dd" )
			return $date->format("Y/m/d");
		else if( $date_format == "dd.mm.yyyy" )
			return $date->format("d.m.Y");
		else if( $date_format == "mm.dd.yyyy" )
			return $date->format("m.d.Y");
		else if( $date_format == "yyyy.mm.dd" )
			return $date->format("Y.m.d");
		else if( $date_format == "dd-mm-yyyy" )
			return $date->format("d-m-Y");
		else if( $date_format == "mm-dd-yyyy" )
			return $date->format("m-d-Y");
		else if( $date_format == "yyyy-dd-mm" )
			return $date->format("Y-m-d");
		else if( $date_format == "mmm dd" )
			return  utf8_encode(strftime("%b %e", $date->getTimestamp())); //http://php.net/manual/en/function.strftime.php
		else if( $date_format == "mmmm dd, yyyy" )
			//return $date->format('F j, Y');
			//return  utf8_encode(strftime("%A, %e %B", $date->getTimestamp()));
			return  utf8_encode(strftime("%B %e, %G", $date->getTimestamp())); //http://php.net/manual/en/function.strftime.php
		
		return $date->format($default_format);
	}
	public function format_data_according_wordpress_settings($date)
	{
			try{
			$date = new DateTime($date); //yyyy-mm-dd
		}catch(Exception $e){return $date;}
		
		return $date->format(get_option('date_format')." ".get_option('time_format'));
	}
	private function get_next_available_date($starting_date, $estimation_rule, $consider_dispatch_dalay, $apply_cutoff_additional_day = false)
	{
		/* Format:
			["working_days"]=> //0 (for Sunday) through 6 (for Saturday)
			  array(2) {
				[0]=>
				string(1) "2"
				[1]=>
				string(1) "5"
			  }
			  */
		//$found = false;	
		$days_left = $estimation_rule['days_delay'] + 1;
		$number_of_days_added =  /* $days_left > 0 ||  $apply_cutoff_additional_day  ? 1 :*/ 0;
		/* wcst_var_dump($apply_cutoff_additional_day);
		wcst_var_dump($days_left); */
		/*if($apply_cutoff_additional_day  && $days_left > 0  ) 
		{
			$days_left++;
		}*/
		//wcst_var_dump($days_left);
		$original_day_of_the_week = $starting_date['day_of_the_week'];
		$original_day_of_the_month = $starting_date['day_of_the_month'];
		$original_month = $starting_date['month'];
		$original_year = $starting_date['year'];
		do
		{
			$found = false;
			$starting_date_to_string = $original_year."-".$original_month."-".$original_day_of_the_month;
			$starting_date['day_of_the_week'] = ($number_of_days_added + $original_day_of_the_week)%7;
			
			$starting_date["day_of_the_month"] = date('j', strtotime($starting_date_to_string.' + '.($number_of_days_added).' days'));
			$starting_date["month"] = date('n', strtotime($starting_date_to_string. ' + '.($number_of_days_added).' days'));
			$starting_date["year"] = date('Y', strtotime($starting_date_to_string. ' + '.($number_of_days_added).' days'));
			
			
			/* wcst_var_dump($number_of_days_added);
			wcst_var_dump($starting_date_to_string);*/
			//wcst_var_dump($estimation_rule['working_days']);  
			//wcst_var_dump($starting_date['day_of_the_week']);  
			//wcst_var_dump($starting_date['day_of_the_month']);  
			foreach($estimation_rule["working_days"] as $working_days)
			{
				 
				//wcst_var_dump($working_days);
				/*wcst_var_dump($starting_date["day_of_the_month"]);  */
				//wcst_var_dump($starting_date['day_of_the_week']);																																//+2 : stand  are counted
				if($starting_date['day_of_the_week'] == $working_days  /*&& (!$consider_dispatch_dalay || $estimation_rule['days_delay'] == 0 ||  $number_of_days_added > $estimation_rule['days_delay'] + 1)*/ )
				{
					$found = true;
					$days_left -- ;
				}
			}
			$is_non_working_day = $this->check_if_date_is_a_non_working_day($starting_date,$estimation_rule);
			if($is_non_working_day)
			{
				$found = false;
				$days_left++ ;
			}
			$number_of_days_added++; 
		}while(!$found || $days_left > 0);
		//wcst_var_dump($starting_date);	
		return $starting_date;
	}
	private function check_if_date_is_a_non_working_day($date, $estimation_rule)
	{
		/*Format:
		  ["non_working_days"]=>
		  array(2) {
			[0]=>
			array(2) {
			  ["day"]=>
			  string(1) "1"
			  ["month"]=>
			  string(1) "3"
			}
			[1]=>
			array(2) {
			  ["day"]=>
			  string(1) "1"
			  ["month"]=>
			  string(2) "11"
			}
		  }
		  */
		if(is_array($estimation_rule["non_working_days"]))
			foreach($estimation_rule["non_working_days"] as $non_working_days)
			{
				if($date['day_of_the_month'] == $non_working_days['day'] && $date['month'] == $non_working_days['month'])
				return true;
					
			}
			return false;
	}
	public function is_valid_date_format($date)
	{
		
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
			return true;
		} else {
			return false;
}
	}
}
?>