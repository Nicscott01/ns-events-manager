<?php
defined( 'ABSPATH' ) || exit;

class NS_EM_ACF_Fields {

	public static function init(): void {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			add_action( 'admin_notices', [ __CLASS__, 'acf_notice' ] );
			return;
		}
		add_action( 'acf/init', [ __CLASS__, 'register_fields' ] );
	}

	public static function acf_notice(): void {
		?>
		<div class="notice notice-warning">
			<p><?php esc_html_e( 'NS Events Manager: Advanced Custom Fields (ACF) is required for event custom fields.', 'ns-events-manager' ); ?></p>
		</div>
		<?php
	}

	public static function register_fields(): void {
		$opts   = ns_em_options();
		$fields = [];
		$order  = 10;

		// -----------------------------------------------------------------------
		// Core Details tab
		// -----------------------------------------------------------------------
		$fields[] = [
			'key'        => 'field_ns_em_tab_core',
			'label'      => __( 'Event Details', 'ns-events-manager' ),
			'name'       => '',
			'type'       => 'tab',
			'placement'  => 'top',
			'menu_order' => $order,
		];
		$order += 10;

		// Event Date
		$fields[] = [
			'key'           => 'field_ns_em_event_date',
			'label'         => __( 'Event Date', 'ns-events-manager' ),
			'name'          => 'event_date',
			'type'          => 'date_picker',
			'display_format' => 'F j, Y',
			'return_format'  => 'Ymd',
			'first_day'      => 0,
			'required'       => 1,
			'wrapper'        => [ 'width' => '25' ],
			'menu_order'     => $order,
		];
		$order += 10;

		// Event End Date (toggleable)
		if ( $opts['enable_end_date'] === '1' ) {
			$fields[] = [
				'key'            => 'field_ns_em_event_end_date',
				'label'          => __( 'End Date', 'ns-events-manager' ),
				'name'           => 'event_end_date',
				'type'           => 'date_picker',
				'display_format' => 'F j, Y',
				'return_format'  => 'Ymd',
				'first_day'      => 0,
				'wrapper'        => [ 'width' => '25' ],
				'menu_order'     => $order,
			];
			$order += 10;
		}

		// Start Time
		$fields[] = [
			'key'            => 'field_ns_em_event_start_time',
			'label'          => __( 'Start Time', 'ns-events-manager' ),
			'name'           => 'event_start_time',
			'type'           => 'time_picker',
			// Display in 12-hour format for editors; stored as H:i:s for PHP DateTime.
			'display_format' => 'g:i A',
			'return_format'  => 'H:i:s',
			'wrapper'        => [ 'width' => '20' ],
			'menu_order'     => $order,
		];
		$order += 10;

		// End Time
		$fields[] = [
			'key'            => 'field_ns_em_event_end_time',
			'label'          => __( 'End Time', 'ns-events-manager' ),
			'name'           => 'event_end_time',
			'type'           => 'time_picker',
			'display_format' => 'g:i A',
			'return_format'  => 'H:i:s',
			'instructions'   => __( 'Leave blank for single-time or all-day events.', 'ns-events-manager' ),
			'wrapper'        => [ 'width' => '20' ],
			'menu_order'     => $order,
		];
		$order += 10;

		// Event Type
		$fields[] = [
			'key'           => 'field_ns_em_event_type',
			'label'         => __( 'Event Type', 'ns-events-manager' ),
			'name'          => 'event_type',
			'type'          => 'select',
			'choices'       => [
				'speaking'  => __( 'Speaking', 'ns-events-manager' ),
				'retreat'   => __( 'Retreat', 'ns-events-manager' ),
				'workshop'  => __( 'Workshop', 'ns-events-manager' ),
				'online'    => __( 'Online', 'ns-events-manager' ),
				'other'     => __( 'Other', 'ns-events-manager' ),
			],
			'default_value' => 'speaking',
			'allow_null'    => 0,
			'multiple'      => 0,
			'ui'            => 1,
			'wrapper'       => [ 'width' => '25' ],
			'menu_order'    => $order,
		];
		$order += 10;

		// Location
		$fields[] = [
			'key'          => 'field_ns_em_event_location',
			'label'        => __( 'Location', 'ns-events-manager' ),
			'name'         => 'event_location',
			'type'         => 'text',
			'instructions' => __( 'City, State or "Online"', 'ns-events-manager' ),
			'placeholder'  => 'Austin, TX',
			'wrapper'      => [ 'width' => '50' ],
			'menu_order'   => $order,
		];
		$order += 10;

		// Venue Name (toggleable)
		if ( $opts['enable_venue_name'] === '1' ) {
			$fields[] = [
				'key'         => 'field_ns_em_venue_name',
				'label'       => __( 'Venue Name', 'ns-events-manager' ),
				'name'        => 'venue_name',
				'type'        => 'text',
				'placeholder' => 'Convention Center',
				'wrapper'     => [ 'width' => '50' ],
				'menu_order'  => $order,
			];
			$order += 10;
		}

		// Price
		$fields[] = [
			'key'          => 'field_ns_em_price',
			'label'        => __( 'Price', 'ns-events-manager' ),
			'name'         => 'price',
			'type'         => 'text',
			'instructions' => __( 'e.g. Free, $299, Contact for pricing', 'ns-events-manager' ),
			'placeholder'  => 'Free',
			'wrapper'      => [ 'width' => '25' ],
			'menu_order'   => $order,
		];
		$order += 10;

		// Capacity (toggleable)
		if ( $opts['enable_capacity'] === '1' ) {
			$fields[] = [
				'key'        => 'field_ns_em_capacity',
				'label'      => __( 'Capacity', 'ns-events-manager' ),
				'name'       => 'capacity',
				'type'       => 'number',
				'min'        => 1,
				'step'       => 1,
				'wrapper'    => [ 'width' => '25' ],
				'menu_order' => $order,
			];
			$order += 10;
		}

		// Featured (toggleable)
		if ( $opts['enable_featured'] === '1' ) {
			$fields[] = [
				'key'         => 'field_ns_em_featured',
				'label'       => __( 'Featured Event', 'ns-events-manager' ),
				'name'        => 'featured_event',
				'type'        => 'true_false',
				'message'     => __( 'Mark as a featured event', 'ns-events-manager' ),
				'ui'          => 1,
				'wrapper'     => [ 'width' => '25' ],
				'menu_order'  => $order,
			];
			$order += 10;
		}

		// -----------------------------------------------------------------------
		// Registration tab
		// -----------------------------------------------------------------------
		$fields[] = [
			'key'        => 'field_ns_em_tab_registration',
			'label'      => __( 'Registration', 'ns-events-manager' ),
			'name'       => '',
			'type'       => 'tab',
			'placement'  => 'top',
			'menu_order' => $order,
		];
		$order += 10;

		// External URL
		$fields[] = [
			'key'          => 'field_ns_em_external_url',
			'label'        => __( 'External URL', 'ns-events-manager' ),
			'name'         => 'external_url',
			'type'         => 'url',
			'instructions' => __( 'Where visitors go when they click the event — registration page, Eventbrite listing, etc.', 'ns-events-manager' ),
			'menu_order'   => $order,
		];
		$order += 10;

		// External URL Label
		$fields[] = [
			'key'          => 'field_ns_em_external_url_label',
			'label'        => __( 'Link Label', 'ns-events-manager' ),
			'name'         => 'external_url_label',
			'type'         => 'text',
			'instructions' => __( 'Button / link text. Leave blank to use the site default set in Settings.', 'ns-events-manager' ),
			'placeholder'  => $opts['default_link_label'],
			'wrapper'      => [ 'width' => '50' ],
			'menu_order'   => $order,
		];
		$order += 10;

		// RSVP Email
		$fields[] = [
			'key'          => 'field_ns_em_rsvp_email',
			'label'        => __( 'RSVP Email', 'ns-events-manager' ),
			'name'         => 'rsvp_email',
			'type'         => 'email',
			'instructions' => __( 'Optional contact email for RSVP requests.', 'ns-events-manager' ),
			'placeholder'  => 'events@example.com',
			'wrapper'      => [ 'width' => '50' ],
			'menu_order'   => $order,
		];
		$order += 10;

		// RSVP Subject
		$fields[] = [
			'key'               => 'field_ns_em_rsvp_subject',
			'label'             => __( 'RSVP Email Subject', 'ns-events-manager' ),
			'name'              => 'rsvp_subject',
			'type'              => 'text',
			'instructions'      => __( 'Optional pre-filled subject line for RSVP emails.', 'ns-events-manager' ),
			'placeholder'       => __( 'RSVP for {Event Name}', 'ns-events-manager' ),
			'conditional_logic' => [
				[
					[
						'field'    => 'field_ns_em_rsvp_email',
						'operator' => '!=empty',
					],
				],
			],
			'menu_order'        => $order,
		];
		$order += 10;

		// RSVP Body
		$fields[] = [
			'key'               => 'field_ns_em_rsvp_body',
			'label'             => __( 'RSVP Email Body', 'ns-events-manager' ),
			'name'              => 'rsvp_body',
			'type'              => 'textarea',
			'instructions'      => __( 'Optional pre-filled message body for RSVP emails.', 'ns-events-manager' ),
			'rows'              => 6,
			'new_lines'         => 'br',
			'conditional_logic' => [
				[
					[
						'field'    => 'field_ns_em_rsvp_email',
						'operator' => '!=empty',
					],
				],
			],
			'menu_order'        => $order,
		];
		$order += 10;

		// -----------------------------------------------------------------------
		// Details tab (full description — toggleable)
		// -----------------------------------------------------------------------
		if ( $opts['enable_full_desc'] === '1' ) {
			$fields[] = [
				'key'        => 'field_ns_em_tab_details',
				'label'      => __( 'Details', 'ns-events-manager' ),
				'name'       => '',
				'type'       => 'tab',
				'placement'  => 'top',
				'menu_order' => $order,
			];
			$order += 10;

			$fields[] = [
				'key'          => 'field_ns_em_full_description',
				'label'        => __( 'Full Description', 'ns-events-manager' ),
				'name'         => 'full_description',
				'type'         => 'wysiwyg',
				'tabs'         => 'all',
				'toolbar'      => 'full',
				'media_upload' => 1,
				'menu_order'   => $order,
			];
			$order += 10;
		}

		acf_add_local_field_group( [
			'key'                   => 'group_ns_em_event',
			'title'                 => sprintf( __( '%s Details', 'ns-events-manager' ), $opts['singular_label'] ),
			'fields'                => $fields,
			'location'              => [
				[ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'ns_event' ] ],
			],
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'field',
			'active'                => true,
		] );
	}
}
