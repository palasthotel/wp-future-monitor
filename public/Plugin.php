<?php
/**
 * Plugin Name: Future Monitor
 * Plugin URI: https://github.com/palasthotel/future-monitor
 * Description: Monitors the future of your system. For example planned posts...
 * Version: 1.0.3
 * Author: Palasthotel <webmaster@palasthotel.de>
 * Author URI: https://palasthotel.de
 * Text Domain: future-monitor
 * Domain Path: /languages
 * Requires at least: 4.0
 * Tested up to: 7.1.2
 * Requires PHP: 8.0
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @copyright Copyright (c) 2020, Palasthotel
 * @package Palasthotel\FutureMonitor
 */

namespace Palasthotel\FutureMonitor;

defined( 'ABSPATH' ) || exit;

require_once dirname(__FILE__) . "/vendor/autoload.php";

class Plugin extends Components\Plugin {

	const DOMAIN = "future-monitor";
	const SCHEDULE_ACTION = "future_monitor_publish_future_posts";
	public Store $store;
	public Schedule $schedule;
	public DashboardWidget $dashboardWidget;

	public function onCreate(): void {
		$this->loadTextdomain(Plugin::DOMAIN, "languages");

		$this->store = new Store();
		$this->schedule = new Schedule($this);
		$this->dashboardWidget = new DashboardWidget($this);

	}

	public function onSiteActivation(): void {
		parent::onSiteActivation();
		$this->schedule->start();
	}

	public function onSiteDeactivation(): void {
		parent::onSiteDeactivation();
		$this->schedule->stop();
	}
}

new Plugin();
