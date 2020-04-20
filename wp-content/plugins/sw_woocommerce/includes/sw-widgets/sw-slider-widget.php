<?php
/**
	* SW Woocommerce Slider
	* Register Widget Woocommerce Slider
	* @author 		flytheme
	* @version     1.0.0
	**/
	if ( !class_exists('sw_woo_slider_widget') ) {
		class sw_woo_slider_widget extends WP_Widget {
		/**
		 * Widget setup.
		 */
		private $snumber = 1;

		function __construct(){
			/* Widget settings. */
			$widget_ops = array( 'classname' => 'sw_woo_slider_widget', 'description' => __('Sw Woo Slider', 'sw_woocommerce') );

			/* Widget control settings. */
			$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'sw_woo_slider_widget' );

			/* Create the widget. */
			parent::__construct( 'sw_woo_slider_widget', __('Sw Woo Slider widget', 'sw_woocommerce'), $widget_ops, $control_ops );
			
			/* Create Shortcode */
			add_shortcode( 'woo_slide', array( $this, 'WS_Shortcode' ) );
			
			/* Create Vc_map */
			if ( class_exists('Vc_Manager') ) {
				add_action( 'vc_before_init', array( $this, 'WS_integrateWithVC' ), 20 );
			}

		}
		
		public function generateID() {
			return $this->id_base . '_' . (int) $this->snumber++;
		}
		
		/**
		* Add Vc Params
		**/
		function WS_integrateWithVC(){
			$terms = get_terms( 'product_cat', array( 'parent' => '', 'hide_empty' => false ) );
			$term = array( __( 'All Categories', 'sw_woocommerce' ) => '' );
			if( count( $terms )  > 0 ){
				foreach( $terms as $cat ){
					$term[$cat->name] = $cat -> slug;
				}
			}
			vc_map( array(
				"name" => __( "SW Woocommerce Slider", 'sw_woocommerce' ),
				"base" => "woo_slide",
				"icon" => "icon-wpb-ytc",
				"class" => "",
				"category" => __( "SW Shortcodes", 'sw_woocommerce'),
				"params" => array(
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Title", 'sw_woocommerce' ),
						"param_name" => "title1",
						"admin_label" => true,
						"value" => '',
						"description" => __( "Title", 'sw_woocommerce' )
						),
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Product Title Length", 'sw_woocommerce' ),
						"param_name" => "title_length",
						"admin_label" => true,
						"value" => 0,
						"description" => __( "Choose Product Title Length if you want to trim word, leave 0 to not trim word", 'sw_woocommerce' )
						),		
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Description", 'sw_woocommerce' ),
						"param_name" => "description",
						"admin_label" => true,
						"value" => '',
						"description" => __( "Description", 'sw_woocommerce' )
						),
					array(
						'type' => 'attach_images',
						'heading' => __( 'Select Image', 'sw_woocommerce' ),
						'param_name' => 'image',
						'description' => __( 'Select Image', 'sw_woocommerce' ),
						'dependency' => array(
							'element' => 'layout',
							'value' => array( 'childcat', 'childcat1', 'childcat2' ),
						)
					),
					 array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Banner Links", 'sw_woocommerce' ),
						"param_name" => "banner_links",
						"value" => '',
						"description" => __( "Each banner link seperate by commas.", 'sw_woocommerce' ),
						"dependency" => array( 
							'element' => 'layout',
							'value' => array( 'childcat', 'childcat1', 'childcat2' ),
							)
						),
					 array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Select Style", 'sw_woocommerce' ),
						"param_name" => "style",
						"admin_label" => true,
						"value" => array('' => 'Select', 'style1' => 'style1', 'style2' => 'style2', 'style3' => 'style3'),
						"description" => __( "Select Style", 'sw_woocommerce' ),
						'dependency' => array(
							'element' => 'layout',
							'value' => array( 'childcat' )
						),
					 ),
					array(
						'type' => 'textfield',
						'heading' => __( 'Select Icon Mobile', 'sw_woocommerce' ),
						'param_name' => 'icon_m',
						'description' => __( 'Select Icon FontAwesome', 'sw_woocommerce' ),
						'dependency' => array(
							'element' => 'layout',
							'value' => array( 'theme_mobile','theme_mobile2'),
							)
						),
					array(
						'type' => 'date',
						'heading' => __( 'Countdown Date', 'sw_woocommerce' ),
						'param_name' => 'date',
						'value' =>'',
						'description' => __( 'Countdown Date', 'sw_woocommerce' ),
						"admin_label" => true,
						'dependency' => array(
							'element' => 'layout',
							'value' => array( 'theme_mobile2','bestsales'),
							)
						),
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Category", 'sw_woocommerce' ),
						"param_name" => "category",
						"admin_label" => true,
						"value" => $term,
						"description" => __( "Select Categories", 'sw_woocommerce' )
						),
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Order By", 'sw_woocommerce' ),
						"param_name" => "orderby",
						"admin_label" => true,
						"value" => array('Name' => 'name', 'Author' => 'author', 'Date' => 'date', 'Modified' => 'modified', 'Parent' => 'parent', 'ID' => 'ID', 'Random' =>'rand', 'Comment Count' => 'comment_count'),
						"description" => __( "Order By", 'sw_woocommerce' )
						),
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Order", 'sw_woocommerce' ),
						"param_name" => "order",
						"admin_label" => true,
						"value" => array('Descending' => 'DESC', 'Ascending' => 'ASC'),
						"description" => __( "Order", 'sw_woocommerce' )
						),
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Number Of Post", 'sw_woocommerce' ),
						"param_name" => "numberposts",
						"admin_label" => true,
						"value" => 5,
						"description" => __( "Number Of Post", 'sw_woocommerce' )
						),
					 array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Number Of Child Category", 'sw_woocommerce' ),
						"param_name" => "number_child",
						"admin_label" => true,
						"value" => 5,
						"description" => __( "Number child category will show.", 'sw_woocommerce' ),
						'dependency' => array(
							'element' => 'layout',
							'value' => array( 'childcat', 'childcat1', 'childcat2' )
						),
					 ),
					  array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Number Best Seller", "sw_woocommerce" ),
						"param_name" => "bnumber",
						"admin_label" => true,
						"value" => 5,
						"description" => __( "Number of items best seller", "sw_woocommerce" ),
							'dependency' => array(
							'element' => 'layout',
							'value' => 'childcat1' ,
						),
					),
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Number row per column", 'sw_woocommerce' ),
						"param_name" => "item_row",
						"admin_label" => true,
						"value" =>array(1,2,3,4),
						"description" => __( "Number row per column", 'sw_woocommerce' )
						),

					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Number of Columns >1200px: ", 'sw_woocommerce' ),
						"param_name" => "columns",
						"admin_label" => true,
						"value" => array(1,2,3,4,5,6),
						"description" => __( "Number of Columns >1200px:", 'sw_woocommerce' )
						),
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Number of Columns on 992px to 1199px:", 'sw_woocommerce' ),
						"param_name" => "columns1",
						"admin_label" => true,
						"value" => array(1,2,3,4,5,6),
						"description" => __( "Number of Columns on 992px to 1199px:", 'sw_woocommerce' )
						),
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Number of Columns on 768px to 991px:", 'sw_woocommerce' ),
						"param_name" => "columns2",
						"admin_label" => true,
						"value" => array(1,2,3,4,5,6),
						"description" => __( "Number of Columns on 768px to 991px:", 'sw_woocommerce' )
						),
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Number of Columns on 480px to 767px:", 'sw_woocommerce' ),
						"param_name" => "columns3",
						"admin_label" => true,
						"value" => array(1,2,3,4,5,6),
						"description" => __( "Number of Columns on 480px to 767px:", 'sw_woocommerce' )
						),
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Number of Columns in 480px or less than:", 'sw_woocommerce' ),
						"param_name" => "columns4",
						"admin_label" => true,
						"value" => array(1,2,3,4,5,6),
						"description" => __( "Number of Columns in 480px or less than:", 'sw_woocommerce' )
						),
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Speed", 'sw_woocommerce' ),
						"param_name" => "speed",
						"admin_label" => true,
						"value" => 1000,
						"description" => __( "Speed Of Slide", 'sw_woocommerce' )
						),
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Auto Play", 'sw_woocommerce' ),
						"param_name" => "autoplay",
						"admin_label" => true,
						"value" => array( 'False' => 'false', 'True' => 'true' ),
						"description" => __( "Auto Play", 'sw_woocommerce' )
						),
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Interval", 'sw_woocommerce' ),
						"param_name" => "interval",
						"admin_label" => true,
						"value" => 5000,
						"description" => __( "Interval", 'sw_woocommerce' )
						),
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Layout", 'sw_woocommerce' ),
						"param_name" => "layout",
						"admin_label" => true,
						"value" => array( 'Layout Default' => 'default', 'Layout Most Views' => 'default1', 'Layout Most Views 2' => 'mostviewed2', 'Layout Most Views 3' => 'mostviewed3', 'Layout Flash Sale' => 'bestsales', 'Layout Child Category' => 'childcat',
						'Layout Child Category2' => 'childcat1', 'Layout Child Category3' => 'childcat2', 'Layout Child Category4' => 'childcat3', 'Layout Toprated' => 'toprated', 'Layout Mobile1' => 'theme_mobile', 'Layout Mobile2' => 'theme_mobile2' ),
						"description" => __( "Layout", 'sw_woocommerce' )
						),
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => __( "Total Items Slided", 'sw_woocommerce' ),
						"param_name" => "scroll",
						"admin_label" => true,
						"value" => 1,
						"description" => __( "Total Items Slided", 'sw_woocommerce' )
						),
					)
) );
}
		/**
			** Add Shortcode
			**/
			function WS_Shortcode( $atts ){
				extract( shortcode_atts(
					array(
						'title1' => '',	
						'title_length' => 0,
						'description' => '',
						'image' =>'',
						'banner_links' => '',	
						'style' => '',	
						'icon_m'=> '',
						'date' =>'',
						'orderby' => 'name',
						'order'	=> 'DESC',
						'category' => '',
						'numberposts' => 5,
						'number_child' => 4,
						'bnumber' => 5,
						'length' => 25,
						'item_row'=> 1,
						'columns' => 4,
						'columns1' => 4,
						'columns2' => 3,
						'columns3' => 2,
						'columns4' => 1,
						'speed' => 1000,
						'autoplay' => 'false',
						'interval' => 5000,
						'layout'  => 'default',
						'scroll' => 1
						), $atts )
				);
				$path = sw_override_check( 'sw-slider', 'default' );
				switch ( $layout ) {
					case 'default':
					$path = sw_override_check( 'sw-slider', 'default' );
					break;	
					case 'default1':
					$path = sw_override_check( 'sw-slider', 'default1' );
					break;
					case 'mostviewed2':
					$path = sw_override_check( 'sw-slider', 'most-viewed' );
					break;
					case 'mostviewed3':
					$path = sw_override_check( 'sw-slider', 'most-viewed2' );
					break;
					case 'bestsales':
					$path = sw_override_check( 'sw-slider', 'bestsales' );
					break;
					case 'childcat':
					$path = sw_override_check( 'sw-slider', 'childcat' );
					break;
					case 'childcat1':
					$path = sw_override_check( 'sw-slider', 'childcat1' );
					break;
					case 'childcat2':
					$path = sw_override_check( 'sw-slider', 'childcat2' );
					break;
					case 'childcat3':
					$path = sw_override_check( 'sw-slider', 'childcat3' );
					break;
					case 'toprated':
					$path = sw_override_check( 'sw-slider', 'toprated' );
					break;
					case 'theme_mobile':
					$path = sw_override_check( 'sw-slider', 'theme-mobile' );
					break;
					case 'theme_mobile2':
					$path = sw_override_check( 'sw-slider', 'theme-mobile2' );
					break;				

					default:
					$path = sw_override_check( 'sw-slider', 'default' );
				}
				ob_start();
					include($path);
				$content = ob_get_clean();
				return $content;
			}
		/**
			* Cut string
			**/
			public function ya_trim_words( $text, $num_words = 30, $more = null ) {
				$text = strip_shortcodes( $text);
				$text = apply_filters('the_content', $text);
				$text = str_replace(']]>', ']]&gt;', $text);
				return wp_trim_words($text, $num_words, $more);
			}
		/**
		 * Display the widget on the screen.
		 */
		public function widget( $args, $instance ) {
			wp_reset_postdata();
			extract($args);
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			$description1 = apply_filters( 'widget_description', empty( $instance['description1'] ) ? '' : $instance['description1'], $instance, $this->id_base );
			echo $before_widget;
			if ( !empty( $title ) && !empty( $description1 ) ) { echo $before_title . $title . $after_title . '<h5 class="category_description clearfix">' . $description1 . '</h5>'; }
			else if (!empty( $title ) && $description1==NULL ){ echo $before_title . $title . $after_title; }
			
			if ( !isset($instance['category']) ){
				$instance['category'] = array();
			}
			$id = $this -> number;
			extract($instance);

			if ( !array_key_exists('widget_template', $instance) ){
				$instance['widget_template'] = 'default';
			}
			
			if ( $tpl =  sw_override_check( 'sw-slider', $instance['widget_template'] ) ){ 			
				$link_img = plugins_url('images/', __FILE__);
				$widget_id = $args['widget_id'];		
				include $tpl;
			}

			/* After widget (defined by themes). */
			echo $after_widget;
		}    
		
		/**
		 * Update the widget settings.
		 */
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			// strip tag on text field
			$instance['title1'] = strip_tags( $new_instance['title1'] );
			$instance['title_length'] = intval( $new_instance['title_length'] );
			$instance['description'] = strip_tags( $new_instance['description'] );
			// int or array
			if ( array_key_exists('category', $new_instance) ){
				if ( is_array($new_instance['category']) ){
					$instance['category'] = $new_instance['category'];
				} else {
					$instance['category'] = $new_instance['category'];
				}
			}
			if ( array_key_exists('style', $new_instance) ){
				$instance['style'] = strip_tags( $new_instance['style'] );
			}
			if ( array_key_exists('orderby', $new_instance) ){
				$instance['orderby'] = strip_tags( $new_instance['orderby'] );
			}

			if ( array_key_exists('order', $new_instance) ){
				$instance['order'] = strip_tags( $new_instance['order'] );
			}

			if ( array_key_exists('numberposts', $new_instance) ){
				$instance['numberposts'] = intval( $new_instance['numberposts'] );
			}
			
			if ( array_key_exists('number_child', $new_instance) ){
				$instance['number_child'] = intval( $new_instance['number_child'] );
			}
			
			if ( array_key_exists('length', $new_instance) ){
				$instance['length'] = intval( $new_instance['length'] );
			}
			
			if ( array_key_exists('item_row', $new_instance) ){
				$instance['item_row'] = intval( $new_instance['item_row'] );
			}
			
			if ( array_key_exists('banner_links', $new_instance) ){
				$instance['banner_links'] = esc_url( $new_instance['banner_links'] );
			}
			
			if ( array_key_exists('image', $new_instance) ){
				$instance['image'] = intval( $new_instance['image'] );
			}
			if ( array_key_exists('date', $new_instance) ){
				$instance['date'] = $new_instance['date'];
			}
			if ( array_key_exists('columns', $new_instance) ){
				$instance['columns'] = intval( $new_instance['columns'] );
			}
			if ( array_key_exists('columns1', $new_instance) ){
				$instance['columns1'] = intval( $new_instance['columns1'] );
			}
			if ( array_key_exists('columns2', $new_instance) ){
				$instance['columns2'] = intval( $new_instance['columns2'] );
			}
			if ( array_key_exists('columns3', $new_instance) ){
				$instance['columns3'] = intval( $new_instance['columns3'] );
			}
			if ( array_key_exists('columns4', $new_instance) ){
				$instance['columns4'] = intval( $new_instance['columns4'] );
			}
			if ( array_key_exists('interval', $new_instance) ){
				$instance['interval'] = intval( $new_instance['interval'] );
			}
			if ( array_key_exists('speed', $new_instance) ){
				$instance['speed'] = intval( $new_instance['speed'] );
			}
			if ( array_key_exists('start', $new_instance) ){
				$instance['start'] = intval( $new_instance['start'] );
			}
			if ( array_key_exists('scroll', $new_instance) ){
				$instance['scroll'] = intval( $new_instance['scroll'] );
			}	
			if ( array_key_exists('autoplay', $new_instance) ){
				$instance['autoplay'] = strip_tags( $new_instance['autoplay'] );
			}
			$instance['widget_template'] = strip_tags( $new_instance['widget_template'] );
			

			
			return $instance;
		}

		function category_select( $field_name, $opts = array(), $field_value = null ){
			$default_options = array(
				'multiple' => false,
				'disabled' => false,
				'size' => 5,
				'class' => 'widefat',
				'required' => false,
				'autofocus' => false,
				'form' => false,
				);
			$opts = wp_parse_args($opts, $default_options);

			if ( (is_string($opts['multiple']) && strtolower($opts['multiple'])=='multiple') || (is_bool($opts['multiple']) && $opts['multiple']) ){
				$opts['multiple'] = 'multiple';
				if ( !is_numeric($opts['size']) ){
					if ( intval($opts['size']) ){
						$opts['size'] = intval($opts['size']);
					} else {
						$opts['size'] = 5;
					}
				}
				if (array_key_exists('allow_select_all', $opts) && $opts['allow_select_all']){
					unset($opts['allow_select_all']);
					$allow_select_all = '<option value="">All Categories</option>';
				}
			} else {
				// is not multiple
				unset($opts['multiple']);
				unset($opts['size']);
				if (is_array($field_value)){
					$field_value = array_shift($field_value);
				}
				if (array_key_exists('allow_select_all', $opts) && $opts['allow_select_all']){
					unset($opts['allow_select_all']);
					$allow_select_all = '<option value="">All Categories</option>';
				}
			}

			if ( (is_string($opts['disabled']) && strtolower($opts['disabled'])=='disabled') || is_bool($opts['disabled']) && $opts['disabled'] ){
				$opts['disabled'] = 'disabled';
			} else {
				unset($opts['disabled']);
			}

			if ( (is_string($opts['required']) && strtolower($opts['required'])=='required') || (is_bool($opts['required']) && $opts['required']) ){
				$opts['required'] = 'required';
			} else {
				unset($opts['required']);
			}

			if ( !is_string($opts['form']) ) unset($opts['form']);

			if ( !isset($opts['autofocus']) || !$opts['autofocus'] ) unset($opts['autofocus']);

			$opts['id'] = $this->get_field_id($field_name);

			$opts['name'] = $this->get_field_name($field_name);
			if ( isset($opts['multiple']) ){
				$opts['name'] .= '[]';
			}
			$select_attributes = '';
			foreach ( $opts as $an => $av){
				$select_attributes .= "{$an}=\"{$av}\" ";
			}
			
			$categories = get_terms('product_cat');
			//print '<pre>'; var_dump($categories);
			// if (!$templates) return '';
			$all_category_ids = array();
			foreach ($categories as $cat) $all_category_ids[] = $cat->slug;
			
			$is_valid_field_value = in_array($field_value, $all_category_ids);
			if (!$is_valid_field_value && is_array($field_value)){
				$intersect_values = array_intersect($field_value, $all_category_ids);
				$is_valid_field_value = count($intersect_values) > 0;
			}
			if (!$is_valid_field_value){
				$field_value = '';
			}

			$select_html = '<select ' . $select_attributes . '>';
			if (isset($allow_select_all)) $select_html .= $allow_select_all;
			foreach ($categories as $cat){			
				$select_html .= '<option value="' . $cat->slug . '"';
				if ($cat->slug == $field_value || (is_array($field_value)&&in_array($cat->slug, $field_value))){ $select_html .= ' selected="selected"';}
				$select_html .=  '>'.$cat->name.'</option>';
			}
			$select_html .= '</select>';
			return $select_html;
		}
		

		/**
		 * Displays the widget settings controls on the widget panel.
		 * Make use of the get_field_id() and get_field_name() function
		 * when creating your form elements. This handles the confusing stuff.
		 */
		public function form( $instance ) {

			/* Set up some default widget settings. */
			$defaults = array();
			$instance = wp_parse_args( (array) $instance, $defaults ); 		

			$title1 			= isset( $instance['title1'] )    	? strip_tags($instance['title1']) : '';
			$title_length	= isset( $instance['title_length'] )  	? intval($instance['title_length']) : 0;
			$description 	= isset( $instance['description'] )   	? strip_tags($instance['description']) : '';
			$categoryid 	= isset( $instance['category'] )  		? $instance['category'] : '';
			$style      	= isset( $instance['style'] )       	? strip_tags($instance['style']) : 'style1';
			$banner_links	= isset( $instance['banner_links'] )    ? strip_tags($instance['banner_links']) : '';
			$image    		= isset( $instance['image'] )     		? strip_tags($instance['image']) : '';
			$date    		= isset( $instance['date'] )     		? strip_tags($instance['date']) : '';
			$orderby    	= isset( $instance['orderby'] )     	? strip_tags($instance['orderby']) : 'ID';
			$order      	= isset( $instance['order'] )       	? strip_tags($instance['order']) : 'ASC';
			$number     	= isset( $instance['numberposts'] ) 	? intval($instance['numberposts']) : 5;
			$number_child   = isset( $instance['number_child'] ) 	? intval($instance['number_child']) : 5;
			$length     	= isset( $instance['length'] )      	? intval($instance['length']) : 25;
			$item_row     	= isset( $instance['item_row'] )      	? intval($instance['item_row']) : 1;
			$columns     	= isset( $instance['columns'] )      	? intval($instance['columns']) : 1;
			$columns1     	= isset( $instance['columns1'] )     	? intval($instance['columns1']) : 1;
			$columns2     	= isset( $instance['columns2'] )      	? intval($instance['columns2']) : 1;
			$columns3     	= isset( $instance['columns3'] )      	? intval($instance['columns3']) : 1;
			$columns4     	= isset( $instance['columns'] )      	? intval($instance['columns4']) : 1;
			$autoplay     	= isset( $instance['autoplay'] )      	? strip_tags($instance['autoplay']) : 'false';
			$interval     	= isset( $instance['interval'] )      	? intval($instance['interval']) : 5000;
			$speed     		= isset( $instance['speed'] )      		? intval($instance['speed']) : 1000;
			$scroll     	= isset( $instance['scroll'] )      	? intval($instance['scroll']) : 1;
			$widget_template   	= isset( $instance['widget_template'] ) ? strip_tags($instance['widget_template']) : 'default';


			?>		
		</p> 
		<div style="background: Blue; color: white; font-weight: bold; text-align:center; padding: 3px"> * Data Config * </div>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('title1'); ?>"><?php _e('Title', 'sw_woocommerce')?></label>
		<br />
		<input class="widefat" id="<?php echo $this->get_field_id('title1'); ?>" name="<?php echo $this->get_field_name('title1'); ?>"
		type="text"	value="<?php echo esc_attr($title1); ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('title_length'); ?>"><?php _e('Product Title Length', 'sw_woocommerce')?></label>
		<br />
		<input class="widefat" id="<?php echo $this->get_field_id('title_length'); ?>" name="<?php echo $this->get_field_name('title_length'); ?>"
		type="text"	value="<?php echo esc_attr($title_length); ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description', 'sw_woocommerce')?></label>
		<br />
		<input class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"
		type="text"	value="<?php echo esc_attr($description); ?>" />
	</p>

	<p id="wgd-<?php echo $this->get_field_id('category'); ?>">
		<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category', 'sw_woocommerce')?></label>
		<br />
		<?php echo $this->category_select('category', array('allow_select_all' => true), $categoryid); ?>
	</p>
	
	<p>
		<label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Style', 'sw_woocommerce')?></label>
		<br />
		<select class="widefat"
			id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>">
			<option value="style1" <?php if ($style=='style1'){?> selected="selected"
				<?php } ?>>
				<?php _e('Style1', 'sw_woocommerce')?>
			</option>
			<option value="style2" <?php if ($order=='style2'){?> selected="selected"	<?php } ?>>
				<?php _e('Style2', 'sw_woocommerce')?>
			</option>
		</select>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Orderby', 'sw_woocommerce')?></label>
		<br />
		<?php $allowed_keys = array('name' => 'Name', 'author' => 'Author', 'date' => 'Date', 'title' => 'Title', 'modified' => 'Modified', 'parent' => 'Parent', 'ID' => 'ID', 'rand' =>'Rand', 'comment_count' => 'Comment Count'); ?>
		<select class="widefat"
		id="<?php echo $this->get_field_id('orderby'); ?>"
		name="<?php echo $this->get_field_name('orderby'); ?>">
		<?php
		$option ='';
		foreach ($allowed_keys as $value => $key) :
			$option .= '<option value="' . $value . '" ';
		if ($value == $orderby){
			$option .= 'selected="selected"';
		}
		$option .=  '>'.$key.'</option>';
		endforeach;
		echo $option;
		?>
	</select>
</p>

<p>
	<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order', 'sw_woocommerce')?></label>
	<br />
	<select class="widefat"
	id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
	<option value="DESC" <?php if ($order=='DESC'){?> selected="selected"
		<?php } ?>>
		<?php _e('Descending', 'sw_woocommerce')?>
	</option>
	<option value="ASC" <?php if ($order=='ASC'){?> selected="selected"	<?php } ?>>
		<?php _e('Ascending', 'sw_woocommerce')?>
	</option>
</select>
</p>

<p>
	<label for="<?php echo $this->get_field_id('numberposts'); ?>"><?php _e('Number of Posts', 'sw_woocommerce')?></label>
	<br />
	<input class="widefat" id="<?php echo $this->get_field_id('numberposts'); ?>" name="<?php echo $this->get_field_name('numberposts'); ?>"
	type="text"	value="<?php echo esc_attr($number); ?>" />
</p>

<p>
	<label for="<?php echo $this->get_field_id('number_child'); ?>"><?php _e('Number child children of Posts', 'sw_woocommerce')?></label>
	<br />
	<input class="widefat" id="<?php echo $this->get_field_id('number_child'); ?>" name="<?php echo $this->get_field_name('number_child'); ?>"
		type="text"	value="<?php echo esc_attr($number_child); ?>" />
</p>
			
<p>
	<label for="<?php echo $this->get_field_id('length'); ?>"><?php _e('Excerpt length (in words): ', 'sw_woocommerce')?></label>
	<br />
	<input class="widefat"
	id="<?php echo $this->get_field_id('length'); ?>" name="<?php echo $this->get_field_name('length'); ?>" type="text" 
	value="<?php echo esc_attr($length); ?>" />
</p> 

<p>
	<label for="<?php echo $this->get_field_id('banner_links'); ?>"><?php _e('Banner Links', 'sw_woocommerce')?></label>
	<br />
	<input class="widefat" id="<?php echo $this->get_field_id('banner_links'); ?>" name="<?php echo $this->get_field_name('banner_links'); ?>"
		type="text"	value="<?php echo esc_attr($banner_links); ?>" />
</p>

<p>
	<label for="<?php echo $this->get_field_id('image'); ?>"><?php _e('Image attachment ID', 'sw_woocommerce')?></label>
	<br />
	<input class="widefat" id="<?php echo $this->get_field_id('image'); ?>" name="<?php echo $this->get_field_name('image'); ?>"
		type="text"	value="<?php echo esc_attr($image); ?>" />
</p>

<p>
	<label for="<?php echo $this->get_field_id('date'); ?>"><?php _e('Date ( for layout best seller )', 'sw_woocommerce')?></label>
	<br />
	<input class="widefat" id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>"
		type="date"	value="<?php echo esc_attr($date); ?>" />
</p>
			
<?php $row_number = array( '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5 ); ?>
<p>
	<label for="<?php echo $this->get_field_id('item_row'); ?>"><?php _e('Number row per column:  ', 'sw_woocommerce')?></label>
	<br />
	<select class="widefat"
	id="<?php echo $this->get_field_id('item_row'); ?>"
	name="<?php echo $this->get_field_name('item_row'); ?>">
	<?php
	$option ='';
	foreach ($row_number as $key => $value) :
		$option .= '<option value="' . $value . '" ';
	if ($value == $item_row){
		$option .= 'selected="selected"';
	}
	$option .=  '>'.$key.'</option>';
	endforeach;
	echo $option;
	?>
</select>
</p> 

<?php $number = array('1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6); ?>
<p>
	<label for="<?php echo $this->get_field_id('columns'); ?>"><?php _e('Number of Columns >1200px: ', 'sw_woocommerce')?></label>
	<br />
	<select class="widefat"
	id="<?php echo $this->get_field_id('columns'); ?>"
	name="<?php echo $this->get_field_name('columns'); ?>">
	<?php
	$option ='';
	foreach ($number as $key => $value) :
		$option .= '<option value="' . $value . '" ';
	if ($value == $columns){
		$option .= 'selected="selected"';
	}
	$option .=  '>'.$key.'</option>';
	endforeach;
	echo $option;
	?>
</select>
</p> 

<p>
	<label for="<?php echo $this->get_field_id('columns1'); ?>"><?php _e('Number of Columns on 992px to 1199px: ', 'sw_woocommerce')?></label>
	<br />
	<select class="widefat"
	id="<?php echo $this->get_field_id('columns1'); ?>"
	name="<?php echo $this->get_field_name('columns1'); ?>">
	<?php
	$option ='';
	foreach ($number as $key => $value) :
		$option .= '<option value="' . $value . '" ';
	if ($value == $columns1){
		$option .= 'selected="selected"';
	}
	$option .=  '>'.$key.'</option>';
	endforeach;
	echo $option;
	?>
</select>
</p> 

<p>
	<label for="<?php echo $this->get_field_id('columns2'); ?>"><?php _e('Number of Columns on 768px to 991px: ', 'sw_woocommerce')?></label>
	<br />
	<select class="widefat"
	id="<?php echo $this->get_field_id('columns2'); ?>"
	name="<?php echo $this->get_field_name('columns2'); ?>">
	<?php
	$option ='';
	foreach ($number as $key => $value) :
		$option .= '<option value="' . $value . '" ';
	if ($value == $columns2){
		$option .= 'selected="selected"';
	}
	$option .=  '>'.$key.'</option>';
	endforeach;
	echo $option;
	?>
</select>
</p> 

<p>
	<label for="<?php echo $this->get_field_id('columns3'); ?>"><?php _e('Number of Columns on 480px to 767px: ', 'sw_woocommerce')?></label>
	<br />
	<select class="widefat"
	id="<?php echo $this->get_field_id('columns3'); ?>"
	name="<?php echo $this->get_field_name('columns3'); ?>">
	<?php
	$option ='';
	foreach ($number as $key => $value) :
		$option .= '<option value="' . $value . '" ';
	if ($value == $columns3){
		$option .= 'selected="selected"';
	}
	$option .=  '>'.$key.'</option>';
	endforeach;
	echo $option;
	?>
</select>
</p> 

<p>
	<label for="<?php echo $this->get_field_id('columns4'); ?>"><?php _e('Number of Columns in 480px or less than: ', 'sw_woocommerce')?></label>
	<br />
	<select class="widefat"
	id="<?php echo $this->get_field_id('columns4'); ?>"
	name="<?php echo $this->get_field_name('columns4'); ?>">
	<?php
	$option ='';
	foreach ($number as $key => $value) :
		$option .= '<option value="' . $value . '" ';
	if ($value == $columns4){
		$option .= 'selected="selected"';
	}
	$option .=  '>'.$key.'</option>';
	endforeach;
	echo $option;
	?>
</select>
</p> 

<p>
	<label for="<?php echo $this->get_field_id('autoplay'); ?>"><?php _e('Auto Play', 'sw_woocommerce')?></label>
	<br />
	<select class="widefat"
	id="<?php echo $this->get_field_id('autoplay'); ?>" name="<?php echo $this->get_field_name('autoplay'); ?>">
	<option value="false" <?php if ($autoplay=='false'){?> selected="selected"
		<?php } ?>>
		<?php _e('False', 'sw_woocommerce')?>
	</option>
	<option value="true" <?php if ($autoplay=='true'){?> selected="selected"	<?php } ?>>
		<?php _e('True', 'sw_woocommerce')?>
	</option>
</select>
</p>

<p>
	<label for="<?php echo $this->get_field_id('interval'); ?>"><?php _e('Interval', 'sw_woocommerce')?></label>
	<br />
	<input class="widefat" id="<?php echo $this->get_field_id('interval'); ?>" name="<?php echo $this->get_field_name('interval'); ?>"
	type="text"	value="<?php echo esc_attr($interval); ?>" />
</p>

<p>
	<label for="<?php echo $this->get_field_id('speed'); ?>"><?php _e('Speed', 'sw_woocommerce')?></label>
	<br />
	<input class="widefat" id="<?php echo $this->get_field_id('speed'); ?>" name="<?php echo $this->get_field_name('speed'); ?>"
	type="text"	value="<?php echo esc_attr($speed); ?>" />
</p>


<p>
	<label for="<?php echo $this->get_field_id('scroll'); ?>"><?php _e('Total Items Slided', 'sw_woocommerce')?></label>
	<br />
	<input class="widefat" id="<?php echo $this->get_field_id('scroll'); ?>" name="<?php echo $this->get_field_name('scroll'); ?>"
	type="text"	value="<?php echo esc_attr($scroll); ?>" />
</p>

<p>
	<label for="<?php echo $this->get_field_id('widget_template'); ?>"><?php _e("Template", 'sw_woocommerce')?></label>
	<br/>

	<select class="widefat"
	id="<?php echo $this->get_field_id('widget_template'); ?>"	name="<?php echo $this->get_field_name('widget_template'); ?>">
	<option value="default" <?php if ($widget_template=='default'){?> selected="selected"
		<?php } ?>>
		<?php _e('Default', 'sw_woocommerce')?>		
	</option>			
	<option value="bestsales" <?php if ($widget_template=='bestsales'){?> selected="selected"
		<?php } ?>>
		<?php _e('Best Selling Slider', 'sw_woocommerce')?>
	</option>
	<option value="bestsales2" <?php if ($widget_template=='bestsales2'){?> selected="selected"
		<?php } ?>>
		<?php _e('Best Selling Slider2', 'sw_woocommerce')?>
	</option>
	<option value="bestsales3" <?php if ($widget_template=='bestsales3'){?> selected="selected"
		<?php } ?>>
		<?php _e('Best Selling Slider3', 'sw_woocommerce')?>
	</option>
	<option value="childcat3" <?php if ($widget_template=='childcat3'){?> selected="selected"
		<?php } ?>>
		<?php _e('Layout Child Category4', 'sw_woocommerce')?>
	</option>
	<option value="childcat4" <?php if ($widget_template=='childcat4'){?> selected="selected"
		<?php } ?>>
		<?php _e('Layout Child Category5', 'sw_woocommerce')?>
	</option>
	<option value="most-viewed" <?php if ($widget_template=='most-viewed'){?> selected="selected"
		<?php } ?>>
		<?php _e('Layout Most Viewed', 'sw_woocommerce')?>
	</option>
	<option value="most-viewed2" <?php if ($widget_template=='most-viewed2'){?> selected="selected"
		<?php } ?>>
		<?php _e('Layout Most Viewed 2', 'sw_woocommerce')?>
	</option>
	<option value="most-viewed3" <?php if ($widget_template=='most-viewed3'){?> selected="selected"
		<?php } ?>>
		<?php _e('Layout Most Viewed 3', 'sw_woocommerce')?>
	</option>
	<option value="most-viewed4" <?php if ($widget_template=='most-viewed4'){?> selected="selected"
		<?php } ?>>
		<?php _e('Layout Most Viewed 4', 'sw_woocommerce')?>
	</option>
	<option value="on-sale" <?php if ($widget_template=='on-sale'){?> selected="selected"
		<?php } ?>>
		<?php _e('Layout On Sale', 'sw_woocommerce')?>
	</option>
	<option value="tab-newarrival" <?php if ($widget_template=='tab-newarrival'){?> selected="selected"
		<?php } ?>>
		<?php _e('Layout Tab New Arrival', 'sw_woocommerce')?>
	</option>
	<option value="tab-featured" <?php if ($widget_template=='tab-featured'){?> selected="selected"
		<?php } ?>>
		<?php _e('Layout Tab Featured Product', 'sw_woocommerce')?>
	</option>
	<option value="tab-bestsales" <?php if ($widget_template=='tab-bestsales'){?> selected="selected"
		<?php } ?>>
		<?php _e('Layout Tab Bestsale Product', 'sw_woocommerce')?>
	</option>
	<option value="toprated" <?php if ($widget_template=='toprated'){?> selected="selected"
		<?php } ?>>
		<?php _e('Layout Top Rated', 'sw_woocommerce')?>
	</option>
</select>
</p>  
<?php
}	
}
}
?>