<?php
/**
 * Template Name: Navigation Page (Fluid Grid)
 *
 * @package BC Douglas Fir Theme
 */

get_header();
?>
<?php if ( bc_douglas_fir_has_active_sidebar() ) : ?>
	<div class="col-md-9 order-1 <?php echo 'sidebar-content' === bc_douglas_fir_get_option( 'default_layout' ) ? '' : 'order-md-0'; ?>">
<?php else : // Full Width Container. ?>
	<div class="col-md-12">
<?php endif; ?>

		<?php get_template_part( 'parts/page-nav-page-fluid-grid' ); ?>

	</div>
<?php if ( bc_douglas_fir_has_active_sidebar() ) : ?>
	<?php
	get_sidebar();
endif;
?>

<?php get_footer(); ?>
