<?php

class DMGReadMoreCommand {
    public function __invoke($args, $assoc_args) {
        $date_before = $assoc_args['date-before'] ?? date('Y-m-d', strtotime('0 days'));
        $date_after = $assoc_args['date-after'] ?? date('Y-m-d', strtotime('-30 days'));

        $query_args = [
            'date_query' => [
                'after'     => $date_after,
                'before'    => $date_before,
                'inclusive' => true,
            ],
            's' => '<!-- wp:create-block/dmg-media-read-more',
            'posts_per_page' => -1,
            'fields' => 'ids'
        ];

        $query = new WP_Query($query_args);

        if ( $query->have_posts() ) {
            WP_CLI::line( implode( "\n", $query->posts ) );
        } else {
            WP_CLI::log( 'No posts found or an error occurred.' );
        }
    }
}