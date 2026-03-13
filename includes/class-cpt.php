<?php
defined( 'ABSPATH' ) || exit;

class NS_EM_CPT {

	public static function init(): void {
		add_action( 'init', [ __CLASS__, 'register' ] );
		add_action( 'admin_init', [ __CLASS__, 'register_meta' ] );
		add_filter( 'enter_title_here', [ __CLASS__, 'title_placeholder' ], 10, 2 );
		add_filter( 'manage_ns_event_posts_columns', [ __CLASS__, 'admin_columns' ] );
		add_action( 'manage_ns_event_posts_custom_column', [ __CLASS__, 'admin_column_content' ], 10, 2 );
		add_filter( 'manage_edit-ns_event_sortable_columns', [ __CLASS__, 'sortable_columns' ] );
		add_action( 'pre_get_posts', [ __CLASS__, 'default_admin_sort' ] );
		add_action( 'template_redirect', [ __CLASS__, 'redirect_single' ] );
	}

	public static function title_placeholder( string $title, \WP_Post $post ): string {
		return $post->post_type === 'ns_event' ? __( 'Event Name', 'ns-events-manager' ) : $title;
	}

	public static function register(): void {
		$opts   = ns_em_options();
		$single = $opts['singular_label'];
		$plural = $opts['plural_label'];
		$menu   = $opts['menu_label'];

		$labels = [
			'name'               => $plural,
			'singular_name'      => $single,
			'menu_name'          => $menu,
			'name_admin_bar'     => $single,
			'add_new'            => __( 'Add New', 'ns-events-manager' ),
			'add_new_item'       => sprintf( __( 'Add New %s', 'ns-events-manager' ), $single ),
			'new_item'           => sprintf( __( 'New %s', 'ns-events-manager' ), $single ),
			'edit_item'          => sprintf( __( 'Edit %s', 'ns-events-manager' ), $single ),
			'view_item'          => sprintf( __( 'View %s', 'ns-events-manager' ), $single ),
			'all_items'          => sprintf( __( 'All %s', 'ns-events-manager' ), $plural ),
			'search_items'       => sprintf( __( 'Search %s', 'ns-events-manager' ), $plural ),
			'not_found'          => sprintf( __( 'No %s found.', 'ns-events-manager' ), strtolower( $plural ) ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'ns-events-manager' ), strtolower( $plural ) ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => true,
			// Must be true for Breakdance's builder to render post loop previews.
			// Single-post requests are intercepted by redirect_single() instead.
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => (bool) $opts['enable_rest'],
			'menu_icon'          => $opts['menu_icon'] ?: 'dashicons-calendar-alt',
			'supports'           => [ 'title', 'thumbnail', 'excerpt' ],
			'has_archive'        => false,
			'rewrite'            => false,
			'hierarchical'       => false,
			'query_var'          => false,
		];

		register_post_type( 'ns_event', $args );
	}

	/**
	 * On the frontend, redirect any single ns_event request to its external URL.
	 * Falls back to the home URL if none is set.
	 *
	 * publicly_queryable stays true so Breakdance's builder can render previews,
	 * but real visitors never land on a WordPress-hosted event page.
	 */
	public static function redirect_single(): void {
		if ( ! is_singular( 'ns_event' ) || self::should_bypass_single_redirect() ) {
			return;
		}

		$url = get_post_meta( get_the_ID(), 'external_url', true );

		wp_redirect( $url ?: home_url(), 301 );
		exit;
	}

	/**
	 * Breakdance renders SSR elements by POSTing to the current post URL and expects JSON back.
	 * Skip frontend redirects for builder/JSON requests so those responses are not converted to HTML.
	 */
	private static function should_bypass_single_redirect(): bool {
		if ( is_admin() || wp_doing_ajax() || wp_is_json_request() || is_preview() ) {
			return true;
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}

		if ( isset( $_GET['_breakdance_doing_ajax'] ) || isset( $_GET['breakdance_iframe'] ) ) {
			return true;
		}

		if ( function_exists( '\Breakdance\isRequestFromBuilderIframe' ) && \Breakdance\isRequestFromBuilderIframe() ) {
			return true;
		}

		if ( function_exists( '\Breakdance\isRequestFromBuilderSsr' ) && \Breakdance\isRequestFromBuilderSsr() ) {
			return true;
		}

		if ( function_exists( '\Breakdance\isRequestFromBuilderDynamicDataGet' ) && \Breakdance\isRequestFromBuilderDynamicDataGet() ) {
			return true;
		}

		return false;
	}

	/**
	 * Register event_date as a sortable REST meta field so Breakdance
	 * and other consumers can order by it.
	 */
	public static function register_meta(): void {
		register_post_meta( 'ns_event', 'event_date', [
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		] );
		register_post_meta( 'ns_event', 'event_type', [
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		] );
		register_post_meta( 'ns_event', 'event_location', [
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		] );
		register_post_meta( 'ns_event', 'external_url', [
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
		] );
		register_post_meta( 'ns_event', 'rsvp_email', [
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_email',
		] );
		register_post_meta( 'ns_event', 'rsvp_subject', [
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		] );
		register_post_meta( 'ns_event', 'rsvp_body', [
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_textarea_field',
		] );
	}

	// -------------------------------------------------------------------------
	// Admin list table customization
	// -------------------------------------------------------------------------

	public static function admin_columns( array $columns ): array {
		$new = [];
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( $key === 'title' ) {
				$new['event_date']     = __( 'Date', 'ns-events-manager' );
				$new['event_type']     = __( 'Type', 'ns-events-manager' );
				$new['event_location'] = __( 'Location', 'ns-events-manager' );
				$new['external_url']   = __( 'External URL', 'ns-events-manager' );
			}
		}
		unset( $new['date'] ); // remove default WP date column
		return $new;
	}

	public static function admin_column_content( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'event_date':
				$raw = get_post_meta( $post_id, 'event_date', true );
				if ( $raw ) {
					// ACF stores dates as Ymd; format for display.
					$ts = strtotime( $raw );
					echo $ts ? esc_html( date( 'M j, Y', $ts ) ) : esc_html( $raw );
				} else {
					echo '—';
				}
				break;

			case 'event_type':
				$val = get_post_meta( $post_id, 'event_type', true );
				echo $val ? esc_html( ucfirst( $val ) ) : '—';
				break;

			case 'event_location':
				$val = get_post_meta( $post_id, 'event_location', true );
				echo $val ? esc_html( $val ) : '—';
				break;

			case 'external_url':
				$url = get_post_meta( $post_id, 'external_url', true );
				if ( $url ) {
					printf(
						'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
						esc_url( $url ),
						esc_html( wp_parse_url( $url, PHP_URL_HOST ) ?: $url )
					);
				} else {
					echo '—';
				}
				break;
		}
	}

	public static function sortable_columns( array $columns ): array {
		$columns['event_date'] = 'event_date';
		return $columns;
	}

	/**
	 * Default admin list to sort by event_date descending (soonest first when
	 * dates are in the future; most recent first otherwise).
	 */
	public static function default_admin_sort( \WP_Query $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( $query->get( 'post_type' ) !== 'ns_event' ) {
			return;
		}
		if ( ! $query->get( 'orderby' ) ) {
			$query->set( 'meta_key', 'event_date' );
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'order', 'ASC' );
		}
		if ( $query->get( 'orderby' ) === 'event_date' ) {
			$query->set( 'meta_key', 'event_date' );
			$query->set( 'orderby', 'meta_value' );
		}
	}
}
