<?php
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( ! class_exists( 'YITH_PayOuts_List_Table' ) ) {

	class YITH_PayOuts_List_Table extends WP_List_Table {

		public function __construct( $args = array() ) {
			parent::__construct( $args );
		}

		/**
		 * get columns
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return array
		 */
		public function get_columns() {
			$columns = array(
				'cb'              => '<input type="checkbox"/>',
				'payout_batch_id' => __( 'Payout Batch ID', 'yith-paypal-payouts-for-woocommerce' ),
				'payout_status'   => __( 'Payout Batch Status', 'yith-paypal-payouts-for-woocommerce' ),
				'payout_mode'     => __( 'Payout Mode', 'yith-paypal-payouts-for-woocommerce' ),
				'payout_detail'   => __( 'Payout Details', 'yith-paypal-payouts-for-woocommerce' )
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

				case 'payout_batch_id':
					$output = empty( $item['payout_batch_id'] ) ? __('N/A', 'yith-paypal-payouts-for-woocommerce') : $item['payout_batch_id'] ;
					break;
				case 'payout_status':
					$status = YITH_Payouts::get_payouts_status();
					$output = isset( $status[ $item['payout_status'] ] ) ? $status[ $item['payout_status'] ] : $item['payout_status'];
					break;
				case 'payout_mode':
					$output = $item['payout_mode'];
					break;

				case 'payout_detail':

					$query_args = array(
						'page'                => 'yith_wc_paypal_payouts_panel',
						'tab'                 => 'payout-list',
						'show_payout_details' => $item['sender_batch_id']
					);

					if ( isset( $_GET['payment_mode'] ) ) {

						$query_args['payment_mode'] = $_GET['payment_mode'];
					}

					$url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );

					$style         = '';
					$class_disable = '';
					if ( '' === $item['sender_batch_id'] ) {
						$style         = "points-event:none;";
						$class_disable = 'disabled';
					}
					$output = sprintf( "<a href='%s' class='button-secondary %s' style='%s'>%s</a>", $url, $class_disable, $style, __( 'Payout Details', 'yith-paypal-payouts-for-woocommerce' ) );
					break;
			}

			echo $output;
		}

		/**
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 * @param object $item
		 *
		 * @return string
		 */
		public function column_cb( $item ) {
			return sprintf(
				'<input type="checkbox" name="yith_payouts_ids[]" value="%s" />', $item['ID']
			);
		}

		/**
		 * return bulk actions
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return array|false|string
		 */
		public function get_bulk_actions() {

			$action = $this->current_action();

			if ( isset( $_REQUEST['yith_payouts_ids'] ) ) {

				$yith_payouts_ids = explode( ',', $_REQUEST['yith_payouts_ids'] );

				$is_delete_action = ( 'delete' == $action || 'force_delete' == $action );
				$force            = 'force_delete' == $action;

				if ( $is_delete_action ) {

					$this->delete( $yith_payouts_ids, $force );
				}

				$this->prepare_items();
			}

			$actions = array(
				'delete'       => __( 'Delete', 'yith-paypal-payouts-for-woocommerce' ),
				'force_delete' => __( 'Force Delete( Payout without Payout Batch ID )', 'yith-paypal-payouts-for-woocommerce' )
			);

			return $actions;
		}


		/**
		 * @param array $payout_ids
		 * @param bool
		 */
		public function delete( $payout_ids, $force ) {

			foreach ( $payout_ids as $key => $payout_id ) {

				$payout_batch_id = YITH_Payouts()->get_payouts( array(
					'ID'     => $payout_id,
					'fields' => array( 'payout_batch_id' )
				) );

				if ( ! empty( $payout_batch_id[0]['payout_batch_id'] ) ) {
					YITH_Payouts::delete_payout( $payout_id );
					YITH_Payout_Items::delete_payout_item( $payout_batch_id[0]['payout_batch_id'] );
				} else {

					if ( $force ) {
						YITH_Payouts::delete_payout( $payout_id );
					}
				}
			}

		}

		/**
		 * Extra controls to be displayed between bulk actions and pagination
		 *
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $which
		 */
		protected function extra_tablenav( $which ) {
			if ( 'top' == $which ) {
				if ( ! empty( $_REQUEST['payout_status'] ) ) {
					echo '<input type="hidden" name="payout_status" value="' . esc_attr( $_REQUEST['payout_status'] ) . '" />';
				}

				$this->payout_payment_mode_dropdown();
			}
		}

		/**
		 * show the dropdown to filter payment mode
		 * @author Salvatore Strano
		 * @since 1.0.0
		 */
		public function payout_payment_mode_dropdown() {

			$payment_mode          = YITH_PayOuts::get_payout_payment_mode();
			$payment_mode_selected = isset( $_REQUEST['payment_mode'] ) ? $_REQUEST['payment_mode'] : 'all';

			?>
            <div class="alignleft actions">
                <label for="filter-by-payment-mode"
                       class="screen-reader-text"><?php _e( 'Payment Mode', 'yith-paypal-payouts-for-woocommerce' ); ?></label>
                <select name="payment_mode" id="filter-by-payment-mode">
                    <option value="all" <?php selected( 'all', $payment_mode_selected ); ?> ><?php _e( 'All Payments mode', 'yith-paypal-payouts-for-woocommerce' ); ?></option>
					<?php foreach ( $payment_mode as $value => $name ): ?>
                        <option value="<?php echo $value; ?>" <?php selected( $value, $payment_mode_selected ); ?> ><?php echo $name; ?></option>
					<?php endforeach; ?>
                </select>
				<?php if ( ! empty( $_REQUEST['payout_status'] ) ) { ?>
                    <input type="hidden" name="payout_status" value="<?php echo $_REQUEST['payout_status']; ?>"/>
					<?php
				} ?>
                <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
            </div>
			<?php
		}


		/** prepare items to display
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
			$s            = isset( $_GET['s'] ) ? $_GET['s'] : '';
			$payout_mode  = isset( $_GET['payment_mode'] ) ? $_GET['payment_mode'] : 'all';
			$query_args   = array(
				'number'        => $per_page,
				'paged'         => $current_page,
				'order'         => 'DESC',
				'orderby'       => 'ID',
				's'             => $s,
				'payout_status' => $this->get_current_view(),
				'payout_mode'   => $payout_mode,
				'fields'        => array(
					'ID',
					'payout_batch_id',
					'sender_batch_id',
					'payout_status',
					'payout_mode'
				)
			);

			$this->items = YITH_Payouts()->get_payouts( $query_args );

			$total_items = YITH_Payouts()->get_payouts( array(
				'fields'        => 'count',
				'payout_status' => $this->get_current_view(),
				'payment_mode'  => $payout_mode,
				's'             => $s
			) );

			$this->set_pagination_args( array(
				'total_items' => $total_items,                  //WE have to calculate the total number of items
				'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
				'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
			) );
		}

		/**
		 * get all views for payouts
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return array
		 */
		protected function get_views() {

			$views = array( 'all' => __( 'All', 'yith-paypal-payouts-for-woocommerce' ) );

			$payout_status = YITH_PayOuts::get_payouts_status();

			$views = array_merge( $views, $payout_status );

			$current_view = $this->get_current_view();
			foreach ( $views as $view_id => $view ) {

				$class_current_view = $view_id == $current_view ? 'current' : '';

				$args = array(
					'payout_status' => $view_id,
					'fields'        => 'count',
					's'             => isset( $_GET['s'] ) ? $_GET['s'] : ''
				);

				$count = YITH_PayOuts()->get_payouts( $args );

				if ( $count > 0 ) {
					$link = esc_url( add_query_arg( array( 'payout_status' => $view_id ) ) );

					$views[ $view_id ] = sprintf( "<a href='%s' class='%s'>%s <span class='count'>(%d)</span></a>", $link, $class_current_view, $view, $count );
				} else {
					unset( $views[ $view_id ] );
				}
			}

			return $views;
		}

		/**
		 * get current view
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return string
		 */
		public function get_current_view() {

			return isset( $_GET['payout_status'] ) ? $_GET['payout_status'] : 'all';
		}


		/**
		 * show the search box
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 * @param string $text
		 * @param string $input_id
		 */
		public function search_box( $text, $input_id ) {

			$input_id = $input_id . '-search-input';
			?>
            <p class="search-box">
                <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo $text; ?>
                    :</label>
                <input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s"
                       value="<?php _admin_search_query(); ?>"/>
                <input type="hidden" name="page" value="yith_wc_paypal_payouts_panel"/>
                <input type="hidden" name="tab" value="payout-list"/>
				<?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
            </p>
			<?php
		}

		public function get_sender_item_ids( $sender_batch_id ) {

			$args = array(
				'sender_batch_id' => $sender_batch_id,
				'fields'          => array( 'sender_item_id' ),
			);

			$results = YITH_Payout_Items()->get_payout_items( $args );

			$ids = array();
			if ( count( $results ) > 0 ) {

				foreach ( $results as $result ) {

					if ( ! empty( $result['sender_item_id'] ) ) {

						$ids[] = $result['sender_item_id'];
					}
				}
			}

			return $ids;
		}

	}

}