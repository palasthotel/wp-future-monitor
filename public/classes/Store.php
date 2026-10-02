<?php


namespace Palasthotel\FutureMonitor;

defined( 'ABSPATH' ) || exit;


use WP_Post;

class Store {

	private ?array $post_ids = null;

	/**
	 * @return int[]
	 */
	public function getScheduledPostIdsFromOptions(): array {

		if ( $this->post_ids === null ) {
			// The cron array is [timestamp][hook][md5 of the args] => event, so posts
			// scheduled for the same second share one hook entry - every event in it
			// counts, not only the first.
			$cron           = get_option( "cron" );
			$this->post_ids = array();
			foreach ( is_array( $cron ) ? $cron : array() as $hooks ) {
				if ( ! is_array( $hooks ) || ! isset( $hooks["publish_future_post"] ) || ! is_array( $hooks["publish_future_post"] ) ) {
					continue;
				}
				foreach ( $hooks["publish_future_post"] as $event ) {
					if ( isset( $event["args"] ) && is_array( $event["args"] ) && ! empty( $event["args"] ) ) {
						$this->post_ids[] = (int) end( $event["args"] );
					}
				}
			}
		}

		return $this->post_ids;
	}

	/**
	 * @return WP_Post[]
	 */
	public function getFuturePostIds(): array {
		return get_posts( array(
			'fields'         => 'ids',
			"post_status"    => "future",
			"post_type"      => "any",
			"order"          => "asc",
			"orderby"        => "post_date",
			"posts_per_page" => - 1,
		) );
	}

	/**
	 * @return WP_Post[]
	 */
	public function getPublishablePostIds(): array {
		return get_posts( array(
			'fields'         => 'ids',
			"post_status"    => "future",
			"post_type"      => "any",
			"order"          => "asc",
			"orderby"        => "post_date",
			"posts_per_page" => - 1,
			"date_query"     => array(
				'before' => 'now',
			),
		) );
	}

}