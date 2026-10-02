=== Future Monitor ===
Contributors: palasthotel, edwardbock, janaeggebrecht
Donate link: https://palasthotel.de/
Tags: dashboard, widget, planned posts, schedule visualization
Requires at least: 4.0
Tested up to: 7.1.2
Requires PHP: 8.0
Stable tag: 1.0.3
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Shows on the dashboard whether your scheduled posts will actually be published, and publishes the ones WordPress missed.

== Description ==

WordPress publishes every scheduled post through its own cron event. If that event gets lost - a plugin clearing the cron schedule, a failed write to the database, a restored backup - the post stays scheduled forever and nobody notices. This is the "missed schedule" problem.

Future Monitor adds a dashboard widget that lists every scheduled post with a verdict:

* ✅ WordPress has a publishing event for the post and will publish it.
* ⚠️ WordPress has no event for it, but Future Monitor's hourly check watches the post and will publish it.
* 🚨 Nothing will publish the post. Open it and save it again.

In addition, an hourly check publishes every scheduled post whose date has already passed.

The widget is shown to users who may edit posts, and each of them only sees the scheduled posts they may edit.

Note that the hourly check is a cron event itself. It helps when single posts lost their event while WP-Cron as a whole still works; if WP-Cron does not run at all, the widget shows the problem, but the check cannot fix it.

== Installation ==

1. Install the plugin from the plugin directory, or upload the `future-monitor` folder to `/wp-content/plugins/`.
2. Activate the plugin on the Plugins screen.
3. Find the "Future posts monitor" widget on the dashboard.

== Frequently Asked Questions ==

= A post shows 🚨. What do I do? =

Open the post and update it without changing its date. WordPress then schedules a new publishing event, and the post shows ✅.

= Does the plugin change my posts? =

Only by publishing scheduled posts whose date has passed - which is what WordPress should have done.

== Changelog ==

= 1.0.3 =
**Bug Fixes**
* gate the dashboard widget behind edit_posts and escape its output (88196ba)

= 1.0.2 =
* Minor cleanup

= 1.0.1 =
* PHP 8.2 update

= 1.0 =
* First release
