<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package BC Douglas Fir Theme
 * @since Twenty Twelve 1.0
 */

get_header(); ?>


	<?php
		$error_messages = array(
			'It was here, we promise!',
			'We\'ve led you astray...',
			'Oh no!',
			'You\'ve found a broken link!',
		);

		// And then randomly choose a line.
		$chosen_error = wptexturize( $error_messages[ mt_rand( 0, count( $error_messages ) - 1 ) ] );
		?>
	<div class="col-12">
		<div class="jumbotron">
			<h1 class="display-4">
				<span class="badge badge-warning badge-pill text-monospace">404</span>
				Page not Found
			</h1>
			<p class="lead"><?php echo wp_kses_post( $chosen_error ); ?></p>
			<hr />
			<h2>Below are a few things you can try to find it:</h2>
			<ol>
				<li>If you typed the web address, double-check if you typed it correctly.</li>
				<li>Try searching for it.<?php get_search_form(); ?></li>
				<li>Browse our <a href="https://www.bellevuecollege.edu/about-us/departments-offices/"> Departments &amp; Offices Directory</a>.</li>
				<li>Use some of the links on this page.</li>
				<li >Click the <a href="javascript:history.go(-1)">Back</a> button and try another link.</li>
			</ol>
		</div>
	</div>


<?php
get_footer();
