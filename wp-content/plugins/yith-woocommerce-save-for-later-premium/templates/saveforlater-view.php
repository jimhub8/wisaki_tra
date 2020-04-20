<?php
/**
 * SaveForLater List page template
 *
 * @author Your Inspiration Themes
 * @package YITH Save for Later
 * @version 1.0.0
 */
$elements=count( $savelist_items );
$show_wishlist_link =  ( defined('YITH_WCWL') && get_option('ywsfl_show_wishlist_link') == 'yes' );
$text = sprintf( _n( '1 Product', '%s Products', count( $savelist_items ), 'yith-woocommerce-save-for-later' ), count( $savelist_items ) );
?>
<div id="ywsfl_general_content" data-num-elements="<?php echo $elements;?>">
    <div id="ywsfl_title_save_list"><h3><?php echo $title_list.'('.$text.' )';?></h3></div>
<?php

if($elements > 0):?>
    <div id="ywsfl_container_list">
        <?php
            foreach( $savelist_items as $item ):
                global $product;
              $product_id   =   ( isset( $item['variation_id']  ) && $item['variation_id']  > 0 ) ? $item['variation_id'] : $item['product_id'];

              
                $product_var = null;

                if( function_exists( 'wc_get_product' ) ) {
                    $product = wc_get_product( $product_id );
                }
                else{
                    $product = get_product( $product_id );
                }
                if( $product !== false && $product->exists() ) :
                    $availability = $product->get_availability();
                    $stock_status = $availability['class'];
                    $url = empty( $current_page ) ? wp_get_referer() : $current_page;
                ?>
                    <div class="ywsfl-row" id="row-<?php echo $item['product_id'];?>" data-row-id="<?php echo $item['product_id'];?>" data-row-variation-id="<?php echo $item['variation_id'];?>">
                        <?php
                          $args =   array(
                              'remove_from_savelist'    => $item['product_id'],
                              'variation_id'            =>  $item['variation_id']
                          )
                        ?>
                        <?php
                        $product_permalink =  $product->is_visible() ? esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $product_id ) ) ) : '';

                        ?>
                        <div class="delete_col"><a href="<?php echo esc_url( add_query_arg( $args, $url ) ) ?>" class="remove_from_savelist" data-product-id="<?php echo $item['product_id'];?>" data-variation-id="<?php echo $item['variation_id'];?>" title="Remove this product">&times;</a></div>
                        <div class="img_product">
                            <?php if( !$product_permalink ):
                                    echo $product->get_image();
                                else:?>
                            <a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $product_id ) ) ) ?>">
                                <?php echo $product->get_image();?>
                            </a>
                            <?php
                                endif;
                            ?>
                        </div>
                        <div class="sub_container_product">
                            <div class="product_name">
                                <?php
                                    if( !$product_permalink ):
                                        echo $product->get_name();
                                    else:

                                ?>
                                <a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $product_id ) ) ) ?>"><?php echo apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ?></a>
                                <?php
                                    endif;

                                $hidden_field   =   '';
                                if( $item['variation_id']!=-1 && !is_null( $item['variation_id'] ) ) {
                                    /**
                                     * @var WC_Product_Variation $product_var
                                     */
                                    $product_var    =   wc_get_product( $item['variation_id'] );
                                    $variations_av = $product_var->get_variation_attributes();


                                    $item_data = array();

                                    // Variation data
                                    if (is_array($variations_av)) {

                                        foreach ($variations_av as $name => $value) {
                                            $label = '';

                                            if ('' === $value)
                                                continue;

                                            $taxonomy = wc_attribute_taxonomy_name(str_replace('attribute_pa_', '', urldecode($name)));

                                            // If this is a term slug, get the term's nice name
                                            if (taxonomy_exists($taxonomy)) {
                                                $term = get_term_by('slug', $value, $taxonomy);
                                                if (!is_wp_error($term) && $term && $term->name) {
                                                    $value = $term->name;
                                                }
                                                $label = wc_attribute_label($taxonomy);

                                            } else {

                                                if (strpos($name, 'attribute_') !== false) {
                                                    $custom_att = str_replace('attribute_', '', $name);

                                                    if ($custom_att != '') {
                                                        $label = wc_attribute_label($custom_att);
                                                    } else {
                                                        $label = $name;
                                                    }
                                                }

                                            }

                                            $item_data[] = array(
                                                'key' => $label,
                                                'value' => $value
                                            );

                                            $hidden_field .=   '<input type="hidden" name="'.strtolower( $name ).'" value="'.$value.'"/>';
                                        }
                                    }

                                    // Output flat or in list format
                                    if (sizeof($item_data) > 0) {
                                        echo '<div class="variation">';
                                        foreach ($item_data as $data) {
                                            echo '<div class="variation-' . $data['key'] . '">';
                                            echo '<span class="variation_name">' . esc_html($data['key']) . ':</span>';
                                            echo '<span class="variation_value">' . wp_kses_post($data['value']) . "</span>";
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                    }
                                }
                                ?>

                                <p class="display_price">
                                    <?php
                                    if( is_a( $product, 'WC_Product_Bundle' ) ){
                                        if( $product->min_price != $product->max_price ){
                                            echo sprintf( '%s - %s', wc_price( $product->min_price ), wc_price( $product->max_price ) );
                                        }
                                        else{
                                            echo wc_price( $product->min_price );
                                        }
                                    }
                                    elseif( $product->get_price() != '0' ) {
                                        echo $product->get_price_html();
                                    }
                                    else {
                                        echo apply_filters( 'yith_free_text', __( 'Free!', 'yith-woocommerce-save-for-later' ) );
                                    }
                                    ?>
                                </p>
                                <p class="display_product_status">
                                    <?php
                                    if( $stock_status == 'out-of-stock' ) {
                                    $stock_status = "Out";
                                    echo '<span class="savelist-out-of-stock">' . __( 'Out of Stock', 'yith-woocommerce-save-for-later' ) . '</span>';
                                    } else {
                                    $stock_status = "In";
                                    echo '<span class="savelist-in-stock">' . __( 'In Stock', 'yith-woocommerce-save-for-later' ) . '</span>';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="cont_buttons">
                                <!-- Add to cart button -->
                                <?php if( isset( $stock_status ) && $stock_status != 'Out' ): ?>

                                  <form method="post">
                                    <?php
                                    $product    =    isset( $product_var ) ? $product_var : $product;
                                    woocommerce_template_loop_add_to_cart();
                                  
                                    if( $item['variation_id']!=-1 ) {

                                        echo '<input type="hidden" name="variation_id" value="'.$item['variation_id'].'"/>';
                                        echo $hidden_field;
                                    }

                                    ?>
                                    <?php if( $show_wishlist_link && ! YITH_WCWL()->is_product_in_wishlist($product_id) ) :
                                        echo do_shortcode('[yith_wcwl_add_to_wishlist]');
                                        ?>

                                    <?php endif;?>
                                <?php endif; ?>
                                  </form>

                            </div>

                    </div>
                    <?php else:?>
                    <?php
                    global $YIT_Save_For_Later;
                    $YIT_Save_For_Later->remove_no_available_product_form_save_list($item['product_id'], $item['variation_id'] );
                    ?>
                <?php endif;?>
        <?php endforeach;?>
    </div>
<?php else:?>
<span class="ywsfl_no_products_message"><?php _e('No Products in save list', 'yith-woocommerce-save-for-later' );?></span>
<?php endif;?>
</div>
