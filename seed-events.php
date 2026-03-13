<?php
/**
 * Seed script — creates (or removes) sample events with full ACF meta.
 *
 * Seed:   wp eval-file web/app/plugins/ns-events-manager-dev/seed-events.php --allow-root
 * Unseed: wp eval-file web/app/plugins/ns-events-manager-dev/seed-events.php --allow-root -- --unseed
 */

$unseed = in_array( '--unseed', $args ?? [], true );

if ( $unseed ) {
	$seeded = get_posts( [
		'post_type'      => 'ns_event',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'meta_key'       => '_ns_em_seeded',
		'meta_value'     => '1',
		'fields'         => 'ids',
	] );

	if ( empty( $seeded ) ) {
		WP_CLI::warning( 'No seeded events found.' );
		return;
	}

	foreach ( $seeded as $id ) {
		wp_delete_post( $id, true );
		WP_CLI::success( "Deleted post ID {$id}" );
	}

	WP_CLI::success( count( $seeded ) . ' seeded event(s) removed.' );
	return;
}

$events = [
	[
		'title'   => 'Move, Breathe, Heal: A Full-Day Wellness Retreat',
		'excerpt' => 'An immersive day of movement, breathwork, and somatic release set against the backdrop of the Texas Hill Country. Limited to 20 participants.',
		'meta'    => [
			'event_date'         => '20260418',
			'event_start_time'   => '09:00:00',
			'event_end_time'     => '17:00:00',
			'event_type'         => 'retreat',
			'event_location'     => 'Wimberley, TX',
			'venue_name'         => 'Blue Hole Ranch',
			'price'              => '$349',
			'external_url'       => 'https://example.com/events/move-breathe-heal',
			'external_url_label' => 'Reserve Your Spot',
			'rsvp_email'         => 'events@example.com',
			'rsvp_subject'       => 'RSVP: Move, Breathe, Heal Retreat',
			'rsvp_body'          => "Hi,\n\nI'd like to RSVP for Move, Breathe, Heal.\n\nName:\nPhone:\n\nThanks!",
			'full_description'   => '<p>This full-day retreat is designed to help you step out of survival mode and into your body. Through a carefully sequenced combination of functional movement, guided breathwork, and group somatic exercises, you\'ll leave with tools you can take home and a nervous system that finally feels like yours again.</p><p>The day includes a farm-to-table lunch, time for journaling and reflection, and a closing integration circle. No prior experience with any of these practices is required — just a willingness to show up.</p>',
		],
	],
	[
		'title'   => 'The Performance Edge: Strength Training for Endurance Athletes',
		'excerpt' => 'A half-day workshop covering the science and practice of integrating strength training into an endurance athlete\'s program without burning out.',
		'meta'    => [
			'event_date'         => '20260510',
			'event_start_time'   => '08:00:00',
			'event_end_time'     => '13:00:00',
			'event_type'         => 'workshop',
			'event_location'     => 'Austin, TX',
			'venue_name'         => 'Embody Performance Center',
			'price'              => '$149',
			'external_url'       => 'https://example.com/events/performance-edge',
			'external_url_label' => 'Register Now',
			'full_description'   => '<p>Most endurance athletes avoid the weight room out of fear it will slow them down or bulk them up. This workshop dismantles that myth with evidence and gives you a practical framework for building strength that transfers directly to your sport.</p><p>You\'ll leave with a 12-week template, a movement screen to identify your biggest limiters, and clarity on how to structure your training week so strength and endurance work together instead of competing.</p>',
		],
	],
	[
		'title'   => 'Keynote: Rethinking Recovery — What Elite Sport Taught Me About Everyday Health',
		'excerpt' => 'A keynote address at the Texas Health & Wellness Summit exploring how recovery principles from professional sport translate to everyday performance and longevity.',
		'meta'    => [
			'event_date'         => '20260606',
			'event_start_time'   => '10:30:00',
			'event_end_time'     => '11:30:00',
			'event_type'         => 'speaking',
			'event_location'     => 'Dallas, TX',
			'venue_name'         => 'Kay Bailey Hutchison Convention Center',
			'price'              => 'Included with summit pass',
			'external_url'       => 'https://example.com/events/tx-wellness-summit',
			'external_url_label' => 'Get Summit Tickets',
			'full_description'   => '<p>After years of working with competitive athletes, the patterns became clear: the habits that separate good from elite aren\'t about pushing harder. They\'re about recovering smarter. In this keynote, we\'ll explore what sleep, stress management, and intentional rest look like when taken seriously — and how anyone can apply these principles regardless of their fitness level or lifestyle.</p>',
		],
	],
	[
		'title'   => 'Women\'s Strength & Vitality Weekend',
		'excerpt' => 'A weekend retreat exclusively for women focused on building physical confidence, metabolic health, and sustainable energy through strength training and nutrition.',
		'meta'    => [
			'event_date'         => '20260718',
			'event_end_date'     => '20260720',
			'event_start_time'   => '14:00:00',
			'event_end_time'     => '12:00:00',
			'event_type'         => 'retreat',
			'event_location'     => 'Sedona, AZ',
			'venue_name'         => 'Red Rock Wellness Lodge',
			'price'              => '$895',
			'external_url'       => 'https://example.com/events/womens-strength-vitality',
			'external_url_label' => 'Apply for a Spot',
			'full_description'   => '<p>This intimate weekend retreat is built for women who are tired of programs designed without them in mind. Across three days, you\'ll train twice daily in small groups, learn how to fuel for performance and not just aesthetics, and leave with a clear, individualized plan for the next 90 days.</p><p>The weekend includes all meals, accommodation at the lodge, morning yoga, and an optional Sunday sunrise hike. Space is intentionally capped at 12 participants.</p>',
		],
	],
	[
		'title'   => 'Foundations of Movement: Free Community Workshop',
		'excerpt' => 'A free 90-minute public workshop covering the fundamental movement patterns everyone should own — regardless of age or fitness background.',
		'meta'    => [
			'event_date'         => '20260815',
			'event_start_time'   => '09:00:00',
			'event_end_time'     => '10:30:00',
			'event_type'         => 'workshop',
			'event_location'     => 'Austin, TX',
			'venue_name'         => 'Zilker Park Pavilion',
			'price'              => 'Free',
			'external_url'       => 'https://example.com/events/foundations-of-movement',
			'external_url_label' => 'RSVP Free',
			'full_description'   => '<p>Good movement shouldn\'t be a privilege. This free community workshop covers the six foundational patterns — squat, hinge, push, pull, carry, and rotate — and why mastering them is the single highest-leverage thing you can do for your long-term health and independence.</p><p>Come as you are. No equipment needed. Appropriate for all ages and fitness levels. Bring water and wear shoes you can move in.</p>',
		],
	],
];

// ACF field key map for scalar fields.
$field_map = [
	'event_date'         => 'field_ns_em_event_date',
	'event_end_date'     => 'field_ns_em_event_end_date',
	'event_start_time'   => 'field_ns_em_event_start_time',
	'event_end_time'     => 'field_ns_em_event_end_time',
	'event_type'         => 'field_ns_em_event_type',
	'event_location'     => 'field_ns_em_event_location',
	'venue_name'         => 'field_ns_em_venue_name',
	'price'              => 'field_ns_em_price',
	'external_url'       => 'field_ns_em_external_url',
	'external_url_label' => 'field_ns_em_external_url_label',
	'rsvp_email'         => 'field_ns_em_rsvp_email',
	'rsvp_subject'       => 'field_ns_em_rsvp_subject',
	'rsvp_body'          => 'field_ns_em_rsvp_body',
	'full_description'   => 'field_ns_em_full_description',
];

foreach ( $events as $event ) {
	$meta = $event['meta'];

	$post_id = wp_insert_post( [
		'post_type'    => 'ns_event',
		'post_status'  => 'publish',
		'post_title'   => $event['title'],
		'post_excerpt' => $event['excerpt'],
	] );

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::error( 'Failed to create event: ' . $event['title'] );
		continue;
	}

	// Mark as seeded so --unseed can find and remove these posts.
	update_post_meta( $post_id, '_ns_em_seeded', '1' );

	foreach ( $field_map as $meta_key => $field_key ) {
		if ( ! isset( $meta[ $meta_key ] ) ) {
			continue;
		}
		update_post_meta( $post_id, $meta_key, $meta[ $meta_key ] );
		update_post_meta( $post_id, '_' . $meta_key, $field_key );
	}

	WP_CLI::success( "Created event: {$event['title']} (ID: {$post_id})" );
}

WP_CLI::success( 'All sample events seeded.' );
