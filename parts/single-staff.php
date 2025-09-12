<?php
/**
 * Single Staff Page
 *
 * @package BC Douglas Fir Theme
 */

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post(); ?>
	<div class="media flex-column flex-md-row">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail(
					'medium',
					array(
						'class' => 'media-object img-fluid img-thumbnail',
						'alt'   => the_title_attribute(
							array(
								'after' => ' Picture',
								'echo'  => false,
							)
						),
					)
				);
			} else {
				echo '<img class="media-object img-fluid img-thumbnail" alt="" src="' . esc_url( get_stylesheet_directory_uri() ) . '/img/thumbnail-default.png" />';
			}
		?>

			<div class="media-body">
				<?php $post_meta_data = get_post_custom( $post->ID ); ?>
				<h2 class="my-0"><?php the_title(); ?></h2>

				<?php if ( isset( $post_meta_data['_staff_position'][0] ) ) { ?>
					<h3 class="mt-0"><?php echo esc_attr( $post_meta_data['_staff_position'][0] ); ?></h3>
				<?php } ?>

				<ul>
					<?php if ( isset( $post_meta_data['_staff_email'][0] ) ) { ?>
						<li>
							<strong>Email: </strong>
							<a href="mailto:<?php echo esc_attr( $post_meta_data['_staff_email'][0] ); ?>"><?php echo esc_attr( $post_meta_data['_staff_email'][0] ); ?></a>
						</li>
					<?php } ?>

					<?php if ( isset( $post_meta_data['_staff_phone'][0] ) ) { ?>
						<li>
							<strong>Phone: </strong>
							<?php echo esc_attr( $post_meta_data['_staff_phone'][0] ); ?>
						</li>
					<?php } ?>

					<?php if ( isset( $post_meta_data['_staff_office_location'][0] ) ) { ?>
						<li>
							<strong>Office Location: </strong>
							<?php echo esc_attr( $post_meta_data['_staff_office_location'][0] ); ?>
						</li>
					<?php } ?>

					<?php if ( isset( $post_meta_data['_staff_office_hours'][0] ) ) { ?>
						<li>
							<strong>Office Hours: </strong>
							<?php echo esc_attr( $post_meta_data['_staff_office_hours'][0] ); ?>
						</li>
					<?php } ?>

				</ul>
				<?php if ( isset( $post_meta_data['_staff_appt_link'][0] ) ) { ?>
					<a class="btn btn-info" href="<?php echo esc_attr( $post_meta_data['_staff_appt_link'][0] ); ?>">Schedule an Appointment</a>
				<?php } ?>

			</div><!-- media-body -->

		</div><!-- media -->
		<?php if ( ! empty( $post->post_content ) ) { ?>
			<h3>Biography:</h3>
			<?php the_content(); ?>
		<?php } ?>

	<?php endwhile; ?>

	<?php wp_reset_postdata();
endif; ?>
