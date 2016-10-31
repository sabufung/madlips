<?php
/**
 * Code Sample of "Facebook Page Albums" plugin
 *
 * @package WordPress
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>"/>
	<meta name="viewport" content="width=device-width"/>
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
</head>
<body <?php body_class(); ?>>
	<div id="main">
		<div id="container">
			<div id="content" role="main">
				<?php
				if (!empty($_GET['id'])) {
					// Photo List
					theme_show_photos($_GET['id']);
				}
				else {
					// Album List
					theme_show_albums();
				}
				?>
			</div><!-- #content -->
		</div><!-- #container -->
	</div>
</body>
</html>
<?php

/**
 * Render the album list
 */
function theme_show_albums() {
	//
	// Get Album List
	//
	$list = facebook_page_albums_get_album_list(wp_parse_args($_GET, array(
		'per_page' => 10
	)));
	if (empty($list)) :
		?>
		<p>
			No album list, but plugin installed correctly.<br/>
			Please check the settings in admin panel.<br/>
			And please check the
			<a href="http://wordpress.org/support/plugin/facebook-page-albums" target="_blank">plugin official site</a>
		</p>
		<?php
		return;
	endif;
	?>
	<ol class="album-list list">
	<?php
	//
	// Loop Album List and Render items
	//
	if (!empty($list)) : foreach ($list as $item) :
		// Link to each album
		$link = add_query_arg('id', $item['id'], site_url('/'));
		?>
		<li class="album">
			<?php
			// It has a thumbnail
			if ($thumb = $item['cover_photo_data']):?>
				<div class="album-thumb thumb">
					<a href="<?php echo $link;?>">
						<img src="<?php echo $thumb['picture'];?>"/>
					</a>
				</div>
			<?php endif; ?>
			<div class="album-info info">
				<h5>
					<a href="<?php echo $link;?>"><?php echo $item['name'];?></a>
				</h5>
				<div class="counts">
					<div class="photos-count">
						Photos: <?php echo $item['count'];?>
					</div>
					<div class="comments-count">
						Comments: <?php echo $item['comments'];?>
					</div>
					<div class="likes-count">
						Likes: <?php echo $item['likes'];?>
					</div>
				</div>
			</div>
		</li>
	<?php
		//alog($item); // Show data structure
	endforeach; endif;?>
	</ol>
	<div class="page-control">
		<?php
		//
		// Page Controller
		//
		$params = facebook_page_albums_get_paging(array(
			'url' => site_url('/')
		));
		if (!empty($params['previous'])) {
			echo '<a class="previous" href="' . $params['previous'] . '">previous</a>';
		}
		if (!empty($params['next'])) {
			echo '<a class="next" href="' . $params['next'] . '">next</a>';
		}
		?>
	</div>
<?php
}


/**
 * Render the photo list
 *
 * @param Integer $id
 * @return null
 */
function theme_show_photos($id) {
	global $paged;

	$per_page = 5;
	if (empty($paged)) $paged = 1;

	// Album Information
	if (!$album = facebook_page_albums_get_album($id)) {
		echo 'failed to get album information';
		return false;
	}


	//
	// Photo List
	//
	if (!$list = facebook_page_albums_get_photo_list($id, array(
		'per_page' => $per_page,
		'paged'    => $paged
	))) {
		echo 'failed to get photo list';
		return false;
	}
	?>
	<div class="photo-list-header">
		<h4>
			<a href="<?php echo $album['link'];?>" target="_blank"><?php echo $album['name'];?></a>
		</h4>
		<div class="counts">
			<div class="photos-count">
				Number of Photos: <?php echo $album['count'];?>
			</div>
			<div class="comments-count">
				Comments: <?php echo $album['comments'];?>
			</div>
			<div class="likes-count">
				Likes: <?php echo $album['likes'];?></div>
		</div>
		<a class="goto-facebook" href="<?php echo $album['link'];?>" target="_blank" title="See on Facebook">See on Facebook</a>
	</div>
	<ol class="photo-list list">
		<?php if (!empty($list)): foreach ($list as $item):?>
			<?php
			//alog($item);
			// It has some images of different sizes.
			$thumbnail = (array) $item['images'][count($item['images']) - 1];?>
			<li class="photo">
				<div class="photo-thumb thumb">
					<a class="photo-link"
					   href="<?php echo $item['source'];?>">
						<img src="<?php echo $thumbnail['source'];?>"/>
					</a>
				</div>
				<div class="photo-info info">
					<div class="counts">
						<div class="comments-count">
							Comments: <?php echo $item['comments'];?>
						</div>
						<div class="likes-count">
							Likes: <?php echo $item['likes'];?>
						</div>
					</div>
				</div>
			</li>
		<?php
			//alog($item); // Show Data Structure.
		endforeach; endif;?>
	</ol>
	<div class="page-control">
		<?php
		//
		// Page Control
		//
		echo paginate_links( array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'total' => ceil($album['count'] / $per_page),
			'current' => $paged,
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
			'mid_size' => 5
		));
		?>
	</div>
<?php
	return true;
}
?>

