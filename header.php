<?php
/**
 * Minimal document shell — used ONLY by WooCommerce's Coming Soon screen.
 *
 * Every normal page renders through resources/views/layouts/app.blade.php (which includes
 * sections.header directly) and never calls get_header(). The only caller of get_header() is
 * WooCommerce's Coming Soon screen (see inc/coming-soon.php). Without ANY header.php, WordPress
 * falls back to wp-includes/theme-compat/header.php, which brings its own ugly default banner
 * AND is what actually opens <html>/<head>/<body> for that flow — so this file can't be empty,
 * it just can't have the theme-compat visual chrome. Keep it a bare shell; pair with footer.php.
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
