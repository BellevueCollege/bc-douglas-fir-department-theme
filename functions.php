<?php
/**
 * BC Douglas Fir Theme Functions
 *
 * All the good stuff (or at least the require to where the good stuff is...)
 *
 * @package BC Douglas Fir Theme
 */

/**
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) || die( 'Sorry, no direct access allowed' );

/*
 * Load theme options framework
 *
 * This legacy code is mainly from the Oenology theme.
 * TODO: Define new organizational schema and migrate content of functions.php
 *
 * theme-setup.php - sets theme functionality
 * wordpress-hooks.php - filters and functionality changes
 * plugin-hooks.php - filters and functionality changes for first- and third-party plugins
 * options-admin.php - defines BC Douglas Fir Theme Admin Only option page -
 *					   consider migrating to customizer
 * options.php - theme options definition
 * options-customizer.php - translate options.php to customizer
 * network-options.php - Network Admin options pane
 */

/**
 * Theme Setup Hooks
 */
require get_template_directory() . '/inc/functions/theme-setup.php';

/**
 * Customizer Setup
 */
require get_template_directory() . '/inc/functions/options-customizer.php';

/*
* Load Options into Variable
*
* There should be a better way of doing this, but this needs to be loaded up
* at this point, so that it can be used globally within the next includes
*/
$bc_douglas_fir_options = bc_douglas_fir_get_options();

/**
 * Globals Class and Functions
 */
require get_template_directory() . '/inc/functions/globals.php';

/**
 * WordPress Hooks used to configure WordPress for BC Douglas Fir Theme
 */
require get_template_directory() . '/inc/functions/wordpress-hooks.php';

/**
 * Plugin Hooks used to configure various Plugins for BC Douglas Fir Theme
 */
require get_template_directory() . '/inc/functions/plugin-hooks.php';


/**
 * Constants used by Classes shortcode
 */
define( 'CLASSESURL', '//www.bellevuecollege.edu/classes/All/' );
define( 'PREREQUISITEURL', '//www.bellevuecollege.edu/transfer/prerequisites/' );

/**
 * Load Bootstrap Navwalker (used for menus)
 */
require_once get_template_directory() . '/inc/wp-bootstrap-navwalker-4.0.2/class-wp-bootstrap-navwalker.php';

/**
 * Load Embedded Plugins
 *
 * These files provide plugin-like functionality embedded within the theme.
 *
 * @since 1.0
 */

/**
 * Slider
 */
if ( true === $bc_douglas_fir_options['slider_toggle'] ) {
	if ( file_exists( get_template_directory() . '/inc/slider/slider.php' ) ) {
		require get_template_directory() . '/inc/slider/slider.php';
	}
}

/**
 * Staff
 */
if ( true === $bc_douglas_fir_options['staff_toggle'] ) {
	if ( file_exists( get_template_directory() . '/inc/staff/staff.php' ) ) {
		require get_template_directory() . '/inc/staff/staff.php';
	}
}

/**
 * SEO Post Fields
 */
if ( file_exists( get_template_directory() . '/inc/seo/seo.php' ) ) {
	require get_template_directory() . '/inc/seo/seo.php';
}

/**
 * Course Description Shortcode
 */
if ( file_exists( get_template_directory() . '/inc/course-descriptions/course-descriptions.php' ) ) {
	require get_template_directory() . '/inc/course-descriptions/course-descriptions.php';
}


// Make this function pluggable.
if ( ! function_exists( 'bc_douglas_fir_pagination' ) ) {
	/**
	 * Custom Pagination
	 *
	 * Output pagination using Globals/BC Douglas Fir Theme styles.
	 * Function is pluggable and can be over-ridden from child themes.
	 */
	function bc_douglas_fir_pagination() {
		$big = 999999999; // need an unlikely integer.

		$paginated_links = paginate_links(
			array(
				'base'               => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format'             => '?paged=%#%',
				'current'            => max( 1, get_query_var( 'paged' ) ),
				'type'               => 'array',
				'prev_text'          => '<i class="fas fa-chevron-left" aria-hidden="true"></i><span class="sr-only">Previous Page</span>',
				'next_text'          => '<i class="fas fa-chevron-right" aria-hidden="true"></i><span class="sr-only">Next Page</span>',
				'before_page_number' => '<span class="sr-only">Page</span>',
			)
		);
		// Output Pagination.
		if ( $GLOBALS['wp_query']->max_num_pages > 1 ) { ?>
			<nav aria-label="Archive Pagination">
				<ul class="pagination justify-content-center">
					<?php
					foreach ( $paginated_links as $link ) {
						// Check if 'Current' class appears in string.
						$is_current = strpos( $link, 'current' );
						if ( false === $is_current ) {
							echo '<li class="page-item">';
							echo $link;
							echo '</li>';
						} else {
							echo '<li class="page-item active">';
							echo $link;
							echo '</li>';
						}
					}
					?>
				</ul>
			</nav>
			<?php
		}
	}
}


/**
 * 'Is Blog' function
 *
 * Returns true if the current page is a blog page
 * Filterable via bc_douglas_fir_is_blog
 *
 * @return boolean $output True if is a blog page, else False.
 */
function bc_douglas_fir_is_blog() {
	$output = false;

	if ( is_home() || is_archive() || is_singular( 'post' ) || is_post_type_archive( 'post' ) ) {
		$output = true;
	} else {
		$output = false;
	}

	/**
	 * Filter which pages are considered blogs.
	 *
	 * @param boolean $output.
	 */
	$output = apply_filters( 'bc_douglas_fir_is_blog', $output );

	// Return filtered output.
	return $output;
}

/**
 * Has Active Sidebar function
 *
 * Check if sidebar widgets are present.
 * Filterable via bc_douglas_fir_active_sidebar
 *
 * @return boolean  True if current page has an active sidebar.
 */
function bc_douglas_fir_has_active_sidebar() {
	$sidebar_is_active = false;

	// Default functionality.
	if ( bc_douglas_fir_is_blog() ) {
		if ( is_active_sidebar( 'top-global-widget-area' ) ||
			is_active_sidebar( 'blog-widget-area' ) ||
			is_active_sidebar( 'global-widget-area' ) ) {
			$sidebar_is_active = true;
		} else {
			$sidebar_is_active = false;
		}
	} else {
		if ( is_active_sidebar( 'top-global-widget-area' ) ||
			is_active_sidebar( 'page-widget-area' ) ||
			is_active_sidebar( 'global-widget-area' ) ) {
			$sidebar_is_active = true;
		} else {
			$sidebar_is_active = false;
		}
	}

	// Disable sidebar if page template is full-width.
	if ( is_page_template( 'page-full-width.php' ) ) {
		$sidebar_is_active = false;
	}

	/**
	 * Add bc_douglas_fir_active_sidebar filter
	 *
	 * Allows plugins and themes to override
	 * active sidebar state
	 *
	 * @param boolean $sidebar_is_active.
	 */
	$sidebar_is_active = apply_filters( 'bc_douglas_fir_active_sidebar', $sidebar_is_active );

	return $sidebar_is_active;
}


/**
 * Display Sidebar hook.
 *
 * Hooks in above Static in sidebar.php. Allow plugins or child themes to
 * add widgets to the sidebar, above other widget areas.
 */
function bc_douglas_fir_display_sidebar() {
	do_action( 'bc_douglas_fir_display_sidebar' );
}

/**
 * Is Multisite Home function
 *
 * Return true if on the multisite root homepage
 * Filterable via bc_douglas_fir_is_multisite_home
 *
 * @return boolean $output True if on the Multisite homepage, else False.
 */
function is_multisite_home() {
	$output = false;

	if ( is_main_site() && is_front_page() ) {
		$output = true;
	} else {
		$output = false;
	}

	/**
	 * Filter Is Multisite Home
	 *
	 * @param boolean $output.
	 */
	$output = apply_filters( 'bc_douglas_fir_is_multisite_home', $output );

	// Return filtered output.
	return $output;
}

/**
 * BC Douglas Fir Theme Trimmed URL function
 *
 * Return trimmed URL (for example, www.bellevuecollege.edu/sample ).
 * Used for Swiftype.
 *
 * @link https://stackoverflow.com/a/4357691 Inspiration for this class.
 * @link https://github.com/BellevueCollege/bc-st-search-client Primary plugin that uses this.
 * @return string $output Trimmed URL of current site.
 */
function bc_douglas_fir_trimmed_url() {
	$site_url = get_site_url( null, '', 'https' );
	$parsed   = wp_parse_url( $site_url );
	$output   = $parsed['host'] . $parsed['path'] ?? '';

	/**
	 * Filter BC Douglas Fir Theme Trimmed URL
	 *
	 * @param string $output Trimmed URL.
	 */
	$output = apply_filters( 'bc_douglas_fir_trimmed_url', $output );

	// Return filtered output.
	return $output;
}


/**
 * Set $bc_douglas_fir_brand variable
 *
 * Used in page templates.
 * TODO: move to function
 */
$bc_douglas_fir_brand_css = '';

$bc_douglas_fir_brand_css = 'globals-lite';



/**
 * BC Douglas Fir Theme CPT Update Post Order action hook
 *
 * Save post order on custom post order page used by Staff and Slider
 */
function bc_douglas_fir_cpt_update_post_order() {

	$post_type = isset( $_POST['postType'] ) ? wp_unslash( $_POST['postType'] ) : null;
	$order     = isset( $_POST['order'] ) ? wp_unslash( $_POST['order'] ) : null;

	/**
	*    Expect: $sorted = array(
	*                menu_order => post-XX
	*            );
	*/
	foreach ( $order as $menu_order => $post_id ) {
		$post_id    = intval( str_ireplace( 'post-', '', $post_id ) );
		$menu_order = intval( $menu_order );
		wp_update_post(
			array(
				'ID'         => $post_id,
				'menu_order' => $menu_order,
			)
		);
	}
	die( '1' );
}
add_action( 'wp_ajax_bc_douglas_fir_cpt_update_post_order', 'bc_douglas_fir_cpt_update_post_order' );

/**
 * Sitewide Notice
 */
function mayflower_sitewide_notice() {
	if ( class_exists( 'MFSN' ) && MFSN::active() ) {
		?>
		<div class="sitewide-notice container">
			<div class="alert alert-danger">
				<?php MFSN::display(); ?>
			</div>
		</div>
		<?php
	}
}

/**
 * Customize Default Editoria11y Plugin Settings
 */
add_filter( 'ed11y_default_options', function ( $options ) {

	// Ignore ACF interfaces that appear in the editor
	$options['ed11y_ignore_elements'] .= ', .acf-block-fields .acf-table, .acf-block-fields .acf-row, .acf-block-fields a';
	
	// Ignore editoria11y decorative images
	$options['ed11y_ignore_elements'] .= ', .a11y-decorative';

	// Disable content auditing within my-calendar plugin, as it's not user-fixable
	$options['ed11y_ignore_elements'] .= ', #my-calendar, #my-calendar *, .mc-main, .mc-main *';

	return $options;
} );