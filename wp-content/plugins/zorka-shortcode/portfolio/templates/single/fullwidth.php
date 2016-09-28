<div class="portfolio-full fullwidth" id="content">
    <div class="post-slideshow">
        <?php if(count($meta_values) > 0){
            foreach($meta_values as $image){
                $urls = wp_get_attachment_image_src($image,'thumbnail-1920x640');
                $img = '';
                if(count($urls)>0)
                    $img = $urls[0];
                ?>
                <div class="item"><img alt="zorka portfolio" src="<?php echo esc_url($img) ?>" /></div>
            <?php }
        }else { if(count($imgThumbs)>0) {?>
            <div class="item"><img alt="zorka portfolio" src="<?php echo esc_url($imgThumbs[0])?>" /></div>
        <?php }
        }
        ?>
    </div>
    <div class="container portfolio-content clearfix">
        <div class="row">
            <div class="col-md-8 portfolio-description">
                <div class="title"><?php the_title() ?></div>
                <?php the_content(); ?>
            </div>
            <div class="col-md-4 portfolio-info">
                <div class="title"><?php esc_html_e('Project Detail','zorka') ?></div>
                <div class="portfolio-info-box">
                    <h6><?php esc_html_e('Date:','zorka') ?></h6>
                    <div class="portfolio-term"><?php echo date(get_option('date_format'),strtotime($post->post_date)) ?></div>
                </div>
                <div class="portfolio-info-box">
                    <h6><?php esc_html_e('Category:','zorka') ?></h6>
                    <div class="portfolio-term"><?php echo wp_kses_post($cat) ?></div>
                </div>
                <div class="portfolio-info-box">
                    <?php if($client!=''){ ?>
                        <h6><?php esc_html_e('Client:','zorka') ?></h6>
                        <div class="portfolio-term"><?php echo esc_html($client); ?></div>
                    <?php } ?>
                </div>
                <div class="portfolio-info-box">

                    <ul class="social-link">
                        <li><h6><?php esc_html_e('Share:','zorka') ?></h6></li>
                        <li><a title="" data-toggle="tooltip" href="http://www.facebook.com/sharer.php?u=<?php echo esc_url(get_permalink($post_id))?>" target="_blank" data-original-title="Facebook"><i class="fa fa-facebook"></i></a></li>
                        <li><a title="" data-toggle="tooltip" href="http://twitter.com/share?url=<?php echo esc_url(get_permalink($post_id)) ?>" target="_blank" data-original-title="Twitter"><i class="fa fa-twitter "></i></a></li>
                        <li><a title="" data-toggle="tooltip" href="http://linkin.com" target="_blank" data-original-title="Linked In"><i class="fa fa-linkedin"></i></a></li>
                        <li><a title="" data-toggle="tooltip" href="http://dribbble.com" target="_blank" data-original-title="Dribbble"><i class="fa fa-dribbble"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <?php zorka_post_nav() ?>
        </div>
        <div class="row">
            <?php  include_once(plugin_dir_path( __FILE__ ).'/related.php'); ?>
        </div>
    </div>

</div>

