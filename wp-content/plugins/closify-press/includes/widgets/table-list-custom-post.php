<?php

class FlexiCustomPostTableList
{
    // property declaration
    private $post_type = CLOSIFY_POST_TYPE;
    private $category_type;
    private $step_num;
    // Pagination: How many items in a single page to list
    private $show_num;
    private $page;
    private $table_id;
    
    // method declaration
    public function __construct($step_num, $post_type, $show_num = 100, $page = 1, $table_id, $category_type = 'closify_category') {
      $this->step_num = $step_num;
      $this->post_type = $post_type;
      $this->show_num = $show_num;
      $this->page = $page;
      $this->category_type = $category_type;
      $this->table_id = $table_id;
    }
    
    public function BuildTable()
    {
      $postType = CLOSIFY_POST_TYPE;

      if(isset($_POST['post_type'])){
        $postType = $_POST['post_type'];
      }
      if(isset($_POST['page'])){
        $this->page =  $_POST['page'];
      }
        $wp_query = null; 
        $wp_query = new WP_Query(); 
        $wp_query->query('showposts='.$this->show_num.'&post_type='.$this->post_type.'&paged='.$this->page); 
      ?>

      <fieldset>
        <legend><strong>(Step <?php echo $this->step_num; ?>)</strong>:Choose source of the Gallery:</legend>
        <div> 

          <div>  
            <h4>Select multiple <?php echo ucfirst($this->post_type); ?> source of images</h4>
          </div>

          <table id="<?php echo $this->table_id; ?>" class="display compact" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th><input type="checkbox" name="all_posts" class="all_posts" value="true" >All</th>
                <th><?php echo ucfirst($this->post_type);?></th>
                <th>Categories</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th>#</th>
                <th><?php echo ucfirst($this->post_type);?></th>
                <th>Categories</th>
              </tr>
            </tfoot>
            <tbody id="the-list" >
            <?php
              while ($wp_query->have_posts()) : $wp_query->the_post(); 
            ?>
                  <tr>
                    <td><?php echo '<input type="checkbox" class="posts" name="post_ids[]" value="'.get_the_ID().'" />' ?></td>
                    <td><?php the_title(); ?></td>
                    <td><?php echo get_the_term_list( get_the_ID(), $this->category_type, "", ", ", "" ) ?></td>
                  </tr>
              <!-- LOOP: Usual Post Template Stuff Here-->

            <?php endwhile; ?>
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