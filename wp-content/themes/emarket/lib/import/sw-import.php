<?php

function sw_import_files() { 
  return array(
    array(
      'import_file_name'             => 'Demo Homepage 1',
      'page_title'                   => 'Home',
      'local_import_file'            => trailingslashit( get_template_directory() ) . 'lib/import/demo-1/demo-content.xml',
      'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'lib/import/demo-1/widgets.json',
      'local_import_revslider'       => array( 
        'slide1' => trailingslashit( get_template_directory() ) . 'lib/import/demo-1/slideshow1.zip' 
        ),
      'local_import_options'         => array(
        array(
          'file_path'   => trailingslashit( get_template_directory() ) . 'lib/import/demo-1/theme_options.txt',
          'option_name' => 'emarket_theme',
          ),
        ),
      'menu_locate'                  => array(
        'primary_menu' => 'Primary Menu',   /* menu location => menu name for that location */
        'vertical_menu' => 'Verticle Menu',
        'mobile_menu' => 'Menu Mobile 1'
        ),
      'import_preview_image_url'     => get_template_directory_uri() . '/lib/import/demo-1/screenshot.png',
      'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately. This import maybe finish on 5-10 minutes', 'emarket' ),
      'preview_url'                  => 'http://demo.wpthemego.com/themes/sw_emarket/',
      ),

array(
  'import_file_name'             => 'Demo Homepage 2',
  'page_title'                   => 'Home Page 2',
  'local_import_file'            => trailingslashit( get_template_directory() ) . 'lib/import/demo-2/demo-content.xml',
  'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'lib/import/demo-2/widgets.json',
  'local_import_revslider'       => array( 
    'slide1' => trailingslashit( get_template_directory() ) . 'lib/import/demo-2/slideshow2.zip',
    'slide2' => trailingslashit( get_template_directory() ) . 'lib/import/demo-2/slideshow3.zip' 
    ),
  'local_import_options'         => array(
    array(
      'file_path'   => trailingslashit( get_template_directory() ) . 'lib/import/demo-2/theme_options.txt',
      'option_name' => 'emarket_theme',
      ),
    ),
  'menu_locate'                  => array(
    'primary_menu' => 'Primary Menu',   /* menu location => menu name for that location */
    'vertical_menu' => 'Verticle Menu',
    ),
  'import_preview_image_url'     => get_template_directory_uri() . '/lib/import/demo-2/screenshot.png',
  'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately. This import maybe finish on 5-10 minutes', 'emarket' ),
  'preview_url'                  => 'http://demo.wpthemego.com/themes/sw_emarket/layout2/',
  ),

array(
  'import_file_name'             => 'Demo Homepage 3',
  'page_title'                   => 'Home Page 3',
  'local_import_file'            => trailingslashit( get_template_directory() ) . 'lib/import/demo-3/demo-content.xml',
  'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'lib/import/demo-3/widgets.json',
  'local_import_revslider'       => array( 
    'slide1' => trailingslashit( get_template_directory() ) . 'lib/import/demo-3/slideshow4.zip' 
    ),
  'local_import_options'         => array(
    array(
      'file_path'   => trailingslashit( get_template_directory() ) . 'lib/import/demo-3/theme_options.txt',
      'option_name' => 'emarket_theme',
      ),
    ),
  'menu_locate'                  => array(
    'primary_menu' => 'Primary Menu',   /* menu location => menu name for that location */
    'vertical_menu' => 'Verticle Menu',
    ),
  'import_preview_image_url'     => get_template_directory_uri() . '/lib/import/demo-3/screenshot.png',
  'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately. This import maybe finish on 5-10 minutes', 'emarket' ),
  'preview_url'                  => 'http://demo.wpthemego.com/themes/sw_emarket/layout3/',
  ),

array(
  'import_file_name'             => 'Demo Homepage 4',
  'page_title'                   => 'Home Page 4',
  'local_import_file'            => trailingslashit( get_template_directory() ) . 'lib/import/demo-4/demo-content.xml',
  'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'lib/import/demo-4/widgets.json',
  'local_import_revslider'       => array( 
    'slide1' => trailingslashit( get_template_directory() ) . 'lib/import/demo-4/slideshow5.zip' 
    ),
  'local_import_options'         => array(
    array(
      'file_path'   => trailingslashit( get_template_directory() ) . 'lib/import/demo-4/theme_options.txt',
      'option_name' => 'emarket_theme',
      ),
    ),
  'menu_locate'                  => array(
    'primary_menu' => 'Primary Menu',   /* menu location => menu name for that location */
    'vertical_menu' => 'Verticle Menu',
    ),
  'import_preview_image_url'     => get_template_directory_uri() . '/lib/import/demo-4/screenshot.png',
  'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately. This import maybe finish on 5-10 minutes', 'emarket' ),
  'preview_url'                  => 'http://demo.wpthemego.com/themes/sw_emarket/layout4/',
  ),

array(
  'import_file_name'             => 'Demo Homepage 5',
  'page_title'                   => 'Home Page 5',
  'local_import_file'            => trailingslashit( get_template_directory() ) . 'lib/import/demo-5/demo-content.xml',
  'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'lib/import/demo-5/widgets.json',
  'local_import_revslider'       => array( 
    'slide1' => trailingslashit( get_template_directory() ) . 'lib/import/demo-5/slideshow6.zip',
    'slide2' => trailingslashit( get_template_directory() ) . 'lib/import/demo-5/slideshow6_1.zip' 
    ),
  'local_import_options'         => array(
    array(
      'file_path'   => trailingslashit( get_template_directory() ) . 'lib/import/demo-5/theme_options.txt',
      'option_name' => 'emarket_theme',
      ),
    ),
  'menu_locate'                  => array(
    'primary_menu' => 'Primary Menu',   /* menu location => menu name for that location */
    'vertical_menu' => 'Verticle Menu',
    ),
  'import_preview_image_url'     => get_template_directory_uri() . '/lib/import/demo-5/screenshot.png',
  'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately. This import maybe finish on 5-10 minutes', 'emarket' ),
  'preview_url'                  => 'http://demo.wpthemego.com/themes/sw_emarket/layout567/',
  ),

array(
  'import_file_name'             => 'Demo Homepage 6',
  'page_title'                   => 'Home Page 6',
  'local_import_file'            => trailingslashit( get_template_directory() ) . 'lib/import/demo-6/demo-content.xml',
  'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'lib/import/demo-6/widgets.json',
  'local_import_revslider'       => array( 
    'slide1' => trailingslashit( get_template_directory() ) . 'lib/import/demo-6/slideshow7.zip',
    'slide2' => trailingslashit( get_template_directory() ) . 'lib/import/demo-6/slide7_1.zip',
    ),
  'local_import_options'         => array(
    array(
      'file_path'   => trailingslashit( get_template_directory() ) . 'lib/import/demo-6/theme_options.txt',
      'option_name' => 'emarket_theme',
      ),
    ),
  'menu_locate'                  => array(
    'primary_menu' => 'Primary Menu',   /* menu location => menu name for that location */
    'vertical_menu' => 'Verticle Menu',
    ),
  'import_preview_image_url'     => get_template_directory_uri() . '/lib/import/demo-6/screenshot.png',
  'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately. This import maybe finish on 5-10 minutes', 'emarket' ),
  'preview_url'                  => 'http://demo.wpthemego.com/themes/sw_emarket/layout6/',
  ),

array(
  'import_file_name'             => 'Demo Homepage 7',
  'page_title'                   => 'Home Page 7',
  'local_import_file'            => trailingslashit( get_template_directory() ) . 'lib/import/demo-7/demo-content.xml',
  'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'lib/import/demo-7/widgets.json',
  'local_import_revslider'       => array( 
    'slide1' => trailingslashit( get_template_directory() ) . 'lib/import/demo-7/slide73.zip',
    ),
  'local_import_options'         => array(
    array(
      'file_path'   => trailingslashit( get_template_directory() ) . 'lib/import/demo-7/theme_options.txt',
      'option_name' => 'emarket_theme',
      ),
    ),
  'menu_locate'                  => array(
    'primary_menu' => 'Primary Menu',   /* menu location => menu name for that location */
    'vertical_menu' => 'Verticle Menu',
    ),
  'import_preview_image_url'     => get_template_directory_uri() . '/lib/import/demo-7/screenshot.png',
  'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately. This import maybe finish on 5-10 minutes', 'emarket' ),
  'preview_url'                  => 'http://demo.wpthemego.com/themes/sw_emarket/layout7/',
  ),
  
array(
  'import_file_name'             => 'Demo Christmas Layout',
  'page_title'                   => 'Home Page 8',
  'local_import_file'            => trailingslashit( get_template_directory() ) . 'lib/import/demo-1/demo-content.xml',
  'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'lib/import/demo-1/widgets.json',
  'local_import_revslider'       => array( 
    'slide1' => trailingslashit( get_template_directory() ) . 'lib/import/demo-8/slide8.zip',
    ),
  'local_import_options'         => array(
    array(
      'file_path'   => trailingslashit( get_template_directory() ) . 'lib/import/demo-8/theme_options.txt',
      'option_name' => 'emarket_theme',
      ),
    ),
  'menu_locate'                  => array(
    'primary_menu' => 'Primary Menu',   /* menu location => menu name for that location */
    'vertical_menu' => 'Verticle Menu',
    ),
  'import_preview_image_url'     => get_template_directory_uri() . '/lib/import/demo-8/screenshot.png',
  'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately. This import maybe finish on 5-10 minutes', 'emarket' ),
  'preview_url'                  => 'http://demo.wpthemego.com/themes/sw_emarket/layout8/',
  ),

array(
  'import_file_name'             => 'Demo Homepage 8',
  'page_title'                   => 'Home Page 9',
  'local_import_file'            => trailingslashit( get_template_directory() ) . 'lib/import/demo-9/demo-content.xml',
  'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'lib/import/demo-9/widgets.json',
  'local_import_options'         => array(
    array(
      'file_path'   => trailingslashit( get_template_directory() ) . 'lib/import/demo-9/theme_options.txt',
      'option_name' => 'emarket_theme',
      ),
    ),
  'menu_locate'                  => array(
    'primary_menu' => 'Primary Menu',   /* menu location => menu name for that location */
    'vertical_menu' => 'Verticle Menu',
    ),
  'import_preview_image_url'     => get_template_directory_uri() . '/lib/import/demo-9/screenshot.png',
  'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately. This import maybe finish on 5-10 minutes', 'emarket' ),
  'preview_url'                  => 'http://demo.wpthemego.com/themes/sw_emarket/layout9/',
  ),
  
array(
  'import_file_name'             => 'Demo Homepage 9',
  'page_title'                   => 'Home Page 10',
  'local_import_file'            => trailingslashit( get_template_directory() ) . 'lib/import/demo-10/demo-content.xml',
  'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'lib/import/demo-10/widgets.json',
  'local_import_revslider'       => array( 
    'slide1' => trailingslashit( get_template_directory() ) . 'lib/import/demo-10/slide10.zip',
    ),
  'local_import_options'         => array(
    array(
      'file_path'   => trailingslashit( get_template_directory() ) . 'lib/import/demo-10/theme_options.txt',
      'option_name' => 'emarket_theme',
      ),
    ),
  'menu_locate'                  => array(
    'primary_menu' => 'Primary Menu',   /* menu location => menu name for that location */
    'vertical_menu' => 'Verticle Menu',
    ),
  'import_preview_image_url'     => get_template_directory_uri() . '/lib/import/demo-10/screenshot.png',
  'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately. This import maybe finish on 5-10 minutes', 'emarket' ),
  'preview_url'                  => 'http://demo.wpthemego.com/themes/sw_emarket/layout10/',
  ),
  
  array(
  'import_file_name'             => 'Demo Homepage 10',
  'page_title'                   => 'Home Page 11',
  'local_import_file'            => trailingslashit( get_template_directory() ) . 'lib/import/demo-11/demo-content.xml',
  'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'lib/import/demo-11/widgets.json',
  'local_import_revslider'       => array( 
    'slide1' => trailingslashit( get_template_directory() ) . 'lib/import/demo-11/slide11.zip',
    ),
  'local_import_options'         => array(
    array(
      'file_path'   => trailingslashit( get_template_directory() ) . 'lib/import/demo-11/theme_options.txt',
      'option_name' => 'emarket_theme',
      ),
    ),
  'menu_locate'                  => array(
    'primary_menu' => 'Primary Menu',   /* menu location => menu name for that location */
    'vertical_menu' => 'Verticle Menu',
    ),
  'import_preview_image_url'     => get_template_directory_uri() . '/lib/import/demo-11/screenshot.png',
  'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately. This import maybe finish on 5-10 minutes', 'emarket' ),
  'preview_url'                  => 'http://demo.wpthemego.com/themes/sw_emarket/layout11/',
  ),
);
}
add_filter( 'pt-ocdi/import_files', 'sw_import_files' );

