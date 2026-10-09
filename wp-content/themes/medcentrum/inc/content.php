<?php
/**
 * Placeholder content for the home page: services, team, locations.
 *
 * Texts are written in English and translated to German and Slovak in
 * languages/*.po. Images are looked up in assets/img/ unless a full URL is
 * given. 'booking' must match the first (stored) name of a service in
 * Bookings → Settings, so the card can preselect it in the booking form.
 */

defined( 'ABSPATH' ) || exit;

function medcentrum_services() {
	return array(
		array(
			'title'   => __( 'General practice', 'medcentrum' ),
			'text'    => __( 'Comprehensive care for adult patients, from acute complaints to long-term follow-up.', 'medcentrum' ),
			'image'   => 'service-general.svg',
			'booking' => 'Allgemeine Untersuchung',
		),
		array(
			'title'   => __( 'Neurology', 'medcentrum' ),
			'text'    => __( 'Headaches, dizziness, sleep or memory problems. EEG and EMG on site.', 'medcentrum' ),
			'image'   => 'service-neuro.svg',
			'booking' => 'Neurologische Untersuchung',
		),
		array(
			'title'   => __( 'Cardiology', 'medcentrum' ),
			'text'    => __( 'ECG, echocardiography and Holter monitoring. Prevention and treatment of heart and vascular disease.', 'medcentrum' ),
			'image'   => 'service-cardio.svg',
			'booking' => 'Kardiologische Untersuchung',
		),
		array(
			'title'   => __( 'Orthopaedics', 'medcentrum' ),
			'text'    => __( 'Back and joint pain, sports injuries. Diagnosis and conservative treatment.', 'medcentrum' ),
			'image'   => 'service-ortho.svg',
			'booking' => 'Orthopädische Untersuchung',
		),
		array(
			'title'   => __( 'Physiotherapy', 'medcentrum' ),
			'text'    => __( 'Individual exercise, manual techniques and rehabilitation after injuries and surgery.', 'medcentrum' ),
			'image'   => 'service-physio.svg',
			'booking' => 'Physiotherapie',
		),
		array(
			'title'   => __( 'Diagnostics and ultrasound', 'medcentrum' ),
			'text'    => __( 'Modern ultrasound equipment and fast results during your visit.', 'medcentrum' ),
			'image'   => 'service-diagnostics.svg',
			'booking' => 'Ultraschall und Diagnostik',
		),
		array(
			'title'   => __( 'Preventive check-ups', 'medcentrum' ),
			'text'    => __( 'Regular check-ups covered by insurance and extended packages for companies.', 'medcentrum' ),
			'image'   => 'service-prevention.svg',
			'booking' => 'Vorsorgeuntersuchung',
		),
		array(
			'title'   => __( 'Laboratory', 'medcentrum' ),
			'text'    => __( 'Blood tests without waiting, most results on the same day.', 'medcentrum' ),
			'image'   => 'service-lab.svg',
			'booking' => 'Blutabnahme und Labor',
		),
	);
}

function medcentrum_team() {
	return array(
		array(
			'name'  => 'MUDr. Jana Kováčová',
			'role'  => __( 'Neurology', 'medcentrum' ),
			'text'  => __( 'Head of department, 18 years of experience. Headaches and sleep disorders.', 'medcentrum' ),
			'image' => 'doctor-1.svg',
		),
		array(
			'name'  => 'MUDr. Martin Horváth, PhD.',
			'role'  => __( 'Cardiology', 'medcentrum' ),
			'text'  => __( 'Echocardiography, hypertension and prevention of heart disease.', 'medcentrum' ),
			'image' => 'doctor-2.svg',
		),
		array(
			'name'  => 'MUDr. Lucia Novotná',
			'role'  => __( 'General practice', 'medcentrum' ),
			'text'  => __( 'Comprehensive care for adults and preventive check-ups.', 'medcentrum' ),
			'image' => 'doctor-3.svg',
		),
		array(
			'name'  => 'doc. MUDr. Peter Baláž, CSc.',
			'role'  => __( 'Orthopaedics', 'medcentrum' ),
			'text'  => __( 'Spine, large joints and sports medicine.', 'medcentrum' ),
			'image' => 'doctor-4.svg',
		),
	);
}

function medcentrum_locations() {
	return array(
		array(
			'name'    => 'MedCentrum Staré Mesto',
			'address' => 'Panenská 12, 811 03 Bratislava',
			'phone'   => '+421 2 123 456 78',
			'hours'   => __( 'Mon – Fri 7:30 – 16:00', 'medcentrum' ),
		),
		array(
			'name'    => 'MedCentrum Ružinov',
			'address' => 'Mlynské nivy 48, 821 09 Bratislava',
			'phone'   => '+421 2 123 456 79',
			'hours'   => __( 'Mon – Fri 7:00 – 18:00', 'medcentrum' ),
		),
		array(
			'name'    => 'MedCentrum Petržalka',
			'address' => 'Einsteinova 20, 851 01 Bratislava',
			'phone'   => '+421 2 123 456 80',
			'hours'   => __( 'Mon – Thu 8:00 – 15:00', 'medcentrum' ),
		),
	);
}

function medcentrum_contact() {
	return array(
		'company' => 'MedCentrum s.r.o.',
		'street'  => 'Panenská 12',
		'city'    => '811 03 Bratislava',
		'phone'   => '+421 2 123 456 78',
		'email'   => 'recepcia@medcentrum.sk',
	);
}

function medcentrum_stats() {
	return array(
		array(
			'icon'   => 'doctors',
			'number' => 25,
			'suffix' => '+',
			'label'  => __( 'experienced doctors and specialists', 'medcentrum' ),
		),
		array(
			'icon'   => 'pin',
			'number' => 3,
			'suffix' => '',
			'label'  => __( 'locations in Bratislava', 'medcentrum' ),
		),
		array(
			'icon'   => 'plus',
			'number' => 12,
			'suffix' => '',
			'label'  => __( 'specialist clinics', 'medcentrum' ),
		),
	);
}

function medcentrum_gallery() {
	return array(
		array(
			'image' => 'gallery-1.svg',
			'alt'   => __( 'Online booking for a specific appointment', 'medcentrum' ),
		),
		array(
			'image' => 'gallery-2.svg',
			'alt'   => __( 'Examination at the clinic', 'medcentrum' ),
		),
		array(
			'image' => 'gallery-3.svg',
			'alt'   => __( 'Modern diagnostics', 'medcentrum' ),
		),
		array(
			'image' => 'gallery-4.svg',
			'alt'   => __( 'In-house laboratory', 'medcentrum' ),
		),
	);
}
