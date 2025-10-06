<?php
/**
 * BC Douglas Fir Theme Lite Search Form
 *
 * Complete search form used in the BC Douglas Fir Theme lite header
 *
 * @package BC Douglas Fir Theme
 */

global $bc_douglas_fir_options;

// Load options if they are not already present.
if ( ! ( is_array( $bc_douglas_fir_options ) ) ) {
	$bc_douglas_fir_options = bc_douglas_fir_get_options();
}

// Set variables for ease of use/configuration.
$limit_searchform_scope = $bc_douglas_fir_options['limit_searchform_scope'];
$search_url_default     = 'https://www.bellevuecollege.edu/search-results/';
$search_url         = ( $limit_searchform_scope && ( '' !== $bc_douglas_fir_options['custom_search_url'] ) ) ?
								$bc_douglas_fir_options['custom_search_url'] : $search_url_default;
$search_field_id    = $limit_searchform_scope ? 'college-search-field-custom' : 'college-search-field';
$filter_value       = ( '' !== $bc_douglas_fir_options['custom_search_scope'] ) ? $bc_douglas_fir_options['custom_search_scope'] : bc_douglas_fir_trimmed_url();
$search_api_key     = '' !== $bc_douglas_fir_options['custom_search_api_key'] ? $bc_douglas_fir_options['custom_search_api_key'] :
							'YUFwdxQ6-Kaa9Zac4rpb'; // <-- Default API Key.
$search_query_peram = 'query';
$filter_peram       = 'scope'; // Hardcoded default.

?>
<form action="<?php echo esc_url( $search_url ); ?>" method="get" class="form-search" id="bc-search">
	<label class="sr-only" for="<?php echo esc_attr( $search_field_id ); ?>">Search</label>
	<div class="input-group" role="search">
		<input type="text" name="<?php echo esc_attr( $search_query_peram ); ?>" class="form-control" maxlength="255" autocomplete="off" id="<?php echo esc_attr( $search_field_id ); ?>"  />

		<?php if ( $limit_searchform_scope ) : ?>
			<?php
			if ( '' === $bc_douglas_fir_options['custom_search_url'] ) :
				// If there is NOT a custom search URL, output filter peram and filter js.
				?>
				<input type="hidden" class="college-search-filter" name="<?php echo esc_attr( $filter_peram ); ?>" value="<?php echo esc_attr( $filter_value ); ?>">
			<?php endif; // no custom search url set. ?>
		<?php endif; // limit searchform scope. ?>
		<div class="input-group-append">
			<button type="submit" class="btn btn-light" id="college-search-submit">Search</button>
			<?php if ( $limit_searchform_scope ) : ?>
				<button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="sr-only">More Search Options</span>
				</button>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?php echo esc_url( $search_url ); ?>" id="college-search-site-link">Search <?php bloginfo( 'name' ); ?></a>
					<a class="dropdown-item" href="<?php echo esc_url( $search_url_default ); ?>" id="college-search-all-link">Search Bellevue College <i class="fa-solid fa-up-right-from-square"></i></a>
				</div>
			<?php endif; // limit searchform scope. ?>
		</div>
	</div>
</form>
