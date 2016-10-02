<?php

if(!class_exists('Closify_Image_Processing'))
{
  
class Closify_Press_Image_Processing
{

  protected static $instance = null;
  
  function __construct() {

  }

  /**
  * Return an instance of this class.
  *
  *
  * @return    object    A single instance of this class.
  */
  public static function get_instance() {

      // If the single instance hasn't been set, set it now.
      if ( null == self::$instance ) {
          self::$instance = new self;
      }

      return self::$instance;
  }

  //This function corps image to create exact square images, no matter what its original size!
  public function cropImage($CurWidth,$CurHeight,$iSize,$DestFolder,$SrcImage,$Quality,$ImageType)
  {	 
      //Check Image size is not 0
      if($CurWidth <= 0 || $CurHeight <= 0) 
      {
          return false;
      }

      //abeautifulsite.net has excellent article about "Cropping an Image to Make Square bit.ly/1gTwXW9
      if($CurWidth>$CurHeight)
      {
          $y_offset = 0;
          $x_offset = ($CurWidth - $CurHeight) / 2;
          $square_size 	= $CurWidth - ($x_offset * 2);
      }else{
          $x_offset = 0;
          $y_offset = ($CurHeight - $CurWidth) / 2;
          $square_size = $CurHeight - ($y_offset * 2);
      }

      $NewCanves 	= imagecreatetruecolor($iSize, $iSize);	
      if(imagecopyresampled($NewCanves, $SrcImage,0, 0, $x_offset, $y_offset, $iSize, $iSize, $square_size, $square_size))
      {
          switch(strtolower($ImageType))
          {
              case 'image/png':
                  imagepng($NewCanves,$DestFolder);
                  break;
              case 'image/gif':
                  imagegif($NewCanves,$DestFolder);
                  break;			
              case 'image/jpeg':
              case 'image/pjpeg':
                  imagejpeg($NewCanves,$DestFolder,$Quality);
                  break;
              default:
                  return false;
          }
      //Destroy image, frees memory	
      if(is_resource($NewCanves)) {imagedestroy($NewCanves);} 
      return true;

      }
  }

  // This function will proportionally resize image 
  public function resizeImage($CurWidth,$CurHeight,$DestFolder,$SrcImage,$Quality,$ImageType, $src_width, $src_height)
  {
      //Check Image size is not 0
      if($CurWidth <= 0 || $CurHeight <= 0) 
      {
          return false;
      }

      $NewWidth  	= ceil($CurWidth);
      $NewHeight 	= ceil($CurHeight);

      //Construct a proportional size of new image
      $NewCanves 	= imagecreatetruecolor($NewWidth, $NewHeight);

      if(strtolower($ImageType) == 'image/png')
      {
        imagealphablending( $NewCanves, false );
        imagesavealpha( $NewCanves, true );
      }else if(strtolower($ImageType) == 'image/gif'){
        $background = imagecolorallocate($NewCanves, 0, 0, 0);
        imagecolortransparent($NewCanves, $background);
      }

      // Resize Image
      if(imagecopyresized($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $src_width, $src_height))
      {
          switch(strtolower($ImageType))
          {
              case 'image/png':
                  imagepng($NewCanves,$DestFolder);
                  break;
              case 'image/gif':
                  imagegif($NewCanves,$DestFolder);
                  break;			
              case 'image/jpeg':
              case 'image/pjpeg':
                  imagejpeg($NewCanves,$DestFolder,$Quality);
                  break;
              default:
                  return false;
          }
        //Destroy image, frees memory	
        if(is_resource($NewCanves)) {
          imagedestroy($NewCanves);  
        } 
        return true;
      }

  }

  public function itech_get_attachment_id_from_url( $image_url ) {

      global $wpdb, $table_prefix;

      $res = $wpdb->get_results('select post_id from ' . $table_prefix . 'postmeta where meta_value like "%' . basename($image_url). '%"');

      if(count($res) == 0)
      {
          $res = $wpdb->get_results('select ID as post_id from ' . $table_prefix . 'posts where guid="' . $image_url . '"');
      }

      if($res == null)
      {
        return false;
      }

      return $res[0]->post_id;

  }

}

}

?>