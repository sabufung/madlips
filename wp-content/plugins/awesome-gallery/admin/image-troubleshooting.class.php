<?php
class ASG_ImageTroubleshooting{
	function __construct(){
		add_action('admin_menu', array($this, '_admin_menu'));
	}

	function _admin_menu(){
		add_submenu_page(null, __('Image display troubleshooting', 'asg'), null, 'install_plugins',
				'asg-image-troubleshooting-easy', array($this, '_render'));
		add_submenu_page(null, __('Image display troubleshooting', 'asg'), null, 'install_plugins',
				'asg-image-troubleshooting-hardcore', array($this, '_render'));
	}

	function _render(){
?>
		<style type="text/css">
			section{
				display: table;
				padding-top: 30px;
				padding-bottom: 20px;
				border-bottom: 1px solid #ccc;
			}
			section div{
				width: 50%;
				display: table-cell;
				padding-right: 40px;
				vertical-align: top;
			}
			section div.image-wrapper{
				padding-right: 0;
			}
			section div img{
				width: 100%;
			}
			ul li{
				list-style-type: square;
				margin-left: 2em;
			}
		</style>
		<div class="wrap asg-screen" id="asg-support-wrap" style="margin: 40px 40px 20px 20px">
			<h1 style="font-size: 42px; font-family: 'Open Sans'; font-weight: normal; margin-bottom: 36px;">"Images not showing" troubleshooting.</h1>
			<p style="font-size: 19px; color: #777">
				It's not that complex, for real.
			</p>
			<?php if ($_REQUEST['page'] == 'asg-image-troubleshooting-easy'): ?>
				<?php $this->render_easy() ?>
			<?php else: ?>
				<?php $this->render_hardcore() ?>
			<?php endif ?>
		</div>
<?php
	}

	function render_easy(){
?>
		<h2 class="nav-tab-wrapper">
				<a href="admin.php?page=asg-image-troubleshooting-easy" class="nav-tab nav-tab-active">Easy way using JetPack</a>
				<a href="admin.php?page=asg-image-troubleshooting-hardcore" class="nav-tab">Hardcore way</a>
		</h2>
		<section>
			<div>
				<h3>The idea</h3>
				<p>
					Basically this method will pass all the image resizing and cropping to Photon module of the
					popular <a href="https://wordpress.org/plugins/jetpack/">JetPack plugin</a> by WordPress.com.
				</p>
				<h3>Pro's</h3>
				<p>
					<ul>
						<li>Pretty much bulletproof.</li>
						<li>This method allows you to configure things really quickly.</li>
						<li>It saves  save some of your server processor power time by passing image resizing and cropping to JetPack.</li>
						<li>Images will be loaded slightly faster.</li>
					</ul>
				</p>
				<h3>Con's</h3>
				<p>
					<ul>
						<li>It adds dependency on JetPack.</li>
						<li>JetPack does not play nice with some complex themes.</li>
					</ul>
				</p>
			</div>
			<div class="image-wrapper"><img src="<?php echo ASG_URL ?>/assets/admin/images/image-troubleshooting/image-1-jetpack.png"></div>
		</section>
		<section>
			<div>
				<h3>Step 1. Install JetPack.</h3>
				<p>
					<a href="plugin-install.php?tab=search&s=jetpack">Search for Jetpack</a>
					at the WordPress plugin manager or <a href="https://wordpress.org/plugins/jetpack/" target="_blank">download it</a>
					from the WordPress plugin directory.</a> Then, install JetPack by clicking "Install Now" or upload the file
					downloaded from WordPress directory <a href="plugin-install.php?tab=upload">at the plugin uploads page</a>.
				</p>
				<p>Do not forget to activate JetPack after installing by clicking "Activate plugin" link.</p>
			</div>
			<div class="image-wrapper">
				<img src="<?php echo ASG_URL ?>/assets/admin/images/image-troubleshooting/image-2-install.png">
			</div>
		</section>
		<section>
			<div>
				<h3>Step 2. Connect JetPack to your WordPress.com account.</h3>
				<p>
					After installing JetPack, you need to connect it to your WordPress.com account. It's time to
					<a href="http://wordpress.com/signup">Sign up</a> if you don't have one yet. It's free. To connect
					your JetPack to WordPress.com account, you just need to click "Connect to WordPress.com" at the
					green banner at the top of the admin area and follow instructions.
				</p>
			</div>
			<div class="image-wrapper">
				<img src="<?php echo ASG_URL ?>/assets/admin/images/image-troubleshooting/image-3-connect.png">
			</div>
		</section>
		<section>
			<div>
				<h3>Step 3. Activate Photon.</h3>
				<p>
					Click "JetPack" at the left navigation at WordPress admin area, and find a "Photon" module among others.
					Then, you just need to click "Activate" and voila! Images should load correctly from this moment.
				</p>
			</div>
			<div class="image-wrapper">
				<img src="<?php echo ASG_URL ?>/assets/admin/images/image-troubleshooting/image-4-activate.png">
			</div>
		</section>
		<section>
			<div>
				<h3>Step 4. Switch to Photon processing.</h3>
				<p>Open <a href="options-general.php?page=asg-options">Awesome Gallery settings</a> and change image processing engine to Photon.</a></p>
			</div>
			<div class="image-wrapper">
				<img src="<?php echo ASG_URL ?>/assets/admin/images/image-troubleshooting/image-8-processing-engine.png">
			</div>
		</section>
<?php
	}

	function render_hardcore(){
?>
		<h2 class="nav-tab-wrapper">
			<a href="admin.php?page=asg-image-troubleshooting-easy" class="nav-tab">Easy way using JetPack</a>
			<a href="admin.php?page=asg-image-troubleshooting-hardcore" class="nav-tab nav-tab-active">Hardcore way</a>
		</h2>

		<section>
			<div>
				<h3>Alright, you want to control it.</h3>
				<p>
					If you are reading this page, then, probably, Awesome Gallery images do not load with a default
					timthumb configuration. To resolve the issue, you'll need some technical experience, so if you
					are not a PHP coder, it is best to stick to <a href="admin.php?page=asg-image-troubleshooting-easy">JetPack way</a>.
				</p>
				<h3>Short intro.</h3>
				<p>First things first. I highly recommend to read <a href="http://www.binarymoon.co.uk/2010/08/timthumb/" target="_blank">
						Timthumb basics</a> to get an understanding how it all works. Basically, it just takes the image
					you reference by URL, downloads it (or reads from the disk), resizes / crops to dimensions you need,
					and stores in a cache directory.
				</p>
				<p>In other words, TimThumb needs the next things to work properly:</p>
				<ul>
					<li>
						<strong>PHP GD library installed.</strong> Most of PHP installation have it nowadays, but who knows.
						You can check if GD is installed on your web server at <a href="edit.php?post_type=awesome-gallery&page=support">Awesome Gallery support page</a>.
					</li>
					<li>
						<strong>A writable cache folder</strong> located at <code>wp-content/awesome-gallery-cache</code> or
						<code>wp-content/uploads/awesome-gallery-cache</code>.
					</li>
					<li>
						<strong>It needs to find the file referenced.</strong>
						If you're using Awesome Gallery with Manual, NEXTGEN, or Posts image source - then it needs
						to know the directory at which the images are stored. It is <code>wp-content/uploads</code> by
						default, but can be changed on some WordPress installations.
						If you're using with image source like Flickr, Instagram, Facebook or another one that fetches images
						from the external server - CURL library should be installed on your server.
					</li>
				</ul>
				<ul>
					<li>
						Timthumb can't find the file hosted at your server because the URL structure does not match
						the file physical location.
					</li>
					<li>Timthumb can't create a cache directory.</li>
				</ul>
			</div>
			<div class="image-wrapper">
				<img src="<?php echo ASG_URL ?>/assets/admin/images/image-troubleshooting/image-5-coding.jpg">
			</div>
		</section>
		<section>
			<div>
				<h3>Quick diagnostics.</h3>
				<p>
					Let's try to feed some image to timthumb and see what happens. Ideally, you should
					see the same image you see next to this paragraph.
					<?php $src = wp_get_attachment_image_src(asg_get_test_image(), 'original') ?>
				</p>
				<a href="<?php echo asg_get_timthumb_url($src[0], array('width' => 800, 'height' => 600)) ?>" target="_blank" class="button button-hero">Open it</a>
				<p>
					If not all is good - you'll see some kind of an error message. We'll discuss most frequent ones below.
				</p>
			</div>
			<div class="image-wrapper">
				<img src="<?php echo $src[0] ?>">
			</div>
		</section>
		<section>
			<div>
				<h3>Image not found.</h3>
				<p>
					In most cases, that is because your WordPress installation uses a non-standard directory naming.
					You'll need to tell Timthumb where to look for images by adding <code>wp-content/awesome-gallery-timthumb-config.php</code>
					file and placing some code there:
				</p>
				<ul>
					<li>You can define <code>LOCAL_FILE_BASE_DIRECTORY</code> constant to tell TimThumb what directory
					matches the site root.</li>
					<li>You can change <code>$_GET['src']</code> value to forge the URL transmitted to match the directory
					paths in your filesystem.</li>
				</ul>
			</div>
			<div class="image-wrapper">
				<img src="<?php echo ASG_URL ?>/assets/admin/images/image-troubleshooting/image-6-not-available.jpg">
			</div>
		</section>
		<section>
			<div>
				<h3>Can't create a cache directory, or write to cache files.</h3>
				<p>
					In this case, it is best to create <code>wp-content/awesome-gallery-cache</code> directory
					with your FTP client or using your hosting control panel and set it 775 / RWXRWXR Unix access
					rights.
				</p>
			</div>
			<div class="image-wrapper">
				<img src="<?php echo ASG_URL ?>/assets/admin/images/image-troubleshooting/image-7-restricted-access.jpg">
			</div>
		</section>
<?php
	}
}
new ASG_ImageTroubleshooting;