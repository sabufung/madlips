<?php
  
  function closify_add_menu_items(){
      add_submenu_page('edit.php?post_type=closify', 'Manage Uploads', 'Closify Manage Uploads', 'edit_posts','closify_manage_list', 'closify_render_menu');
  }

  function closify_render_menu(){
        
    /** WordPress Administration Bootstrap */
    require_once (ABSPATH . '/wp-admin/admin.php' );

    $title = __( 'Manage Closify Uploads', 'closify-uploader' );
    set_current_screen( 'upload' );
    if ( ! current_user_can( 'upload_files' ) )
        wp_die( __( 'You do not have permission to upload files.', 'closify-uploader' ) );
    
    ?>
    <div class="wrap">
    <?php screen_icon(); ?>
    <h2><?php echo esc_html( $title ); ?> <?php
    if ( isset( $_REQUEST['s'] ) && $_REQUEST['s'] )
        printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;', 'closify-uploader' ) . '</span>', get_search_query() ); ?>
    </h2>

    <?php
    $message = '';
    $closify_media_list = new Closify_Media_List_Table();
    $pagenum = $closify_media_list->get_pagenum();
    $doaction = $closify_media_list->current_action();
    $message = closify_process_bulk_action($closify_media_list);
    $closify_media_list->prepare_items();
    
    if ( isset( $_GET['posted'] ) && (int) $_GET['posted'] ) {
        $message = __( 'Media attachment updated.', 'closify-uploader' );
        $_SERVER['REQUEST_URI'] = esc_url(remove_query_arg( array( 'posted' ), $_SERVER['REQUEST_URI'] ));
    }

    if ( isset( $_GET['attached'] ) && (int) $_GET['attached'] ) {
        $attached = (int) $_GET['attached'];
        $message = sprintf( _n( 'Reattached %d attachment.', 'Reattached %d attachments.', $attached ), $attached );
        $_SERVER['REQUEST_URI'] = esc_url(remove_query_arg( array( 'attached' ), $_SERVER['REQUEST_URI'] ));
    }

    if ( isset( $_GET['deleted'] ) && (int) $_GET['deleted'] ) {
        $message = sprintf( _n( 'Media attachment permanently deleted.', '%d media attachments permanently deleted.', $_GET['deleted'] ), number_format_i18n( $_GET['deleted'] ) );
        $_SERVER['REQUEST_URI'] = esc_url(remove_query_arg( array( 'deleted' ), $_SERVER['REQUEST_URI'] ));
    }

    if ( isset( $_GET['trashed'] ) && (int) $_GET['trashed'] ) {
        $message = sprintf( _n( 'Media attachment moved to the trash.', '%d media attachments moved to the trash.', $_GET['trashed'] ), number_format_i18n( $_GET['trashed'] ) );
        $message .= ' <a href="' . esc_url( wp_nonce_url( 'edit.php?post_type=closify?doaction=undo&action=untrash&ids='.( isset( $_GET['ids'] ) ? $_GET['ids'] : '' ), "bulk-media" ) ) . '">' . __( 'Undo', 'closify-uploader' ) . '</a>';
        $_SERVER['REQUEST_URI'] = esc_url(remove_query_arg( array( 'trashed' ), $_SERVER['REQUEST_URI'] ));
    }

    if ( isset( $_GET['untrashed'] ) && (int) $_GET['untrashed'] ) {
        $message = sprintf( _n( 'Media attachment restored from the trash.', '%d media attachments restored from the trash.', $_GET['untrashed'] ), number_format_i18n( $_GET['untrashed'] ) );
        $_SERVER['REQUEST_URI'] = esc_url(remove_query_arg( array( 'untrashed' ), $_SERVER['REQUEST_URI'] ));
    }

    if ( isset( $_GET['approved'] ) ) {
        $message = 'The photo was approved';
    }

    $messages[1] = __( 'Media attachment updated.', 'closify-uploader' );
    $messages[2] = __( 'Media permanently deleted.', 'closify-uploader' );
    $messages[3] = __( 'Error saving media attachment.', 'closify-uploader' );
    $messages[4] = __( 'Media moved to the trash.', 'closify-uploader' ) . ' <a href="' . esc_url( wp_nonce_url( 'edit.php?post_type=closify?doaction=undo&action=untrash&ids='.( isset( $_GET['ids'] ) ? $_GET['ids'] : '' ), "bulk-media" ) ) . '">' . __( 'Undo', 'closify-uploader' ) . '</a>';
    $messages[5] = __( 'Media restored from the trash.', 'closify-uploader' );

    if ( isset( $_GET['message'] ) && (int) $_GET['message'] ) {
        $message = $messages[$_GET['message']];
        $_SERVER['REQUEST_URI'] = esc_url(remove_query_arg( array( 'message' ), $_SERVER['REQUEST_URI'] ));
    }

    if ( !empty( $message ) ) { ?>
    <div id="message" class="updated"><p><?php echo $message; ?></p></div>
    <?php } ?>

    <form id="posts-filter" action="" method="get">
      <input type="hidden" name="page" value="closify_manage_list" />
      <input type="hidden" name="post_type" value="closify" />
    <?php $closify_media_list->search_box( __( 'Search Media', 'closify-uploader' ), 'media' ); ?>

    <?php $closify_media_list->display(); ?>

    <div id="ajax-response"></div>
    <?php find_posts_div(); ?>
    <br class="clear" />

    </form>
    </div>
    <?php
    
  }
  
  	/**
	 * Since WP 3.5-beta-1 WP Media interface shows private attachments as well
	 * We don't want that, so we force WHERE statement to post_status = 'inherit'
	 *
	 * @since 0.3
	 *
	 * @param string $where WHERE statement
	 * @return string WHERE statement
	 */
	function closify_filter_posts_where( $where ) {
		if ( !is_admin() || !function_exists( 'get_current_screen' ) )
			return $where;

		$screen = get_current_screen();
		if ( ! defined( 'DOING_AJAX' ) && $screen && isset( $screen->base ) && $screen->base == 'upload' && ( !isset( $_GET['page'] ) || $_GET['page'] != 'closify_manage_list' ) ) {
			$where = str_replace( "post_status = '".CLOSIFY_POST_STATUS."'", "post_status = 'inherit'", $where );
		}
		return $where;
	}
    
    /**
	 * Approve a media file
	 *
	 * TODO: refactor in 0.6
	 *
	 * @return [type] [description]
	 */
	function closify_approve_media() {
		// Check permissions, attachment ID, and nonce

		if ( false === closify_check_perms_and_nonce() || 0 === (int) $_GET['id'] ) {
			wp_safe_redirect( get_admin_url( null, 'edit.php?post_type=closify&page=closify_manage_list&error=id_or_perm' ) );
		}

		$post = get_post( $_GET['id'] );

		if ( is_object( $post ) && $post->post_status == CLOSIFY_POST_STATUS ) {
			$post->post_status = 'inherit';
			wp_update_post( $post );

			do_action( 'closify_media_approved', $post );
			wp_safe_redirect( get_admin_url( null, 'edit.php?post_type=closify&page=closify_manage_list&approved=1' ) );
		}
        
        die();
	}
    
    function closify_check_perms_and_nonce() {
		return current_user_can( 'edit_posts' ) && wp_verify_nonce( $_REQUEST['closify_nonce'], CLOSIFY_NONCE );
	}

	/**
	 * Delete post and redirect to referrer
	 *
	 * @return [type] [description]
	 */
	function closify_delete_post() {
		if ( closify_check_perms_and_nonce() && 0 !== (int) $_GET['id'] ) {
			if ( wp_delete_post( (int) $_GET['id'], true ) )
				$args['deleted'] = 1;
		}

		wp_safe_redirect( esc_url_raw(add_query_arg( $args, wp_get_referer() )) );
		exit;
	}
    
    function closify_process_bulk_action($wp_media_list_table) {

        // security check!
        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if ( ! wp_verify_nonce( $nonce, $action ) )
                wp_die( 'Nope! Security check failed!' );

        }

        $action = $wp_media_list_table->current_action();

        switch ( $action ) {

          case 'delete':
            foreach ( (array) $_REQUEST['media'] as $post_id_delete ) {
                if ( !current_user_can( 'edit_post', $post_id_delete ) )
                    wp_die( __( 'You are not allowed to approve this file upload.' ) );

                $post = get_post( $post_id_delete );

                if ( is_object( $post ) ) {
                    wp_delete_post( $post_id_delete, true );

                    do_action( 'closify_media_deleted', $post );
                }else{
                  return 'No file object found';
                }
            }
            return 'Selected files has been deleted';
            break;
          case 'approve':
              
            foreach ( (array) $_REQUEST['media'] as $post_id_approve ) {
                if ( !current_user_can( 'edit_post', $post_id_approve ) )
                    wp_die( __( 'You are not allowed to approve this file upload.' ) );

                $post = get_post( $post_id_approve );

                if ( is_object( $post ) && $post->post_status == CLOSIFY_POST_STATUS ) {
                    
                  $post->post_status = 'inherit';
                    
                  wp_update_post( $post );

                  do_action( 'closify_media_approved', $post );
                }else{
                  return 'No file object found';
                }
            }
            return 'Selected files has been approved';
            break;

          default:
              // do nothing or something else
              return;
              break;
        }

        return;
    }
?>
