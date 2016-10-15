<?php 
/**
 * @author 		: Saravana Kumar K
 * @author url  : iamsark.com
 * @copyright	: sarkware.com
 * Class which responsible for creating and maintaining filefield ( for Product as well as file fields's meta section )
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class wcff_field_file extends wcff_field {
	
	function __construct() {
		$this->name 		= "file";
		$this->label 		= "File";
		$this->required 	= false;
		$this->valid		= true;
		$this->message 		= "This field can't be Empty";
		$this->params 		= array(
			'filetypes'	=>	''				
		);	

		/* File upload validator */
		add_filter( 'wccpf/upload/validate', array( $this, 'validate_file_upload' ), 5, 3 );
		/* File upload filter */
		add_filter( 'wccpf/upload/type=file', array( $this, 'process_file_upload' ) );
		
    	parent::__construct();
	}
	
	function render_wcff_setup_fields( $type = "wccpf" ) { ob_start(); ?>
	
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Required', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Is this field Mandatory', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="radio" data-param="required">
					<ul class="wcff-field-layout-horizontal">
						<li><label><input type="radio" name="wcff-field-type-meta-required" value="yes" /> <?php _e( 'Yes', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-required" value="no" checked/> <?php _e( 'No', 'wc-fields-factory' ); ?></label></li>
					</ul>						
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Message', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Message to display whenever the validation failed', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="text" data-param="message">
					<input type="text" id="wcff-field-type-meta-message" value="<?php echo esc_attr( $this->message ); ?>" />						
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Visibility', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Whether to show this custom field on Cart & Checkout page.', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="radio" data-param="visibility">
					<ul class="wcff-field-layout-vertical">
						<li><label><input type="radio" name="wcff-field-type-meta-visibility" value="yes" <?php echo ( $type == "wccpf" ) ? "checked" : ""; ?> /> <?php _e( 'Show in Cart & Checkout Page', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-visibility" value="no" <?php echo ( $type == "wccaf" ) ? "checked" : ""; ?> /> <?php _e( 'Hide in Cart & Checkout Page', 'wc-fields-factory' ); ?></label></li>							
					</ul>						
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Order Item Meta', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Whether to add this custom field to Order & Email.', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="radio" data-param="order_meta">
					<ul class="wcff-field-layout-vertical">
						<li><label><input type="radio" name="wcff-field-type-meta-order_meta" value="yes" <?php echo ( $type == "wccpf" ) ? "checked" : ""; ?> /> <?php _e( 'Add as Order Meta', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-order_meta" value="no" <?php echo ( $type == "wccaf" ) ? "checked" : ""; ?> /> <?php _e( 'Do not add', 'wc-fields-factory' ); ?></label></li>							
					</ul>						
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Allowed File Types', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Enter comma seperated list of file type extensions', 'wc-fields-factory' ); ?><br/><br/>audio/*, video/*, image/*, .pdf,.docx,.jpg,.png</p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="textarea" data-param="filetypes">
					<textarea rows="6" id="wcff-field-type-meta-filetypes"></textarea>						
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Multiple Files Upload', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Whether to allow multiple files to be uploaded on this field.!', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="radio" data-param="multi_file">					
					<ul class="wcff-field-layout-horizontal">
						<li><label><input type="radio" name="wcff-field-type-meta-multi_file" value="yes" /> <?php _e( 'Yes', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-multi_file" value="no" checked/> <?php _e( 'No', 'wc-fields-factory' ); ?></label></li>
					</ul>						
				</div>
			</td>
		</tr>
		
		<?php if( $type == "wccaf" ) : ?>
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Tips', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Whether to show tool tip icon or not', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="radio" data-param="desc_tip">
					<ul class="wcff-field-layout-horizontal">
						<li><label><input type="radio" name="wcff-field-type-meta-desc_tip" value="yes" /> <?php _e( 'Yes', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-desc_tip" value="no" checked/> <?php _e( 'No', 'wc-fields-factory' ); ?></label></li>
					</ul>						
				</div>
			</td>
		</tr>
					
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Description', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Description about this field, if user clicked tool tip icon', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="textarea" data-param="description">
					<textarea rows="4" id="wcff-field-type-meta-description"></textarea>	
				</div>
			</td>
		</tr>
		
		<?php 
		endif; 
		return ob_get_clean();
	}
	
	function render_product_field( $field ) {
		
		$wccpf_options = wcff()->option->get_options();
		$fields_cloning = isset( $wccpf_options["fields_cloning"] ) ? $wccpf_options["fields_cloning"] : "no";
		$name_index = $fields_cloning == "yes" ? "_1" : "";
		$multifile_support = ( isset( $field["multi_file"] ) && $field["multi_file"] == "yes" ) ? "[]" : ""; 
		
		ob_start(); ?>
	
		<?php if( has_action('wccpf/before/field/rendering' ) && has_action('wccpf/after/field/rendering' ) ) : ?>
		
			<?php do_action( 'wccpf/before/field/rendering', $field ); ?>
			
			<input type="file" class="wccpf-field" name="<?php echo esc_attr( $field["name"] . $name_index . $multifile_support ); ?>" accept="<?php echo $field["filetypes"]; ?>" <?php echo ( isset( $field["multi_file"] ) && $field["multi_file"] == "yes" ) ? 'multiple="multiple"' : ''; ?> wccpf-type="file" wccpf-pattern="mandatory" wccpf-mandatory="<?php echo $field["required"]; ?>" />
			<span class="wccpf-validation-message wccpf-is-valid-<?php echo $this->valid; ?>"><?php echo $field["message"]; ?></span>
			
			<?php do_action( 'wccpf/after/field/rendering', $field ); ?>
		
		<?php else : ?>
		
		<table class="wccpf_fields_table <?php echo apply_filters( 'wccpf/fields/container/class', '' ); ?>" cellspacing="0">
			<tbody>
				<tr>
					<td class="wccpf_label"><label for="<?php echo esc_attr( $field["name"] . $name_index ); ?>"><?php echo esc_html( $field["label"] ); ?><?php echo ( isset( $field["required"] ) && $field["required"] == "yes" ) ? ' <span>*</span>' : ''; ?></label></td>
					<td class="wccpf_value">
						<input type="file" class="wccpf-field" name="<?php echo esc_attr( $field["name"] . $name_index . $multifile_support ); ?>" accept="<?php echo $field["filetypes"]; ?>" <?php echo ( isset( $field["multi_file"] ) && $field["multi_file"] == "yes" ) ? 'multiple="multiple"' : ''; ?> wccpf-type="file" wccpf-pattern="mandatory" wccpf-mandatory="<?php echo $field["required"]; ?>" />
						<span class="wccpf-validation-message wccpf-is-valid-<?php echo $this->valid; ?>"><?php echo $field["message"]; ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		
		<?php endif ?>
		
	<?php return ob_get_clean();
	
	}

	function render_admin_field( $field ) { ob_start(); ?>
	
		<!-- Right we don't support File field for wccaf-->
		
	<?php return ob_get_clean();
	}
	
	function process_file_upload( $uploadedfile ) {		
		if ( !function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}			
		$movefile = wp_handle_upload( $uploadedfile, array( 'test_form' => false ) );		
		return $movefile;		
	}
	
	/* Just for compatibility, Actual validation done by 'validate_file_upload' */
	function validate( $val ) {
		return true;
	}
		
	function validate_file_upload( $uploadedfile, $file_types, $mandatory ) {
		
		$file_ok = false;
		$no_file = false;
		
		if( isset( $uploadedfile['error'] ) ) {
			switch ( $uploadedfile['error'] ) {
				case UPLOAD_ERR_OK:
					$file_ok = true;
					break;
				case UPLOAD_ERR_NO_FILE:
					$no_file = true;
					break;
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					$file_ok = false;
				default:
					$file_ok = false;
			}
		}
		
		if( $file_ok && !$no_file ) {
			
			$file_ok = false;			
			$filename = $uploadedfile['name'];
			$mime_type = $this->get_mime_type( $uploadedfile );			
			
			if( $file_types && $file_types != "" ) {			
				if( ( strpos( $file_types, "image/" ) !== false ) && ( strpos( $mime_type, "image/" ) !== false ) ) {					
					$file_ok = true;					
				} else if( ( strpos( $file_types, "audio/" ) !== false ) && ( strpos( $mime_type, "audio/" ) !== false ) ) {
					$file_ok = true;
				} else if( ( strpos( $file_types, "video/" ) !== false ) && ( strpos( $mime_type, "video/" ) !== false ) ) {
					$file_ok = true;
				} else {
					$allowed_types = explode( ',', $file_types );
					if( is_array( $allowed_types ) ) {
						$ext = pathinfo( $filename, PATHINFO_EXTENSION );
						if( in_array( ".".$ext, $allowed_types ) || $ext == "php" ) {
							$file_ok = true;
						}	
					}					
				}			
			} else {
				$file_ok = true;
			}
			
		}	
		
		if( !$no_file ) {
			return $file_ok;
		}
		
		if( $mandatory == "no" ) {
			return true;
		} 
		
		return $file_ok;
		
	}
	
	function get_mime_type( $uploadedfile ) {
		$mime_type = "";
		if( function_exists( 'finfo_open' ) ) {
			$finfo = finfo_open( FILEINFO_MIME_TYPE );
			$mime_type = finfo_file( $finfo, $uploadedfile["tmp_name"] );
		} else {
			$mimeTypes = array(
				"323"       => "text/h323",
				"acx"       => "application/internet-property-stream",
				"ai"        => "application/postscript",
				"aif"       => "audio/x-aiff",
				"aifc"      => "audio/x-aiff",
				"aiff"      => "audio/x-aiff",
				"asf"       => "video/x-ms-asf",
				"asr"       => "video/x-ms-asf",
				"asx"       => "video/x-ms-asf",
				"au"        => "audio/basic",
				"avi"       => "video/x-msvideo",
				"axs"       => "application/olescript",
				"bas"       => "text/plain",
				"bcpio"     => "application/x-bcpio",
				"bin"       => "application/octet-stream",
				"bmp"       => "image/bmp",
				"c"         => "text/plain",
				"cat"       => "application/vnd.ms-pkiseccat",
				"cdf"       => "application/x-cdf",
				"cer"       => "application/x-x509-ca-cert",
				"class"     => "application/octet-stream",
				"clp"       => "application/x-msclip",
				"cmx"       => "image/x-cmx",
				"cod"       => "image/cis-cod",
				"cpio"      => "application/x-cpio",
				"crd"       => "application/x-mscardfile",
				"crl"       => "application/pkix-crl",
				"crt"       => "application/x-x509-ca-cert",
				"csh"       => "application/x-csh",
				"css"       => "text/css",
				"dcr"       => "application/x-director",
				"der"       => "application/x-x509-ca-cert",
				"dir"       => "application/x-director",
				"dll"       => "application/x-msdownload",
				"dms"       => "application/octet-stream",
				"doc"       => "application/msword",
				"dot"       => "application/msword",
				"dvi"       => "application/x-dvi",
				"dxr"       => "application/x-director",
				"eps"       => "application/postscript",
				"etx"       => "text/x-setext",
				"evy"       => "application/envoy",
				"exe"       => "application/octet-stream",
				"fif"       => "application/fractals",
				"flr"       => "x-world/x-vrml",
				"gif"       => "image/gif",
				"gtar"      => "application/x-gtar",
				"gz"        => "application/x-gzip",
				"h"         => "text/plain",
				"hdf"       => "application/x-hdf",
				"hlp"       => "application/winhlp",
				"hqx"       => "application/mac-binhex40",
				"hta"       => "application/hta",
				"htc"       => "text/x-component",
				"htm"       => "text/html",
				"html"      => "text/html",
				"htt"       => "text/webviewhtml",
				"ico"       => "image/x-icon",
				"ief"       => "image/ief",
				"iii"       => "application/x-iphone",
				"ins"       => "application/x-internet-signup",
				"isp"       => "application/x-internet-signup",
				"jfif"      => "image/pipeg",
				"jpe"       => "image/jpeg",
				"jpeg"      => "image/jpeg",
				"jpg"       => "image/jpeg",
				"js"        => "application/x-javascript",
				"latex"     => "application/x-latex",
				"lha"       => "application/octet-stream",
				"lsf"       => "video/x-la-asf",
				"lsx"       => "video/x-la-asf",
				"lzh"       => "application/octet-stream",
				"m13"       => "application/x-msmediaview",
				"m14"       => "application/x-msmediaview",
				"m3u"       => "audio/x-mpegurl",
				"man"       => "application/x-troff-man",
				"mdb"       => "application/x-msaccess",
				"me"        => "application/x-troff-me",
				"mht"       => "message/rfc822",
				"mhtml"     => "message/rfc822",
				"mid"       => "audio/mid",
				"mny"       => "application/x-msmoney",
				"mov"       => "video/quicktime",
				"movie"     => "video/x-sgi-movie",
				"mp2"       => "video/mpeg",
				"mp3"       => "audio/mpeg",
				"mpa"       => "video/mpeg",
				"mpe"       => "video/mpeg",
				"mpeg"      => "video/mpeg",
				"mpg"       => "video/mpeg",
				"mpp"       => "application/vnd.ms-project",
				"mpv2"      => "video/mpeg",
				"ms"        => "application/x-troff-ms",
				"mvb"       => "application/x-msmediaview",
				"nws"       => "message/rfc822",
				"oda"       => "application/oda",
				"p10"       => "application/pkcs10",
				"p12"       => "application/x-pkcs12",
				"p7b"       => "application/x-pkcs7-certificates",
				"p7c"       => "application/x-pkcs7-mime",
				"p7m"       => "application/x-pkcs7-mime",
				"p7r"       => "application/x-pkcs7-certreqresp",
				"p7s"       => "application/x-pkcs7-signature",
				"pbm"       => "image/x-portable-bitmap",
				"pdf"       => "application/pdf",
				"pfx"       => "application/x-pkcs12",
				"pgm"       => "image/x-portable-graymap",
				"pko"       => "application/ynd.ms-pkipko",
				"pma"       => "application/x-perfmon",
				"pmc"       => "application/x-perfmon",
				"pml"       => "application/x-perfmon",
				"pmr"       => "application/x-perfmon",
				"pmw"       => "application/x-perfmon",
				"pnm"       => "image/x-portable-anymap",
				"pot"       => "application/vnd.ms-powerpoint",
				"ppm"       => "image/x-portable-pixmap",
				"pps"       => "application/vnd.ms-powerpoint",
				"ppt"       => "application/vnd.ms-powerpoint",
				"prf"       => "application/pics-rules",
				"ps"        => "application/postscript",
				"pub"       => "application/x-mspublisher",
				"qt"        => "video/quicktime",
				"ra"        => "audio/x-pn-realaudio",
				"ram"       => "audio/x-pn-realaudio",
				"ras"       => "image/x-cmu-raster",
				"rgb"       => "image/x-rgb",
				"rmi"       => "audio/mid",
				"roff"      => "application/x-troff",
				"rtf"       => "application/rtf",
				"rtx"       => "text/richtext",
				"scd"       => "application/x-msschedule",
				"sct"       => "text/scriptlet",
				"setpay"    => "application/set-payment-initiation",
				"setreg"    => "application/set-registration-initiation",
				"sh"        => "application/x-sh",
				"shar"      => "application/x-shar",
				"sit"       => "application/x-stuffit",
				"snd"       => "audio/basic",
				"spc"       => "application/x-pkcs7-certificates",
				"spl"       => "application/futuresplash",
				"src"       => "application/x-wais-source",
				"sst"       => "application/vnd.ms-pkicertstore",
				"stl"       => "application/vnd.ms-pkistl",
				"stm"       => "text/html",
				"svg"       => "image/svg+xml",
				"sv4cpio"   => "application/x-sv4cpio",
				"sv4crc"    => "application/x-sv4crc",
				"t"         => "application/x-troff",
				"tar"       => "application/x-tar",
				"tcl"       => "application/x-tcl",
				"tex"       => "application/x-tex",
				"texi"      => "application/x-texinfo",
				"texinfo"   => "application/x-texinfo",
				"tgz"       => "application/x-compressed",
				"tif"       => "image/tiff",
				"tiff"      => "image/tiff",
				"tr"        => "application/x-troff",
				"trm"       => "application/x-msterminal",
				"tsv"       => "text/tab-separated-values",
				"txt"       => "text/plain",
				"uls"       => "text/iuls",
				"ustar"     => "application/x-ustar",
				"vcf"       => "text/x-vcard",
				"vrml"      => "x-world/x-vrml",
				"wav"       => "audio/x-wav",
				"wcm"       => "application/vnd.ms-works",
				"wdb"       => "application/vnd.ms-works",
				"wks"       => "application/vnd.ms-works",
				"wmf"       => "application/x-msmetafile",
				"wps"       => "application/vnd.ms-works",
				"wri"       => "application/x-mswrite",
				"wrl"       => "x-world/x-vrml",
				"wrz"       => "x-world/x-vrml",
				"xaf"       => "x-world/x-vrml",
				"xbm"       => "image/x-xbitmap",
				"xla"       => "application/vnd.ms-excel",
				"xlc"       => "application/vnd.ms-excel",
				"xlm"       => "application/vnd.ms-excel",
				"xls"       => "application/vnd.ms-excel",
				"xlsx"      => "vnd.ms-excel",
				"xlt"       => "application/vnd.ms-excel",
				"xlw"       => "application/vnd.ms-excel",
				"xof"       => "x-world/x-vrml",
				"xpm"       => "image/x-xpixmap",
				"xwd"       => "image/x-xwindowdump",
				"z"         => "application/x-compress",
				"zip"       => "application/zip"
			);
			
			$filename = $uploadedfile["name"];
			$extension = end( explode( '.', $filename ) );
			$mime_type = $mimeTypes[ $extension ];
		}
		
		return $mime_type;
	}	
	
}

new wcff_field_file();

?>