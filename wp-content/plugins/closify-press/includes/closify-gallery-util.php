<?php

// $container = $container.'<div class="Closify_Wrapper" data-caption="'.$gallery_img_array['img_title'][$i].'"><a href="'.$gallery_img_array['full'][$i][0].'" data-lightbox="closify-gallery-'.$closify_id.'"><img src="'.$gallery_img_array['thumb'][$i][0].'" /></a></div>';
function Closify_Translate_Images_to_Photoswipe_HTML($gallery, $effect, $random_id, $itemPerPage = 10, $disable_caption = "off", $imgWidth = '', $titleEnabled = 'off', $isMultiPage)
{
    // Update caption flag
    $caption = ($disable_caption!="on")?true:false;
    
    $itemWidth = '';
    
    // Update title flag
    $title = ($titleEnabled!="on")?true:false;
    
    // Update item's width
    if($imgWidth != '') $itemWidth = ';width:'.$imgWidth.'px;';
    
    // Update option range
    $options = '';
    for($i=10;$i<35;$i=$i+5)
    {
        if($i==$itemPerPage){
            $options = $options . '<option selected>'.$i.'</option>';
        }else{
            $options = $options . '<option>'.$i.'</option>';
        }
    }
    
    $numPages = '';
    if($isMultiPage){
        $itemWidth = $itemWidth.'opacity:0;';
        $numPages = '<form class="closify-jpages-form">
            <label>items per page: </label>
            <select id="closify-select-'.$random_id.'">
                '.$options.'
            </select>
        </form>';
    }
    
    $holder = '<div class="closify-holder closify-holder-'.$random_id.'"></div>';
    $htmlStart = '<div id="itemContainer-'.$random_id.'" class="closify-gallery" itemscope itemtype="http://schema.org/ImageGallery">';
    $htmlEnd = '</div>';
    $htmlBody = '';
    $titleText = "";
    $captionText = "";
    $titleCopyright = "";
    
    for($i=0; $i<count($gallery['full']);$i++)
    {
        if($caption) 
            $captionText = ' : '.$gallery['img_desc'][$i];
        if($title) 
            $titleText = $gallery['img_title'][$i];
        
        $htmlBody = $htmlBody.'<figure class="closify-figure-gallery-item" style="'.$itemWidth.'" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
            <a href="'.$gallery['full'][$i][0].'" itemprop="contentUrl" data-size="'.$gallery['full'][$i][1].'x'.$gallery['full'][$i][2].'">
                <img class="closify-animated '.$effect.'" src="'.$gallery['thumb'][$i][0].'" itemprop="thumbnail" alt="'.$gallery['img_title'][$i].'" />
            </a>
            <figcaption itemprop="caption description">'.$titleText.$captionText.'</figcaption>
        </figure>';
    }

    
    return $numPages.$holder.$htmlStart.$htmlBody.$htmlEnd;
}


// Generate footer HTML template for photoswipe 
function Closify_Photoswipe_Footer_Template()
{
    $template = '<div id="closify-photo-swipe-template" class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="pswp__bg"></div>
                    
                    <!-- Slides wrapper with overflow:hidden. -->
                    
                    <div class="pswp__scroll-wrap">
                        <div class="pswp__container">
                            <div class="pswp__item"></div>
                            <div class="pswp__item"></div>
                            <div class="pswp__item"></div>
                        </div>
                        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
                        <div class="pswp__ui pswp__ui--hidden">

                            <div class="pswp__top-bar">

                                <!--  Controls are self-explanatory. Order can be changed. -->
                                <div class="pswp__counter"></div>
                                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                                <button class="pswp__button pswp__button--share" title="Share"></button>
                                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                                
                                <!-- element will get class pswp__preloader--active when preloader is running -->
                                <div class="pswp__preloader">
                                    <div class="pswp__preloader__icn">
                                    <div class="pswp__preloader__cut">
                                        <div class="pswp__preloader__donut"></div>
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                                <div class="pswp__share-tooltip"></div> 
                            </div>

                            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
                            </button>

                            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
                            </button>

                            <div class="pswp__caption">
                                <div class="pswp__caption__center"></div>
                            </div>

                        </div>

                    </div>

                </div>';
                
                return $template; 
}