<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('init', 'register_sparky_post');
function register_sparky_post(){
	$labels = array(
		'name'               => _x( 'Sparky Logos', 'post type general name', 'sparky-logo-textdomain' ),
		'singular_name'      => _x( 'Sparky Logos', 'post type singular name', 'sparky-logo-textdomain' ),
		'menu_name'          => _x( 'Sparky Logos', 'admin menu', 'sparky-logo-textdomain' ),
		'name_admin_bar'     => _x( 'Sparky Logos', 'add new on admin bar', 'sparky-logo-textdomain' ),
		'add_new'            => _x( 'Add New', 'Logo', 'sparky-logo-textdomain' ),
		'add_new_item'       => __( 'Add New Logo', 'sparky-logo-textdomain' ),
		'new_item'           => __( 'New Logo', 'sparky-logo-textdomain' ),
		'edit_item'          => __( 'Edit Logo', 'sparky-logo-textdomain' ),
		'view_item'          => __( 'View Logo', 'sparky-logo-textdomain' ),
		'all_items'          => __( 'All Logos', 'sparky-logo-textdomain' ),
		'search_items'       => __( 'Search Logos', 'sparky-logo-textdomain' ),
		'parent_item_colon'  => __( 'Parent Logos:', 'sparky-logo-textdomain' ),
		'not_found'          => __( 'No logos found.', 'sparky-logo-textdomain' ),
		'not_found_in_trash' => __( 'No logos found in Trash.', 'sparky-logo-textdomain' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'thumbnail' )
	);

	register_post_type( 'sparky_logo', $args );
}

function sparky_logos_taxonomy() {  
    register_taxonomy(  
        'sparky_logo_categories',         //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces). 
        'sparky_logo',                  //post type name
        array(  
            'hierarchical' => true,  
            'label' => 'Categories',  //Display name
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'sparky_logo_categories', // This controls the base slug that will display before each term
                'with_front' => false // Don't display the category base before 
            )
        )  
    );  
}  
add_action( 'init', 'sparky_logos_taxonomy');

function sparky_logo_shortcode_fun( $atts ) {
	$atts = shortcode_atts( array(
		'category' => '',
		'number_of_items' => '-1',
		'type' => ''
	), $atts, 'sparky_logo' );

	$category = $atts['category'];

	$number_of_items = $atts['number_of_items'];
	$type = $atts['type'];
	$container_class = '';
	if($type === 'gallery'){
		$container_class = '';
	}
	else if($type === 'slider'){
		$container_class = 'owl-carousel';
		wp_register_style( 'owl_carousel_style', SPARKY_PLUGIN_URL . '/admin/owl-carousel/owl.carousel.css', false, '1.0.0' );
        wp_enqueue_style( 'owl_carousel_style' );

        wp_register_style( 'owl_theme_style', SPARKY_PLUGIN_URL . '/admin/owl-carousel/owl.theme.css', false, '1.0.0' );
        wp_enqueue_style( 'owl_theme_style' );

        wp_register_script( 'owl_carousel', SPARKY_PLUGIN_URL . '/admin/owl-carousel/owl.carousel.min.js', false, '1.0.0' );
        wp_enqueue_script( 'owl_carousel' );

        wp_register_script( 'sparky-frontend', SPARKY_PLUGIN_URL . '/front-end/sparky.js', false, '1.0.0' );
        wp_enqueue_script( 'sparky-frontend' );

	}
	global $wp_query;
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

	if($category  === 'all'){
		query_posts( array(  
		    'post_type' => 'sparky_logo', 
		    'paged' => $paged, 
		    'posts_per_page' => $number_of_items
		) ); 
	}
	else{
		query_posts( array(  
		    'post_type' => 'sparky_logo', 
		    'paged' => $paged, 
		    'posts_per_page' => $number_of_items, 
		    'tax_query' => array( 
		        array( 
		            'taxonomy' => 'sparky_logo_categories', //or tag or custom taxonomy
		            'field' => 'id', 
		            'terms' => $category
		        ) 
		    ) 
		) ); 
	}
	echo '<div class="sl_container '.$container_class.'" >';
	while (have_posts()) : the_post(); 
		$logo_image = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) ); 
		if(!empty($logo_image)){
			$featreimg = '<img src='.$logo_image.' class="sl_logo_img">';
		}		
			echo '<div class="sl_logo_container" style="width:auto">
					<div class="sl_item">
						<h2>'. get_the_title().'</h2>
						<p>'.get_the_excerpt().'</p>'.
						$featreimg.
						'<span class="logooos_effectspan" style=""></span>
					</div>
				</div>
			  ';
		
	endwhile;
	echo '</div><div class="sl_paging">';
			next_posts_link('Next');
			previous_posts_link('Previous');
	echo '</div>';
	// Reset Query
	wp_reset_query();
}
add_shortcode( 'sparky_logo', 'sparky_logo_shortcode_fun' );

// init process for registering our button
add_action('init', 'sparky_logo_shortcode_button_init');
function sparky_logo_shortcode_button_init() {
     //Abort early if the user will never see TinyMCE
    if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
        return;
    //Add a callback to regiser our tinymce plugin   
    add_filter("mce_external_plugins", "sparky_logo_register_tinymce_plugin"); 

    // Add a callback to add our button to the TinyMCE toolbar
    add_filter('mce_buttons', 'sparky_logo_add_tinymce_button');
}


//This callback registers our plug-in
function sparky_logo_register_tinymce_plugin($plugin_array) {
    $plugin_array['sparky_logo_button'] = SPARKY_PLUGIN_URL.'/admin/sparky-logo.js';
    return $plugin_array;
}

//This callback adds our button to the toolbar
function sparky_logo_add_tinymce_button($buttons) {
    //Add the button ID to the $button array
    $buttons[] = "sparky_logo_button";
    return $buttons;
}



add_action('wp_ajax_shortcode_handler', 'shortcode_handler');
add_action( 'wp_ajax_nopriv_shortcode_handler', 'shortcode_handler' );
function shortcode_handler(){
	$taxonomies = get_categories('taxonomy=sparky_logo_categories&type=sparky_logo'); 
	$option = '';
	foreach ($taxonomies as $taxonomy){
		$term_id = $taxonomy->term_id;
		$name = $taxonomy->name;
		$option .='<option value="'.$term_id.'">'.$name.'</option>';
	}
	$output =
	'<div class="sl_overlay"></div>
	<div class="sl_popup">
		<button class="sl_close">X</button>
		<table width="100%">
			<tr>
				<td>Choose category</td>
				<td>
					<select id="sl_category">
					<option value="all">All</option>'.$option.'
					</select>
				</td>
			</tr>

			<tr>
				<td>Choose type</td>
				<td>
					<select id="sl_type">
						<option value="gallery">Gallery</option>
						<option value="slider">Slider</option>
					</select>
				</td>
			</tr>

			<tr>
				<td>Number of Items</td>
				<td>
					<input type="number" value="10" id="sl_no_items">
					<span class="sl_note">Enter -1 if you want to display all items</span>
				</td>
			</tr>

			<tr>
				<th></th>
				<th><button class="sl_button" id="sl_shortcode">Insert </button></th>
			</tr>
		</table>
	</div>';
	echo $output;
	exit;
}

function sl_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'sl_excerpt_length', 999 );

?>