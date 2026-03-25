<?php
/**
 * BC Douglas Fir Theme Staff Output
 *
 * @package BC Douglas Fir Theme
 */

// Start showing staff list.
$loop = new WP_Query(
	array(
		'post_type'      => 'staff',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);


$bc_douglas_fir_options = bc_douglas_fir_get_options();
if ( 'list-view' === $bc_douglas_fir_options['staff_layout'] && $bc_douglas_fir_options['staff_toggle'] ) {
	?>
	<div class="staff-details">
		<?php
		while ( $loop->have_posts() ) :
			$loop->the_post();
			// Load Post Metatadata.
			$post_meta_data = get_post_custom( get_the_ID() );
			?>

		<div class="media">
			<a class="align-self-start mr-3" href="<?php the_permalink(); ?>">
				<?php
				if ( true === $bc_douglas_fir_options['staff_picture_toggle'] ) {
					if ( has_post_thumbnail() ) {
						the_post_thumbnail(
							'thumbnail',
							array(
								'class' => 'img-fluid img-thumbnail',
								'alt'   => the_title_attribute(
									array(
										'after'  => "'s profile",
										'echo'   => false,
									)
								),
							)
						);
					} else {
						echo '<img class="img-fluid img-thumbnail a11y-decorative" alt="' . the_title_attribute( array( 'echo' => false ) ) . '\'s profile" src="' . esc_url( get_stylesheet_directory_uri() ) . '/img/thumbnail-default.png" />';
					}
				}
				?>
			</a>

			<div class="media-body">
				<h2 class="mb-0"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

				<?php if ( isset( $post_meta_data['_staff_position'][0] ) ) { ?>
					<h3 class="text-secondary"><?php echo esc_attr( $post_meta_data['_staff_position'][0] ); ?></h3>
				<?php } ?>

				<ul class="staff-details-list">
					<?php if ( isset( $post_meta_data['_staff_email'][0] ) ) { ?>
						<li>
							<strong>Email: </strong>
							<a href="mailto:<?php echo esc_attr( $post_meta_data['_staff_email'][0] ); ?>"><?php echo esc_attr( $post_meta_data['_staff_email'][0] ); ?></a>
						</li>
					<?php } ?>

					<?php
					if ( true === $bc_douglas_fir_options['staff_phone_toggle'] ) {
						if ( isset( $post_meta_data['_staff_phone'][0] ) ) {
							?>
							<li>
								<strong>Phone: </strong>
								<?php echo esc_attr( $post_meta_data['_staff_phone'][0] ); ?>
							</li>
							<?php
						}
					}
					?>

					<?php
					if ( true === $bc_douglas_fir_options['staff_location_toggle'] ) {
						if ( isset( $post_meta_data['_staff_office_location'][0] ) ) {
							?>
							<li>
								<strong>Office Location: </strong>
								<?php echo esc_attr( $post_meta_data['_staff_office_location'][0] ); ?>
							</li>
							<?php
						}
					}
					?>

					<?php
					if ( true === $bc_douglas_fir_options['staff_hours_toggle'] ) {
						if ( isset( $post_meta_data['_staff_office_hours'][0] ) ) {
							?>
						<li>
							<strong>Office Hours: </strong>
							<?php echo esc_attr( $post_meta_data['_staff_office_hours'][0] ); ?>
						</li>
							<?php
						}
					}
					?>

				</ul>
				<?php
				if ( true === $bc_douglas_fir_options['staff_bio_toggle'] ) {
					if ( '' === get_post()->post_content ) {
						if ( true === $bc_douglas_fir_options['staff_more_toggle'] ) {
							?>
						<p>
							<a href="<?php the_permalink(); ?>">...more about <?php the_title(); ?></a>
						</p>
							<?php
						}
					} else { // post content not empty.
						?>
					<h3 class="staff-biography">Biography:</h3>
						<?php
						$content_array = explode( ' ', get_the_content() );
						$content_count = count( $content_array );
						echo wp_kses_post( the_excerpt() ?? '' );

						if ( $content_count < 55 ) { // echo excerpt and 'more' link if content is less than 55 words.
							if ( true === $bc_douglas_fir_options['staff_more_toggle'] ) {
								?>
							<p><a href="<?php the_permalink(); ?>">...more about <?php the_title(); ?></a></p>
								<?php
							}
						}
					}
				} else { // staff_bio_toggle == false.
					if ( true === $bc_douglas_fir_options['staff_more_toggle'] ) {
						?>
					<p>
						<a href="<?php the_permalink(); ?>">...more about <?php the_title(); ?></a>
					</p>
						<?php
					}
				}
				?>
			</div><!-- media-body -->

		</div><!-- media -->
		<hr />
		<?php endwhile; ?>

	</div><!-- Staff Details -->

<?php } elseif ( 'grid-view' === $bc_douglas_fir_options['staff_layout'] && $bc_douglas_fir_options['staff_toggle'] ) { ?>
	<div class="row">
	<?php
	// ########################
	// Start showing staff grid
	// ########################

	while ( $loop->have_posts() ) :
		$loop->the_post();
		// Load Post Metatadata.
		$post_meta_data = get_post_custom( get_the_ID() );
		?>

		<div class="col-lg-4 col-sm-6 staff-details">
			<div class="card text-center">
				<?php
				if ( true === $bc_douglas_fir_options['staff_picture_toggle'] ) {
					if ( has_post_thumbnail() ) {
						?>
						<a href="<?php the_permalink(); ?>">
							<?php
							echo get_the_post_thumbnail(
								get_the_ID(),
								'medium',
								array(
									'class' => 'card-img-top',
									'alt'   => the_title_attribute(
										array(
											'after' => '\'s profile',
											'echo'  => false,
										)
									),
								)
							);
							?>
						</a>
						<?php
					} else {
						echo '<img class="card-img-top a11y-decorative" alt="" src="' . esc_url( get_stylesheet_directory_uri() ) . '/img/thumbnail-default.png" />';
					}
				}
				?>

				<div class="card-body">
					<h2 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php if ( isset( $post_meta_data['_staff_position'][0] ) ) { ?>
						<p class="card-subtitle text-secondary">
							<?php echo esc_attr( $post_meta_data['_staff_position'][0] ); ?>
						</p>
					<?php } ?>

				</div><!-- caption staff-details staff-details-grid-top-->
				<?php
				if ( true === $bc_douglas_fir_options['staff_more_toggle'] ) {
					?>
					<div class="card-footer text-secondary">
						<small><a class="text-sm" href="<?php the_permalink(); ?>">More Details<span class="sr-only"> about <?php the_title(); ?></span></a></small>
					</div>
				<?php } ?>
			</div><!--card -->
		</div> <!-- end of col-md-4 -->
		<?php
		endwhile;
	?>
	</div>
	<?php
} elseif ( ! $bc_douglas_fir_options['staff_toggle'] ) {
	echo '<!--Staff functionality is not currently enabled on this website-->';
}

wp_reset_postdata();
