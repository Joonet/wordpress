<?php
add_action('init',                                  'create_custom_post_type');
add_action('manage_material_posts_custom_column',   'custom_columns_material');


add_filter('manage_material_posts_columns',     'set_edit_material_columns');


/* Action functions */

function create_custom_post_type() {
	/* Unset post_tag */
	register_taxonomy('post_tag', '', array());
    
    /* Materials */
    $labels = array(
		'name'               => 'Materials',
		'singular_name'      => 'Material',
		'menu_name'          => 'Materials',
		'name_admin_bar'     => 'Material',
		'add_new'            => 'Add Material',
		'add_new_item'       => 'Add new Material',
		'new_item'           => 'New Material',
		'edit_item'          => 'Edit Material',
		'view_item'          => 'View Material',
		'all_items'          => 'All Materials',
		'search_items'       => 'Search Material',
	);
	register_post_type( 'material',        
		array(
            'labels' 			=> $labels,
            'label' 			=> 'Material',
            'public' 			=> true,
            'has_archive' 		=> false,
			'hierarchical'  	=> false,
            'menu_icon' 		=> 'dashicons-hammer',
			'capability_type'   => 'post',			
			'menu_position'     => 5,
			'supports'          => array( 'title', 'editor', 'thumbnail', 'page-attributes' ) 
		)
    );    
}

function custom_columns_material( $column ) {
	global $post;
	switch($column)
	{		
        case 'price':
            echo '<strong>¥' . get_post_meta( $post->ID, 'price', 1 ) .'</strong>';
        break;
        
		case 'color':
            echo get_post_meta( $post->ID, 'color', 1 );         
		break;
        
        case 'accuracy':
            echo get_post_meta( $post->ID, 'accuracy', 1 );         
		break;
        
        case 'tolerance_time':
            echo str_replace( "\n", '<br>', get_post_meta( $post->ID, 'tolerance_time', 1 ) );
		break;
        
        case 'estimated_delivery':
            echo get_post_meta( $post->ID, 'estimated_delivery', 1 ) . ' hours';         
		break;
        
        case 'painting':
            if ( get_post_meta( $post->ID, 'painting', 1 ) )
                echo '<strong>YES, ¥' . get_post_meta( $post->ID, 'price_painting', 1 ) .'</strong>';
            else 
                echo '-';
		break;
        
        case 'silk_screen':
            if ( get_post_meta( $post->ID, 'silk_screen', 1 ) )
                echo '<strong>YES, ¥' . get_post_meta( $post->ID, 'price_silk_screen', 1 ) .'</strong>';
            else 
                echo '-';
		break;
	}
}


/* Filter functions */

function set_edit_material_columns( $columns ) {
	$date_title = $columns['date'];
    unset( $columns['date'] );
    
    // foreach ( $columns as $key => $val ) {
		// $new_columns[ $key ] = $val;
		// if ( $key == 'cb' ) {
			// $new_columns['post_id'] = 'ID';
		// }
	// }
	
	$columns['price']              = 'Price';
	$columns['color']              = 'Color';
	$columns['accuracy']           = 'Accuracy';
	$columns['tolerance_time']     = 'Tolerance Time';
	$columns['estimated_delivery'] = 'Estimated Delivery';
	$columns['painting']           = 'Painting';
	$columns['silk_screen']        = 'Silk screen';
	$columns['date']               = $date_title;

	return $columns;
}
