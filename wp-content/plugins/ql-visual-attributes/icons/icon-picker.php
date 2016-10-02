<?php
/**
 * Icon picker markup. Loaded in ql-main.php.
 *
 * Created by QueryLoop
 * @since 1.0.0
 */

$attr_term = $attribute_attr.$term;
?>

<div class="va-preview-icon icon-preview-<?php echo $attr_term; ?> <?php echo $icon? 'icon-preview-on' : '' ; ?>">
	<?php echo $icon? '<a class="remove-icon dashicons dashicons-no-alt" href="#"></a>': ''; ?>
	<i class="<?php echo $icon? $icon : 'genericon'; ?>"></i>
</div>

<?php foreach( $this->get_icon_sets() as $key => $icon ) : ?>
	<a class="open-icons open-<?php echo "$key-$attr_term"; ?> button" href="#"><?php echo $icon['button_label']; ?></a>
<?php endforeach; ?>

<?php
$this->input_field( array(
	'id' => $icon_id,
	'class' => 'va_icon selected-icon-'.$attr_term,
	'post_id' => $post_id,
) ); ?>