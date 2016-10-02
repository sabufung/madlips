<?php
  
  $effectList = array('none', 'bounce','flash','pulse','rubberBand','shake','headShake','swing','tada','wobble','jello','bounceIn','bounceInDown','bounceInLeft','bounceInRight','bounceInUp','bounceOut','bounceOutDown','bounceOutLeft','bounceOutRight','bounceOutUp','fadeIn','fadeInDown','fadeInDownBig','fadeInLeft','fadeInLeftBig','fadeInRight','fadeInRightBig','fadeInUp','fadeInUpBig','fadeOut','fadeOutDown','fadeOutDownBig','fadeOutLeft','fadeOutLeftBig','fadeOutRight','fadeOutRightBig','fadeOutUp','fadeOutUpBig','flipInX','flipInY','flipOutX','flipOutY','lightSpeedIn','lightSpeedOut','rotateIn','rotateInDownLeft','rotateInDownRight','rotateInUpLeft','rotateInUpRight','rotateOut','rotateOutDownLeft','rotateOutDownRight','rotateOutUpLeft','rotateOutUpRight','hinge','rollIn','rollOut','zoomIn','zoomInDown','zoomInLeft','zoomInRight','zoomInUp','zoomOut','zoomOutDown','zoomOutLeft','zoomOutRight','zoomOutUp','slideInDown','slideInLeft','slideInRight','slideInUp','slideOutDown','slideOutLeft','slideOutRight','slideOutUp');
  
  $effects = array();
  
  // Gallery effects
  foreach($effectList as $effectType)
  {
    $effects[$effectType] = ucfirst($effectType);
  }
  
  // Extract current Wordpress Media Sizes
  // Update media sized for thumbnail option
    $media_sizes = array();
    foreach (get_intermediate_image_sizes() as $s) {
        if (isset($_wp_additional_image_sizes[$s])) {
            $width = intval($_wp_additional_image_sizes[$s]['width']);
            $height = intval($_wp_additional_image_sizes[$s]['height']);
        } else {
            $width = get_option($s.'_size_w');
            $height = get_option($s.'_size_h');
        }
        if($width != '' && $height!='')
          $media_sizes[$s] = $s.' ('.$width.' x '.$height.')';
    }
    $media_sizes['full'] = 'full size';
?>

<html>
<head>
  
  <link rel="stylesheet" type="text/css" href="<?php echo CLOSIFY_ITECH_PLUGIN_URL.'/assets/css/animate.min.css'; ?>">
  <link rel="stylesheet" type="text/css" href="<?php echo CLOSIFY_ITECH_PLUGIN_URL.'/assets/css/jquery.dataTables.min.css'; ?>">
  <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
  
  <style>
    body{
      font-family: 'Roboto', sans-serif;
      font-size:12px;
    }
    table, td, tr
    {
      font-size:small;
      text-align:center;
    }
    select
    {
      margin: 10;
      padding: 10;
      font-family: inherit;
    }
    button
    {
      padding: 10;
      margin: 5px;
      font-family: 'Roboto';
    }
  </style>
</head>
<body>
  <form>
    <fieldset>
        <legend><strong>(Step 1)</strong>:Choose Gallery Effects</legend>
          <select id="closify-effect">
            <?php 
              foreach($effects as $key=>$effect)
              {
                echo '<option value="'.$key.'">'.$effect.'</option>';
              }
            ?>
          </select>
          <div id="effect-demo" style="width:40px;height:40px;background: #4679BD;margin:0 auto">
          </div>
    </fieldset>
    
    <br><br>

    <fieldset>
        <legend><strong>(Step 2)</strong>:Gallery Options</legend>
          <label style="margin-left:10px;">Disable caption:</label>
          <input id="disable-caption" class="closify-caption" type="checkbox">
          <br>
          <label style="margin-left:10px;">Disable title:</label>
          <input id="disable-title" class="closify-title" type="checkbox">
          <br>
          <label style="margin-left:10px;">Enable pagination for gallery:</label>
          <input id="cloasify-pagination" class="cloasify-pagination" type="checkbox">
          <br>
          <label style="margin-left:10px;">Define gallery item width (Default=150):</label>
          <input style="width:80px;padding:10;margin:10" id="closify_gallery_width" step="10" size="6" type="number" placeholder="Width" >
          <br>
          <label style="margin-left:10px;">Number of images per page (Default=10):</label>
          <select id="image-perpage-select">
            <option>10</option>
            <option>15</option>
            <option>20</option>
            <option>25</option>
            <option>30</option>
            <option>35</option>
          </select>
          <br>
          <label style="margin-left:10px;">Thumbnail Size:</label>
          <select id="closify-thumb-size">
            <?php 
              foreach($media_sizes as $key=>$size)
              {
                $selected = '';
                if($key == 'medium') $selected = 'selected';
                echo '<option value="'.$key.'" '.$selected.'>'.$size.'</option>';
              }
            ?>
          </select>
    </fieldset>
    
    <br><br>
    
    <?php
    
    // Print posts table
      $table_id1 = "closifyPostTable";
      $post_table = new FlexiCustomPostTableList('3', CLOSIFY_POST_TYPE, 100, 1, $table_id1);
      $post_table->BuildTable();
    ?>
    
    <br><br>
    
    <fieldset>
        <legend><strong>(Step 4)</strong>:Just select from all users and <span style="color:red">UNCHECK to SPECIFY</span></legend>
        <label style="margin-left:10px;">Select images from all uploaders with no exceptions:</label>
        <input id="select-all" class="closify-caption" type="checkbox" checked>
    </fieldset>
    
    <br><br>
    <div id="user-filter-section" style="display:none">
      <?php

        $table_id2 = "userRolesPostTable";

        $roles_table = new FlexiUserRoleTableList('5', $table_id2);
        $roles_table->BuildTable();

      ?>
<br><br>
      <?php
      // Print users table  
        $table_id3 = "usersPostTable";
        $post_table = new FlexiUserTableList('6', $table_id3, '');
        $post_table->BuildTable();
      ?>
    </div>
    <button onclick="insert()">Insert</button>
  </form>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo CLOSIFY_ITECH_PLUGIN_URL.'/assets/js/jquery.dataTables.min.js'; ?>"></script>
  <script type="text/javascript">
    var closifyPostTable = jQuery('#<?php echo $table_id1;?>').DataTable();
    var userRolesPostTable = jQuery('#<?php echo $table_id2;?>').DataTable();
    var usersPostTable = jQuery('#<?php echo $table_id3;?>').DataTable();
    var allChecked = true;
    
    jQuery(document).ready(function($){
        
        // Fire when select value changes
        $( "#closify-effect" ).change(function(e) {
          $('#effect-demo').removeClass();
          $('#effect-demo').addClass("closify-animated "+$( "#closify-effect" ).find(":selected").text()+" infinite");
        });

        function clear_all(tableName)
        {
          var rows = tableName.rows({ 'search': 'applied' }).nodes();
          jQuery('input[type="checkbox"]', rows).prop('checked', false);
        }

        function check_all(tableName)
        {
          var rows = tableName.rows({ 'search': 'applied' }).nodes();
          jQuery('input[type="checkbox"]', rows).prop('checked', true);
        }
    
        $('#select-all').change(function(el){
           allChecked = $(this).prop("checked");
           console.log("checked:"+allChecked);
           if(allChecked){
             $("#user-filter-section").hide("fast");
           }else{
             $("#user-filter-section").show("slow");
           }
        });
    
        $('input[type="checkbox"]').click(function(el){
            if($('.all_posts').is(":checked")){
              check_all(closifyPostTable);
            }
            else if(el.toElement.className == 'all_posts'){
              clear_all(closifyPostTable);
            }
            
            if($('.all_users').is(":checked")){
                check_all(usersPostTable);
            }
            else if(el.toElement.className == 'all_users'){
                clear_all(usersPostTable);
            }
            
            if($('.all_roles').is(":checked")){
                check_all(userRolesPostTable);
            }
            else if(el.toElement.className == 'all_roles'){
                clear_all(userRolesPostTable);
            }
        });
        
    });
    
    function insert()
    {
      shortCodeOpen = '[closify-collage ';
      postIDs = 'closify_ids="';
      effect = 'effect="';
      roles = 'roles="';
      users = 'user_ids="';
      captions = 'disable_caption=';
      pagination = 'pagination=';
      title = 'disable_title=';
      allCheckedProb = 'all_checked="';
      thumbSize = 'thumb_size="';
      imagePerPage = 'img_per_page="';
      width = 'image_width="';
      
      allCheckedProb = allCheckedProb + allChecked + '" ';
      
      // Parse waving width
      if($( "#closify_gallery_width" ).val() != "")
        width = width+ $( "#closify_gallery_width" ).val() + '" ';
      else
        width="";
        
      // insert closify ids
      closifyPostTable.$('input[type="checkbox"]').each(function () {
           if (this.checked && this.className == "posts") {
               postIDs = postIDs + $(this).val() +","; 
           }
      });
      
      // Gallery pagination
      if ($('input.cloasify-pagination').is(':checked')) {
        pagination = pagination + '"' + $('input.cloasify-pagination').val() + '" ';
      }else{
        pagination = '';
      }
      
      // Caption
      if ($('input.closify-caption').is(':checked')) {
        captions = captions + '"' + $('input.closify-caption').val() + '" ';
      }else{
        captions = '';
      }
      
      // Title disabled or not
      if ($('input.closify-title').is(':checked')) {
        title = title + '"' + $('input.closify-title').val() + '" ';
      }else{
        title = '';
      }
      
      // remove last extra comma
      // remove last extra comma
      if(postIDs != 'closify_ids="'){
        postIDs = postIDs.substring(0, postIDs.length - 1);
        postIDs = postIDs + '" ';
      }else{
        postIDs = '';
      }

      // Parse the selected effect
      thumbSize = thumbSize + $( "#closify-thumb-size option:selected" ).val();
      thumbSize = thumbSize + '" '
      
      // Parse the selected effect
      imagePerPage = imagePerPage + $( "#image-perpage-select option:selected" ).val();
      imagePerPage = imagePerPage + '" '
      
      // Parse the selected effect
      effect = effect + $( "#closify-effect option:selected" ).val();
      effect = effect + '" '
      
      // insert roles
      userRolesPostTable.$('input[type="checkbox"]').each(function () {
           if (this.checked && this.className == "roles") {
               roles = roles + $(this).val() +","; 
           }
      });
      
      // insert user ids
      usersPostTable.$('input[type="checkbox"]').each(function () {
           if (this.checked && this.className == "users") {
               users = users + $(this).val() +","; 
           }
      });
      
      // remove last extra comma
      if(users != 'user_ids="'){
        users = users.substring(0, users.length - 1);
        users = users + '" ';
      }else{
        users='';
      }
      
      // remove last extra comma
      if(roles != 'roles="'){
        roles = roles.substring(0, roles.length - 1);
        roles = roles + '" ';
      }else{
        roles='';
      }
      
      // 
      
      parent.closify_insert_data(shortCodeOpen+postIDs+allCheckedProb+effect+users+roles+captions+title+thumbSize+pagination+imagePerPage+width+']');
      parent.tinyMCE.activeEditor.windowManager.close(window);
    }
    
  </script>
</body>