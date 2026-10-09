<?php defined( 'ABSPATH' ) || exit; ?>
<section class="section intro" id="about">
	<div class="wrap wrap--wide">
		<div class="intro__text reveal">
			<p class="eyebrow"><?php esc_html_e( 'Your health in the best hands', 'medcentrum' ); ?></p>
			<h2 class="section__title"><?php esc_html_e( 'A medical centre in the heart of Bratislava', 'medcentrum' ); ?></h2>
			<p class="lead"><?php esc_html_e( 'MedCentrum is a modern medical facility that brings general practice, specialists, diagnostics and a laboratory together under one roof. Book your examination online for a specific day and time – no phone calls and no time in the waiting room.', 'medcentrum' ); ?></p>
			<a class="btn btn--primary" href="<?php echo esc_url( medcentrum_section_url( 'contact' ) ); ?>"><?php esc_html_e( 'More about our locations', 'medcentrum' ); ?></a>
		</div>

		<ul class="stats">
			<?php foreach ( medcentrum_stats() as $stat ) : ?>
				<li class="stat reveal">
					<?php echo medcentrum_icon( $stat['icon'], 'stat__icon' ); // phpcs:ignore ?>
					<span class="stat__number"><span data-count="<?php echo esc_attr( $stat['number'] ); ?>"><?php echo esc_html( $stat['number'] ); ?></span><?php echo esc_html( $stat['suffix'] ); ?></span>
					<span class="stat__label"><?php echo esc_html( $stat['label'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
