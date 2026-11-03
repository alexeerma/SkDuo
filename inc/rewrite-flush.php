<?php
/**
 * One-time rewrite rules flush.
 *
 * /uudised/page/2/ (and every later blog-archive page) was 404ing — the pagination
 * rewrite rule for the posts page was stale. Bump SKDUO_REWRITE_FLUSH_VERSION any
 * time a future change needs another flush; this only runs flush_rewrite_rules()
 * once per version, not on every request.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function (): void {
	$version = '20260827-uudised-pagination';

	if ( get_option( 'skduo_rewrite_flush_version' ) === $version ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'skduo_rewrite_flush_version', $version, false );
}, 20 );
