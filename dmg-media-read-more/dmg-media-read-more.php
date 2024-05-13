<?php
/**
 * Plugin Name:       Dmg Media Read More
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       dmg-media-read-more
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_dmg_media_read_more_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'create_block_dmg_media_read_more_block_init' );

function add_title_search_to_rest_api ( $args, $request ) {
    if ( isset( $request['search_title'] ) ) {
        $args['s'] = $request['search_title'];
        add_filter( 'posts_search', function ( $search, $wp_query ) {
            global $wpdb;
            if ( ! empty( $search ) ) {
                // Target post_title specifically for the search
                $search = preg_replace("/\($wpdb->posts.post_content LIKE (.*?)\)/", "(" . $wpdb->posts . ".post_title LIKE $1)", $search);
            }
            return $search;
        }, 10, 2 );
    }
    return $args;
}

add_filter('rest_post_query', 'add_title_search_to_rest_api', 10, 2);

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require_once __DIR__ . '/class-dmgreadmorecommand.php';
    WP_CLI::add_command( 'dmg-read-more search', 'DMGReadMoreCommand' );
}