<?php
/*
* Stop execution if someone tried to get file directly.
*/
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Manage all the functionality regarding stock management
 *
 * @since  1.0.0
 */
if ( ! class_exists( 'Stock_Management_Manager' ) ){

	class Stock_Management_Manager {

		/**
		 * Constructor.
		 *
		 * Fire all required wp actions
		 *
		 * @since  1.0.0
		 */
		function __construct(){

			add_action( 'init', [
				$this,
				'register_stock_management_cpt'
			], 20 );

			add_action( 'admin_enqueue_scripts', [
				$this,
				'enqueue_styles'
			] );

			add_action( 'add_meta_boxes', [
				$this,
				'meta_box'
			]);

			add_action( 'save_post', [
				$this,
				'save_metadata'
			]);

			add_action( 'woocommerce_payment_complete', [
				$this,
				'assign_user'
			]);

			// 				add_action( 'woocommerce_new_order', [
			// 			$this,
			// 			'assign_user'
			// 		], 1, 2);

			add_action( 'admin_menu',[ $this,
					'register_submenu' ]
			);

			add_action( 'pre_get_posts',[ $this,
					'custom_search_query' ]
			);

			add_action( 'wp_trash_post', [
				$this,
				'delete_post'
			]);

			add_action( 'untrash_post', [
				$this,
				'untrash_post'
			]);

		}

		public function custom_search_query( $query ){

			if( !is_admin() ){
				return;
			}
			$custom_fields = array(
				// put all the meta fields you want to search for here
				"order_id",
				"sold_to"
			);
			$searchterm = $query->query_vars['s'];

			// we have to remove the "s" parameter from the query, because it will prevent the posts from being found
			$query->query_vars['s'] = "";

			if ($searchterm != "") {
				$meta_query = array('relation' => 'OR');
				foreach($custom_fields as $cf) {
					array_push($meta_query, array(
						'key' => $cf,
						'value' => $searchterm,
						'compare' => 'LIKE'
					));
				}
				$query->set("meta_query", $meta_query);
			}
		}

		/**
		 * Register custom post type for stock
		 *
		 * @since     1.0.0
		 */
		public function register_stock_management_cpt() {

			$labels = [
				'name'                  => __( 'Stock', 'Post Type General Name', 'stock-management' ),
				'singular_name'         => __( 'Stock', 'Post Type Singular Name', 'stock-management' ),
				'menu_name'             => __( 'Stock', 'stock-management' ),
				'name_admin_bar'        => __( 'Stock', 'stock-management' ),
				'archives'              => __( 'Stock Archives', 'stock-management' ),
				'attributes'            => __( 'Stock Attributes', 'stock-management' ),
				'parent_item_colon'     => __( 'Parent Stock:', 'stock-management' ),
				'all_items'             => __( 'All Stock', 'stock-management' ),
				'add_new_item'          => __( 'Add New Stock', 'stock-management' ),
				'add_new'               => __( 'Add New', 'stock-management' ),
				'new_item'              => __( 'New Stock', 'stock-management' ),
				'edit_item'             => __( 'Edit Stock', 'stock-management' ),
				'update_item'           => __( 'Update Stock', 'stock-management' ),
				'view_item'             => __( 'View Stock', 'stock-management' ),
				'view_items'            => __( 'View Stock List', 'stock-management' ),
				'search_items'          => __( 'Search Stock', 'stock-management' ),
				'not_found'             => __( 'Not found', 'stock-management' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'stock-management' ),
				'featured_image'        => __( 'Featured Image', 'stock-management' ),
				'set_featured_image'    => __( 'Set featured image', 'stock-management' ),
				'remove_featured_image' => __( 'Remove featured image', 'stock-management' ),
				'use_featured_image'    => __( 'Use as featured image', 'stock-management' ),
				'insert_into_item'      => __( 'Insert into Stock', 'stock-management' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Stock', 'stock-management' ),
				'items_list'            => __( 'Stock list', 'stock-management' ),
				'items_list_navigation' => __( 'Stock list navigation', 'stock-management' ),
				'filter_items_list'     => __( 'Filter Stock list', 'stock-management' ),
			];

			$args   = [
				'label'               => __( 'Stock', 'stock-management' ),
				'description'         => __( 'Stock Management', 'stock-management' ),
				'labels'              => $labels,
				'supports'            => [ 'title' ],
				'taxonomies'          => [],
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => false,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'rewrite'             => false,
				'capability_type'     => 'page',
				'show_in_rest'        => false,
			];

			register_post_type( 'stock-management', $args );

		}

		/**
		 * Enqueue admin styles
		 *
		 * @since  1.0.0
		 */
		public function enqueue_styles( ) {

			wp_enqueue_style( 'stock-management', SM_URL . 'includes/assets/css/stock-management-admin.css', [], SM_VERSION );

		}

		/**
		 * Register Sub menu page
		 *
		 * @since  1.0.0
		 */
		public function register_submenu()
		{
			add_submenu_page(
				'edit.php?post_type=stock-management',
				__( 'Email Template', 'easy-facebook-likebox' ),
				__( 'Email Template', 'easy-facebook-likebox' ),
				'manage_options',
				'sm-email-template',
				[ $this, 'email_template_html' ],
		);
		}

		/**
		 * Meta box HTML
		 *
		 * @since 1.0.0
		 */
		public function email_template_html(){

			/**
			 * Load template for meta box
			 */
			if ( $url = locate_template( [ 'stock-management/views/html-email-template.php' ] ) ) {
				$url = $url;
			} else {
				$url = SM_DIR . 'includes/views/html-email-template.php';
			}
			include $url;

		}

		/**
		 * Register custom meta box
		 *
		 * @since     1.0.0
		 */
		public function meta_box(){

			add_meta_box(
				'stock-management',
				__('Manage', 'stock-management'),
				[ $this, 'meta_box_html' ],
				'stock-management'
			);

		}

		/**
		 * Meta box HTML
		 *
		 * @since 1.0.0
		 *
		 * @param $post
		 */
		public function meta_box_html( $post ){

			if( isset( $post->ID ) ){
				$values = get_post_custom( $post->ID );
			}else{
				$values = false;
			}

			$products = get_posts([
				'numberposts' => 50,
				'post_type'   => 'product',
				'post_status'    => 'publish'
			]);

			/**
			 * Load template for meta box
			 */
			if ( $url = locate_template( [ 'stock-management/views/html-meta-box.php' ] ) ) {
				$url = $url;
			} else {
				$url = SM_DIR . 'includes/views/html-meta-box.php';
			}
			include $url;

		}

		/**
		 * Save meta box values
		 *
		 * @since 1.0.0
		 *
		 * @param $post_id
		 */
		public function save_metadata( $post_id ){


			if( !$this->is_stock_management_post_type()
			    || !isset( $_POST )
			    || !isset( $_POST['stock_management_nonce'] )
			    || !wp_verify_nonce( $_POST['stock_management_nonce'], 'stock_management' )
			){
				return false;
			}

			update_post_meta( $post_id, 'sold', sanitize_text_field( $_POST['sold'] ) );

			if( $this->is_valid_field_value( $_POST['product'] ) ){

				$stock_added = get_post_meta( $post_id, 'stock_added', true );

				if(!$stock_added){
					$stock_updated = $this->update_stock($_POST['product']);
					if( $stock_updated ){
						update_post_meta( $post_id, 'stock_added', true );
					}
				}

				update_post_meta( $post_id, 'product', sanitize_text_field( $_POST['product'] ) );
			}

			if( $this->is_valid_field_value( $_POST['sold_to'] ) ){
				update_post_meta( $post_id, 'sold_to', sanitize_text_field( $_POST['sold_to'] ) );
			}
			if( $this->is_valid_field_value( $_POST['password'] ) ){
				update_post_meta( $post_id, 'password', sanitize_text_field( $_POST['password'] ) );
			}
			if( $this->is_valid_field_value( $_POST['order_id'] ) ){
				update_post_meta( $post_id, 'order_id', sanitize_text_field( $_POST['order_id'] ) );
			}

		}

		// 	public function assign_user(  $oder_id, $order ){

		// 		if( !$oder_id ){
		// 			return;
		// 		}

		// 		if( $order->data['billing']['email'] ){
		// 			$email = $order->data['billing']['email'];
		// 			$available_stock = $this->get_available_stock();
		// 			if( $available_stock ){
		// 				$available_stock_id = $available_stock['ID'];
		// 				update_post_meta( $available_stock_id, 'sold', 'on');
		// 				update_post_meta( $available_stock_id, 'sold_to', $email);
		// 				update_post_meta( $available_stock_id, 'order_id', $oder_id);
		// 				update_post_meta( $oder_id, 'stock_id', $available_stock_id);

		// 				$order_url = get_site_url().'/checkout/order-received/'.$oder_id.'/?key='.$order->data['order_key'];

		// 				$to = $email;
		// 				$subject = get_option( 'sm-email-subject', false );
		// 				$message = get_option( 'sm-email-message', false );
		// 				$message = str_replace('{username}', $available_stock['post_title'], $message);
		// 				$message = str_replace('{password}', $available_stock['password'], $message);
		// 				$message .= '<br><a href="'.$order_url.'">View Order</a>';

		// 				$headers = array('Content-Type: text/html; charset=ISO-8859-1');
		// 				$email_sent = wp_mail( $to, $subject, $message, $headers );
		// 			}


		// 		}


		// 	}


		public function assign_user(  $oder_id){



			if( !$oder_id ){
				return;
			}

			//$order = new WC_Order( $order_id );

			if( isset( $_POST['billing_email']) && !empty($_POST['billing_email']) ){
				$email = $_POST['billing_email'];
				$available_stock = $this->get_available_stock();
//				echo '<pre>'; print_r($_POST); echo '</pre>'; exit;
				if( $available_stock ){
					$available_stock_id = $available_stock['ID'];
					update_post_meta( $available_stock_id, 'sold', 'on');
					update_post_meta( $available_stock_id, 'sold_to', $email);
					update_post_meta( $available_stock_id, 'order_id', $oder_id);
					update_post_meta( $oder_id, 'stock_id', $available_stock_id);

					//$order_url = get_site_url().'/checkout/order-received/'.$oder_id.'/?key='.$order->data['order_key'];

					$to = $email;
					$subject = get_option( 'sm-email-subject', false );
					$message = get_option( 'sm-email-message', false );
					$message = str_replace('{username}', $available_stock['post_title'], $message);
					$message = str_replace('{password}', $available_stock['password'], $message);
					//$message .= '<br><a href="'.$order_url.'">View Order</a>';

					$headers = array('Content-Type: text/html; charset=ISO-8859-1');
					$email_sent = wp_mail( $to, $subject, $message, $headers );
				}


			}


		}

		/**
		 * If current page is stock management
		 *
		 * @return bool
		 */
		private function is_stock_management_post_type(){
			if ( 'stock-management' === get_post_type() ) {
				return true;
			}else{
				return false;
			}
		}

		/**
		 * * If field value is valid
		 *
		 * @param $field
		 *
		 * @return bool
		 */
		private function is_valid_field_value( $field ) {

			if ( isset( $field ) ) {

				return true;

			}else{

				return false;
			}
		}

		/**
		 * Get single available stock
		 *
		 * @since 1.0.0
		 *
		 * @return false|mixed|void
		 */
		public function get_available_stock($product_id){

			$args = [
				'post_type'   => 'stock-management',
				'posts_per_page' => 1,
				'meta_query'  => [
					[
						'key' => 'sold',
						'value' => null,
						'compare' => '='
					],
					[
						'key' => 'product',
						'value' => $product_id,
						'compare' => '='
					]
				]
			];

			$stock = new WP_Query($args);

			if ( $stock->have_posts() ) {
				if( isset($stock->posts[0]) ){
					$single_stock = (array) $stock->posts[0];
					$single_stock['sold'] = get_post_meta($single_stock['ID'], 'sold', true );
					$single_stock['sold_to'] = get_post_meta($single_stock['ID'], 'sold_to', true );
					$single_stock['password'] = get_post_meta($single_stock['ID'], 'password', true );
					$single_stock['order_id'] = get_post_meta($single_stock['ID'], 'order_id', true );
					$single_stock = apply_filters('stock_management_after_query_custom_fields', $single_stock );
					return $single_stock;
				}else{
					wp_reset_postdata();
					return false;
				}

				wp_reset_postdata();
			}else{
				return false;
			}

		}

		/**
		 * Return if stock is available
		 *
		 * @return false|mixed
		 */
		public function get_available_stock_id(){
			$stock = $this->get_available_stock();

			if( isset( $stock['ID'] ) ){
				return $stock['ID'];
			}else{
				return false;
			}
		}

		public function update_stock($id){

			if( !$id  ){
				return false;
			}

			$stock_quantity = get_post_meta($id, '_stock', true);
			$stock_quantity = $stock_quantity+1;

			$updated = wc_update_product_stock($id, $stock_quantity);

			if( $updated ){
				return true;
			}else{
				return false;
			}

		}

		function delete_post( $id ) {

			$product_id = get_post_meta( $id, 'product', true );

			if( !$product_id ){
				return;
			}

			$stock_quantity = get_post_meta($product_id, '_stock', true);

			if( $stock_quantity == 0 ){
				$stock_quantity = $stock_quantity;
			}else{
				$stock_quantity = $stock_quantity - 1;
			}


			$updated = update_post_meta($product_id, '_stock', $stock_quantity);

			delete_post_meta( $id, 'stock_added' );

			if( $updated && $stock_quantity == 0 ){
				update_post_meta( $product_id, '_stock_status', wc_clean( 'outofstock' ) );

				// 3. Updating post term relationship
				wp_set_post_terms( $product_id, 'outofstock', 'product_visibility', false );

				// And finally (optionally if needed)
				wc_delete_product_transients( $product_id ); // Clear/refresh the variation cache
			}

			function untrash_post($id){
				$product_id = get_post_meta( $id, 'product', true );

				if( !$product_id ){
					return;
				}

				$stock_quantity = get_post_meta($product_id, '_stock', true);

				$stock_quantity = $stock_quantity+1;
				// 2. Updating the stock quantity
				update_post_meta( $product_id, '_stock_status', wc_clean( 'instock' ) );

				// 3. Updating post term relationship
				wp_set_post_terms( $product_id, 'instock', 'product_visibility', true );

				// And finally (optionally if needed)
				wc_delete_product_transients( $product_id ); // Clear/refresh the variation cache
				$updated = update_post_meta($product_id, '_stock', $stock_quantity);

				update_post_meta( $id, 'stock_added', true );
			}

		}

	}

	new Stock_Management_Manager();

}