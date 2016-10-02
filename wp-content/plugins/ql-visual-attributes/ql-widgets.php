<?php
/**
 * QL Visual_Attributes widgets. Includes Related Docs and Search Docs widgets.
 *
 * @package QueryLoop
 * @subpackage Widgets
 */

/**
 * Related Docs Widget. Displays documents based on a specific category, tag, or descendants of the current document.
 *
 * @since 1.0.0
 */
class QL_Visual_Attributes_Browse extends WP_Widget {

	function __construct() {
		$base = 'ql_visual_attributes_browse';
		$widget_ops = array(
			'classname' => $base . ' ql-visual_attributes',
			'description' => __( 'Displays attributes in your site in a visual fashion.', 'queryloop' )
		);
		$control_ops = array(
			'id_base' => $base
		);
		parent::__construct( $base, __( 'Browse by Visual Attributes', 'queryloop' ), $widget_ops, $control_ops );
	}

	/**
	 * Update values
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['attribute'] = $new_instance['attribute'];
		$instance['term'] = $new_instance['term'];
		$instance['limit'] = $new_instance['limit'];

		return $instance;
	}

	/**
	 * Return default values for widget instance.
	 *
	 * @return array
	 */
	function get_widget_defaults() {
		return array(
			'title' => __( 'Browse by Visual Attributes', 'queryloop' ),
			'attribute' => '',
			'term' => '',
			'limit' => 5,
		);
	}

	/**
	 * Render output
	 *
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {
		global $post;

		$instance = wp_parse_args( (array) $instance, $this->get_widget_defaults() );

		if ( get_query_var( 'post_type' ) != $this->cpt || is_post_type_archive() ) {
			return;
		}

		$out = '';

		if ( '' != $out ) {
			$out = $args['before_widget'] . $out . $args['after_widget'];
		}

		echo $out;

		if ( ! wp_script_is( 'ql_visual_attributes_js' ) ) {
			wp_enqueue_style( 'ql_visual_attributes_css' );
			wp_enqueue_script( 'ql_visual_attributes_js' );
		}
	}

	/**
	 * Render widget control.
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	function form( $instance ) {

		$defaults = $this->get_widget_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'queryloop' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>"
				   name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" type="text"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'attribute' ); ?>"><?php _e( 'Product Attribute:', 'queryloop' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'attribute' ); ?>"
				   name="<?php echo $this->get_field_name( 'attribute' ); ?>" value="<?php echo $instance['attribute']; ?>" class="widefat va-suggest-attribute" type="text"/>
			<small class="howto"><?php _e( 'Choose the product attribute first.', 'queryloop' ); ?></small> 
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'term' ); ?>"><?php _e( 'Attribute Term:', 'queryloop' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'term' ); ?>"
				   name="<?php echo $this->get_field_name( 'term' ); ?>" value="<?php echo $instance['term']; ?>" class="widefat va-suggest-term" type="text"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Number of Documents to Show:', 'queryloop' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'limit' ); ?>"
				   name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $instance['limit']; ?>" class="text" type="text" size="3"/>
			<small class="howto"><?php _e( 'Not used for Child Documents mode.', 'queryloop' ); ?></small>
		</p>

	<?php
	}

} // related documents