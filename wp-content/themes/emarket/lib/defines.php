<?php
$lib_dir = trailingslashit( str_replace( '\\', '/', get_template_directory() . '/lib/' ) );

if( !defined('EMARKET_DIR') ){
	define( 'EMARKET_DIR', $lib_dir );
}

if( !defined('EMARKET_URL') ){
	define( 'EMARKET_URL', trailingslashit( get_template_directory_uri() ) . 'lib' );
}

if( !defined('EMARKET_OPTIONS_URL') ){
	define( 'EMARKET_OPTIONS_URL', trailingslashit( get_template_directory_uri() ) . 'lib/options/' ); 
}

if ( !defined('SW_THEME') ){
	define( 'SW_THEME', 'emarket_theme' . get_locale() );
}

defined('EMARKET_THEME') or die;

if (!isset($content_width)) { $content_width = 940; }

define("EMARKET_PRODUCT_TYPE","product");
define("EMARKET_PRODUCT_DETAIL_TYPE","product_detail");

require_once( get_template_directory().'/lib/options.php' );
function emarket_Options_Setup(){
	global $emarket_options, $options, $options_args;

	$options = array();
	$options[] = array(
		'title' => esc_html__('General', 'emarket'),
		'desc' => wp_kses( __('<p class="description">The theme allows to build your own styles right out of the backend without any coding knowledge. Upload new logo and favicon or get their URL.</p>', 'emarket'), array( 'p' => array( 'class' => array() ) ) ),
			//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
			//You dont have to though, leave it emarket for default.
		'icon' => EMARKET_URL.'/options/img/glyphicons/glyphicons_019_cogwheel.png',
			//Lets leave this as a emarket section, no options just some intro text set above.
		'fields' => array(	

			array(
				'id' => 'sitelogo',
				'type' => 'upload',
				'title' => esc_html__('Logo Image', 'emarket'),
				'sub_desc' => esc_html__( 'Use the Upload button to upload the new logo and get URL of the logo', 'emarket' ),
				'std' => get_template_directory_uri().'/assets/img/logo-default.png'
				),

			array(
				'id' => 'favicon',
				'type' => 'upload',
				'title' => esc_html__('Favicon', 'emarket'),
				'sub_desc' => esc_html__( 'Use the Upload button to upload the custom favicon', 'emarket' ),
				'std' => ''
				),

			array(
				'id' => 'tax_select',
				'type' => 'multi_select_taxonomy',
				'title' => esc_html__('Select Taxonomy', 'emarket'),
				'sub_desc' => esc_html__( 'Select taxonomy to show custom term metabox', 'emarket' ),
				),

			array(
				'id' => 'title_length',
				'type' => 'text',
				'title' => esc_html__('Title Length Of Item Listing Page', 'emarket'),
				'sub_desc' => esc_html__( 'Choose title length if you want to trim word, leave 0 to not trim word', 'emarket' ),
				'std' => 0
				),
			array(
			   'id' => 'page_404',
			   'type' => 'pages_select',
			   'title' => esc_html__('404 Page Content', 'emarket'),
			   'sub_desc' => esc_html__('Select page 404 content', 'emarket'),
			   'std' => ''
			),
		)		
);

$options[] = array(
	'title' => esc_html__('Schemes', 'emarket'),
	'desc' => wp_kses( __('<p class="description">Custom color scheme for theme. Unlimited color that you can choose.</p>', 'emarket'), array( 'p' => array( 'class' => array() ) ) ),
			//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
			//You dont have to though, leave it emarket for default.
	'icon' => EMARKET_URL.'/options/img/glyphicons/glyphicons_163_iphone.png',
			//Lets leave this as a emarket section, no options just some intro text set above.
	'fields' => array(		
		array(
			'id' => 'scheme',
			'type' => 'radio_img',
			'title' => esc_html__('Color Scheme', 'emarket'),
			'sub_desc' => esc_html__( 'Select one of 3 predefined schemes', 'emarket' ),
			'desc' => '',
			'options' => array(
				'default' => array('title' => 'Default', 'img' => get_template_directory_uri().'/assets/img/default.png'),
				'orange' => array('title' => 'Orange', 'img' => get_template_directory_uri().'/assets/img/orange.png'),
				'orange2' => array('title' => 'Orange2', 'img' => get_template_directory_uri().'/assets/img/orange2.png'),
				'orange3' => array('title' => 'Orange3', 'img' => get_template_directory_uri().'/assets/img/orange3.png'),
				'orange4' => array('title' => 'Orange4', 'img' => get_template_directory_uri().'/assets/img/orange4.png'),
				'blue' => array('title' => 'Blue', 'img' => get_template_directory_uri().'/assets/img/blue.png'),
				'blue2' => array('title' => 'Blue 2', 'img' => get_template_directory_uri().'/assets/img/blue2.png'),
				'plum' => array('title' => 'Plum', 'img' => get_template_directory_uri().'/assets/img/plum.png'),
				'green' => array('title' => 'Green', 'img' => get_template_directory_uri().'/assets/img/green.png'),
				'green2' => array('title' => 'Green2', 'img' => get_template_directory_uri().'/assets/img/green2.png'),
				), //Must provide key => value(array:title|img) pairs for radio options
			'std' => 'default'
			),
		
		array(
			'id' => 'custom_color',
			'title' => esc_html__( 'Enable Custom Color', 'emarket' ),
			'type' => 'checkbox',
			'sub_desc' => esc_html__( 'Check this field to enable custom color and when you update your theme, custom color will not lose.', 'emarket' ),
			'desc' => '',
			'std' => '0'
		),

		array(
			'id' => 'developer_mode',
			'title' => esc_html__( 'Developer Mode', 'emarket' ),
			'type' => 'checkbox',
			'sub_desc' => esc_html__( 'Turn on/off compile less to css and custom color', 'emarket' ),
			'desc' => '',
			'std' => '0'
			),

		array(
			'id' => 'scheme_color',
			'type' => 'color',
			'title' => esc_html__('Color', 'emarket'),
			'sub_desc' => esc_html__('Select main custom color.', 'emarket'),
			'std' => ''
			),

		)
);

$options[] = array(
	'title' => esc_html__('Layout', 'emarket'),
	'desc' => wp_kses( __('<p class="description">SmartAddons Framework comes with a layout setting that allows you to build any number of stunning layouts and apply theme to your entries.</p>', 'emarket'), array( 'p' => array( 'class' => array() ) ) ),
			//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
			//You dont have to though, leave it emarket for default.
	'icon' => EMARKET_URL.'/options/img/glyphicons/glyphicons_319_sort.png',
			//Lets leave this as a emarket section, no options just some intro text set above.
	'fields' => array(
		array(
			'id' => 'layout_home',
			'type' => 'select',
			'title' => esc_html__('Box Layout', 'emarket'),
			'sub_desc' => esc_html__( 'Select Layout Box or Wide', 'emarket' ),
			'options' => array(
				'full' => esc_html__( 'Default', 'emarket' ),
				'boxed' => esc_html__( 'Boxed', 'emarket' ),
				'wide' => esc_html__( 'Wide 1650', 'emarket' )
				),
			'std' => 'full'
			),

		array(
			'id' => 'bg_box_img',
			'type' => 'upload',
			'title' => esc_html__('Background Box Image', 'emarket'),
			'sub_desc' => '',
			'std' => ''
			),
		array(
			'id' => 'sidebar_left_expand',
			'type' => 'select',
			'title' => esc_html__('Left Sidebar Expand', 'emarket'),
			'options' => array(
				'2' => '2/12',
				'3' => '3/12',
				'4' => '4/12',
				'5' => '5/12', 
				'6' => '6/12',
				'7' => '7/12',
				'8' => '8/12',
				'9' => '9/12',
				'10' => '10/12',
				'11' => '11/12',
				'12' => '12/12'
				),
			'std' => '3',
			'sub_desc' => esc_html__( 'Select width of left sidebar.', 'emarket' ),
			),

		array(
			'id' => 'sidebar_right_expand',
			'type' => 'select',
			'title' => esc_html__('Right Sidebar Expand', 'emarket'),
			'options' => array(
				'2' => '2/12',
				'3' => '3/12',
				'4' => '4/12',
				'5' => '5/12',
				'6' => '6/12',
				'7' => '7/12',
				'8' => '8/12',
				'9' => '9/12',
				'10' => '10/12',
				'11' => '11/12',
				'12' => '12/12'
				),
			'std' => '3',
			'sub_desc' => esc_html__( 'Select width of right sidebar medium desktop.', 'emarket' ),
			),
		array(
			'id' => 'sidebar_left_expand_md',
			'type' => 'select',
			'title' => esc_html__('Left Sidebar Medium Desktop Expand', 'emarket'),
			'options' => array(
				'2' => '2/12',
				'3' => '3/12',
				'4' => '4/12',
				'5' => '5/12',
				'6' => '6/12',
				'7' => '7/12',
				'8' => '8/12',
				'9' => '9/12',
				'10' => '10/12',
				'11' => '11/12',
				'12' => '12/12'
				),
			'std' => '4',
			'sub_desc' => esc_html__( 'Select width of left sidebar medium desktop.', 'emarket' ),
			),
		array(
			'id' => 'sidebar_right_expand_md',
			'type' => 'select',
			'title' => esc_html__('Right Sidebar Medium Desktop Expand', 'emarket'),
			'options' => array(
				'2' => '2/12',
				'3' => '3/12',
				'4' => '4/12',
				'5' => '5/12',
				'6' => '6/12',
				'7' => '7/12',
				'8' => '8/12',
				'9' => '9/12',
				'10' => '10/12',
				'11' => '11/12',
				'12' => '12/12'
				),
			'std' => '4',
			'sub_desc' => esc_html__( 'Select width of right sidebar.', 'emarket' ),
			),
		array(
			'id' => 'sidebar_left_expand_sm',
			'type' => 'select',
			'title' => esc_html__('Left Sidebar Tablet Expand', 'emarket'),
			'options' => array(
				'2' => '2/12',
				'3' => '3/12',
				'4' => '4/12',
				'5' => '5/12',
				'6' => '6/12',
				'7' => '7/12',
				'8' => '8/12',
				'9' => '9/12',
				'10' => '10/12',
				'11' => '11/12',
				'12' => '12/12'
				),
			'std' => '4',
			'sub_desc' => esc_html__( 'Select width of left sidebar tablet.', 'emarket' ),
			),
		array(
			'id' => 'sidebar_right_expand_sm',
			'type' => 'select',
			'title' => esc_html__('Right Sidebar Tablet Expand', 'emarket'),
			'options' => array(
				'2' => '2/12',
				'3' => '3/12',
				'4' => '4/12',
				'5' => '5/12',
				'6' => '6/12',
				'7' => '7/12',
				'8' => '8/12',
				'9' => '9/12',
				'10' => '10/12',
				'11' => '11/12',
				'12' => '12/12'
				),
			'std' => '4',
			'sub_desc' => esc_html__( 'Select width of right sidebar tablet.', 'emarket' ),
			),				
		)
);

$options[] = array(
	'title' => esc_html__('Header & Footer', 'emarket'),
	'desc' => wp_kses( __('<p class="description">SmartAddons Framework comes with a header and footer setting that allows you to build style header.</p>', 'emarket'), array( 'p' => array( 'class' => array() ) ) ),
			//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
			//You dont have to though, leave it emarket for default.
	'icon' => EMARKET_URL.'/options/img/glyphicons/glyphicons_336_read_it_later.png',
			//Lets leave this as a emarket section, no options just some intro text set above.
	'fields' => array(
		array(
			'id' => 'header_style',
			'type' => 'select',
			'title' => esc_html__('Header Style', 'emarket'),
			'sub_desc' => esc_html__('Select Header style', 'emarket'),
			'options' => array(
				'style1'  => esc_html__( 'Style 1', 'emarket' ),
				'style2'  => esc_html__( 'Style 2', 'emarket' ),
				'style3'  => esc_html__( 'Style 3', 'emarket' ),
				'style4'  => esc_html__( 'Style 4', 'emarket' ),
				'style5'  => esc_html__( 'Style 5', 'emarket' ),
				'style6'  => esc_html__( 'Style 6', 'emarket' ),
				'style7'  => esc_html__( 'Style 7', 'emarket' ),
				'style8'  => esc_html__( 'Style 8', 'emarket' ),
				'style9'  => esc_html__( 'Style 9', 'emarket' ),
				'style10'  => esc_html__( 'Style 10', 'emarket' ),
				'style11'  => esc_html__( 'Style 11', 'emarket' ),
				'style12'  => esc_html__( 'Style 12', 'emarket' ),
				'style13'  => esc_html__( 'Style 13', 'emarket' ),
				),
			'std' => 'style1'
			),

		array(
			'id' => 'disable_search',
			'title' => esc_html__( 'Disable Search', 'emarket' ),
			'type' => 'checkbox',
			'sub_desc' => esc_html__( 'Check this to disable search on header', 'emarket' ),
			'desc' => '',
			'std' => '0'
			),

		array(
			'id' => 'disable_cart',
			'title' => esc_html__( 'Disable Cart', 'emarket' ),
			'type' => 'checkbox',
			'sub_desc' => esc_html__( 'Check this to disable cart on header', 'emarket' ),
			'desc' => '',
			'std' => '0'
			),				

		array(
			'id' => 'footer_style',
			'type' => 'pages_select',
			'title' => esc_html__('Footer Style', 'emarket'),
			'sub_desc' => esc_html__('Select Footer style', 'emarket'),
			'std' => ''
			),

		array(
			'id' => 'copyright_style',
			'type' => 'select',
			'title' => esc_html__('Copyright Style', 'emarket'),
			'sub_desc' => esc_html__('Select Copyright style', 'emarket'),
			'options' => array(
				'style1'  => esc_html__( 'Style 1', 'emarket' ),
				'style2'  => esc_html__( 'Style 2', 'emarket' ),
				'style3'  => esc_html__( 'Style 3', 'emarket' ),
				'style4'  => esc_html__( 'Style 4', 'emarket' ),
				),
			'std' => 'style1'
			),

		array(
			'id' => 'footer_copyright',
			'type' => 'editor',
			'sub_desc' => '',
			'title' => esc_html__( 'Copyright text', 'emarket' )
			),	

		)
);

$options[] = array(
	'title' => esc_html__('Mobile Layout', 'emarket'),
	'desc' => wp_kses( __('<p class="description">SmartAddons Framework comes with a mobile setting home page layout.</p>', 'emarket'), array( 'p' => array( 'class' => array() ) ) ),
			//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
			//You dont have to though, leave it emarket for default.
	'icon' => EMARKET_URL.'/options/img/glyphicons/glyphicons_163_iphone.png',
			//Lets leave this as a emarket section, no options just some intro text set above.
	'fields' => array(				
		array(
			'id' => 'mobile_enable',
			'type' => 'checkbox',
			'title' => esc_html__('Enable Mobile Layout', 'emarket'),
			'sub_desc' => '',
			'desc' => '',
					'std' => '1'// 1 = on | 0 = off
					),

		array(
			'id' => 'mobile_logo',
			'type' => 'upload',
			'title' => esc_html__('Logo Mobile Image', 'emarket'),
			'sub_desc' => esc_html__( 'Use the Upload button to upload the new mobile logo', 'emarket' ),
			'std' => get_template_directory_uri().'/assets/img/logo-default.png'
			),
		
		array(
			'id' => 'mobile_logo_account',
			'type' => 'upload',
			'title' => esc_html__('Logo Mobile My Account Page', 'emarket'),
			'sub_desc' => esc_html__( 'Use the Upload button to upload the new mobile logo in my account page', 'emarket' ),
			'std' => get_template_directory_uri().'/assets/img/icon-myaccount.png'
		),

		array(
			'id' => 'sticky_mobile',
			'type' => 'checkbox',
			'title' => esc_html__('Sticky Mobile', 'emarket'),
			'sub_desc' => '',
			'desc' => '',
					'std' => '0'// 1 = on | 0 = off
					),

		array(
			'id' => 'mobile_content',
			'type' => 'pages_select',
			'title' => esc_html__('Mobile Layout Content', 'emarket'),
			'sub_desc' => esc_html__('Select content index for this mobile layout', 'emarket'),
			'std' => ''
			),

		array(
			'id' => 'mobile_header_style',
			'type' => 'select',
			'title' => esc_html__('Header Mobile Style', 'emarket'),
			'sub_desc' => esc_html__('Select header mobile style', 'emarket'),
			'options' => array(
				'mstyle1'  => esc_html__( 'Style 1', 'emarket' ),
				'mstyle2'  => esc_html__( 'Style 2', 'emarket' ),
				),
			'std' => 'style1'
			),

		array(
			'id' => 'mobile_footer_style',
			'type' => 'select',
			'title' => esc_html__('Footer Mobile Style', 'emarket'),
			'sub_desc' => esc_html__('Select footer mobile style', 'emarket'),
			'options' => array(
				'mstyle1'  => esc_html__( 'Style 1', 'emarket' ),
				'mstyle2'  => esc_html__( 'Style 2', 'emarket' ),
			),
			'std' => 'style1'
			),
		
		array(
			'id' => 'mobile_addcart',
			'type' => 'checkbox',
			'title' => esc_html__('Enable Add To Cart Button', 'emarket'),
			'sub_desc' => esc_html__( 'Enable Add To Cart Button on product listing', 'emarket' ),
			'desc' => '',
				'std' => '0'// 1 = on | 0 = off
		),
		
		array(
			'id' => 'mobile_header_inside',
			'type' => 'checkbox',
			'title' => esc_html__('Enable Header Other Pages', 'emarket'),
			'sub_desc' => esc_html__( 'Enable header in other pages which are different with homepage', 'emarket' ),
			'desc' => '',
				'std' => '0'// 1 = on | 0 = off
		),

		array(
			'id' => 'mobile_jquery',
			'type' => 'checkbox',
			'title' => esc_html__('Include Jquery Revolution', 'emarket'),
			'sub_desc' => '',
			'desc' => '',
			'std' => '0'// 1 = on | 0 = off
			),
	)
);

$options[] = array(
	'title' => esc_html__('Navbar Options', 'emarket'),
	'desc' => wp_kses( __('<p class="description">If you got a big site with a lot of sub menus we recommend using a mega menu. Just select the dropbox to display a menu as mega menu or dropdown menu.</p>', 'emarket'), array( 'p' => array( 'class' => array() ) ) ),
			//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
			//You dont have to though, leave it emarket for default.
	'icon' => EMARKET_URL.'/options/img/glyphicons/glyphicons_157_show_lines.png',
			//Lets leave this as a emarket section, no options just some intro text set above.
	'fields' => array(
		array(
			'id' => 'menu_type',
			'type' => 'select',
			'title' => esc_html__('Menu Type', 'emarket'),
			'options' => array( 'dropdown' => 'Dropdown Menu', 'mega' => 'Mega Menu' ),
			'std' => 'mega'
			),

		array(
			'id' => 'menu_location',
			'type' => 'menu_location_multi_select',
			'title' => esc_html__('Mega Menu Location', 'emarket'),
			'sub_desc' => esc_html__( 'Select theme location to active mega menu.', 'emarket' ),
			'std' => 'primary_menu'
			),		

		array(
			'id' => 'sticky_menu',
			'type' => 'checkbox',
			'title' => esc_html__('Active sticky menu', 'emarket'),
			'sub_desc' => '',
			'desc' => '',
			'std' => '0'// 1 = on | 0 = off
			),
		
		array(
			'id' => 'more_menu',
			'type' => 'checkbox',
			'title' => esc_html__('Active More Menu', 'emarket'),
			'sub_desc' => esc_html__('Active more menu if your primary menu is too long', 'emarket'),
			'desc' => '',
			'std' => '0'// 1 = on | 0 = off
			),

		array(
			'id' => 'menu_event',
			'type' => 'select',
			'title' => esc_html__('Menu Event', 'emarket'),
			'options' => array( '' => esc_html__( 'Hover Event', 'emarket' ), 'click' => esc_html__( 'Click Event', 'emarket' ) ),
			'std' => ''
			),

		array(
			'id' => 'menu_number_item',
			'type' => 'text',
			'title' => esc_html__( 'Number Item Vertical', 'emarket' ),
			'sub_desc' => esc_html__( 'Number item vertical to show', 'emarket' ),
			'std' => 8
			),	

		array(
			'id' => 'menu_title_text',
			'type' => 'text',
			'title' => esc_html__('Vertical Title Text', 'emarket'),
			'sub_desc' => esc_html__( 'Change title text on vertical menu', 'emarket' ),
			'std' => ''
			),

		array(
			'id' => 'menu_more_text',
			'type' => 'text',
			'title' => esc_html__('Vertical More Text', 'emarket'),
			'sub_desc' => esc_html__( 'Change more text on vertical menu', 'emarket' ),
			'std' => ''
			),

		array(
			'id' => 'menu_less_text',
			'type' => 'text',
			'title' => esc_html__('Vertical Less Text', 'emarket'),
			'sub_desc' => esc_html__( 'Change less text on vertical menu', 'emarket' ),
			'std' => ''
			),
		array(
			'id' => 'info_typon2',
			'type' => 'info',
			'title' => esc_html__( 'Responsive Menu Config', 'emarket' ),
			'desc' => '',
			'class' => 'emarket-opt-info'
		),
		
		array(
			'id' => 'mobile_menu',
			'type' => 'menu_location_multi_select',
			'title' => esc_html__('Mobile & Responsive Menu Location', 'emarket'),
			'sub_desc' => esc_html__( 'Select theme location to active mobile menu.', 'emarket' ),
			'std' => 'primary_menu'
		),
		
		array(
			'id' => 'mobile_menu_title',
			'type' => 'text',
			'title' => esc_html__('Mobile Menu Title', 'emarket'),
			'sub_desc' => esc_html__( 'Change title heading of menu responsive. If there are many menu, each title separated by commas.', 'emarket' ),
			'std' => ''
		),
		)
);
$options[] = array(
	'title' => esc_html__('Blog Options', 'emarket'),
	'desc' => wp_kses( __('<p class="description">Select layout in blog listing page.</p>', 'emarket'), array( 'p' => array( 'class' => array() ) ) ),
		//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
		//You dont have to though, leave it emarket for default.
	'icon' => EMARKET_URL.'/options/img/glyphicons/glyphicons_071_book.png',
		//Lets leave this as a emarket section, no options just some intro text set above.
	'fields' => array(
		array(
			'id' => 'sidebar_blog',
			'type' => 'select',
			'title' => esc_html__('Sidebar Blog Layout', 'emarket'),
			'options' => array(
				'full' => esc_html__( 'Full Layout', 'emarket' ),		
				'left'	=>  esc_html__( 'Left Sidebar', 'emarket' ),
				'right' => esc_html__( 'Right Sidebar', 'emarket' ),
				),
			'std' => 'left',
			'sub_desc' => esc_html__( 'Select style sidebar blog', 'emarket' ),
			),
		array(
			'id' => 'blog_layout',
			'type' => 'select',
			'title' => esc_html__('Layout blog', 'emarket'),
			'options' => array(
				'list'	=>  esc_html__( 'List Layout', 'emarket' ),
				'grid' =>  esc_html__( 'Grid Layout', 'emarket' )								
				),
			'std' => 'list',
			'sub_desc' => esc_html__( 'Select style layout blog', 'emarket' ),
			),
		array(
			'id' => 'blog_column',
			'type' => 'select',
			'title' => esc_html__('Blog column', 'emarket'),
			'options' => array(								
				'2' => '2 columns',
				'3' => '3 columns',
				'4' => '4 columns'								
				),
			'std' => '2',
			'sub_desc' => esc_html__( 'Select style number column blog', 'emarket' ),
			),
		)
);	
$options[] = array(
	'title' => esc_html__('Product Options', 'emarket'),
	'desc' => wp_kses( __('<p class="description">Select layout in product listing page.</p>', 'emarket'), array( 'p' => array( 'class' => array() ) ) ),
		//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
		//You dont have to though, leave it emarket for default.
	'icon' => EMARKET_URL.'/options/img/glyphicons/glyphicons_202_shopping_cart.png',
		//Lets leave this as a emarket section, no options just some intro text set above.
	'fields' => array(
		array(
			'id' => 'info_typo1',
			'type' => 'info',
			'title' => esc_html( 'Product Categories Config', 'emarket' ),
			'desc' => '',
			'class' => 'emarket-opt-info'
			),
		
		array(
			'id' => 'product_colcat_large',
			'type' => 'select',
			'title' => esc_html__('Product Category Listing column Desktop', 'emarket'),
			'options' => array(
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',							
				),
			'std' => '4',
			'sub_desc' => esc_html__( 'Select number of column on Desktop Screen', 'emarket' ),
			),

		array(
			'id' => 'product_colcat_medium',
			'type' => 'select',
			'title' => esc_html__('Product Listing Category column Medium Desktop', 'emarket'),
			'options' => array(
				'2' => '2',
				'3' => '3',
				'4' => '4',	
				'5' => '5',
				'6' => '6',
				),
			'std' => '3',
			'sub_desc' => esc_html__( 'Select number of column on Medium Desktop Screen', 'emarket' ),
			),

		array(
			'id' => 'product_colcat_sm',
			'type' => 'select',
			'title' => esc_html__('Product Listing Category column Tablet', 'emarket'),
			'options' => array(
				'2' => '2',
				'3' => '3',
				'4' => '4',	
				'5' => '5',
				'6' => '6'
				),
			'std' => '2',
			'sub_desc' => esc_html__( 'Select number of column on Tablet Screen', 'emarket' ),
			),
		
		array(
			'id' => 'info_typo1',
			'type' => 'info',
			'title' => esc_html( 'Product General Config', 'emarket' ),
			'desc' => '',
			'class' => 'emarket-opt-info'
			),
			
		array(
			'id' => 'product_banner',
			'title' => esc_html__( 'Select Banner', 'emarket' ),
			'type' => 'select',
			'sub_desc' => '',
			'options' => array(
				'' => esc_html__( 'Use Banner', 'emarket' ),
				'listing' => esc_html__( 'Use Category Product Image', 'emarket' ),
				),
			'std' => '',
			),

		array(
			'id' => 'product_listing_banner',
			'type' => 'upload',
			'title' => esc_html__('Listing Banner Product', 'emarket'),
			'sub_desc' => esc_html__( 'Use the Upload button to upload banner product listing', 'emarket' ),
			'std' => get_template_directory_uri().'/assets/img/logo-default.png'
			),

		array(
			'id' => 'product_col_large',
			'type' => 'select',
			'title' => esc_html__('Product Listing column Desktop', 'emarket'),
			'options' => array(
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',							
				),
			'std' => '4',
			'sub_desc' => esc_html__( 'Select number of column on Desktop Screen', 'emarket' ),
			),

		array(
			'id' => 'product_col_medium',
			'type' => 'select',
			'title' => esc_html__('Product Listing column Medium Desktop', 'emarket'),
			'options' => array(
				'2' => '2',
				'3' => '3',
				'4' => '4',	
				'5' => '5',
				'6' => '6',
				),
			'std' => '3',
			'sub_desc' => esc_html__( 'Select number of column on Medium Desktop Screen', 'emarket' ),
			),

		array(
			'id' => 'product_col_sm',
			'type' => 'select',
			'title' => esc_html__('Product Listing column Tablet', 'emarket'),
			'options' => array(
				'2' => '2',
				'3' => '3',
				'4' => '4',	
				'5' => '5',
				'6' => '6'
				),
			'std' => '2',
			'sub_desc' => esc_html__( 'Select number of column on Tablet Screen', 'emarket' ),
			),

		array(
			'id' => 'sidebar_product',
			'type' => 'select',
			'title' => esc_html__('Sidebar Product Layout', 'emarket'),
			'options' => array(
				'left'	=> esc_html__( 'Left Sidebar', 'emarket' ),
				'full' => esc_html__( 'Full Layout', 'emarket' ),		
				'right' => esc_html__( 'Right Sidebar', 'emarket' )
				),
			'std' => 'left',
			'sub_desc' => esc_html__( 'Select style sidebar product', 'emarket' ),
			),

		array(
			'id' => 'product_quickview',
			'title' => esc_html__( 'Quickview', 'emarket' ),
			'type' => 'checkbox',
			'sub_desc' => '',
			'desc' => esc_html__( 'Turn On/Off Product Quickview', 'emarket' ),
			'std' => '1'
			),
		
		array(
			'id' => 'product_listing_countdown',
			'title' => esc_html__( 'Enable Countdown', 'emarket' ),
			'type' => 'checkbox',
			'sub_desc' => '',
			'desc' => esc_html__( 'Turn On/Off Product Countdown on product listing', 'emarket' ),
			'std' => '1'
			),
		
		array(
			'id' => 'product_soldout',
			'title' => esc_html__( 'Product Sold Out', 'emarket' ),
			'type' => 'checkbox',
			'sub_desc' => '',
			'desc' => esc_html__( 'Turn On/Off product sold out label', 'emarket' ),
			'std' => '1'
			),
		
		array(
			'id' => 'product_number',
			'type' => 'text',
			'title' => esc_html__('Product Listing Number', 'emarket'),
			'sub_desc' => esc_html__( 'Show number of product in listing product page.', 'emarket' ),
			'std' => 12
			),
		
		array(
				'id' => 'newproduct_time',
				'title' => esc_html__( 'New Product', 'emarket' ),
				'type' => 'number',
				'sub_desc' => '',
				'desc' => esc_html__( 'Set day for the new product label from the date publish product.', 'emarket' ),
				'std' => '1'
				),
		
		array(
			'id' => 'info_typo1',
			'type' => 'info',
			'title' => esc_html( 'Product Single Config', 'emarket' ),
			'desc' => '',
			'class' => 'emarket-opt-info'
			),
		
		array(
			'id' => 'sidebar_product_detail',
			'type' => 'select',
			'title' => esc_html__('Sidebar Product Single Layout', 'emarket'),
			'options' => array(
				'left'	=> esc_html__( 'Left Sidebar', 'emarket' ),
				'full' => esc_html__( 'Full Layout', 'emarket' ),		
				'right' => esc_html__( 'Right Sidebar', 'emarket' )
				),
			'std' => 'left',
			'sub_desc' => esc_html__( 'Select style sidebar product single', 'emarket' ),
			),
		
		array(
			'id' => 'product_single_style',
			'type' => 'select',
			'title' => esc_html__('Product Detail Style', 'emarket'),
			'options' => array(
				'default'	=> esc_html__( 'Default', 'emarket' ),
				'style1' 	=> esc_html__( 'Full Width', 'emarket' ),	
				'style2' 	=> esc_html__( 'Full Width With Accordion', 'emarket' ),	
				'style3' 	=> esc_html__( 'Full Width With Accordion 1', 'emarket' ),	
			),
			'std' => 'default',
			'sub_desc' => esc_html__( 'Select style for product single', 'emarket' ),
			),
		
		array(
			'id' => 'product_single_thumbnail',
			'type' => 'select',
			'title' => esc_html__('Product Thumbnail Position', 'emarket'),
			'options' => array(
				'bottom'	=> esc_html__( 'Bottom', 'emarket' ),
				'left' 		=> esc_html__( 'Left', 'emarket' ),	
				'right' 	=> esc_html__( 'Right', 'emarket' ),	
				'top' 		=> esc_html__( 'Top', 'emarket' ),					
			),
			'std' => 'bottom',
			'sub_desc' => esc_html__( 'Select style for product single thumbnail', 'emarket' ),
			),		
		
		
		array(
			'id' => 'product_zoom',
			'title' => esc_html__( 'Product Zoom', 'emarket' ),
			'type' => 'checkbox',
			'sub_desc' => '',
			'desc' => esc_html__( 'Turn On/Off image zoom when hover on single product', 'emarket' ),
			'std' => '1'
			),
		
		array(
			'id' => 'product_brand',
			'title' => esc_html__( 'Enable Product Brand Image', 'emarket' ),
			'type' => 'checkbox',
			'sub_desc' => '',
			'desc' => esc_html__( 'Turn On/Off product brand image show on single product.', 'emarket' ),
			'std' => '1'
			),

		array(
			'id' => 'product_single_countdown',
			'title' => esc_html__( 'Enable Countdown Single', 'emarket' ),
			'type' => 'checkbox',
			'sub_desc' => '',
			'desc' => esc_html__( 'Turn On/Off Product Countdown on product single', 'emarket' ),
			'std' => '1'
			),
		
		array(
			'id' => 'info_typo1',
			'type' => 'info',
			'title' => esc_html( 'Config For Product Categories Widget', 'emarket' ),
			'desc' => '',
			'class' => 'emarket-opt-info'
			),

		array(
			'id' => 'product_number_item',
			'type' => 'text',
			'title' => esc_html__( 'Category Number Item Show', 'emarket' ),
			'sub_desc' => esc_html__( 'Choose to number of item category that you want to show, leave 0 to show all category', 'emarket' ),
			'std' => 8
			),	

		array(
			'id' => 'product_more_text',
			'type' => 'text',
			'title' => esc_html__( 'Category More Text', 'emarket' ),
			'sub_desc' => esc_html__( 'Change more text on category product', 'emarket' ),
			'std' => ''
			),

		array(
			'id' => 'product_less_text',
			'type' => 'text',
			'title' => esc_html__( 'Category Less Text', 'emarket' ),
			'sub_desc' => esc_html__( 'Change less text on category product', 'emarket' ),
			'std' => ''
			)	
		)
);		
$options[] = array(
	'title' => esc_html__('Typography', 'emarket'),
	'desc' => wp_kses( __('<p class="description">Change the font style of your blog, custom with Google Font.</p>', 'emarket'), array( 'p' => array( 'class' => array() ) ) ),
			//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
			//You dont have to though, leave it emarket for default.
	'icon' => EMARKET_URL.'/options/img/glyphicons/glyphicons_151_edit.png',
			//Lets leave this as a emarket section, no options just some intro text set above.
	'fields' => array(
		array(
			'id' => 'info_typo1',
			'type' => 'info',
			'title' => esc_html( 'Global Typography', 'emarket' ),
			'desc' => '',
			'class' => 'emarket-opt-info'
			),

		array(
			'id' => 'google_webfonts',
			'type' => 'google_webfonts',
			'title' => esc_html__('Use Google Webfont', 'emarket'),
			'sub_desc' => esc_html__( 'Insert font style that you actually need on your webpage.', 'emarket' ), 
			'std' => ''
			),

		array(
			'id' => 'webfonts_weight',
			'type' => 'multi_select',
			'sub_desc' => esc_html__( 'For weight, see Google Fonts to custom for each font style.', 'emarket' ),
			'title' => esc_html__('Webfont Weight', 'emarket'),
			'options' => array(
				'100' => '100',
				'200' => '200',
				'300' => '300',
				'400' => '400',
				'500' => '500',
				'600' => '600',
				'700' => '700',
				'800' => '800',
				'900' => '900'
				),
			'std' => ''
			),

		array(
			'id' => 'info_typo2',
			'type' => 'info',
			'title' => esc_html( 'Header Tag Typography', 'emarket' ),
			'desc' => '',
			'class' => 'emarket-opt-info'
			),

		array(
			'id' => 'header_tag_font',
			'type' => 'google_webfonts',
			'title' => esc_html__('Header Tag Font', 'emarket'),
			'sub_desc' => esc_html__( 'Select custom font for header tag ( h1...h6 )', 'emarket' ), 
			'std' => ''
			),

		array(
			'id' => 'info_typo2',
			'type' => 'info',
			'title' => esc_html( 'Main Menu Typography', 'emarket' ),
			'desc' => '',
			'class' => 'emarket-opt-info'
			),

		array(
			'id' => 'menu_font',
			'type' => 'google_webfonts',
			'title' => esc_html__('Main Menu Font', 'emarket'),
			'sub_desc' => esc_html__( 'Select custom font for main menu', 'emarket' ), 
			'std' => ''
			),
			
		array(
			'id' => 'info_typo2',
			'type' => 'info',
			'title' => esc_html( 'Custom Typography', 'emarket' ),
			'desc' => '',
			'class' => 'emarket-opt-info'
		),

		array(
			'id' => 'custom_font',
			'type' => 'google_webfonts',
			'title' => esc_html__('Custom Font', 'emarket'),
			'sub_desc' => esc_html__( 'Select custom font for custom class', 'emarket' ), 
			'std' => ''
		),
		
		array(
			'id' => 'custom_font_class',
			'title' => esc_html__( 'Custom Font Class', 'emarket' ),
			'type' => 'text',
			'sub_desc' => esc_html__( 'Put custom class to this field. Each class separated by commas.', 'emarket' ),
			'desc' => '',
			'std' => '',
		),
	)
);

$options[] = array(
	'title' => __('Social', 'emarket'),
	'desc' => wp_kses( __('<p class="description">This feature allow to you link to your social.</p>', 'emarket'), array( 'p' => array( 'class' => array() ) ) ),
		//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
		//You dont have to though, leave it blank for default.
	'icon' => EMARKET_URL.'/options/img/glyphicons/glyphicons_222_share.png',
		//Lets leave this as a blank section, no options just some intro text set above.
	'fields' => array(
		array(
			'id' => 'social-share-fb',
			'title' => esc_html__( 'Facebook', 'emarket' ),
			'type' => 'text',
			'sub_desc' => '',
			'desc' => '',
			'std' => '',
			),
		array(
			'id' => 'social-share-tw',
			'title' => esc_html__( 'Twitter', 'emarket' ),
			'type' => 'text',
			'sub_desc' => '',
			'desc' => '',
			'std' => '',
			),
		array(
			'id' => 'social-share-tumblr',
			'title' => esc_html__( 'Tumblr', 'emarket' ),
			'type' => 'text',
			'sub_desc' => '',
			'desc' => '',
			'std' => '',
			),
		array(
			'id' => 'social-share-in',
			'title' => esc_html__( 'Linkedin', 'emarket' ),
			'type' => 'text',
			'sub_desc' => '',
			'desc' => '',
			'std' => '',
			),
		array(
			'id' => 'social-share-instagram',
			'title' => esc_html__( 'Instagram', 'emarket' ),
			'type' => 'text',
			'sub_desc' => '',
			'desc' => '',
			'std' => '',
			),
		array(
			'id' => 'social-share-go',
			'title' => esc_html__( 'Google+', 'emarket' ),
			'type' => 'text',
			'sub_desc' => '',
			'desc' => '',
			'std' => '',
			),
		array(
			'id' => 'social-share-pi',
			'title' => esc_html__( 'Pinterest', 'emarket' ),
			'type' => 'text',
			'sub_desc' => '',
			'desc' => '',
			'std' => '',
			)

		)
);

$options[] = array(
	'title' => esc_html__('Maintaincece Mode', 'emarket'),
	'desc' => wp_kses( __('<p class="description">Enable and config for Maintaincece mode.</p>', 'emarket'), array( 'p' => array( 'class' => array() ) ) ),
//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
//You dont have to though, leave it emarket for default.
	'icon' => EMARKET_URL.'/options/img/glyphicons/glyphicons_083_random.png',
//Lets leave this as a emarket section, no options just some intro text set above.
	'fields' => array(
		array(
			'id' => 'maintaince_enable',
			'title' => esc_html__( 'Enable Maintaincece Mode', 'emarket' ),
			'type' => 'checkbox',
			'sub_desc' => esc_html__( 'Turn on/off Maintaince mode on this website', 'emarket' ),
			'desc' => '',
			'std' => '0'
			),

		array(
			'id' => 'maintaince_background',
			'title' => esc_html__( 'Maintaince Background', 'emarket' ),
			'type' => 'upload',
			'sub_desc' => esc_html__( 'Choose maintance background image', 'emarket' ),
			'desc' => '',
			'std' => get_template_directory_uri().'/assets/img/maintaince/bg-main.jpg'
			),

		array(
			'id' => 'maintaince_content',
			'title' => esc_html__( 'Maintaince Content', 'emarket' ),
			'type' => 'editor',
			'sub_desc' => esc_html__( 'Change text of maintaince mode', 'emarket' ),
			'desc' => '',
			'std' => ''
			),

		array(
			'id' => 'maintaince_date',
			'title' => esc_html__( 'Maintaince Date', 'emarket' ),
			'type' => 'date',
			'sub_desc' => esc_html__( 'Put date to this field to show countdown date on maintaince mode.', 'emarket' ),
			'desc' => '',
			'placeholder' => 'mm/dd/yy',
			'std' => ''
			),

		array(
			'id' => 'maintaince_form',
			'title' => esc_html__( 'Maintaince Form', 'emarket' ),
			'type' => 'text',
			'sub_desc' => esc_html__( 'Put shortcode form to this field and it will be shown on maintaince mode frontend.', 'emarket' ),
			'desc' => '',
			'std' => ''
			),

		)
);

$options[] = array(
	'title' => esc_html__('Popup Config', 'emarket'),
	'desc' => wp_kses( __('<p class="description">Enable popup and more config for Popup.</p>', 'emarket'), array( 'p' => array( 'class' => array() ) ) ),
			//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
			//You dont have to though, leave it emarket for default.
	'icon' => EMARKET_URL.'/options/img/glyphicons/glyphicons_083_random.png',
			//Lets leave this as a emarket section, no options just some intro text set above.
	'fields' => array(
		array(
			'id' => 'popup_active',
			'type' => 'checkbox',
			'title' => esc_html__( 'Active Popup Subscribe', 'emarket' ),
			'sub_desc' => esc_html__( 'Check to active popup subscribe', 'emarket' ),
			'desc' => '',
						'std' => '0'// 1 = on | 0 = off
						),	

		array(
			'id' => 'popup_background',
			'title' => esc_html__( 'Popup Background', 'emarket' ),
			'type' => 'upload',
			'sub_desc' => esc_html__( 'Choose popup background image', 'emarket' ),
			'desc' => '',
			'std' => get_template_directory_uri().'/assets/img/popup/bg-main.jpg'
			),

		array(
			'id' => 'popup_content',
			'title' => esc_html__( 'Popup Content', 'emarket' ),
			'type' => 'editor',
			'sub_desc' => esc_html__( 'Change text of popup mode', 'emarket' ),
			'desc' => '',
			'std' => ''
			),	

		array(
			'id' => 'popup_form',
			'title' => esc_html__( 'Popup Form', 'emarket' ),
			'type' => 'text',
			'sub_desc' => esc_html__( 'Put shortcode form to this field and it will be shown on popup mode frontend.', 'emarket' ),
			'desc' => '',
			'std' => ''
			),

		)
);

$options[] = array(
	'title' => esc_html__('Advanced', 'emarket'),
	'desc' => wp_kses( __('<p class="description">Custom advanced with Cpanel, Widget advanced, Developer mode </p>', 'emarket'), array( 'p' => array( 'class' => array() ) ) ),
			//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
			//You dont have to though, leave it emarket for default.
	'icon' => EMARKET_URL.'/options/img/glyphicons/glyphicons_083_random.png',
			//Lets leave this as a emarket section, no options just some intro text set above.
	'fields' => array(		

		array(
			'id' => 'social_share',
			'title' => esc_html__( 'Social Share', 'emarket' ),
			'type' => 'checkbox',
			'sub_desc' => esc_html__( 'Turn on/off social share', 'emarket' ),
			'desc' => '',
			'std' => '1'
			),

		array(
			'id' => 'breadcrumb_active',
			'title' => esc_html__( 'Turn Off Breadcrumb', 'emarket' ),
			'type' => 'checkbox',
			'sub_desc' => esc_html__( 'Turn off breadcumb on all page', 'emarket' ),
			'desc' => '',
			'std' => '0'
			),

		array(
			'id' => 'back_active',
			'type' => 'checkbox',
			'title' => esc_html__('Back to top', 'emarket'),
			'sub_desc' => '',
			'desc' => '',
						'std' => '1'// 1 = on | 0 = off
						),	

		array(
			'id' => 'direction',
			'type' => 'select',
			'title' => esc_html__('Direction', 'emarket'),
			'options' => array( 'ltr' => 'Left to Right', 'rtl' => 'Right to Left' ),
			'std' => 'ltr'
			),

		)
);

$options_args = array();

	//Setup custom links in the footer for share icons
$options_args['share_icons']['facebook'] = array(
	'link' => 'http://www.facebook.com/SmartAddons.page',
	'title' => 'Facebook',
	'img' => EMARKET_URL.'/options/img/glyphicons/glyphicons_320_facebook.png'
	);
$options_args['share_icons']['twitter'] = array(
	'link' => 'https://twitter.com/smartaddons',
	'title' => 'Folow me on Twitter',
	'img' => EMARKET_URL.'/options/img/glyphicons/glyphicons_322_twitter.png'
	);
$options_args['share_icons']['linked_in'] = array(
	'link' => 'http://www.linkedin.com/in/smartaddons',
	'title' => 'Find me on LinkedIn',
	'img' => EMARKET_URL.'/options/img/glyphicons/glyphicons_337_linked_in.png'
	);


	//Choose a custom option name for your theme options, the default is the theme name in lowercase with spaces replaced by underscores
$options_args['opt_name'] = EMARKET_THEME;

	$options_args['google_api_key'] = 'AIzaSyAL_XMT9t2KuBe2MIcofGl6YF1IFzfB4L4'; //must be defined for use with google webfonts field type

	//Custom menu title for options page - default is "Options"
	$options_args['menu_title'] = esc_html__('Theme Options', 'emarket');

	//Custom Page Title for options page - default is "Options"
	$options_args['page_title'] = esc_html__('Emarket Options ', 'emarket') . wp_get_theme()->get('Name');

	//Custom page slug for options page (wp-admin/themes.php?page=***) - default is "emarket_theme_options"
	$options_args['page_slug'] = 'emarket_theme_options';

	//page type - "menu" (adds a top menu section) or "submenu" (adds a submenu) - default is set to "menu"
	$options_args['page_type'] = 'submenu';

	//custom page location - default 100 - must be unique or will override other items
	$options_args['page_position'] = 27;
	$emarket_options = new Emarket_Options( $options, $options_args );
}
add_action( 'admin_init', 'emarket_Options_Setup', 0 );
emarket_Options_Setup();

add_filter( 'sw_googlefont_api_key_filter', 'emarket_custom_google_api_key' );
function emarket_custom_google_api_key(){
	$webfont = ( emarket_options()->getCpanelValue( 'google_webfonts_api' ) != '' ) ? emarket_options()->getCpanelValue( 'google_webfonts_api' ) : 'AIzaSyAL_XMT9t2KuBe2MIcofGl6YF1IFzfB4L4';
	return $webfont;
}

function emarket_widget_setup_args(){
	$emarket_widget_areas = array(
		
		array(
			'name' => esc_html__('Sidebar Left Blog', 'emarket'),
			'id'   => 'left-blog',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget' => '</div></div>',
			'before_title' => '<div class="block-title-widget"><h2><span>',
			'after_title' => '</span></h2></div>'
			),
		array(
			'name' => esc_html__('Sidebar Right Blog', 'emarket'),
			'id'   => 'right-blog',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget' => '</div></div>',
			'before_title' => '<div class="block-title-widget"><h2><span>',
			'after_title' => '</span></h2></div>'
			),
		
		array(
			'name' => esc_html__('Top Header', 'emarket'),
			'id'   => 'top',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),

		array(
			'name' => esc_html__('Top Header3', 'emarket'),
			'id'   => 'top2',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),
		array(
			'name' => esc_html__('Top Header6', 'emarket'),
			'id'   => 'top3',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),
		array(
			'name' => esc_html__('Top Header7', 'emarket'),
			'id'   => 'top4',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),
		
		array(
			'name' => esc_html__('Top Header13', 'emarket'),
			'id'   => 'top5',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),
			
		array(
			'name' => esc_html__('Mid Header', 'emarket'),
			'id'   => 'mid-header',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),

		array(
			'name' => esc_html__('Mid Header3', 'emarket'),
			'id'   => 'mid-header2',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),

		array(
			'name' => esc_html__('Mid Header6', 'emarket'),
			'id'   => 'mid-header6',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),
			
		array(
			'name' => esc_html__('Mid Header9', 'emarket'),
			'id'   => 'mid-header9',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),
		array(
			'name' => esc_html__('Bottom Right', 'emarket'),
			'id'   => 'bottom-right-home5',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),
		array(
			'name' => esc_html__('Bottom Header', 'emarket'),
			'id'   => 'bottom-header',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),
		array(
			'name' => esc_html__('Bottom Header6', 'emarket'),
			'id'   => 'bottom-header2',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),
		array(
			'name' => esc_html__('Header Menu Bar', 'emarket'),
			'id'   => 'menu-bar',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),
		array(
			'name' => esc_html__('Sidebar Left Product', 'emarket'),
			'id'   => 'left-product',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget' => '</div></div>',
			'before_title' => '<div class="block-title-widget"><h2><span>',
			'after_title' => '</span></h2></div>'
			),
		
		array(
			'name' => esc_html__('Sidebar Right Product', 'emarket'),
			'id'   => 'right-product',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget' => '</div></div>',
			'before_title' => '<div class="block-title-widget"><h2><span>',
			'after_title' => '</span></h2></div>'
			),

		array(
			'name' => esc_html__('Sidebar Left Detail Product', 'emarket'),
			'id'   => 'left-product-detail',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget' => '</div></div>',
			'before_title' => '<div class="block-title-widget"><h2><span>',
			'after_title' => '</span></h2></div>'
			),
		
		array(
			'name' => esc_html__('Sidebar Right Detail Product', 'emarket'),
			'id'   => 'right-product-detail',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget' => '</div></div>',
			'before_title' => '<div class="block-title-widget"><h2><span>',
			'after_title' => '</span></h2></div>'
			),
		
		array(
			'name' => esc_html__('Sidebar Bottom Detail Product', 'emarket'),
			'id'   => 'bottom-detail-product',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s" data-scroll-reveal="enter bottom move 20px wait 0.2s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),
		
		array(
				'name' => esc_html__('Bottom Detail Product Mobile', 'emarket'),
				'id'   => 'bottom-detail-product-mobile',
				'before_widget' => '<div id="%1$s" class="widget %1$s %2$s" data-scroll-reveal="enter bottom move 20px wait 0.2s"><div class="widget-inner">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>'
		),

		array(
				'name' => esc_html__('Filter Mobile', 'emarket'),
				'id'   => 'filter-mobile',
				'before_widget' => '<div id="%1$s" class="widget %1$s %2$s" data-scroll-reveal="enter bottom move 20px wait 0.2s"><div class="widget-inner">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>'
		),
		array(
			'name' => esc_html__('Footer Copyright', 'emarket'),
			'id'   => 'footer-copyright',
			'before_widget' => '<div id="%1$s" class="widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
			),
		);
return apply_filters( 'emarket_widget_register', $emarket_widget_areas );
}