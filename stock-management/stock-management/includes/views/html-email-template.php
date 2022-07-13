<?php
/**
 * Represents the view for Email Template
 *
 * @since  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if( isset( $_POST ) && !empty( $_POST ) ){
	if( isset( $_POST['stock_management_nonce'] ) && wp_verify_nonce( $_POST['stock_management_nonce'], 'stock_management' ) ){
		if( isset( $_POST['subject'] ) && !empty( $_POST['subject'] ) ){
			update_option('sm-email-subject', sanitize_text_field( $_POST['subject'] ));
		}

		if( isset( $_POST['message'] ) && !empty( $_POST['message'] ) ){
			update_option('sm-email-message',  $_POST['message']);
		}
		if( isset( $_POST['username'] ) && !empty( $_POST['username'] ) ){
			update_option('sm-username-label', sanitize_text_field( $_POST['username'] ));
		}

		if( isset( $_POST['password'] ) && !empty( $_POST['password'] ) ){
			update_option('sm-password-label',  sanitize_text_field( $_POST['password'] ));
		}
	}

}

$subject = get_option( 'sm-email-subject', false );
$message = get_option( 'sm-email-message', false );
$username = get_option( 'sm-username-label', false );
$password = get_option( 'sm-password-label', false );

?>

<div class="email-template-wrap">
    <div class="email-template-fields-wrap">
        <form name="sm_email_template" class="sm_email_template" action="" method="post" id="sm_email_template">
            <div class="form-row">
                <label for="subject"><?php esc_html_e('Subject:', 'stock-management'); ?></label>
                <input name="subject" id="subject" type="text"
					<?php  if( isset( $subject ) && !empty( $subject ) ){ ?>
                        value="<?php echo $subject; ?>"
					<?php } ?>
                       class="postbox" />
            </div>
            <div class="form-row">
                <label for="message"><?php esc_html_e('Message:', 'stock-management'); ?></label>
                <textarea name="message" id="message"><?php  if( isset( $message ) && !empty( $message ) ){ echo $message;  } ?></textarea>
            </div>
            <div class="form-row">
                <label for="subject"><?php esc_html_e('Username Label:', 'stock-management'); ?></label>
                <input name="username" id="username" type="text"
					<?php  if( isset( $username ) && !empty( $username ) ){ ?>
                        value="<?php echo $username; ?>"
					<?php } ?>
                       class="postbox" />
            </div>
            <div class="form-row">
                <label for="password"><?php esc_html_e('Password Label:', 'stock-management'); ?></label>
                <input name="password" id="password" type="text"
					<?php  if( isset( $password ) && !empty( $password ) ){ ?>
                        value="<?php echo $password; ?>"
					<?php } ?>
                       class="postbox" />
            </div>
            <div class="form-row">
                <input type="submit" class="button-primary" value="<?php esc_html_e('Save', 'stock-management'); ?>" name="sm_email_template">
            </div>
			<?php wp_nonce_field('stock_management', 'stock_management_nonce'); ?>
        </form>
    </div>
</div>	