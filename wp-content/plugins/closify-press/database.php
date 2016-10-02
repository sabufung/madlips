<?php

/**
 * Description of activation
 *
 * @author aelbuni
 */

  
  function install_closify_database()
  {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    $create_table_script = "CREATE TABLE IF NOT EXISTS ".CLOSIFY_UPLOAD_TABLE_NAME." (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL ,
        att_id BIGINT(20) UNIQUE NOT NULL ,
        closify_id BIGINT(20) NOT NULL ,
        att_size VARCHAR(45) DEFAULT '' NULL ,
        time datetime DEFAULT '0000-00-00 00:00:00' ,
        dropbox_sent TINYINT(1) NULL ,
        drive_sent TINYINT(1) NULL ,
        ftp_sent TINYINT(1) NULL ,
        dropbox_cdn VARCHAR(255) NULL ,
        drive_cdn VARCHAR(255) NULL ,
        short_url VARCHAR(45) NULL ,
        PRIMARY KEY (id),
        UNIQUE INDEX att_id_UNIQUE (att_id ASC)) 
        $charset_collate;";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); 
    dbDelta( $create_table_script ); 
    
    add_option( "closify_db_version", CLOSIFY_DB_VERSION );
  }
  
  function uninstall_closify_database()
  {
    //drop a custom db table
    global $wpdb;
    $wpdb->query( "DROP TABLE IF EXISTS ".CLOSIFY_UPLOAD_TABLE_NAME );
  }
  

?>
