<?php get_header();?>
    <main role="main" class="site-content-archive">
        <div class="container clearfix">
            <div class="blog-wrapper">
                <div  class="blog-inner blog-single clearfix">
                    <?php
                    // Start the Loop.
                    while ( have_posts() ) : the_post();
                        // Include the page content template.
                        $job   = get_post_meta(get_the_ID(), 'job', true);
                        $face_url = get_post_meta( get_the_ID(), 'face_url', true );
                        $twitter_url = get_post_meta( get_the_ID(), 'twitter_url', true );
                        $google_url = get_post_meta( get_the_ID(), 'google_url', true );
                        $dribbble_url = get_post_meta( get_the_ID(), 'dribbble_url', true );
                        $linkedin_url = get_post_meta( get_the_ID(), 'linkedin_url', true );
                        $phone   = get_post_meta(get_the_ID(), 'phone', true);
                        $email   = get_post_meta(get_the_ID(), 'email', true);
                        $image_id  = get_post_thumbnail_id();
                        $image_url = wp_get_attachment_image( $image_id, 'thumbnail-200x200', false, array( 'alt' => get_the_title(), 'title' => get_the_title()));
                        ?>
                        <article id="post-<?php get_the_ID(); ?>">
                            <div class="page-single-our-team">
                                <div class="our-team-info margin-bottom-70">
                                    <div class="our-team-image">
                                        <?php echo wp_kses_post($image_url); ?>
                                    </div>
                                    <div class="our-team-contact">
                                        <h3 class="our-team-name" ><?php the_title(); ?></h3>
                                        <p class="our-team-job"><?php echo esc_html($job); ?></p>
                                        <?php if (!empty($phone)): ?>
                                            <div class="our-team-phone">
                                                <p><?php esc_html_e('Phone:  ','zorka')?></p><?php echo esc_html($phone); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($email)):?>
                                            <div class="our-team-email">
                                                <p><?php esc_html_e('Email:  ','zorka')?></p><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                                            </div>
                                        <?php endif; ?>
                                        <div class="our-team-social">
                                            <?php if (!empty($face_url) || !empty($twitter_url) || !empty($google_url) || !empty($dribbble_url) || !empty($linkedin_url)): ?>
                                                <ul>
                                                    <?php if (!empty($face_url)): ?>
                                                        <li><a href="<?php echo esc_url($face_url); ?>" class="facebook" title="Facebook"><i class="fa fa-facebook"></i></a></li>
                                                    <?php endif; ?>
                                                    <?php if (!empty($twitter_url)): ?>
                                                        <li><a href="<?php echo esc_url($twitter_url); ?>" class="twitter" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                                                    <?php endif; ?>
                                                    <?php if (!empty($google_url)):?>
                                                        <li><a href="<?php echo esc_url($google_url); ?>" class="google" title="Google"><i class="fa fa-google-plus"></i></a></li>
                                                    <?php endif; ?>
                                                    <?php if (!empty($dribbble_url)):?>
                                                        <li><a href="<?php echo esc_url($dribbble_url); ?>" class="dribbble" title="Dribbble"><i class="fa fa-dribbble"></i></a></li>
                                                    <?php endif; ?>
                                                    <?php if (!empty($linkedin_url)):?>
                                                        <li><a href="<?php echo esc_url($linkedin_url); ?>" class="linkedin" title="Linkedin"><i class="fa fa-linkedin"></i></a></li>
                                                    <?php endif; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="our-team-content">
                                    <?php echo get_the_content(); ?>
                                </div>
                            </div>
                        </article>
                    <?php endwhile;?>
                </div>
            </div>
        </div>
    </main>
<?php get_footer(); ?>