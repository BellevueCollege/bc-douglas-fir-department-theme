<?php
/**
 * BC Douglas Fir Theme Staff Custom Post Type
 *
 * @package BC Douglas Fir Theme
 */

/**
 * Hide 'Page Links To' in Staff post
 *
 * @param array $post_types Array of Post Types.
 */
function remove_plt_from_staff( $post_types ) {
	$key = array_search( 'staff', $post_types );
	if ( $key !== false ) {
		unset( $post_types[ $key ] );
	}

	return $post_types;
}
add_filter( 'page-links-to-post-types', 'remove_plt_from_staff' );

/**
 * Register the Staff CPT
 *
 * @todo Add REST support for Gutenberg - 'show_in_rest'       => true,.
 */
function bc_staff_register() {
	$labels = array(
		'name'               => 'Staff',
		'singular_name'      => 'Staff',
		'add_new'            => 'Add New',
		'Staff',
		'add_new_item'       => 'Add New Staff',
		'edit_item'          => 'Edit Staff',
		'new_item'           => 'New Staff',
		'all_items'          => 'Staff List',
		'view_item'          => 'View Staff',
		'search_items'       => 'Search Staff',
		'not_found'          => 'No Staff found',
		'not_found_in_trash' => 'No Staff found in Trash',
		'parent_item_colon'  => '',
		'menu_name'          => 'Staff',
	);

	$args = array(
		'labels'        => $labels,
		'public'        => true,
		'show_ui'       => true,
		'hierarchical'  => true,
		'has_archive'   => true,
		'rewrite'       => true,
		'menu_position' => 4,
		'show_in_rest'  => true,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'category', 'author', 'revisions', 'author', 'comments' ),
		'taxonomies'    => array(),
	);

	register_post_type( 'staff', $args );
}
add_action( 'init', 'bc_staff_register' );


/**
 * Add Sort Page Menu
 */
function mayflower_register_staff_sort_page() {
	add_submenu_page(
		'edit.php?post_type=staff',
		'Order Slides',
		'Re-Order',
		'edit_pages',
		'staff-order',
		'staff_order_page'
	);
}
add_action( 'admin_menu', 'mayflower_register_staff_sort_page' );

/**
 * Customize WP List Table
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Extend the List Table Class
 */
class Staff_List_Table extends WP_List_Table {

	/**
	 * Constructor, we override the parent to pass our own arguments
	 * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'plural' => 'mayflower_staff', // plural label, also this well be one of the table css class.
			)
		);
	}

}

/**
 * Build Sorting Interface
 */
function staff_order_page() {
	?>
	<div class="wrap">
		<h2>Sort Slides</h2>
		<p>Simply drag the slide up or down and it will be saved in that order.</p>
	<?php
	$slides = new WP_Query(
		array(
			'post_type'      => 'staff',
			'posts_per_page' => -1,
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
		)
	);
	?>
	<?php if ( $slides->have_posts() ) : ?>

		<table class="wp-list-table widefat fixed posts" id="sortable-table">
			<thead>
				<tr>
					<th class="column-order">Re-Order</th>
					<th class="column-thumbnail">Thumbnail</th>
					<th class="column-title">Title</th>
					<th class="column-title">Details</th>
				</tr>
			</thead>
			<tbody data-post-type="staff">
			<?php
			while ( $slides->have_posts() ) :
				$slides->the_post();
				?>
				<tr id="post-<?php the_ID(); ?>">
					<td class="column-order"><img src="<?php echo esc_url( get_template_directory_uri() . '/img/row-move.png' ); ?>" title="" alt="Move Icon" width="16" height="16" class="" /></td>
					<td class="column-thumbnail"><?php the_post_thumbnail( 'edit-screen-thumbnail' ); ?></td>
					<td class="column-title"><strong><?php the_title(); ?></strong></td>
					<td class="column-details"><div class="excerpt"><?php the_excerpt(); ?></div></td>
				</tr>
			<?php endwhile; ?>
			</tbody>
			<tfoot>
				<tr>
					<th class="column-order">Order</th>
					<th class="column-thumbnail">Thumbnail</th>
					<th class="column-title">Title</th>
					<th class="column-title">Details</th>
				</tr>
			</tfoot>

		</table>

	<?php else : ?>

		<p>No slides found, why not <a href="post-new.php?post_type=staff">create one?</a></p>

	<?php endif; ?>
	<?php wp_reset_postdata(); // Don't forget to reset again! ?>

	<style>
		/* Dodgy CSS ^_^ */
		#sortable-table td { background: white; }
		#sortable-table .column-order { padding: 3px 10px; width: 60px; }
			#sortable-table .column-order img { cursor: move; }
		#sortable-table td.column-order { vertical-align: middle; text-align: center; }
		#sortable-table .column-thumbnail { width: auto; }
		#sortable-table tbody tr.ui-state-highlight {
		height:202px;
		width: 100%;
		background:white !important;
		-webkit-box-shadow: inset 0px 1px 2px 1px rgba(0, 0, 0, 0.1);
		-moz-box-shadow: inset 0px 1px 2px 1px rgba(0, 0, 0, 0.1);
		box-shadow: inset 0px 1px 2px 1px rgba(0, 0, 0, 0.1);
		}
	</style>
	</div><!-- .wrap -->

	<?php

}

/**
 * Enqueue Scripts for Sorting Inteface
 */
function mayflower_staff_enqueue_scripts() {
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'mayflower-admin-scripts', get_template_directory_uri() . '/js/sorting-v2.js', array( 'jquery', 'jquery-ui-sortable' ), '1', false );
	// sorting-v2.js posts menu order updates; nonce must match the AJAX handler in functions.php.
	bc_douglas_fir_localize_post_order_script( 'mayflower-admin-scripts' );
}
add_action( 'admin_enqueue_scripts', 'mayflower_staff_enqueue_scripts' );

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'add_staff_custom_meta_box' );
add_action( 'load-post-new.php', 'add_staff_custom_meta_box' );

/**
 * Custom Meta Boxes
 */

/**
 * Add Custom Meta Box for Staff
 */
function add_staff_custom_meta_box() {
	add_meta_box(
		'staff_custom_meta_box', // $id
		'Staff Information', // $title
		'show_custom_meta_box', // $callback
		'staff', // $page
		'normal', // $context
		'high'
	); // $priority
}
add_action( 'add_meta_boxes', 'add_staff_custom_meta_box' );


// Field Array.
$prefix                   = '_staff_';
$staff_custom_meta_fields = array(
	array(
		'label' => 'Email',
		'desc'  => '',
		'id'    => $prefix . 'email',
		'type'  => 'email',
	),
	array(
		'label' => 'Position',
		'desc'  => '',
		'id'    => $prefix . 'position',
		'type'  => 'position',
	),
	array(
		'label' => 'Phone',
		'desc'  => '',
		'id'    => $prefix . 'phone',
		'type'  => 'phone',
	),
	array(
		'label' => 'Office Hours',
		'desc'  => '',
		'id'    => $prefix . 'office_hours',
		'type'  => 'office_hours',
	),
	array(
		'label' => 'Office Location',
		'desc'  => '',
		'id'    => $prefix . 'office_location',
		'type'  => 'office_location',
	),
	array(
		'label' => 'Appointment Schedule Link',
		'desc'  => '',
		'id'    => $prefix . 'appt_link',
		'type'  => 'appt_link',
	),
);

/**
 * Build Staff Meta Box
 */
function show_custom_meta_box() {
	global $staff_custom_meta_fields, $post;
	// Use nonce for verification.
	echo '<input type="hidden" name="staff_custom_meta_box_nonce" value="' . esc_attr( wp_create_nonce( basename( __FILE__ ) ) ) . '" />';
	// Begin the field table and loop.
	echo '<table class="form-table">';
	foreach ( $staff_custom_meta_fields as $field ) {
		// get value of this field if it exists for this post.
		$meta = get_post_meta( $post->ID, $field['id'], true );
		// begin a table row with.
		echo '<tr>
				<th scope="row"><label for="' . esc_attr( $field['id'] ) . '">' . esc_attr( $field['label'] ) . '</label></th>
				<td>';

		echo '<input type="text" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $meta ) . '" size="30" />
			<br /><span class="description">' . esc_attr( $field['desc'] ) . '</span>';

		echo '</td></tr>';
	} // end foreach.
	echo '</table>'; // end table.
}

/**
 * Save the Meta Data from Meta Box
 *
 * @param int $post_id Current Post ID.
 * @global $staff_custom_meta_fields.
 */
function save_custom_meta( $post_id ) {
	global $staff_custom_meta_fields;

	if ( ! isset( $_POST['staff_custom_meta_box_nonce'] ) ) {
		return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['staff_custom_meta_box_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// check autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// Use the saved post's type, not $_POST, so staff CPT saves get edit_post checks.
	$post_type = get_post_type( $post_id );
	if ( 'page' === $post_type ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// loop through fields and save the data.
	foreach ( $staff_custom_meta_fields as $field ) {
		// Autosave and bulk actions may omit meta box fields.
		if ( ! isset( $_POST[ $field['id'] ] ) ) {
			continue;
		}

		$old = get_post_meta( $post_id, $field['id'], true );
		$raw = wp_unslash( $_POST[ $field['id'] ] );

		// Pick sanitizer by field type before writing post meta.
		if ( 'email' === $field['type'] ) {
			$new = sanitize_email( $raw );
		} elseif ( 'appt_link' === $field['type'] ) {
			$new = esc_url_raw( $raw );
		} else {
			$new = sanitize_text_field( $raw );
		}

		if ( $new && $new !== $old ) {
			update_post_meta( $post_id, $field['id'], $new );
		} elseif ( '' === $new && $old ) {
			delete_post_meta( $post_id, $field['id'], $old );
		}
	} // end foreach
}
add_action( 'save_post', 'save_custom_meta' );



if ( is_admin() ) :
	/**
	 * Removed unneeded metaboxes
	 */
	function staff_remove_meta_boxes() {
		remove_meta_box( 'categorydiv', 'staff', 'normal' );
		remove_meta_box( 'tagsdiv-post_tag', 'staff', 'normal' );
		remove_meta_box( 'authordiv', 'staff', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'staff', 'normal' );
		remove_meta_box( 'commentsdiv', 'staff', 'normal' );
		remove_meta_box( 'revisionsdiv', 'staff', 'normal' );
	}
	add_action( 'admin_menu', 'staff_remove_meta_boxes' );
endif;


/**
 * Custom Default Post Title for Staff CPT
 */
function mayflower_staff_title_text( $title ) {
	$screen = get_current_screen();
	if ( 'staff' == $screen->post_type ) {
		$title = 'Name of Staff';
	}
	return $title;
}

add_filter( 'enter_title_here', 'mayflower_staff_title_text' );


/**
 * Custom Columns on Staff CPT Admin Screen
 *
 * @param array $staff_columns Array of Columns.
 */
function mayflower_add_new_staff_columns( $staff_columns ) {
		$staff_columns = array(
			'cb'                    => '<input type="checkbox" />',
			'staff-thumbnail'       => 'Photo',
			'title'                 => 'Name',
			'staff_email'           => 'Email',
			'staff_position'        => 'Position',
			'staff_phone'           => 'Phone',
			'staff_hours'           => 'Office Hours',
			'staff_office_location' => 'Office Location',
		);

		return $staff_columns;
}
add_filter( 'manage_edit-staff_columns', 'mayflower_add_new_staff_columns' );



/**
 * Define Column Contents
 *
 * @param string $column Column Type.
 * @param int    $post_id ID of Post.
 */
function mayflower_manage_staff_columns( $column, $post_id ) {
	global $post;

	switch ( $column ) {

		case 'staff-thumbnail':
			echo get_the_post_thumbnail( $post->ID, 'edit-screen-thumbnail' );
			break;

		case 'staff_email':
			/* Get the post meta. */
			$staff_meta = get_post_meta( $post_id, '_staff_email', true );

			if ( empty( $staff_meta ) ) {
				echo '';
			} else {
				echo esc_attr( $staff_meta );
			}

			break;

		case 'staff_position':
			/* Get the post meta. */
			$staff_meta = get_post_meta( $post_id, '_staff_position', true );

			if ( empty( $staff_meta ) ) {
				echo '';
			} else {
				echo esc_attr( $staff_meta );
			}
			break;

		case 'staff_phone':
			/* Get the post meta. */
			$staff_meta = get_post_meta( $post_id, '_staff_phone', true );

			if ( empty( $staff_meta ) ) {
				echo '';
			} else {
				echo esc_attr( $staff_meta );
			}
			break;

		case 'staff_hours':
			/* Get the post meta. */
			$staff_meta = get_post_meta( $post_id, '_staff_office_hours', true );

			if ( empty( $staff_meta ) ) {
				echo '';
			} else {
				echo esc_attr( $staff_meta );
			}
			break;

		case 'staff_office_location':
			/* Get the post meta. */
			$staff_meta = get_post_meta( $post_id, '_staff_office_location', true );

			if ( empty( $staff_meta ) ) {
				echo '';
			} else {
				echo esc_attr( $staff_meta );
			}
			break;
		case 'staff_appt_link':
			/* Get the post meta. */
			$staff_meta = get_post_meta( $post_id, '_staff_appt_link', true );

			if ( empty( $staff_meta ) ) {
				echo '';
			} else {
				echo esc_attr( $staff_meta );
			}
			break;
		default:
	} // end switch
}
add_action( 'manage_staff_posts_custom_column', 'mayflower_manage_staff_columns', 10, 2 );
