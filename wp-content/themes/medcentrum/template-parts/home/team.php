<?php defined( 'ABSPATH' ) || exit; ?>
<section class="section team" id="doctors">
	<svg class="team__watermark" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><circle cx="24" cy="24" r="21" fill="none" stroke="currentColor" stroke-width="2.5"/><path d="M7 26h10l3.5-8 5 15 4.5-11 2.5 4H41" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
	<div class="wrap wrap--wide">
		<div class="team__intro reveal">
			<p class="eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
			<h2 class="section__title"><?php echo medcentrum_kses_br( __( 'Experienced specialists,<br>exceptional care.', 'medcentrum' ) ); ?></h2>
			<p class="lead"><?php esc_html_e( 'Our examinations are carried out by doctors with many years of experience at leading hospitals. From your GP to the neurologist and the physiotherapist, you are in the best hands.', 'medcentrum' ); ?></p>
		</div>

		<ul class="team__grid">
			<?php foreach ( medcentrum_team() as $doctor ) : ?>
				<li class="doctor reveal">
					<img class="doctor__photo" src="<?php echo esc_url( medcentrum_img( $doctor['image'] ) ); ?>" alt="<?php echo esc_attr( $doctor['name'] ); ?>" width="600" height="720" loading="lazy">
					<span class="doctor__role"><?php echo esc_html( $doctor['role'] ); ?></span>
					<h3 class="doctor__name"><?php echo esc_html( $doctor['name'] ); ?></h3>
					<p class="doctor__text"><?php echo esc_html( $doctor['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
