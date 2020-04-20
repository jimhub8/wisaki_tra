<?php

/**
 * WC_Brands class.
 */
class pw_woocommerc_brans_Wc_Brands
{
    const Error_BRANDS = 115;
    var $template_url;
    var $plugin_path;

    public function __construct()
    {
        $this->template_url = apply_filters('woocommerce_template_url', 'woocommerce/');
        add_filter('template_include', array($this, 'template_loader'));

        //pw_brand_VC_all_view_product
        add_action('restrict_manage_posts', array($this, 'restrict_listings_by_properties'));
        add_filter('parse_query', array($this, 'convert_id_to_term_in_query'));

        add_filter('post_type_link', array($this, 'post_type_link'), 11, 2);

        add_action('woocommerce_archive_description', array($this, 'brand_description'));

        //add_action( 'woocommerce_product_meta_end', array( $this,'pw_woocommerc_show_brand' )) ;

        add_action('woocommerce_after_shop_loop_item', array($this, 'brand_show_title'));

        add_action('woocommerce_loaded', array($this, 'register_hooks'));

		add_action( 'woocommerce_product_duplicate_before_save', array( $this, 'duplicate_store_temporary_brands' ), 10, 2 );		
		add_action( 'woocommerce_new_product', array( $this, 'duplicate_add_product_brand_terms' ) );
		
        //For same theme if don't display brand in single page
        //add_action( 'woocommerce_before_single_product', array( $this, 'single_product' ) );
        remove_action('woocommerce_product_meta_end', array($this, 'pw_woocommerc_show_brand'));
        add_action('init', array($this, 'pw_woocommerce_brand_single_position'));

		add_filter( 'woocommerce_product_tabs', array( $this, 'product_tab' ) );

		add_filter( 'woocommerce_sortable_taxonomies', array( $this, 'sort_brands' ) );		
		
		// Yoast SEO compatible
		if( defined( 'WPSEO_VERSION' ) ){
			add_action( 'init', array( $this, 'pw_add_seo_replacement' ) );
		}
		
		//Rest Api
		add_action( 'rest_api_init', array( $this, 'rest_api_register_routes' ) );
		add_action( 'woocommerce_rest_insert_product', array( $this, 'rest_api_maybe_set_brands' ), 10, 2 );
		add_filter( 'woocommerce_rest_prepare_product', array( $this, 'rest_api_prepare_brands_to_product' ), 10, 2 ); // WC 2.6.x
		add_filter( 'woocommerce_rest_prepare_product_object', array( $this, 'rest_api_prepare_brands_to_product' ), 10, 2 ); // WC 3.x
		add_action( 'woocommerce_rest_insert_product', array( $this, 'rest_api_add_brands_to_product' ), 10, 3 ); // WC 2.6.x
		add_action( 'woocommerce_rest_insert_product_object', array( $this, 'rest_api_add_brands_to_product' ), 10, 3 ); // WC 3.x
		
    }
	
	function sort_brands( $sortable ) {
		$sortable[] = 'product_brand';
		return $sortable;
	}	
	public function pw_add_seo_replacement() {
		if( ! class_exists( 'WPSEO_Replace_Vars' ) ){
			return;
		}

		WPSEO_Replace_Vars::register_replacement( '%%product_brand%%', array( $this, 'pw_fuc_retrieve_seo_replacement_value' ) );
	}

	public function pw_fuc_retrieve_seo_replacement_value( $var, $post ) {
		if( ! isset( $post->ID ) ){
			return $var;
		}

		$brands = wp_get_post_terms( $post->ID, 'product_brand' );

		if( empty( $brands ) ){
			return $var;
		}

		$brand = $brands[0];
		return $brand->name;
	}
	
	public function rest_api_register_routes() {
		if ( ! is_a( WC()->api, 'WC_API' ) ) {
			return;
		}

		require_once(plugin_dirname_pw_woo_brand.'/classes/class-wc-brands-rest-api.php' );

		WC()->api->WC_Brands_REST_API = new WC_Brands_REST_API();
		WC()->api->WC_Brands_REST_API->register_routes();
	}
	
	public function rest_api_maybe_set_brands( $post, $request ) {
		if ( isset( $request['brands'] ) && is_array( $request['brands'] ) ) {
			$terms = array_map( 'absint', $request['brands'] );
			wp_set_object_terms( $post->ID, $terms, 'product_brand' );
		}
	}	

	public function rest_api_prepare_brands_to_product( $response, $post ) {
		$post_id = is_callable( array( $post, 'get_id' ) ) ? $post->get_id() : ( ! empty( $post->ID ) ? $post->ID : null );

		if ( empty( $response->data['brands'] ) ) {
			$terms = array();

			foreach ( wp_get_post_terms( $post_id, 'product_brand' ) as $term ) {
				$terms[] = array(
					'id'   => $term->term_id,
					'name' => $term->name,
					'slug' => $term->slug,
				);
			}

			$response->data['brands'] = $terms;
		}

		return $response;
	}
	
	public function rest_api_add_brands_to_product( $product, $request, $creating = true ) {
		$product_id = is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : ( ! empty( $product->ID ) ? $product->ID : null );
		$params     = $request->get_params();
		$brands     = isset( $params['brands'] ) ? $params['brands'] : array();

		if ( ! empty( $brands ) ) {
			$brands = array_map( 'absint', $brands );
			wp_set_object_terms( $product_id, $brands, 'product_brand' );
		}
	}	
	
	public function product_tab( $tabs ) {
		global $post;
		$show_brand_tab = get_option( 'pw_woocommerce_enable_brand_tab' );
		if( $show_brand_tab == 'yes' || !$show_brand_tab ){
            $terms = get_the_terms($post->ID, 'product_brand');
            if (is_array($terms)) {
				$tabs['pwb_tab'] = array(
					'title' 	  => __( 'Brand', 'woocommerce-brands' ),
					'priority' 	=> 20,
					'callback' 	=> array( $this, 'product_tab_content' )
				);
			}
		}
		return $tabs;
	}

	public function product_tab_content() {

		global $product;
		$brands = wp_get_object_terms( $product->get_id(), 'product_brand' );

		ob_start();
		?>

		<h2><?php echo __('Brand', 'woocommerce-brands'); ?></h2>
		<?php foreach( $brands as $brand ): ?>

		  <?php
		  $brand_logo = get_term_meta( $brand->term_id, 'thumbnail_id', true );
		  $brand_logo = wp_get_attachment_image( $brand_logo, 'thumbnail' );
		  ?>

		  <div id="tab-product_brand_tab-content">
			<h3><?php echo $brand->name;?></h3>
			<?php if( !empty($brand->description) ) echo '<div>'.do_shortcode($brand->description).'</div>';?>
			<?php if( !empty($brand_logo) ) echo '<span>'.$brand_logo.'</span>';?>
		  </div>

		<?php endforeach; ?>

		<?php
		echo ob_get_clean();

	}
  
    function pw_woocommerce_brand_single_position()
    {
        $position = get_option('pw_woocommerce_brands_position_single_brand', "default");
        if ($position == 'default') {
            add_action('woocommerce_product_meta_end', array($this, 'pw_woocommerc_show_brand'), 10);
        } elseif ($position == '1') {
            add_action('woocommerce_single_product_summary', array($this, 'pw_woocommerc_show_brand'), 3);
        } elseif ($position == '2') {
            add_action('woocommerce_single_product_summary', array($this, 'pw_woocommerc_show_brand'), 7);
        } elseif ($position == '3') {
            add_action('woocommerce_single_product_summary', array($this, 'pw_woocommerc_show_brand'), 15);
        } elseif ($position == '4') {
            add_action('woocommerce_single_product_summary', array($this, 'pw_woocommerc_show_brand'), 25);
        } elseif ($position == '5') {
            add_action('woocommerce_single_product_summary', array($this, 'pw_woocommerc_show_brand'), 35);
        } elseif ($position == '6') {
            add_action('woocommerce_single_product_summary', array($this, 'pw_woocommerc_show_brand'), 55);
        }
    }

    function single_product($post_ID)
    {

        global $post;
        global $wp_query;
        //echo $post_ID;
        $product_id = $post->ID;

        //get_option('pw_woocommerce_brands_text_single')=="yes" || get_option('pw_woocommerce_brands_image_single')=="yes")        

        if (is_admin() || !$wp_query->post->ID) {
            //return;
        }

        $terms = get_the_terms($post->ID, 'product_brand');

        $brands_list_output = '';
        $brand_image_output = '';
        $brands_list_comma = ', ';
        $i = 0;

        foreach ($terms as $brand) {
            $brands_list_output .= '<a href="' . get_term_link($brand->slug, 'product_brand') . '">' . $brand->name . '</a>';

            /*
            $thumbnail=get_woocommerce_term_meta($term->term_id,'thumbnail_id', true);
                                        if($thumbnail)
                                        {
                                            $ratio = get_option( 'pw_woocommerce_brands_image_single_image_size', "150:150" );
                                            list( $width, $height ) = explode( ':', $ratio );

                                            //$image = wp_get_attachment_thumb_url( $thumbnail );
                                            $image = current( wp_get_attachment_image_src( $thumbnail, 'full' ) );
                */
            $thumbnail = get_woocommerce_term_meta($brand->term_id, 'thumbnail_id', true);
            $brand_image_src = current(wp_get_attachment_image_src($thumbnail, 'full'));
            //$brand_image_src = $brand_image_src_term['src'];
            $brands_list_output .= '<a href="' . get_term_link($brand->slug, 'product_brand') . '"><img src="' . $brand_image_src . '" alt="' . $brand->name . '"/></a>';

        }
        $tax = get_option('pw_woocommerce_brands_text', '');

        if (count($terms) > 0) {

            if ($tax <> '') {
                $show = '<div class=""><h3>' . $tax . '</h3> ' . $brands_list_output . '</div>';
            } else {
                $show = '<div class="">' . $brands_list_output . '</div>';
            }
        }
        echo "<script type='text/javascript'>
            jQuery(document).ready(function(){
                jQuery('" . ($show) . "').insertAfter('div[itemprop=\"description\"]');
            });
        </script>
        ";
    }

    public function register_hooks()
    {
        if (version_compare(WC_VERSION, '2.6.0', '>=')) {
            add_filter('loop_shop_post_in', array($this, 'woocommerce_brands_layered_nav_init'));
        } else {
            add_filter('loop_shop_post_in', array($this, 'woocommerce_brands_layered_nav_init_deprecated'));
        }
    }

    public function woocommerce_brands_layered_nav_init_deprecated($filtered_posts)
    {
        global $woocommerce, $_chosen_attributes;
        if (is_active_widget(false, false, 'woocommerce_brand_nav', true) && !is_admin()) {
            if (!empty($_GET['filter_product_brand'])) {
                $terms = array_map('intval', explode(',', $_GET['filter_product_brand']));
                if (sizeof($terms) > 0) {
                    $_chosen_attributes['product_brand']['terms'] = $terms;
                    $_chosen_attributes['product_brand']['query_type'] = 'and';
                    $matched_products = get_posts(
                        array(
                            'post_type' => 'product',
                            'numberposts' => -1,
                            'post_status' => 'publish',
                            'fields' => 'ids',
                            'no_found_rows' => true,
                            'tax_query' => array(
                                'relation' => 'AND',
                                array(
                                    'taxonomy' => 'product_brand',
                                    'terms' => $terms,
                                    'field' => 'id'
                                )
                            )
                        )
                    );
                    $woocommerce->query->layered_nav_post__in = array_merge($woocommerce->query->layered_nav_post__in, $matched_products);
                    $woocommerce->query->layered_nav_post__in[] = 0;
                    if (sizeof($filtered_posts) == 0) {
                        $filtered_posts = $matched_products;
                        $filtered_posts[] = 0;
                    } else {
                        $filtered_posts = array_intersect($filtered_posts, $matched_products);
                        $filtered_posts[] = 0;
                    }
                }
            }
        }
        return (array)$filtered_posts;
    }

    public function post_type_link($permalink, $post)
    {
//        global $wp_version;
//        if ($post->post_type !== 'product') {
//            return $permalink;
//        }
//        if (false === strpos($permalink, '%')) {
//            return $permalink;
//        }
//
//        $terms = get_the_terms($post->ID, 'product_brand');
//
//        if (!empty($terms)) {
//            if (function_exists('wp_list_sort')) {
//                $terms = wp_list_sort($terms, 'term_id', 'ASC');
//            } else {
//                usort($terms, '_usort_terms_by_ID'); // order by ID
//            }
//            $category_object = apply_filters('proword_product_post_type_link_brand', $terms[0], $terms, $post);
//            $category_object = get_term($category_object, 'product_brand');
//            $product_cat = $category_object->slug;
//
//            if ($category_object->parent) {
//                $ancestors = get_ancestors($category_object->term_id, 'product_brand');
//                foreach ($ancestors as $ancestor) {
//                    $ancestor_object = get_term($ancestor, 'product_brand');
//                    $product_cat = $ancestor_object->slug . '/' . $product_cat;
//                }
//            }
//        } else {
//            $product_cat = _x('uncategorized', 'slug', 'woocommerce');
//        }
//
//        $find = array(
//            '%' . 'product_brand' . '%'
//        );
//
//        $replace = array(
//            $product_cat
//        );
//
//        $permalink = str_replace($find, $replace, $permalink);
//
//        return $permalink;


        // Abort if post is not a product
        if ($post->post_type !== 'product')
            return $permalink;

        // Abort early if the placeholder rewrite tag isn't in the generated URL
        if (false === strpos($permalink, '%'))
            return $permalink;

        // Get the custom taxonomy terms in use by this post
        $terms = get_the_terms($post->ID, 'product_brand');

        if (empty($terms)) {
            // If no terms are assigned to this post, use a string instead (can't leave the placeholder there)
            $product_brand = _x('uncategorized', 'slug', 'woocommerce-brands');
        } else {
            // Replace the placeholder rewrite tag with the first term's slug
            $first_term = array_shift($terms);
//            $first_term = ($terms->parent == 0) ? $terms : get_term($terms->parent, 'product_brand');

            $product_brand = $first_term->slug;
        }

        $find = array(
            '%product_brand%'
        );

        $replace = array(
            $product_brand
        );

        $replace = array_map('sanitize_title', $replace);

        $permalink = str_replace($find, $replace, $permalink);

        return $permalink;
    } // End post_type_link()

    public function woocommerce_brands_layered_nav_init($filtered_posts)
    {
        $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();

        if (is_active_widget(false, false, 'woocommerce_brand_nav', true) && !is_admin()) {

            if (!empty($_GET['filter_product_brand'])) {

                $terms = array_map('intval', explode(',', $_GET['filter_product_brand']));

                if (sizeof($terms) > 0) {
                    $matched_products = get_posts(
                        array(
                            'post_type' => 'product',
                            'numberposts' => -1,
                            'post_status' => 'publish',
                            'fields' => 'ids',
                            'no_found_rows' => true,
                            'tax_query' => array(

                                'relation' => 'AND',
                                array(
                                    'taxonomy' => 'product_brand',
                                    'terms' => $terms,
                                    'field' => 'id'
                                )
                            )
                        )
                    );

                    $filtered_posts = array_merge($filtered_posts, $matched_products);

                    if (sizeof($filtered_posts) == 0) {
                        $filtered_posts = $matched_products;
                    } else {
                        $filtered_posts = array_intersect($filtered_posts, $matched_products);
                    }

                }

            }

        }

        return (array)$filtered_posts;
    }

    private function includes()
    {
        //woocommerce_product_meta_end
        //woocommerce_single_product_summary
        //woocommerce_after_single_product_summary
        //woocommerce_before_single_product_summary
        //add_action( 'woocommerce_product_meta_end', array( $this,'pw_woocommerc_show_brand' )) ;
    }


    public function brand_show_title()
    {
        global $post;
        $id = $post->ID;
        $brand = "";
        if (get_option('pw_wooccommerce_display_brand_in_product_shop') == "yes" || get_option('pw_woocommerce_image_brand_shop_page') == "yes") {
            $terms = get_the_terms($post->ID, 'product_brand');
            if (is_array($terms)) {
                $brand .= '<span class="pw_brand_product_list">';
                if (get_option('pw_wooccommerce_display_brand_in_product_shop') == "yes") {

                    $tax = get_option('pw_woocommerce_brands_text', '');
                    $brand .= '<div class="wb-posted_in">' . $tax . '</div>';
                    if (is_array($terms)) {
                        $i = 0;
                        foreach ($terms as $b) {
                            $url = '';
                            $url = esc_html(get_woocommerce_term_meta($b->term_id, 'url', true));
                            if ($url != '') {
                                $brand .= '<a href="' . $url . '">' . $b->name . '</a>';
                            } else {
                                $brand .= '<a href="' . get_term_link($b->slug, 'product_brand') . '">' . $b->name . '</a>';
                            }
                            if ($i < count($terms) - 1) {
                                $brand .= ', ';
                            }
                            $i++;
                        }
                    }
                }
            }
//			if(get_option('pw_wooccommerce_display_brand_in_product_shop')=="yes")
//			{
//				$tax=(get_option('pw_woocommerce_brands_text')=="" ? "Brand" : get_option('pw_woocommerce_brands_text'));
//				$brand.= $this->pw_woocommerc_get_brands( $post->ID, ', ', '<div class="wb-posted_in">' . $tax . ': ', '</div>');
//				//echo $brand;
//				/* ==== For some theme = = = */
//	/*          echo $brand;
//				echo "
//					<script type='text/javascript'>
//						jQuery(document).ready(function(){
//							//alert('fg');
//							jQuery('.htheme_single_wc_item').each(function(){
//								elem=jQuery(this).find('.wb-posted_in');
//								jQuery(this).find('.wb-posted_in').remove();
//								jQuery(this).find('.htheme_col_3').append(elem);
//							});
//						});
//					</script>
//					";
//	*/
//			}
            if (get_option('pw_woocommerce_image_brand_shop_page') == "yes") {
                $product_cats = wp_get_post_terms($post->ID, 'product_brand', array("fields" => "ids"));

                $ratio = get_option('pw_woocommerce_image_brand_shop_page_image_size', "150:150");

                list($width, $height) = explode(':', $ratio);
                foreach ($product_cats as $cat) {
                    $thumbnail = get_woocommerce_term_meta($cat, 'thumbnail_id', true);
                    if ($thumbnail) {
                       // $image = wp_get_attachment_thumb_url($thumbnail);
						$image = current(wp_get_attachment_image_src($thumbnail, 'full'));
                        $brand .= '<img src="' . $image . '" style="width:' . $width . 'px;height:' . $height . 'px" />';
                    }
                }
            }
            $brand .= '</span>';
        }

        $position = get_option('pw_woocommerce_brands_position_product_list', "default");
        if ($position == 'default') {
            echo $brand;
        } elseif ($position == 'before_price') {
            echo "<script type='text/javascript'>
				jQuery(document).ready(function(){
					if(jQuery('li.post-" . ($id) . " .pw_brand_product_list').length < 1){
						jQuery('" . ($brand) . "').insertBefore('li.post-" . ($id) . " span.price');
					}
				});
			</script>";
        } elseif ($position == 'after_price') {
            echo "<script type='text/javascript'>
				jQuery(document).ready(function(){
					if(jQuery('li.post-" . ($id) . " .pw_brand_product_list').length < 1){
						jQuery('" . ($brand) . "').insertAfter('li.post-" . ($id) . " span.price');
					}
				});
			</script>";
        } elseif ($position == 'before_title') {
            echo "<script type='text/javascript'>
					jQuery(document).ready(function(){
						if(jQuery('li.post-" . ($id) . " .pw_brand_product_list').length < 1){
							jQuery('" . ($brand) . "').insertBefore('li.post-" . ($id) . " h2');
						}
					});
				</script>";
        } elseif ($position == 'after_title') {
            echo "<script type='text/javascript'>
					jQuery(document).ready(function(){
						if(jQuery('li.post-" . ($id) . " .pw_brand_product_list').length < 1){
							jQuery('" . ($brand) . "').insertAfter('li.post-" . ($id) . " h2');
						}
					});
				</script>";
        } elseif ($position == 'before_addcart') {
            echo "<script type='text/javascript'>
				jQuery(document).ready(function(){
					if(jQuery('li.post-" . ($id) . " .pw_brand_product_list').length < 1){
						jQuery('" . ($brand) . "').insertBefore('li.post-" . ($id) . " a.add_to_cart_button');
					}
				});
			</script>";
        } elseif ($position == 'after_addcart') {
            echo "<script type='text/javascript'>
				jQuery(document).ready(function(){
					if(jQuery('li.post-" . ($id) . " .pw_brand_product_list').length < 1){
						jQuery('" . ($brand) . "').insertAfter('li.post-" . ($id) . " a.add_to_cart_button');
					}
				});
			</script>";
        }
    }

    //jQuery('adsds').insertBefore(target);
    public function brand_description()
    {

        if (!is_tax('product_brand'))
            return;

        if (!get_query_var('term'))
            return;

        $thumbnail = '';
        $term = get_term_by('slug', get_query_var('term'), 'product_brand');
        if (get_option('pw_woocommerce_brands_image_list') == "yes") {

            $thumbnail = $this->get_brand_thumbnail_url($term->term_id, 'full');
        }
        $url = "";
        $url = esc_html(get_woocommerce_term_meta($term->term_id, 'url', true));
        if (version_compare(WC()->version, '3.0.0', '>=')) {
            $wc_get_3 = 'wc_get_template';
        } else {
            $wc_get_3 = 'woocommerce_get_template';
        }

        $wc_get_3('brand-description.php', array(
            'thumbnail' => $thumbnail,
            'name' => $term->name,
            'url' => $url
        ), 'woocommerce-brands', $this->plugin_path() . '/templates/');

    }

    public function get_brand_thumbnail_url($brand_id, $size = 'full')
    {
        $thumbnail_id = get_woocommerce_term_meta($brand_id, 'thumbnail_id', true);

        if ($thumbnail_id)
            return current(wp_get_attachment_image_src($thumbnail_id, $size));
    }

    public function pw_woocommerc_show_brand()
    {
        global $post;
        if (is_singular('product')) {

            if (get_option('pw_woocommerce_brands_show_categories') == "yes")
                $get_terms = "product_cat";
            else
                $get_terms = "product_brand";

			$taxonomy = get_taxonomy($get_terms);			
			$labels = $taxonomy->labels;
			$tax = get_option('pw_woocommerce_brands_text', '');
			if($tax!='')
				$tax.=':';
			$terms = get_the_terms($post->ID, $get_terms);
			$show_desc='';$show_text='';$show_img='';
			$show_img=false;;
			
			if ($terms) {
				$i=0;
				$brands_list_comma='';
				$show_text.= '<div class="wb-posted_in">'. $tax .' ';	
				$show_img .='<div class="wb-single-img-cnt" >';
				foreach ($terms as $term) {
					//Text
					if($i < count($terms) - 1) {
						$brands_list_comma = ' , ';								
					}
					$url = "";
					$url = esc_html(get_woocommerce_term_meta($term->term_id, 'url', true));
					if ($url != "") {
						$show_text.= '<a href="' .  $url . '">'.  $term->name .'</a>'.$brands_list_comma;
					}
					else{
						$show_text.= '<a href="' .get_term_link( $term->slug, $get_terms ).'">'.  $term->name .'</a>'.$brands_list_comma;
					}
					$brands_list_comma='';	
					$i++;						
				
					//Image
					
					$url = "";
					$url = esc_html(get_woocommerce_term_meta($term->term_id, 'url', true));	
					$thumbnail = get_woocommerce_term_meta($term->term_id, 'thumbnail_id', true);
					if ($thumbnail) {
						$ratio = get_option('pw_woocommerce_brands_image_single_image_size', "150:150");
						list($width, $height) = explode(':', $ratio);
						$image = current(wp_get_attachment_image_src($thumbnail, 'full'));
						if ($url != "") {
							$show_img .= '<a href="' . $url . '"><img src="' . $image . '"  alt="' . $term->name . '" style="width:' . $width . 'px;height:' . $height . 'px" /></a>';
						} else {
							$show_img .=  '<a href="' . get_term_link( $term->slug, $get_terms ).'"><img src="' . $image . '"  alt="' . $labels->name . '" style="width:' . $width . 'px;height:' . $height . 'px" /></a>';
						}
					}					
				}	
				$show_text.= '</div>';
				$show_img.= '</div>';
			}
			
			$brands = wp_get_post_terms($post->ID, $get_terms, array("fields" => "ids"));
			if (get_option('pw_woocommerce_brands_desc_single') == "yes") {
				if ($brands) {
					$prod_term = get_term($brands[0], 'product_brand');
					$description = $prod_term->description;
					$show_desc.= '<div class="shop_cat_desc">' . $description . '</div>';
				}
			}
			
			if (get_option('pw_woocommerce_brands_text_single') != "yes"){
				$show_text='';		
			}
			
			if (get_option('pw_woocommerce_brands_image_single') != "yes") {
				$show_img='';
			}
			echo $show_text . $show_img . $show_desc;
			

        }
    }

	public function duplicate_store_temporary_brands( $duplicate, $original ) {
		$terms = get_the_terms( $original->get_id(), 'product_brand' );
		if ( ! is_array( $terms ) ) {
			return;
		}

		$ids = array();
		foreach ( $terms as $term ) {
			$ids[] = $term->term_id;
		}
		$duplicate->add_meta_data( 'duplicate_temp_brand_ids', $ids );
	}
	public function duplicate_add_product_brand_terms( $product_id ) {
		$product = wc_get_product( $product_id );
		$term_ids = $product->get_meta( 'duplicate_temp_brand_ids' );
		if ( empty( $term_ids ) ) {
			return;
		}
		$term_taxonomy_ids = wp_set_object_terms( $product_id, $term_ids, 'product_brand' );
		$product->delete_meta_data( 'duplicate_temp_brand_ids' );
		$product->save();
	}	
	

    public function pw_woocommerc_get_brands($post_id = 0, $sep = ', ', $before = '', $after = '')
    {
        global $post;

        if ($post_id)
            $post_id = $post->ID;

        return get_the_term_list($post_id, 'product_brand', $before, $sep, $after);
    }

    /**
     * Get the plugin path
     */
    public function plugin_path()
    {
        if ($this->plugin_path) return $this->plugin_path;

        return $this->plugin_path = untrailingslashit(plugin_dir_path(dirname(__FILE__)));
    }
	
    public function woocommerce_catalog()
    {
        global $post, $product;
        //	print_r($product);
        $tax = get_option('pw_woocommerce_brands_text', '');
        echo $this->pw_woocommerc_get_brands($post->ID, ', ', ' <div class="wb-posted_in">' . $tax, '</div>');
    }

    public function template_loader($template)
    {

        $find = array('woocommerce.php');
        $file = '';
        //if(get_option('display_brand_in_product_listing')=="yes"){
        //	if ( is_tax( 'product_cat' ) )
        //	{
        //	add_action('woocommerce_after_shop_loop_item_title',array( $this, 'woocommerce_catalog'));
        //	}
        //}
        if (is_tax('product_brand')) {
            $term = get_queried_object();
            $file = 'taxonomy-' . $term->taxonomy . '.php';
            $find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
            $find[] = $this->template_url . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
            $find[] = $file;
            $find[] = $this->template_url . $file;
        }

        if ($file) {
            $template = locate_template($find);
            if (!$template) $template = $this->plugin_path() . '/templates/' . $file;
        }
        return $template;
    }

    ///////////////ADD FILTER TO ADMIN LIST////////////////////
    public function restrict_listings_by_properties()
    {
        global $typenow;
        global $wp_query;
        if ($typenow == 'product') {
            $taxonomy = 'product_brand';
            $business_taxonomy = get_taxonomy($taxonomy);
            wp_dropdown_categories(array(
                'show_option_all' => __("Show All a {$business_taxonomy->label}"),
                'taxonomy' => $taxonomy,
                'name' => 'product_brand',
                'orderby' => 'name',
                'selected' => (isset($wp_query->query['product_brand']) ? $wp_query->query['product_brand'] : ''),
                'hierarchical' => true,
                'depth' => 3,
                'show_count' => true, // Show # listings in parens
                'hide_empty' => true, // Don't show businesses w/o listings
            ));
        }
    }

    public function convert_id_to_term_in_query($query)
    {
        global $pagenow;
        $post_type = 'product'; // change HERE
        $taxonomy = 'product_brand'; // change HERE
        $q_vars = &$query->query_vars;
        if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
            $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
            $q_vars[$taxonomy] = $term->slug;
        }
    }


}

new pw_woocommerc_brans_Wc_Brands();
?>