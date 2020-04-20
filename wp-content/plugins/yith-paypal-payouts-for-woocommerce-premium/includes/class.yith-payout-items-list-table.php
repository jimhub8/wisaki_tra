<?php
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( ! class_exists( 'YITH_PayOut_Items_List_Table' ) ) {

	class YITH_PayOut_Items_List_Table extends WP_List_Table {

		public function __construct( $args = array() ) {
			parent::__construct( $args );

			add_action( 'admin_footer', array( $this, 'payout_item_preview_template' ) );

		}

		/**
		 * get columns
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return array
		 */
		public function get_columns() {
			$columns = array(
				'receiver'           => __( 'Receiver', 'yith-paypal-payouts-for-woocommerce' ),
				'transaction_id'     => __( 'Transaction ID', 'yith-paypal-payouts-for-woocommerce' ),
				'transaction_status' => __( 'Transaction Status', 'yith-paypal-payouts-for-woocommerce' ),
				'amount'             => __( 'Amount', 'yith-paypal-payouts-for-woocommerce' ),
				'fee'                => __( 'Fee', 'yith-paypal-payout-for-woocommerce' ),
                'details'           => ''
			);

			return $columns;
		}

		/**
		 * @param object $item
		 * @param string $column_name
		 */
		public function column_default( $item, $column_name ) {

			$output = '';
			switch ( $column_name ) {

				case 'amount' :
				case 'fee':
					$currency = $item['currency'];
					$output   = wc_price( $item[ $column_name ], array( 'currency' => $currency ) );
					break;

				case 'transaction_status':

					$status = YITH_Payout_Items()->get_transaction_status();

					$output = ! empty( $status[ $item['transaction_status'] ] ) ? $status[ $item['transaction_status'] ] : $item['transaction_status'];
					break;
				case 'transaction_id':

					$output = ! empty( $item[ $column_name ] ) ? $item[ $column_name ] : 'N/A';

					break;

                case 'details':
	                $output = '<a href ="" class="payout-item-preview" data-ID="' . $item["ID"] . '">View more</a>';
                    break;
				default:

					$output = ! empty( $item[ $column_name ] ) ? $item[ $column_name ] : 'N/A';
					break;
			}

			echo $output;
		}


		/**
		 *prepare items to display
		 * @author Salvatore Strano
		 * @since 1.0.0
		 */
		public function prepare_items() {
			$per_page              = 15;
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			$current_page = $this->get_pagenum();

			$query_args = array(
				'number'          => $per_page,
				'sender_batch_id' => $_GET['show_payout_details'],
				'paged'           => $current_page,
				'fields'          => array(
					'ID',
					'receiver',
					'transaction_id',
					'transaction_status',
					'amount',
					'fee',
					'currency'
				)
			);

			$this->items = YITH_Payout_Items()->get_payout_items( $query_args );

			$total_items = YITH_Payout_Items()->get_payout_items( array(
				'fields'          => 'count',
				'payout_batch_id' => $_GET['show_payout_details'],
			) );

			$this->set_pagination_args( array(
				'total_items' => $total_items,                  //WE have to calculate the total number of items
				'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
				'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
			) );
		}


		/**
		 * get payout item details
		 * @author Salvatore Strano
		 * @since 1.0.1
		 *
		 * @param int $payout_item_id
		 *
		 * @return array
		 */
		public static function payout_item_preview_get_payout_item_details( $payout_item_id ) {

			if ( ! $payout_item_id ) {
				return array();
			}

			$args = array(
				'fields' => array(
					'ID',
					'sender_batch_id',
					'transaction_id',
					'transaction_status',
					'amount'
				),
				'ID'     => $payout_item_id
			);


			$payout_item = YITH_Payout_Items()->get_payout_items( $args );
			$payout_item = count( $payout_item ) == 1 ? $payout_item[0] : array();

			$status              = YITH_Payout_Items()->get_transaction_status();
			$payout_item_details = array(
				'transaction_id'          => ! empty( $payout_item['transaction_id'] ) ? $payout_item['transaction_id'] : 'N/A',
				'transaction_status_name' => $status[ $payout_item['transaction_status'] ],
				'transaction_status'      => $payout_item['transaction_status'],
				'item_html'               => self::get_payout_item_preview_item_html( $payout_item )
			);

			return $payout_item_details;

		}

		/**
		 * @param array $payout_item
		 *
		 * @return string
		 */
		public static function get_payout_item_preview_item_html( $payout_item ) {

			$sender_batch_id = $payout_item['sender_batch_id'];
			$args            = array(
				'fields' => array(
					'payout_mode',
					'order_id'
				),

				'sender_batch_id' => $sender_batch_id

			);
			$payout          = YITH_Payouts()->get_payouts( $args );
			$payout          = $payout[0];


			switch ( $payout['payout_mode'] ) {
				case 'commission':
					$items = self::get_commission_items_html( $sender_batch_id );
					break;
				case 'affiliate':
				    $items = self::get_affiliate_items_html( $sender_batch_id );
					break;
				default:
					$items = self::get_instant_items_html( $payout['order_id'], $payout_item );
					break;
			}

			$columns = array(
				'order_commission' => __( 'Order/Commission ID', 'yith-paypal-payouts-for-woocommerce' ),
				'total'            => __( 'Total', 'yith-paypal-payouts-for-woocommerce' )
			);

			$html = '
		<div class="wc-order-preview-table-wrapper">
			<table cellspacing="0" class="wc-order-preview-table">
				<thead>
					<tr>';

			foreach ( $columns as $column => $label ) {
				$html .= '<th class="wc-order-preview-table__column--' . esc_attr( $column ) . '">' . esc_html( $label ) . '</th>';
			}

			$html .= '
					</tr>
				</thead>
				<tbody>';
			$html .= $items;

			$html .= '</tbody></table></div>';


			return $html;


		}

		/**
		 * get the commission details for a batch payment
		 * @author  Salvatore Strano
		 * @since 1.0.1
		 *
		 * @param string $sender_batch_id
		 *
		 * @return string
		 */
		public static function get_commission_items_html( $sender_batch_id ) {


			$query_args           = array(
				'fields'          => array(
					'fee'
				),
				'sender_batch_id' => $sender_batch_id
			);
			$receiver_fees = YITH_Payout_Items()->get_payout_items( $query_args );

			$row_fees_html = '';
			$total_fee = 0;
			if( count( $receiver_fees ) > 0 ){

			    foreach( $receiver_fees as $receiver_fee ){

			        $fee = isset( $receiver_fee['fee'] ) ? $receiver_fee['fee']  : 0;
			        $total_fee += $fee;
                }
				$new_formatted_total = wc_price( $total_fee );
				$row_fees_html        = '<tr class="wc-order-preview-table__item wc-order-preview-table__item--net-total">
								<td class="wc-order-preview-table__column--order_commission"><strong>' . __( 'Total Fee', 'yith-paypal-payouts-for-woocommerce' ) . '</strong></td>	
								<td class="wc-order-preview-table__column--total">' . $new_formatted_total . '</td>	
								</tr>';
            }

			$payment_id     = $payment_id = str_replace( 'commission_', '', $sender_batch_id );
			$commission_ids = array();

			if ( function_exists( 'YITH_Vendors' ) ) {
				$paid_ids       = YITH_Vendors()->payments->get_commissions_by_payment_id( $payment_id, 'paid' );
				$processing_ids = YITH_Vendors()->payments->get_commissions_by_payment_id( $payment_id, 'processing' );
				$unpaid_ids     = YITH_Vendors()->payments->get_commissions_by_payment_id( $payment_id, 'unpaid' );

				$commission_ids = array_merge( $paid_ids, $processing_ids, $unpaid_ids );
			}

			$html = '';
			if ( count( $commission_ids ) > 0 ) {
				$args_query = array(
					'page' => 'yith_vendor_commissions',
					'view' => ''
				);

				$total = 0;
				foreach ( $commission_ids as $commission_id ) {
					$commission       = YITH_Commission( $commission_id );
					$commission_total = $commission->get_amount( 'display' );
					$total            += $commission->get_amount();
					$order            = $commission->get_order();

					$order_url = $order->get_edit_order_url();

					$customer = $order->get_user();

					if ( $customer ) {
						$customer_name = $customer->user_firstname . ' ' . $customer->last_name;
					} else {
						$customer_name = 'Guest';
					}
					$order_url          = '<a href="' . $order_url . '">#' . $order->get_order_number() . ' ' . $customer_name . '</a>';
					$args_query['view'] = $commission_id;
					$url                = esc_url( add_query_arg( $args_query, admin_url( 'admin.php' ) ) );
					$link               = sprintf( '<a href="%s" target="_blank">%s %s</a>', $url, _x( 'Commission','Commission for order #123', 'yith-paypal-payouts-for-woocommerce' ), '#' . $commission_id );
					$text               = $link . ' ' . _x( 'for order','Commission for order #123', 'yith-paypal-payout-for-woocommerce' ) . ' ' . $order_url;

					$html .= '<tr class="wc-order-preview-table__item wc-order-preview-table__item--' . $commission_id . '">
								<td class="wc-order-preview-table__column--order_commission">' . $text . '</td>	
								<td class="wc-order-preview-table__column--total">' . $commission_total . '</td>	
								</tr>';
				}

				$html.= $row_fees_html;

				$new_formatted_total = wc_price( $total+$total_fee );
				$html                .= '<tr class="wc-order-preview-table__item wc-order-preview-table__item--net-total">
								<td class="wc-order-preview-table__column--order_commission"><strong>' . __( 'Total transaction', 'yith-paypal-payouts-for-woocommerce' ) . '</strong></td>	
								<td class="wc-order-preview-table__column--total">' . $new_formatted_total . '</td>	
								</tr>';
			} else {
				$html = '<tr class="wc-order-preview-table__item wc-order-preview-table__item--no-row">
							<td colspan="2" class="wc-order-preview-table__column--no-row">' . __( 'No commission found for this transaction ID', 'yith-paypal-payouts-for-woocommerce' ) . '</td>
						</tr>';
			}

			$html .= '</tr>';


			return $html;
		}

		/**
		 * @param string $order_id
		 * @param array $payout_item
		 * get order detail for batch id
		 *
		 * @author Salvatore Strano
		 * @since 1.0.1
		 * @return string
		 */
		public static function get_instant_items_html( $order_id, $payout_item ) {

			$html = '';
			if ( ! empty( $order_id ) ) {

				$order           = wc_get_order( $order_id );
				$formatted_total = $order->get_formatted_order_total();
				$total           = $order->get_total();
				$url             = $order->get_edit_order_url();
				$customer        = $order->get_user();

				if ( $customer ) {

					$customer_name = $customer->user_firstname . ' ' . $customer->last_name;
				} else {
					$customer_name = 'Guest';
				}
				$url  = '<a href="' . $url . '">#' . $order->get_order_number() . ' ' . $customer_name . '</a>';
				$html = '<tr class="wc-order-preview-table__item wc-order-preview-table__item--' . $order_id . '">
								<td class="wc-order-preview-table__column--order_commission">' . $url . '</td>	
								<td class="wc-order-preview-table__column--total">' . $formatted_total . '</td>	
								</tr>';


				$query_args           = array(
					'fields'          => array(
						'receiver',
						'amount',
                        'fee'
					),
					'sender_batch_id' => $payout_item['sender_batch_id']
				);
				$receiver_commissions = YITH_Payout_Items()->get_payout_items( $query_args );
				if ( count( $receiver_commissions ) > 0 ) {

					$currency = $order->get_currency();
					$total_fee = 0;
					foreach ( $receiver_commissions as $receiver_commission ) {

						$amount           = $receiver_commission['amount'];
						$fee = isset( $receiver_commission['fee'] ) ? $receiver_commission['fee'] : 0 ;
						$total_fee+= $fee;
						$total            -= $amount - $fee;
						$formatted_amount = '-' . wc_price( $amount, array( 'currency', $currency ) );
						$html             .= '<tr class="wc-order-preview-table__item wc-order-preview-table__item--receiver-commission">
                                                <td class="wc-order-preview-table__column--order_commission">' . __( 'Receiver Commission', 'yith-paypal-payouts-for-woocommerce' ) . '
                                                    <p><small>' . $receiver_commission['receiver'] . '</small></p>
                                                </td>	
                                                <td class="wc-order-preview-table__column--total">' . $formatted_amount . '</td>	
								            </tr>';
					}

					$new_formatted_fee = '-'.wc_price( $total_fee, array( 'currency', $currency ) );
					$html                .= '<tr class="wc-order-preview-table__item wc-order-preview-table__item--net-total">
								<td class="wc-order-preview-table__column--order_commission"><strong>' . __( 'Fee', 'yith-paypal-payouts-for-woocommerce' ) . '</strong></td>	
								<td class="wc-order-preview-table__column--total">' . $new_formatted_fee . '</td>	
								</tr>';
					$new_formatted_total = wc_price( $total, array( 'currency', $currency ) );
					$html                .= '<tr class="wc-order-preview-table__item wc-order-preview-table__item--net-total">
								<td class="wc-order-preview-table__column--order_commission"><strong>' . __( 'Net Total', 'yith-paypal-payouts-for-woocommerce' ) . '</strong></td>	
								<td class="wc-order-preview-table__column--total">' . $new_formatted_total . '</td>	
								</tr>';
				}

			} else {
				$html = '<tr class="wc-order-preview-table__item wc-order-preview-table__item--no-row">
							<td colspan="2" class="wc-order-preview-table__column--no-row">' . __( 'No commission found for this transaction ID', 'yith-paypal-payouts-for-woocommerce' ) . '</td>
						</tr>';
			}

			return $html;
		}

		/**
		 * @param string $sender_batch_id
         * @return string
		 */
		public  static function get_affiliate_items_html( $sender_batch_id ){
			$args = array(
				'sender_batch_id' => $sender_batch_id,
				'fields'          => array( 'sender_item_id', 'amount', 'fee' ),
			);

			$affiliate_query_args = array(
				'page' => 'yith_wcaf_panel',
				'tab'  => 'payments'
			);

			$results = YITH_Payout_Items()->get_payout_items( $args );
			$html = '';
			if( count( $results ) > 0 ) {
			    $total =0;
				$total_fee = 0;
				$href_text            = _x( 'Affiliate Payment', 'Affiliate Payment for : list payment ids','yith-paypal-payouts-for-woocommerce' );
				foreach ( $results as $result ) {

				    $affiliate_id = str_replace( 'affiliate_payment_','', $result['sender_item_id'] );
				    $affiliate_total = $result['amount'];
				    $fee = isset( $result['fee'] ) ? $result['fee']  : 0;
				    $total_fee += $fee;
				    $total += $affiliate_total;
				    $affiliate_formatted_total = wc_price( $affiliate_total );
					$affiliate_query_args['payment_id'] = $affiliate_id;
					$url = esc_url( add_query_arg( $affiliate_query_args , admin_url( 'admin.php' ) ) );
					$url = sprintf('<a href="%s" target="_blank">%s</a>', $url, '#'.$affiliate_id );
					$html             .= '<tr class="wc-order-preview-table__item wc-order-preview-table__item--receiver-commission">
                                                <td class="wc-order-preview-table__column--order_commission">'.$href_text.' '.$url.'
                                                    
                                                </td>	
                                                <td class="wc-order-preview-table__column--total">' . $affiliate_formatted_total . '</td>	
								            </tr>';

				}

				$new_formatted_fee = wc_price( $total_fee );
				$html                .= '<tr class="wc-order-preview-table__item wc-order-preview-table__item--net-total">
								<td class="wc-order-preview-table__column--order_commission"><strong>' . __( 'Fee', 'yith-paypal-payouts-for-woocommerce' ) . '</strong></td>	
								<td class="wc-order-preview-table__column--total">' . $new_formatted_fee . '</td>	
								</tr>';

				$new_formatted_total = wc_price( $total+$total_fee );
				$html                .= '<tr class="wc-order-preview-table__item wc-order-preview-table__item--net-total">
								<td class="wc-order-preview-table__column--order_commission"><strong>' . __( 'Total transaction', 'yith-paypal-payouts-for-woocommerce' ) . '</strong></td>	
								<td class="wc-order-preview-table__column--total">' . $new_formatted_total . '</td>	
								</tr>';
			}else{
				$html = '<tr class="wc-order-preview-table__item wc-order-preview-table__item--no-row">
							<td colspan="2" class="wc-order-preview-table__column--no-row">' . __( 'No affiliate payments found for this transaction ID', 'yith-paypal-payouts-for-woocommerce' ) . '</td>
						</tr>';
            }
			return $html;
        }

		public function payout_item_preview_template() {
			?>
            <script type="text/template" id="tmpl-wc-modal-view-payout-item">
                <div class="wc-backbone-modal wc-payout-item-preview wc-order-preview">
                    <div class="wc-backbone-modal-content">
                        <section class="wc-backbone-modal-main" role="main">
                            <header class="wc-backbone-modal-header">
                                <mark class="order-status status-{{ data.transaction_status }}"><span>{{ data.transaction_status_name }}</span>
                                </mark>
								<?php /* translators: %s: order ID */ ?>
                                <h1><?php echo esc_html( sprintf( __( 'Transaction ID #%s', 'woocommerce' ), '{{ data.transaction_id }}' ) ); ?></h1>
                                <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                                    <span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce' ); ?></span>
                                </button>
                            </header>
                            <article>
                                {{{ data.item_html }}}
                            </article>
                        </section>
                    </div>
                </div>
            </script>
			<?php

		}
	}

}