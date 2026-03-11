<?php
defined( 'ABSPATH' ) || exit;

class NS_EM_Settings {

	const OPTION_KEY = 'ns_events_manager_options';

	public static function init(): void {
		add_action( 'admin_menu', [ __CLASS__, 'add_page' ] );
		add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
		add_action( 'update_option_' . self::OPTION_KEY, [ __CLASS__, 'maybe_schedule_flush' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
		add_action( 'admin_notices', [ __CLASS__, 'flush_notice' ] );
		NS_EM_Rewrites::init();
	}

	public static function add_page(): void {
		add_options_page(
			__( 'Events Manager', 'ns-events-manager' ),
			__( 'Events Manager', 'ns-events-manager' ),
			'manage_options',
			'ns-events-manager',
			[ __CLASS__, 'render_page' ]
		);
	}

	public static function enqueue_assets( string $hook ): void {
		if ( $hook !== 'settings_page_ns-events-manager' ) {
			return;
		}
		wp_enqueue_style(
			'ns-em-admin',
			NS_EM_URL . 'assets/admin.css',
			[],
			NS_EM_VERSION
		);
	}

	public static function register_settings(): void {
		register_setting(
			'ns_em_settings_group',
			self::OPTION_KEY,
			[ 'sanitize_callback' => [ __CLASS__, 'sanitize' ] ]
		);

		// --- General ---
		add_settings_section( 'ns_em_general', __( 'General', 'ns-events-manager' ), '__return_false', 'ns-events-manager' );

		self::add_field( 'menu_label', __( 'Menu Label', 'ns-events-manager' ), 'text', 'ns_em_general',
			__( 'Label shown in the WordPress admin menu.', 'ns-events-manager' ) );
		self::add_field( 'singular_label', __( 'Singular Label', 'ns-events-manager' ), 'text', 'ns_em_general',
			__( 'e.g. Event', 'ns-events-manager' ) );
		self::add_field( 'plural_label', __( 'Plural Label', 'ns-events-manager' ), 'text', 'ns_em_general',
			__( 'e.g. Events', 'ns-events-manager' ) );
		self::add_field( 'cpt_slug', __( 'URL Slug', 'ns-events-manager' ), 'text', 'ns_em_general',
			__( 'Base slug used internally (no single-post pages are generated). Changing this flushes rewrite rules.', 'ns-events-manager' ) );
		self::add_field( 'menu_icon', __( 'Menu Icon', 'ns-events-manager' ), 'text', 'ns_em_general',
			__( 'Dashicon slug, e.g. <code>dashicons-calendar-alt</code>.', 'ns-events-manager' ) );
		self::add_field( 'enable_rest', __( 'Enable REST API', 'ns-events-manager' ), 'checkbox', 'ns_em_general',
			__( 'Expose events in the WP REST API (required for Breakdance post loop builders).', 'ns-events-manager' ) );
		self::add_field( 'default_link_label', __( 'Default Link Label', 'ns-events-manager' ), 'text', 'ns_em_general',
			__( 'Fallback button/link text when an event has no custom label set. e.g. <code>Learn More</code> or <code>Register</code>.', 'ns-events-manager' ) );

		// --- Event Fields ---
		add_settings_section( 'ns_em_fields', __( 'Optional Fields', 'ns-events-manager' ), function () {
			echo '<p>' . esc_html__( 'Toggle optional ACF fields on or off.', 'ns-events-manager' ) . '</p>';
		}, 'ns-events-manager' );

		self::add_field( 'enable_end_date', __( 'End Date Field', 'ns-events-manager' ), 'checkbox', 'ns_em_fields',
			__( 'Show an End Date picker (for multi-day events).', 'ns-events-manager' ) );
		self::add_field( 'enable_venue_name', __( 'Venue Name Field', 'ns-events-manager' ), 'checkbox', 'ns_em_fields',
			__( 'Show a Venue Name field (e.g. convention center or studio name).', 'ns-events-manager' ) );
		self::add_field( 'enable_full_desc', __( 'Full Description Field', 'ns-events-manager' ), 'checkbox', 'ns_em_fields',
			__( 'Show a rich-text Full Description field (in addition to the post excerpt).', 'ns-events-manager' ) );
		self::add_field( 'enable_capacity', __( 'Capacity Field', 'ns-events-manager' ), 'checkbox', 'ns_em_fields',
			__( 'Show a numeric Capacity field (max attendees).', 'ns-events-manager' ) );
		self::add_field( 'enable_featured', __( 'Featured Field', 'ns-events-manager' ), 'checkbox', 'ns_em_fields',
			__( 'Show a "Featured Event" toggle for highlighting select events.', 'ns-events-manager' ) );
	}

	private static function add_field( string $key, string $label, string $type, string $section, string $description = '' ): void {
		add_settings_field(
			'ns_em_' . $key,
			$label,
			[ __CLASS__, 'render_field' ],
			'ns-events-manager',
			$section,
			[
				'key'         => $key,
				'type'        => $type,
				'description' => $description,
				'label_for'   => 'ns_em_' . $key,
			]
		);
	}

	public static function render_field( array $args ): void {
		$opts  = ns_em_options();
		$key   = $args['key'];
		$type  = $args['type'];
		$value = $opts[ $key ] ?? '';
		$id    = 'ns_em_' . $key;
		$name  = self::OPTION_KEY . '[' . $key . ']';

		if ( $type === 'checkbox' ) {
			printf(
				'<label><input type="checkbox" id="%s" name="%s" value="1" %s> %s</label>',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( $value, '1', false ),
				esc_html( $args['description'] )
			);
		} else {
			printf(
				'<input type="%s" id="%s" name="%s" value="%s" class="regular-text">',
				esc_attr( $type ),
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value )
			);
			if ( $args['description'] ) {
				printf( '<p class="description">%s</p>', wp_kses( $args['description'], [ 'code' => [] ] ) );
			}
		}
	}

	public static function sanitize( array $input ): array {
		$clean = [];

		$text_fields = [
			'singular_label', 'plural_label', 'menu_label',
			'cpt_slug', 'menu_icon', 'default_link_label',
		];
		$checkbox_fields = [
			'enable_rest', 'enable_end_date', 'enable_venue_name',
			'enable_capacity', 'enable_featured', 'enable_full_desc',
		];

		foreach ( $text_fields as $f ) {
			$clean[ $f ] = sanitize_text_field( $input[ $f ] ?? '' );
		}
		foreach ( $checkbox_fields as $f ) {
			$clean[ $f ] = isset( $input[ $f ] ) && $input[ $f ] === '1' ? '1' : '0';
		}

		if ( $clean['cpt_slug'] ) {
			$clean['cpt_slug'] = sanitize_title( $clean['cpt_slug'] );
		}

		return $clean;
	}

	public static function maybe_schedule_flush( $old, $new ): void {
		if ( ( $old['cpt_slug'] ?? '' ) !== ( $new['cpt_slug'] ?? '' ) ) {
			NS_EM_Rewrites::schedule();
			set_transient( 'ns_em_slug_changed', '1', 30 );
		}
	}

	public static function flush_notice(): void {
		if ( ! get_transient( 'ns_em_slug_changed' ) ) {
			return;
		}
		delete_transient( 'ns_em_slug_changed' );
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Events Manager: Settings saved and permalinks flushed.', 'ns-events-manager' ); ?></p>
		</div>
		<?php
	}

	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap ns-em-settings">
			<h1><?php esc_html_e( 'Events Manager Settings', 'ns-events-manager' ); ?></h1>
			<p class="description">
				<?php esc_html_e( 'Events have no individual post pages — the External URL on each event is where visitors are sent. Use Breakdance\'s post loop builder to list events on any page.', 'ns-events-manager' ); ?>
			</p>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'ns_em_settings_group' );
				do_settings_sections( 'ns-events-manager' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
