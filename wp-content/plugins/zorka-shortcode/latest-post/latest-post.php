<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 3/4/15
 * Time: 2:41 PM
 */
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if (!class_exists('Zorka_Latest_Post')):
	class Zorka_Latest_Post {
		function __construct(){
			add_shortcode('zorka_latest_post', array($this, 'zorka_latest_post'));
			add_action( 'after_setup_theme', array($this,'zorka_register_image_size'));
		}
		function zorka_register_image_size(){
			add_image_size( 'thumbnail-230x170', 230, 170, true );
		}

		function zorka_latest_post($atts) {
			$title=$layout_style=$column=$item_amount = $is_slider =  $html = $el_class = $g5plus_animation = $css_animation  =$duration = $delay = $styles_animation = '';
			extract( shortcode_atts( array(
				'layout_style'  => 'style1',
				'title'  => '',
				'column'        => '2',
				'item_amount'   => '9',
				'is_slider' => false ,
				'el_class'      => '',
				'css_animation' => '',
				'duration'      => '',
				'delay'         => ''
			), $atts ) );

			$g5plus_animation .= ' ' . esc_attr($el_class);
			$g5plus_animation .= g5plus_getCSSAnimation( $css_animation );


			$r = new WP_Query( apply_filters( 'shortcode_related_args', array(
				'posts_per_page'      => $item_amount,
				'no_found_rows'       => true,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'orderby' => 'date',
				'order' => 'DESC',
				'post_type' => 'post',
				'tax_query' => array(
					array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => array('post-format-quote','post-format-link','post-format-audio'),
						'operator' => 'NOT IN'
					)
				)
			)));
			$class_col=' col-lg-'.(12/$column).' col-md-'.(12/$column).' col-sm-12  col-xs-12';
			ob_start();
			if ($r->have_posts()) :
				?>
				<?php if($layout_style=='style1'): ?>
				<div  class="zorka-latest-post <?php echo esc_attr($layout_style) ?><?php echo esc_attr($g5plus_animation) ?>" <?php echo g5plus_getStyleAnimation($duration,$delay); ?>>
					<div class="row">
						<?php if  ($is_slider) :
						$class_col=' col-xs-12';?>
						<div data-plugin-options='{"items" : <?php echo esc_attr($column) ?>,"itemsDesktop" : [1199, <?php echo esc_attr($column) ?>],"itemsDesktopSmall" : [980,2],"itemsTablet": [768,1],"pagination":false,"autoPlay": true}' class="owl-carousel">
							<?php endif; ?>
							<?php while ( $r->have_posts() ) : $r->the_post(); ?>
								<div  class="latest-post-item <?php echo esc_attr($class_col); ?>">
									<?php
									$thumbnail = zorka_post_thumbnail('thumbnail-230x170');
									if (!empty($thumbnail)) : ?>
										<div class="zorka-latest-post-image">
											<?php echo wp_kses_post($thumbnail); ?>
										</div>
									<?php endif; ?>
									<div class="zorka-latest-post-content">
										<a class="zorka-latest-post-title" href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
										<span class="zorka-latest-post-date zorka-latest-post-meta"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"> <?php echo  get_the_date(get_option('date_format'));?> </a></span>
										<span class="zorka-latest-post-author zorka-latest-post-meta"><?php printf('<a href="%1$s">%2$s</a>',esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),esc_html( get_the_author() )); ?></span>
										<?php if ( comments_open() || get_comments_number() ) : ?>
											<span class="zorka-latest-post-comment-count zorka-latest-post-meta">
                                                        <?php comments_popup_link( esc_html__('0 Comment','zorka'),__('1 Comment','zorka'),__('% Comments','zorka')); ?>
                                                    </span>
										<?php endif; ?>
										<?php the_excerpt(); ?>
										<a class="zorka-latest-post-read-more" href="<?php the_permalink(); ?>" rel="bookmark" title="<?php esc_html_e("Read more","zorka"); ?>"><?php esc_html_e("Read more","zorka"); ?></a>
									</div>
								</div>
							<?php endwhile; ?>
							<?php if  ($is_slider) : ?>
						</div>
					<?php endif; ?>
					</div>
				</div>
			<?php else:?>
				<div  class="zorka-latest-post <?php echo esc_attr($layout_style) ?><?php echo esc_attr($g5plus_animation) ?>" <?php echo g5plus_getStyleAnimation($duration,$delay); ?>>
					<h2><?php echo esc_attr($title) ?></h2>
					<?php if  ($is_slider) :?>
					<div data-plugin-options='{"singleItem" : true,"pagination":false,"autoPlay": true}' class="owl-carousel">
						<?php endif; ?>
						<?php while ( $r->have_posts() ) : $r->the_post(); ?>
							<div  class="latest-post-item">
								<?php
								$thumbnail = zorka_post_thumbnail('thumbnail');
								if (!empty($thumbnail)) : ?>
									<div class="zorka-latest-post-image">
										<?php echo wp_kses_post($thumbnail); ?>
									</div>
								<?php endif; ?>
								<div class="zorka-latest-post-content">
									<a class="zorka-latest-post-title" href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
									<a class="zorka-latest-post-read-more" href="<?php the_permalink(); ?>" rel="bookmark" title="<?php esc_html_e("Read more","zorka"); ?>"><?php esc_html_e("Read more","zorka"); ?></a>
								</div>
								<div class="clearfix"></div>
							</div>
						<?php endwhile; ?>
						<?php if  ($is_slider) : ?>
					</div>
				<?php endif; ?>
				</div>
			<?php endif;?>
			<?php
			endif;
			wp_reset_postdata();
			zorka_archive_loop_reset();
			$content = ob_get_clean();
			return $content;
		}
	}
	new Zorka_Latest_Post;
endif;