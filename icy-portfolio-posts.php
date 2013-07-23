<?php
/*
Plugin Name: Icy Portfolio Posts
Plugin Script: icy-portfolio-posts.php
Plugin URI: http://www.icypixels.com/
Description: A simple and handy portfolio post type plugin, which ensures your content is not lost when switching themes. Brought to you by <a href="http://www.icypixels.com" title="Icy Pixels WordPress Themes">Icy Pixels</a> (<a href="http://twitter.com/theicypixels/">Twitter</a> | <a href="https://www.facebook.com/pages/Icy-Pixels/170508899756996">Facebook</a>). 
Version: 1.0
License: GPL 3.0
Author: Icy Pixels
Author URI: http://www.icypixels.com
*/


//Creating the Portfolios Custom post type
function icy_create_post_type_portfolios() 
{
	$labels = array(
		'name' => __( 'Portfolio Items','framework'),
		'singular_name' => __( 'Portfolio','framework' ),
		'add_new' => __('Add New','framework'),
		'add_new_item' => __('Add New Portfolio','framework'),
		'new_item' => __('New Portfolio','framework'),
		'edit_item' => __('Edit Portfolio','framework'),
		'view_item' => __('View Portfolio','framework'),
		'search_items' => __('Search Portfolio','framework'),
		'not_found' =>  __('No Portfolio found','framework'),
		'not_found_in_trash' => __('No Portfolio found in Trash','framework'), 
		'parent_item_colon' => ''
	  );    
	  
	  $args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => false,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'query_var' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
        'rewrite' => array('slug'=>'portfolio','with_front'=>true),
        'has_archive' => true, 
		'supports' => array('title','editor','thumbnail','custom-fields','page-attributes', 'excerpt'),
	  ); 
	  
	  register_post_type(__( 'portfolio', 'framework' ),$args);  
      flush_rewrite_rules(); 
}

// Creating the Portfolio type taxonomy
function icy_build_taxonomies() {
    $labels = array(
        'name' => __( 'Portfolio Type', 'framework' ),
        'singular_name' => __( 'Portfolio Type', 'framework' ),
        'search_items' =>  __( 'Search Portfolio Types', 'framework' ),
        'popular_items' => __( 'Popular Portfolio Types', 'framework' ),
        'all_items' => __( 'All Portfolio Types', 'framework' ),
        'parent_item' => __( 'Parent Portfolio Type', 'framework' ),
        'parent_item_colon' => __( 'Parent Portfolio Type:', 'framework' ),
        'edit_item' => __( 'Edit Portfolio Type', 'framework' ), 
        'update_item' => __( 'Update Portfolio Type', 'framework' ),
        'add_new_item' => __( 'Add New Portfolio Type', 'framework' ),
        'new_item_name' => __( 'New Portfolio Type Name', 'framework' ),
        'separate_items_with_commas' => __( 'Separate Portfolio types with commas', 'framework' ),
        'add_or_remove_items' => __( 'Add or remove Portfolio types', 'framework' ),
        'choose_from_most_used' => __( 'Choose from the most used Portfolio types', 'framework' ),
        'menu_name' => __( 'Portfolio Types', 'framework' )
    );
    
	register_taxonomy(
	    'type', 
	    array( __( 'portfolio', 'framework' )), 
	    array(
	        'hierarchical' => true, 
	        'labels' => $labels,
	        'show_ui' => true,
	        'query_var' => true,
	        'rewrite' => array('slug' => 'type', 'hierarchical' => true)
	    )
	); 
}

// Sorting Enabling
function icy_create_portfolio_sort_page() {    
    $icy_sort_page = add_submenu_page('edit.php?post_type=portfolio', 'Sort Portfolio', __('Sort Portfolio', 'framework'), 'edit_posts', basename(__FILE__), 'icy_portfolio_sort');
    
    add_action('admin_print_styles-' . $icy_sort_page, 'icy_print_sort_styles');
    add_action('admin_print_scripts-' . $icy_sort_page, 'icy_print_sort_scripts');
}

function icy_portfolio_sort() {
    $portfolio = new WP_Query('post_type=portfolio&posts_per_page=-1&orderby=menu_order&order=ASC');
?>
    <div class="wrap">
        <div id="icon-tools" class="icon32"><br /></div>
        <h2><?php _e('Sort portfolios', 'framework'); ?></h2>
        <p><?php _e('Click, drag, re-order. Repeat as neccessary. Portfolio item at the top will appear first. Each 4th item will have a large image.', 'framework'); ?></p>

        <ul id="portfolio_list">
            <?php while( $portfolio->have_posts() ) : $portfolio->the_post(); ?>
                <?php if( get_post_status() == 'publish' ) { ?>
                    <li id="<?php the_id(); ?>" class="menu-item">
                        <dl class="menu-item-bar">
                            <dt class="menu-item-handle">
                                <span class="menu-item-title"><?php the_title(); ?></span>
                            </dt>
                        </dl>
                        <ul class="menu-item-transport"></ul>
                    </li>
                <?php } ?>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </ul>
    </div>
<?php }

function icy_save_portfolio_sorted_order() {
    global $wpdb;
    
    $order = explode(',', $_POST['order']);
    $counter = 0;
    
    foreach($order as $portfolio_id) {
        $wpdb->update($wpdb->posts, array('menu_order' => $counter), array('ID' => $portfolio_id));
        $counter++;
    }
    die(1);
}

function icy_print_sort_scripts() {
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('icy_portfolio_sort', plugins_url( 'icy_portfolio_sort.js', __FILE__ ));
}

function icy_print_sort_styles() {
    wp_enqueue_style('nav-menu');
}


// Adding Custom Columns
function icy_portfolio_edit_columns($columns){  

        $columns = array(  
            "cb" => "<input type=\"checkbox\" />",  
            "title" => __( 'Portfolio Item Title', 'framework' ),
            "type" => __( 'type', 'framework' )
        );  
  
        return $columns;  
}  
  
function icy_portfolio_custom_columns($column){  
        global $post;  
        switch ($column)  
        {    
            case __( 'type', 'framework' ):  
                echo get_the_term_list($post->ID, __( 'type', 'framework' ), '', ', ','');  
                break;
        }  
}  


/* Call our custom functions ---------------------------------------------*/
add_action( 'init', 'icy_create_post_type_portfolios' );
add_action( 'init', 'icy_build_taxonomies', 0 );

add_action('admin_menu', 'icy_create_portfolio_sort_page');
add_action('wp_ajax_portfolio_sort', 'icy_save_portfolio_sorted_order');

add_filter("manage_edit-portfolio_columns", "icy_portfolio_edit_columns");  
add_action("manage_posts_custom_column",  "icy_portfolio_custom_columns");

?>