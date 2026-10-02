<?php


namespace Palasthotel\FutureMonitor;

defined( 'ABSPATH' ) || exit;

/**
 * @property Plugin plugin
 */
class DashboardWidget {

	public $plugin;

	const ID = "future-posts-monitor";

	public function __construct(Plugin $plugin) {
		$this->plugin = $plugin;
		add_action('wp_dashboard_setup', array($this, 'setup'));
	}

	public function setup(){
		// The dashboard is reachable with the "read" capability, so without this
		// check a subscriber would see the titles of every scheduled post. Core
		// gates its own widgets the same way.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}
		wp_add_dashboard_widget(self::ID, __("Future posts monitor", 'future-monitor'), array($this, 'render_future_posts'));
	}

	public function render_future_posts(){
		$post_ids = $this->plugin->store->getScheduledPostIdsFromOptions();
		$watched_ids = $this->plugin->store->getFuturePostIds();

		$query = new \WP_Query(array(
			"post_status" => "future",
			"post_type" => "any",
			"order" => "asc",
			"orderby" => "post_date",
			"posts_per_page" => -1,
		));
		while ($query->have_posts()){
			$query->the_post();

			// An author is only shown what they may edit anyway.
			if ( ! current_user_can( 'edit_post', get_the_ID() ) ) {
				continue;
			}

			echo "<p>";
			if(in_array(get_the_ID(), $post_ids)) {
				echo "✅ ";
			} else if( $this->plugin->schedule->isScheduled() && in_array(get_the_ID(), $watched_ids)){
				echo "⚠️ ";
			} else {
				echo "🚨 ";
			}
			printf( "<a href='%s' target='_blank'>", esc_url( get_edit_post_link( get_the_ID() ) ) );
			echo esc_html( get_the_title() );
			echo "</a>";
			echo "<br/>";
			echo "<span class='description'>";

			echo esc_html(
				date_i18n(
					get_option("date_format")." – ".get_option("time_format"),
					strtotime(get_post()->post_date)
				)
			);

			echo "</span>";
			echo "</p>";

		}
		wp_reset_postdata();
		echo "<hr>";

		echo "<p>";
		esc_html_e("✅️ posts are monitored by WordPress Core for future publication.", 'future-monitor');
		echo "</p>";

		echo "<p>";
		esc_html_e("⚠️ posts are not monitored by WordPress Core for future publication, but I promise to publish them (Future Monitor Plugin).", 'future-monitor');
		echo "</p>";

		echo "<p>";
		esc_html_e("Open and resave all future posts with 🚨.", 'future-monitor');
		echo "</p>";


	}
}
