<?php

class FlexiUserTableList
{
    // property declaration
    private $user_roles;
    private $table_id;
    private $step_num;
    
    // method declaration
    public function __construct($step_num, $table_id, $user_role = 'subscriber') {
      $this->step_num = $step_num;
      $this->user_roles = $user_role;
      $this->table_id = $table_id;
    }
    
    public function BuildTable()
    {
      $blogusers = get_users( 'blog_id=1&orderby=nicename&role='.$this->user_roles );
      ?>

      <fieldset>
        <legend><strong>(Step <?php echo $this->step_num; ?>)</strong>:Select by user:</legend>
        <div> 
          
          <div>  
            <h3>Select multiple Users as a source of images</h3>
          </div>

          <table id="<?php echo $this->table_id; ?>" class="display compact" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th><input type="checkbox" class="all_users" name="all_users" value="selected" >All</th>
                <th>Nickname</th>
                <th>Email</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th>#</th>
                <th>Nickname</th>
                <th>Email</th>
              </tr>
            </tfoot>
            <tbody id="the-list" >
                 <tr>
                   <td><?php echo '<input type="checkbox" class="users" name="user_ids[]" value="0" />' ?></td>
                   <td>Guests</td>
                   <td>Guests</td>
                 </tr>
            <?php
              foreach ( $blogusers as $user ) {
            ?>
                  <tr>
                    <td><?php echo '<input type="checkbox" class="users" name="user_ids[]" value="'.$user->ID.'" />' ?></td>
                    <td><?php echo $user->user_login; ?></td>
                    <td><?php echo $user->user_email; ?></td>
                  </tr>
              <!-- LOOP: Usual Post Template Stuff Here-->

            <?php } ?>
                </tbody>
              </table>
            </div>
          </fieldset>

      <nav>
      <?php previous_posts_link('&laquo; Newer') ?>
      <?php next_posts_link('Older &raquo;') ?>
      </nav>

      <?php 
        $wp_query = null; 
    }
    
}

?>