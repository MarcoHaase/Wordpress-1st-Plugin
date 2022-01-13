<?php
/**
 * Plugin Name: My First Plugin
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: The very first plugin that I have ever created.
 * Version: 1.0
 * Author: Your Name
 * Author URI: http://www.mywebsite.com
 */

function mhplg_no_rows_found_function($query)
{ 
  $query->set('no_found_rows', true); 
}

add_action('pre_get_posts', 'mhplg_no_rows_found_function');

/**
 * Add data attributes to the query block to describe the block query.
 *
 * @param string $block_content Default query content.
 * @param array  $block Parsed block.
 * @return string
 */
function mhplg_query_render_block( $block_content, $block ) {
	if ( 'core/query' === $block['blockName'] ) {
		$query_id      = $block['attrs']['queryId'];
		$container_end = strpos( $block_content, '>' );

		$paged = absint( $_GET[ 'query-' . $query_id . '-page' ] ?? 1 );
		//$paged = $block['attrs']['query']['offset']/$block['attrs']['query']['perPage'] + 1;
		$custom_posts = new WP_Query();
		$custom_posts->query('post_type=post');
		$count = 0;
		while ($custom_posts->have_posts()) :
			$custom_posts->the_post();
			$count++;
		endwhile;
		print_r($count);
		$block['attrs']['query']['pages'] = ceil($count/$block['attrs']['query']['perPage']);
	
		$block_content = substr_replace( $block_content, ' data-paged="' . esc_attr( $paged ) . '" data-attrs="' . esc_attr( json_encode( $block ) ) . '"', $container_end, 0 );
	}

	return $block_content;
}
\add_filter( 'render_block', __NAMESPACE__ . '\mhplg_query_render_block', 10, 2 );

/**
 * Replace the pagination block with a View More button.
 *
 * @param string $block_content Default pagination content.
 * @param array  $block Parsed block.
 * @return string
 */
function mhplg_query_pagination_render_block( $block_content, $block ) {
	if ( 'core/query-pagination' === $block['blockName'] ) {
		$block_content = sprintf( '<a href="#" class="view-more-query button">%s</a>', esc_html__( 'View More' ) );
	}
	return $block_content;
}
\add_filter( 'render_block', __NAMESPACE__ . '\mhplg_query_pagination_render_block', 10, 2 );

/**
 * AJAX function render more posts.
 *
 * @return void
 */
function mhplg_query_pagination_render_more_query() {
	if (isset($_GET['attrs'])) :
		$block = json_decode( stripslashes( $_GET['attrs'] ), true );
		$paged = absint( $_GET['paged'] ?? 1 );

		if ( $block ) {
			$block['attrs']['query']['offset'] += $block['attrs']['query']['perPage'] * $paged;
			echo $block['attrs']['query']['offset'];
			echo render_block( $block );
		}
	endif;
}
//add_action( 'wp_ajax_query_render_more_pagination', __NAMESPACE__ . '\query_pagination_render_more_query' );
//add_action( 'wp_ajax_nopriv_query_render_more_pagination', __NAMESPACE__ . '\query_pagination_render_more_query' );
add_action( 'wp_enqueue_scripts', 'mhplg_query_pagination_render_more_query' );

function mhplg_my_theme_scripts() {
    wp_enqueue_script( 'my-great-script', '/wp-content/plugins/my-first-plugin/js/jquery-3.6.0.min.js', array( 'jquery' ), '3.6.0', true );
    wp_enqueue_script( 'my-first-plugin', '/wp-content/plugins/my-first-plugin/js/my-first-plugin.js', array( 'jquery' ), '0.0.1', true );
}
add_action( 'wp_enqueue_scripts', 'mhplg_my_theme_scripts' );
