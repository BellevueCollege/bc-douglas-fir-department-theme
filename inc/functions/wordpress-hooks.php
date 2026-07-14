<?php
/**
 * BC Douglas Fir Theme Theme WordPress Core Hooks
 *
 * Contains all of the Theme's functions that
 * hook into core action/filter hooks, other
 * than Theme Setup functions, Plugin functions,
 * and Settings API functions
 *
 * @package BC Douglas Fir Theme
 */

/**
 * Add parent class to wp_nav_menu parent list items
 *
 * Allows menu item to be targeted when on child page
 *
 * @param array $items Array of menu items.
 */
function mayflower_add_menu_parent_class( $items ) {
	$parents = array();
	foreach ( $items as $item ) {
		if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
			$parents[] = $item->menu_item_parent;
		}
	}
	foreach ( $items as $item ) {
		if ( in_array( $item->ID, $parents ) ) {
			$item->classes[] = 'menu-item-parent';
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'mayflower_add_menu_parent_class' );

/**
 * Filter Widget Title
 *
 * Filter Hook: widget_title
 *
 * Filter 'widget_title' to output
 * a non-breaking space (&nbsp;) if
 * no title is defined. This output
 * is necessary in order for the
 * custom $after_widget output, that
 * wraps the Widget content in a
 * show/hide container, to be rendered.
 *
 * @param string $title Title of Widget.
 * @since   Oenology 2.6
 */
function mayflower_filter_widget_title( $title ) {
	if ( $title ) {
		return $title;
	} else {
		return '&nbsp';
	}
}
add_filter( 'widget_title', 'mayflower_filter_widget_title' );

/**
 * Output default Post Title if none is provided
 *
 * Filter Hook: the_title
 *
 * Filter 'the_title' to output '(Untitled)' if
 * no Post Title is provided
 *
 * @param string $title Title of Post.
 * @since   Oenology 2.0
 */
function mayflower_untitled_post( $title ) {
	if ( '' === $title ) {
		return apply_filters( 'mayflower_untitled_post_title', '<em>(' . __( 'Untitled', 'bc-douglas-fir' ) . ')</em>' );
	} else {
		return $title;
	}
}
add_filter( 'the_title', 'mayflower_untitled_post', 10, 1 );

/**
 * Remove default gallery shortcode inline styles
 *
 * Filter Hook: use_default_gallery_style
 *
 * Return false for the 'use_default_gallery_style'
 * filter, so that WordPress does not output
 * <style> tags and code for galleries in the document
 * body.
 *
 * @since   Oenology 2.2
 */
add_filter( 'use_default_gallery_style', '__return_false' );


/**
 * Add 'current_cat' class for single posts
 *
 * Filter Hook: wp_list_categories
 *
 * Filter 'wp_list_categories' to add a
 * "current-cat" CSS class declaration.
 *
 * @link    http://www.studiograsshopper.ch/code-snippets/dynamic-category-menu-highlighting-for-single-posts/ StudioGrasshopper
 *
 * @since   Oenology 2.0
 * @param string $output List of Categories.
 */
function mayflower_show_current_cat_on_single( $output ) {

	global $post;

	if ( is_singular( 'post' ) ) {

		$categories = wp_get_post_categories( $post->ID );

		foreach ( $categories as $catid ) {
			$cat = get_category( $catid );
			// Find cat-item-ID in the string.
			if ( preg_match( '#cat-item-' . $cat->cat_ID . '#', $output ) ) {
				$output = str_replace( 'cat-item-' . $cat->cat_ID, 'cat-item-' . $cat->cat_ID . ' current-cat', $output );
			}
		}
	}
	return $output;
}
// Hook current_cat function into 'wp_list_categories'.
add_filter( 'wp_list_categories', 'mayflower_show_current_cat_on_single' );

/**
 * Output optimized document titles
 *
 * Uses WordPress 4.1+ title framework
 *
 * @param array $title_parts Page title parts.
 * @global $post
 */
function mayflower_document_title_parts( $title_parts ) {
	global $post;

	if ( is_front_page() ) {
		$title_parts['tagline'] = '';
		$title_parts['site']    = __( 'Bellevue College' );
	}
	// Output custom title if available.
	$post_meta_data = get_post_custom( $post->ID ?? null );
	if ( isset( $post_meta_data['_seo_custom_page_title'][0] ) ) {
		$title_parts['title']   = $post_meta_data['_seo_custom_page_title'][0];
		$title_parts['tagline'] = '';
		$title_parts['site']    = '';
	}
	return $title_parts;
}

add_filter( 'document_title_parts', 'mayflower_document_title_parts', 10, 1 );
add_filter( 'document_title_separator', 'mayflower_document_title_separator', 10, 1 );

/**
 * Separator used in page titles
 *
 * @param string $mayflower_document_title_separator Separator string.
 */
function mayflower_document_title_separator( $mayflower_document_title_separator ) {
	return is_front_page() ? '@' : '::';
}

/**
 * Customize excerpts to include Gutenberg blocks, shortcode content and add a custom read more
 *
 * @param string $excerpt Page Excerpt Text.
 */
function mayflower_the_excerpt_override( $excerpt ) {
	$read_more = ' <a class="read-more" href="' . get_permalink() . '">' . __( '...more about ', 'bc-douglas-fir' ) . get_the_title() . '</a>';

	$excerpt_from_content = wpautop(
		wp_trim_words(
			preg_replace( '~(?:\[/?)[^/\]]+/?\]~s', '', get_the_content() ),
			55,
			$read_more
		)
	);

	// Returns excerpt from content if there is not a custom/manual excerpt.
	return has_excerpt() ? $excerpt : $excerpt_from_content;
}
add_filter( 'the_excerpt', 'mayflower_the_excerpt_override' );


/**
 * Disable default widgets we don't want to use in BC Douglas Fir Theme
 */
function mayflower_remove_default_widgets() {

	unregister_widget( 'WP_Widget_Calendar' );
	unregister_widget( 'WP_Widget_Search' );
	unregister_widget( 'WP_Widget_Meta' );
	unregister_widget( 'WP_Widget_Recent_Comments' );
	unregister_widget( 'WP_Widget_Pages' );
}
add_action( 'widgets_init', 'mayflower_remove_default_widgets' );


/**
 * Remove WP version number from head
 */
remove_action( 'wp_head', 'wp_generator' );

/**
 * Gutenberg Time!
 *
 * Add hooks for Gutenberg features
 *
 * Gutenberg beta plugin v2.0
 */

/**
 * Enqueue block editor canvas styles.
 *
 * Uses enqueue_block_assets so styles reach both the iframed and legacy
 * (non-iframe) block editor content documents. Guarded to admin only so
 * this sheet is not printed on the frontend.
 */
function mayflower_block_editor_styles() {
	if ( ! is_admin() ) {
		return;
	}

	wp_enqueue_style(
		'mayflower-block-editor-styles',
		get_theme_file_uri( 'css/block-editor.css' ),
		array(),
		wp_get_theme()->get( 'Version' ),
		'all'
	);
}
add_action( 'enqueue_block_assets', 'mayflower_block_editor_styles' );

/**
 * Disable blocks
 */
function mayflower_blacklist_blocks() {
	wp_enqueue_script(
		'mayflower-blacklist-blocks',
		get_theme_file_uri( 'js/blocks-blacklist.js', __FILE__ ),
		array( 'wp-blocks' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'enqueue_block_editor_assets', 'mayflower_blacklist_blocks' );



/**
 * Customize WordPress Visual Editor
 *
 * Add and change stylesheets and buttons in the
 * WP visual editor interface
 */

/**
 * Add theme stylesheets to Visual editor
 */
function mayflower_add_editor_styles() {
	$globals = new Globals();
	add_editor_style(
		array(
			$globals->url . 'c/g.css?=' . $globals->version,
			'style.css?=' . wp_get_theme()->get( 'Version' ),
			'css/custom-editor-style.css',
		)
	);
}
add_action( 'init', 'mayflower_add_editor_styles' );

/**
 * TinyMCE Changes
 */

/**
 * Show Kitchen Sink by default
 *
 * @param array $args All TinyMCE args.
 */
function mayflower_unhide_kitchensink( $args ) {
	$args['wordpress_adv_hidden'] = false;
	return $args;
}
add_filter( 'tiny_mce_before_init', 'mayflower_unhide_kitchensink' );

/**
 * Remove Address and H1 blocks from TinyMCE
 *
 * @param array $init Formats in TinyMCE.
 */
function mayflower_tinymce_buttons_remove( $init ) {
	// remove address and h1.
	$init['block_formats'] = 'Paragraph=p; Preformatted=pre; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6';
	return $init;
}
add_filter( 'tiny_mce_before_init', 'mayflower_tinymce_buttons_remove' );

/**
 * Remove text color selector
 *
 * @param array $buttons Buttons in TinyMCE.
 */
function mayflower_tinymce_buttons( $buttons ) {
	// Remove the text color selector.
	$remove = array( 'forecolor' );

	return array_diff( $buttons, $remove );
}
add_filter( 'mce_buttons_2', 'mayflower_tinymce_buttons' );

/**
 * Formats dropdown menu with block styles
 */

/**
 * Add Formats dropdown menu
 *
 * @param array $buttons Buttons in TinyMCE.
 */
function mayflower_mce_buttons_2( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}
add_filter( 'mce_buttons_2', 'mayflower_mce_buttons_2' );

/**
 * Add styles to the dropdown
 *
 * @param array $settings Styles available in TinyMCE.
 */
function mayflower_mce_before_init( $settings ) {

	$style_formats             = array(
		array(
			'title'   => 'Intro (.lead)',
			'block'   => 'p',
			'classes' => 'lead',
			'wrapper' => false,
		),
		array(
			'title'   => 'Alert (.alert-warning)',
			'block'   => 'div',
			'classes' => 'alert alert-warning',
			'wrapper' => true,
		),
		array(
			'title'   => 'Alert-Danger (.alert-danger)',
			'block'   => 'div',
			'classes' => 'alert alert-danger',
			'wrapper' => true,
		),
		array(
			'title'   => 'Alert-Info (.alert-info)',
			'block'   => 'div',
			'classes' => 'alert alert-info',
			'wrapper' => true,
		),
		array(
			'title'   => 'Alert-Success (.alert-success)',
			'block'   => 'div',
			'classes' => 'alert alert-success',
			'wrapper' => true,
		),
		array(
			'title'   => 'Well (.well)',
			'block'   => 'div',
			'classes' => 'well',
			'wrapper' => true,
		),
	);
	$settings['style_formats'] = json_encode( $style_formats );
	return $settings;
}
add_filter( 'tiny_mce_before_init', 'mayflower_mce_before_init' );

// Make this function pluggable.
if ( ! function_exists( 'mayflower_body_class_ia' ) ) {

	/**
	 * Assign global_nav_selection to body_class
	 *
	 * @param array $classes Body Classes.
	 */
	function mayflower_body_class_ia( $classes ) {

		// return the $classes array.
		return $classes;
	}
}
add_filter( 'body_class', 'mayflower_body_class_ia' );

/**
 * Register sidebars / widget areas
 */

/**
 * Register Sidebar Hook
 *
 * Allow plugins and themes to register additional
 * sidebars via the mayflower_register_sidebar hook
 */
function mayflower_register_sidebar() {
	do_action( 'mayflower_register_sidebar' );
}
add_action( 'widgets_init', 'mayflower_register_sidebar' );

/**
 * Register BC Douglas Fir Theme Sidebars
 *
 * Use hook to register mayflower sidebars
 */

/**
 * Top Global Widget Area - located just below the sidebar nav.
 */
function mayflower_register_top_global_sidebar() {
	register_sidebar(
		array(
			'name'          => __( 'Top Global Sidebar Widget Area', 'bc-douglas-fir' ),
			'id'            => 'top-global-widget-area',
			'description'   => __( 'This is the top global widget area. Items will appear on all pages throughout the web site.', 'bc-douglas-fir' ),
			'before_widget' => '<div class="wp-widget wp-widget-global %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title px-3">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'mayflower_register_sidebar', 'mayflower_register_top_global_sidebar', 2 );

/**
 * Static Page Widget Area - located just below the global nav on static pages.
 */
function mayflower_register_static_sidebar() {
	register_sidebar(
		array(
			'name'          => __( 'Static Page Sidebar Widget Area', 'bc-douglas-fir' ),
			'id'            => 'page-widget-area',
			'description'   => __( 'This is the static page widget area. Items will appear on all static pages.', 'bc-douglas-fir' ),
			'before_widget' => '<div class="wp-widget wp-widget-static %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title px-3">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'mayflower_register_sidebar', 'mayflower_register_static_sidebar', 4 );

/**
 * Blog Widget Area - located just below the global nav on blog pages.
 */
function mayflower_register_blog_sidebar() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar Widget Area', 'bc-douglas-fir' ),
			'id'            => 'blog-widget-area',
			'description'   => __( 'This is the blog widget area. Items will appear on all blog related pages.', 'bc-douglas-fir' ),
			'before_widget' => '<div class="wp-widget wp-widget-blog %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title px-3">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'mayflower_register_sidebar', 'mayflower_register_blog_sidebar', 6 );

/**
 * Bottom Global Widget Area - located just below the sidebar nav.
 */
function mayflower_register_bottom_global_sidebar() {
	register_sidebar(
		array(
			'name'          => __( 'Bottom Global Sidebar Widget Area', 'bc-douglas-fir' ),
			'id'            => 'global-widget-area',
			'description'   => __( 'This is the bottom global widget area. Items will appear on all pages throughout the web site.', 'bc-douglas-fir' ),
			'before_widget' => '<div class="wp-widget wp-widget-global %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title px-3">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'mayflower_register_sidebar', 'mayflower_register_bottom_global_sidebar', 8 );

/**
 * Hide Widget Title Bar when Empty
 *
 * @param string $output Title content.
 */
function mayflower_widget_empty_title( $output = '' ) {
	if ( '&nbsp' === $output ) {
		return '';
	}
	return $output;
}
add_filter( 'widget_title', 'mayflower_widget_empty_title' );

/**
 * Add meta tag to force IE to Edge mode in dashboard
 */
function mayflower_force_ie_edge_admin() {
	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
}
add_action( 'admin_head', 'mayflower_force_ie_edge_admin' );

/**
 * Add 'active' class to active menu items
 *
 * @param array  $classes Nav Classes.
 * @param string $item Not sure what this is for.
 */
function mayflower_nav_active_class( $classes, $item ) {
	if ( in_array( 'current-menu-item', $classes ) || preg_grep( '/^current-.*-ancestor$/i', $classes ) ) {
			$classes[] = 'active ';
	}

	// Apply active class on blog post parent.
	if ( is_singular( 'post' ) ) {
		if ( in_array( 'current_page_parent', $classes ) ) {
			$classes[] = 'active ';
		}
	}

	// Apply 'active' style to any menu item with the added class of 'staff' to highlight staff parent.
	if ( is_singular( 'staff' ) ) {
		if ( in_array( 'staff', $classes ) ) {
			$classes[] = 'active ';
		}
	}

	return $classes;
}
add_filter( 'nav_menu_css_class', 'mayflower_nav_active_class', 10, 2 );

/**
 * Load Scripts and Styles
 */

/**
 * Async/Defer load
 *
 * Allow insertion of async / defer tags to support loading outside scripts
 *
 * @link https://ikreativ.com/async-with-wordpress-enqueue/ Adapted from this!
 * @param string $url URL of script.
 */
function mayflower_defer_async_scripts( $url ) {
	if ( strpos( $url, '#asyncload' ) ) {
		if ( is_admin() ) {
			return str_replace( '#asyncload', '', $url );
		} else {
			return str_replace( '#asyncload', '', $url ) . "' async='async";
		}
	} elseif ( strpos( $url, '#deferload' ) ) {
		if ( is_admin() ) {
			return str_replace( '#deferload', '', $url );
		} else {
			return str_replace( '#deferload', '', $url ) . "' defer='defer";
		}
	} elseif ( strpos( $url, '#asyncdeferload' ) ) {
		if ( is_admin() ) {
			return str_replace( '#asyncdeferload', '', $url );
		} else {
			return str_replace( '#asyncdeferload', '', $url ) . "' defer='defer' async='async";
		}
	} else {
		return $url;
	}
}
add_filter( 'clean_url', 'mayflower_defer_async_scripts', 11, 1 );

/**
 * Enqueue BC Douglas Fir Theme scripts and styles
 */
function mayflower_scripts() {
	$globals = new Globals();
	wp_enqueue_style( 'globals', $globals->url . 'c/g.css', null, $globals->version, 'screen' );
	wp_enqueue_style( 'globals-print', $globals->url . 'c/p.css', null, $globals->version, 'print' );
	wp_enqueue_style( 'bc-douglas-fir', get_stylesheet_uri(), null, wp_get_theme()->get( 'Version' ) );

	wp_enqueue_script( 'jquery' );
	// wp_enqueue_script( 'globals-head', $globals->url . 'j/ghead-full.min.js', array( 'jquery' ), $globals->version, false );
	wp_enqueue_script( 'globals', $globals->url . 'j/gfoot-full.min.js', array( 'jquery' ), $globals->version, true );
	wp_enqueue_script( 'menu', get_template_directory_uri() . '/js/menu.js#deferload', array( 'jquery' ), wp_get_theme()->get( 'Version' ), true );

	wp_enqueue_script( 'youvisit', 'https://www.youvisit.com/tour/Embed/js2#asyncdeferload', null, 'auto', true );

	/**
	 * Search script
	 */
	if ( bc_douglas_fir_get_option( 'limit_searchform_scope' ) ) {
		$bc_douglas_fir_options      = bc_douglas_fir_get_options();
		$limit_searchform_scope = $bc_douglas_fir_options['limit_searchform_scope'];
		$search_url_default     = 'https://www.bellevuecollege.edu/search-results/';
		$search_url             = ( $limit_searchform_scope && ( '' !== $bc_douglas_fir_options['custom_search_url'] ) ) ?
										$bc_douglas_fir_options['custom_search_url'] : $search_url_default;
		$search_field_id        = $limit_searchform_scope ? 'college-search-field-custom' : 'college-search-field';
		$filter_value           = bc_douglas_fir_trimmed_url();
		$search_api_key         = '' !== $bc_douglas_fir_options['custom_search_api_key'] ? $bc_douglas_fir_options['custom_search_api_key'] :
									'YUFwdxQ6-Kaa9Zac4rpb'; // <-- Default API Key
		$search_query_peram     = 'txtQuery';
		$filter_peram           = 'site[]'; // hardcoded default.

		wp_enqueue_script( 'search', get_template_directory_uri() . '/js/search.js#deferload', array( 'jquery', 'globals' ), wp_get_theme()->get( 'Version' ), true );
		wp_add_inline_script(
			'search',
			'var limit_searchform_scope =' . esc_attr( $limit_searchform_scope ) .
				'; var search_api_key ="' . esc_attr( $search_api_key ) .
				'"; var filter_value ="' . esc_attr( $filter_value ) .
				'"; var search_field_id ="' . esc_attr( $search_field_id ) .
				'"; var custom_search_url ="' . esc_attr( $bc_douglas_fir_options['custom_search_url'] ) .
				'"; var search_url_default ="' . esc_attr( $search_url_default ) .
				'";',
			'before'
		);
	}

	if ( current_user_can( 'edit_posts' ) ) {
		wp_enqueue_script( 'a11y-warnings-js', get_template_directory_uri() . '/js/a11y-warnings.js#deferload', array( 'jquery' ), time(), true );
		wp_enqueue_style( 'a11y-warnings-css', get_template_directory_uri() . '/css/a11y-warnings.css', null, time() );
	}

	if ( is_page_template( 'page-nav-page-fluid-grid.php' ) ) {
		wp_enqueue_script( 'imagesloaded' );
		wp_enqueue_script( 'masonry' );
		wp_enqueue_script( 'page-nav-page-fluid-grid', get_template_directory_uri() . '/js/page-nav-page-fluid-grid.js', array( 'imagesloaded', 'masonry' ), wp_get_theme()->get( 'Version' ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'mayflower_scripts' );

/**
 * Enqueue Custom Admin Page Stylesheet
 */
function mayflower_enqueue_admin_style() {

	// define admin stylesheet.
	$admin_handle     = 'mayflower_admin_stylesheet';
	$admin_stylesheet = get_template_directory_uri() . '/css/mayflower-admin.css';

	wp_enqueue_style( $admin_handle, $admin_stylesheet, '', '1' );
}
add_action( 'admin_print_styles-appearance_page_mayflower-settings', 'mayflower_enqueue_admin_style', 11 );

/**
 * Enqueue Dashboard Stylesheet
 *
 * Used for Staff and Slider custom post types
 *
 * @param string $hook Style Path.
 */
function mayflower_dashboard_styles( $hook ) {
	$css_path = get_template_directory_uri() . '/css/dashboard.css';
	if ( 'edit.php?post_type=staff' !== $hook ) {
		wp_register_style( 'mayflower_dashboard', $css_path, null, '1' );
		wp_enqueue_style( 'mayflower_dashboard' );
	}
}
add_action( 'admin_enqueue_scripts', 'mayflower_dashboard_styles' );


/**
 * Add responsive classes to images
 *
 * @param string $html Image html.
 */
function mayflower_bootstrap_responsive_images( $html ) {
	$classes = 'img-fluid'; // separated by spaces, e.g. 'img image-link'.

	// check if there are already classes assigned to the anchor.
	if ( preg_match( '/<img.*? class="/', $html ) ) {
		$html = preg_replace( '/(<img.*? class=".*?)(".*?\/>)/', '$1 ' . $classes . ' $2', $html );
	} else {
		$html = preg_replace( '/(<img.*?)(\/>)/', '$1 class="' . $classes . '" $2', $html );
	}
	return $html;
}
add_filter( 'the_content', 'mayflower_bootstrap_responsive_images', 10 );
add_filter( 'post_thumbnail_html', 'mayflower_bootstrap_responsive_images', 10 );

/**
 * Alt Text Verification
 *
 * Taken from WP Accessibility Plugin https://wordpress.org/plugins/wp-accessibility/ Version 1.4.6
 *
 * @param array $form_fields All form fields.
 * @param post  $post post data.
 */
function wpa_insert_alt_verification( $form_fields, $post ) {
	$mime = get_post_mime_type( $post->ID );
	if ( 'image/jpeg' === $mime || 'image/png' === $mime || 'image/gif' === $mime ) {
		$no_alt                = get_post_meta( $post->ID, '_no_alt', true );
		$alt                   = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );
		$checked               = checked( $no_alt, 1, false );
		$form_fields['no_alt'] = array(
			'label' => __( 'Decorative', 'bc-douglas-fir' ),
			'input' => 'html',
			'value' => 1,
			'html'  => "<input name='attachments[$post->ID][no_alt]' id='attachments-$post->ID-no_alt' value='1' type='checkbox' aria-describedby='wpa_help' $checked /> <em class='help' id='wpa_help'>" . __( '<strong>Image is purely decorative.</strong> This will strip alt text from the image, and should not be used if image contributes to page content.', 'bc-douglas-fir' ) . '</em>',
		);
	}
	return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'wpa_insert_alt_verification', 10, 2 );


/**
 * Save Alt Verification
 *
 * @param post       $post Post Data.
 * @param attachment $attachment Attachment info.
 */
function wpa_save_alt_verification( $post, $attachment ) {
	if ( isset( $attachment['no_alt'] ) ) {
		update_post_meta( $post['ID'], '_no_alt', 1 );
	} else {
		delete_post_meta( $post['ID'], '_no_alt' );
	}
	return $post;
}
add_filter( 'attachment_fields_to_save', 'wpa_save_alt_verification', 10, 2 );
