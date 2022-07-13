<?php
/**
 * Represents the view for the custom meta box
 *
 * @since  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="stock-management-wrap">
    <div class="stock-management-fields-wrap">
		<?php  do_action('sm_before_meta_box', $post ); ?>
        <div class="form-row">
            <label for="password"><?php esc_html_e('Password:', 'stock-management'); ?></label>
            <input name="password" id="password" type="text"
				<?php if( isset( $values['password'][0] ) && !empty( $values['password'][0] ) ){ ?>
                    value="<?php esc_attr_e( $values['password'][0] ); ?>"
				<?php } ?>
                   class="postbox" />
        </div>
		<?php
		if( $products ){
			?>
            <div class="form-row">
                <label for="product"><?php esc_html_e('Product:', 'stock-management'); ?></label>
                <select name="product" id="product">
                    <option value="0"><?php esc_html_e('--Select one--', 'stock-management'); ?></option>
					<?php foreach ( $products as $product ){ ?>
                        <option <?php selected($product->ID, $values['product'][0] ); ?> value="<?php esc_attr_e($product->ID); ?>"><?php esc_html_e($product->post_title); ?></option>
					<?php } ?>
                </select>
            </div>
		<?php } ?>
        <div class="form-row">
            <label for="sold-to"><?php esc_html_e('Sold To:', 'stock-management'); ?></label>
            <input name="sold_to" id="sold-to" type="email" disabled
				<?php if( isset( $values['sold_to'][0] ) && !empty( $values['sold_to'][0] ) ){ ?>
                   value="<?php esc_attr_e( $values['sold_to'][0] ); ?>"
				   <?php } ?>class="postbox" />
        </div>
        <div class="form-row">
            <label for="order-id"><?php esc_html_e('Order ID:', 'stock-management'); ?></label>
            <input name="order_id" id="order-id" type="number" min="0" disabled
				<?php if( isset( $values['order_id'][0] ) && !empty( $values['order_id'][0] ) ){ ?>
                    value="<?php esc_attr_e( $values['order_id'][0] ); ?>"
				<?php } ?>
                   class="postbox" />
        </div>
        <div class="form-row">
            <label for="sold"><?php esc_html_e('Sold:', 'stock-management'); ?></label>
            <input name="sold" id="sold" type="checkbox" <?php checked('on', $values['sold'][0] ); ?>class="postbox" disabled />
        </div>
		<?php
		wp_nonce_field('stock_management', 'stock_management_nonce');
		do_action('sm_after_meta_box', $post ); ?>
    </div>
	<?php if( isset( $values['order_id'][0] ) && !empty( $values['order_id'][0] ) ){ ?>
        <div class="button-row">
            <a href="<?php echo esc_url(admin_url('post.php?post='.$values['order_id'][0].'&action=edit')) ?>" class="button button-primary"><?php esc_html_e('View Order', 'stock-management'); ?></a>
        </div>
	<?php } ?>
</div>