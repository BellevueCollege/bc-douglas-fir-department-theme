<?php

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'add_seo_seo_meta_box' );
add_action( 'load-post-new.php', 'add_seo_seo_meta_box' );

/////////////////////////
// Custom Meta Boxes
/////////////////////////



/* Adds a box to the main column on the Post and Page edit screens */
function add_seo_seo_meta_box() {
    $screens = array( 'post', 'page' );
    foreach ($screens as $screen) {
        add_meta_box(
            'seo_seo_meta_box',
            'SEO',
            'seo_seo_meta_box',
            $screen,
            'normal',
            'low'
        );
    }
}

add_action('add_meta_boxes', 'add_seo_seo_meta_box');


// Field Array
$prefix = '_seo_';
$seo_seo_meta_fields = array(
	array(
		'label'=> 'Custom Page Title',
		'desc'	=> '',
		'id'	=> $prefix.'custom_page_title',
		'type'	=> 'input'
	),
	array(
		'label'=> 'Meta Keywords',
		'desc'	=> '',
		'id'	=> $prefix.'meta_keywords',
		'type'	=> 'text_area'
	),
	array(
		'label'=> 'Meta Description',
		'desc'	=> '',
		'id'	=> $prefix.'meta_description',
		'type'	=> 'text_area'
	),
);

// The Callback
function seo_seo_meta_box() {
global $seo_seo_meta_fields, $post;
// Use nonce for verification
echo '<input type="hidden" name="seo_seo_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
	// Begin the field table and loop
	echo '<table class="form-table">';
	foreach ($seo_seo_meta_fields as $field) {
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);
		// begin a table row with
		echo '<tr>
				<th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
				<td>';
				switch($field['type']) {
					// case items will go here

					// text
					case 'input':
						echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="60" />
							<br /><span class="description">'.$field['desc'].'</span>';
					break;

					case 'text_area':

					case 'textarea':
					    echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" cols="58" rows="4">'.$meta.'</textarea>
					        <br /><span class="description">'.$field['desc'].'</span>';
					break;



				} //end switch
		echo '</td></tr>';
	} // end foreach
	echo '</table>'; // end table
}

/**
 * Save SEO meta box values when a post or page is updated.
 *
 * @param int $post_id Post being saved.
 * @return int Post ID.
 */
function save_seo_meta( $post_id ) {
	global $seo_seo_meta_fields;

	if ( ! isset( $_POST['seo_seo_meta_box_nonce'] ) ) {
		return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['seo_seo_meta_box_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, basename( __FILE__ ) ) ) {
		return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	$post_type = get_post_type( $post_id );
	if ( 'page' === $post_type ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	foreach ( $seo_seo_meta_fields as $field ) {
		// Autosave and bulk actions may omit meta box fields.
		if ( ! isset( $_POST[ $field['id'] ] ) ) {
			continue;
		}

		$old = get_post_meta( $post_id, $field['id'], true );
		$raw = wp_unslash( $_POST[ $field['id'] ] );

		if ( 'text_area' === $field['type'] || 'textarea' === $field['type'] ) {
			$new = sanitize_textarea_field( $raw );
		} else {
			$new = sanitize_text_field( $raw );
		}

		if ( $new && $new !== $old ) {
			update_post_meta( $post_id, $field['id'], $new );
		} elseif ( '' === $new && $old ) {
			delete_post_meta( $post_id, $field['id'], $old );
		}
	}
}
add_action('save_post', 'save_seo_meta');

?>
