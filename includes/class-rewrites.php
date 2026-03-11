<?php
defined( 'ABSPATH' ) || exit;

class NS_EM_Rewrites {

	public static function init(): void {
		add_action( 'init', [ __CLASS__, 'maybe_flush' ] );
	}

	public static function flush(): void {
		flush_rewrite_rules();
	}

	public static function schedule(): void {
		set_transient( 'ns_em_flush_rewrites', '1', 60 );
	}

	public static function maybe_flush(): void {
		if ( get_transient( 'ns_em_flush_rewrites' ) ) {
			delete_transient( 'ns_em_flush_rewrites' );
			flush_rewrite_rules();
		}
	}
}
