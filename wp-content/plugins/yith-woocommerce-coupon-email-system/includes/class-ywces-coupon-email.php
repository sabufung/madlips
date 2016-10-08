<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YWCES_Coupon_Mail' ) ) {

	/**
	 * Implements Coupon Mail for YWCES plugin
	 *
	 * @class   YWCES_Coupon_Mail
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 * @extends WC_Email
	 *
	 */
	class YWCES_Coupon_Mail extends WC_Email {

		/**
		 * @var int $mail_body content of the email
		 */
		var $mail_body;

		/**
		 * @var int $template the template of the email
		 */
		var $template_type;

		/**
		 * Constructor
		 *
		 * Initialize email type and set templates paths
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->id             = 'yith-coupon-email-system';
			$this->customer_email = true;
			$this->description    = __( 'YITH WooCommerce Coupon Email System offers an automatic way to send a coupon to your users according to specific events.', 'yith-woocommerce-coupon-email-system' );
			$this->title          = __( 'Coupon Email System', 'yith-woocommerce-coupon-email-system' );
			$this->template_html  = '/emails/coupon-email.php';
			$this->template_plain = '/emails/plain/coupon-email.php';
			$this->enabled        = 'yes';

			parent::__construct();

		}

		/**
		 * Trigger email send
		 *
		 * @since   1.0.0
		 *
		 * @param   $mail_body
		 * @param   $mail_subject
		 * @param   $mail_address
		 * @param   $template
		 *
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function trigger( $mail_body, $mail_subject, $mail_address, $template = false ) {

			$this->heading       = $mail_subject;
			$this->subject       = $mail_subject;
			$this->mail_body     = $mail_body;
			$this->template_type = $template;
			$this->recipient     = $mail_address;
			$this->email_type    = get_option( 'ywces_mail_type' );

			if ( ! $this->get_recipient() ) {
				return false;
			}

			return $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), "" );

		}

		/**
		 * Send the email.
		 *
		 * @since   1.0.0
		 *
		 * @param   string $to
		 * @param   string $subject
		 * @param   string $message
		 * @param   string $headers
		 * @param   string $attachments
		 *
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function send( $to, $subject, $message, $headers, $attachments ) {

			add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
			add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
			add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

			$message = apply_filters( 'woocommerce_mail_content', $this->style_inline( $message ) );

			if ( defined( 'YWCES_PREMIUM' ) && get_option( 'ywces_mandrill_enable' ) == 'yes' ) {

				$return = YWCES_Mandrill()->send_email( $to, $subject, $message, $headers, $attachments );

			} else {

				$return = wp_mail( $to, $subject, $message, $headers, $attachments );

			}

			remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
			remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
			remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

			return $return;

		}

		/**
		 * Apply inline styles to dynamic content.
		 *
		 * @since   1.1.4
		 *
		 * @param   string|null $content
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function style_inline( $content ) {

			// make sure we only inline CSS for html emails
			if ( in_array( $this->get_content_type(), array( 'text/html', 'multipart/alternative' ) ) && class_exists( 'DOMDocument' ) ) {

				ob_start();

				if ( array_key_exists( $this->template_type, YITH_WCES()->_email_templates ) ) {

					$path   = YITH_WCES()->_email_templates[ $this->template_type ]['path'];
					$folder = YITH_WCES()->_email_templates[ $this->template_type ]['folder'];

					wc_get_template( $folder . '/email-styles.php', array(), '', $path );
					$css = ob_get_clean();

				} else {

					wc_get_template( 'emails/email-styles.php' );
					wc_get_template( '/emails/email-styles.php', array(), '', YWCES_TEMPLATE_PATH );
					$css = apply_filters( 'woocommerce_email_styles', ob_get_clean() );
				}

				// apply CSS styles inline for picky email clients
				try {
					$emogrifier = new Emogrifier( $content, $css );
					$content    = $emogrifier->emogrify();
				} catch ( Exception $e ) {
					$logger = new WC_Logger();
					$logger->add( 'emogrifier', $e->getMessage() );
				}

			}

			return $content;

		}

		/**
		 * Get HTML content
		 *
		 * @since   1.0.0
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_content_html() {

			ob_start();

			wc_get_template( $this->template_html, array(
				'email_heading' => $this->get_heading(),
				'mail_body'     => $this->mail_body,
				'template'      => $this->template_type,
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this,
			), '', YWCES_TEMPLATE_PATH );

			return ob_get_clean();

		}

		/**
		 * Get Plain content
		 *
		 * @since   1.0.0
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_content_plain() {

			ob_start();

			wc_get_template( $this->template_plain, array(
				'email_heading' => $this->get_heading(),
				'mail_body'     => $this->mail_body,
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			), '', YWCES_TEMPLATE_PATH );

			return ob_get_clean();

		}

		/**
		 * Get email content type.
		 *
		 * @since   1.0.9
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_content_type() {
			switch ( get_option( 'ywces_mail_type' ) ) {
				case 'html' :
					return 'text/html';
				default :
					return 'text/plain';
			}
		}

		/**
		 * Checks if this email is enabled and will be sent.
		 * @since   1.0.9
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function is_enabled() {
			return true;
		}

		/**
		 * Admin Panel Options Processing - Saves the options to the DB
		 *
		 * @since   1.0.0
		 * @return  boolean|null
		 * @author  Alberto Ruggiero
		 */
		public function process_admin_options() {

			$tab_name = ( defined( 'YWCES_PREMIUM' ) ? 'premium-general' : 'general' );

			woocommerce_update_options( $this->form_fields[ $tab_name ] );

		}

		/**
		 * Setup email settings screen.
		 *
		 * @since   1.0.0
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function admin_options() {
			$tab_name = ( defined( 'YWCES_PREMIUM' ) ? 'premium-general' : 'general' );

			?>
			<table class="form-table">
				<?php woocommerce_admin_fields( $this->form_fields[ $tab_name ] ); ?>
			</table>
			<?php

		}

		/**
		 * Initialise Settings Form Fields
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function init_form_fields() {

			$tab_name = ( defined( 'YWCES_PREMIUM' ) ? 'premium-general' : 'general' );

			$this->form_fields = include( YWCES_DIR . '/plugin-options/' . $tab_name . '-options.php' );

		}

	}

}

return new YWCES_Coupon_Mail();