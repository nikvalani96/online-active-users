=== Online Active Users ===
Contributors: valani9099, alkesh7
Tags: online users, active users, user tracking, user status, user activity
Requires at least: 6.3
Tested up to: 7.1.4
Stable tag: 3.5.2
Requires PHP: 8.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Plugin Name: Online Active Users
Plugin Title: Online Active Users Plugin
Plugin URI: https://wordpress.org/plugins/online-active-users/
Author: Webizito
Author URI: https://webizito.com/
Text Domain: online-active-users
Version: 3.5.2

Monitor online users, track active users, display user status and review user activity with WooCommerce customer chat.

== Description ==

**WP Online Active Users** helps you answer a simple question: **who is online on my website right now?**

Monitor **online users** from one dashboard and see signed-in members who are currently active. WP Online Active Users shows clear online status without counting anonymous visitors or advertising bots as registered users.

See **active users** in a responsive list with recent activity, account details and last-seen information. Display the live active-user count on posts, pages, widgets or the WordPress admin bar with a lightweight shortcode and block.

Improve **user tracking** with searchable filters for role, country and online status. This helps website owners and administrators quickly understand signed-in activity without adding complex analytics setup.

Review **user status** at a glance with online, offline and inactive indicators. The dashboard also provides user details that help administrators understand recent sessions and member availability.

Use the **user activity** view to monitor recent behavior and last-seen timestamps. WP Online Active Users works with membership websites, communities, forums, online learning sites and customer portals.

For **WooCommerce** stores, monitor logged-in customers alongside normal WordPress users. The Pro add-on extends this with **WooCommerce** customer support chat, order history, recently viewed product history, returning-user notifications, session controls, announcements, live page viewers, feedback campaigns and activity exports.

WP Online Active Users works with popular WordPress themes, blocks, widgets and page builders. It keeps the Free features lightweight while allowing Pro tools to be added when your website needs more customer engagement features.

Use the included shortcode to display the live count of active signed-in users on posts, pages, sidebars or templates:

`[webi_active_user]`

**Key Online User and User Tracking Features**

* **Real-time online users** — Instantly see which signed-in users are currently active.
* **Active users dashboard** — Review online, offline and inactive user totals with recent activity.
* **Last-seen user activity** — Check recent activity, account details and user status information.
* **Accurate user tracking status** — Use the configured online and inactive time windows for useful dashboard reporting.
* **Front-end active user count** — Display the live count with a shortcode, widget or block.
* **Admin bar counter** — Keep the number of active users visible while managing your website.
* **WooCommerce customer tracking** — Monitor signed-in customers visiting your online store.
* **User activity filters** — Filter the activity table by role, country, online status and other available details.
* **Country and timezone details** — Enable optional IP-based location lookup for signed-in users.
* **Responsive admin dashboard** — View online, offline and inactive user totals on desktop, tablet and mobile.
* **Lightweight display options** — Use the plugin with posts, pages, widgets, shortcodes and page builders.

**Unlock More with Online Active Users Pro**

Upgrade your site from basic activity monitoring to a complete customer engagement and self-service toolkit.

* **WooCommerce order history in chat** — Let signed-in customers securely view their latest order, previous orders or a specific order through support chat.
* **Read-only WooCommerce order cards** — Show permitted order date, status, total, purchased items and the customer's native WooCommerce order link.
* **Recently viewed history in chat** — Let customers ask for recently viewed products or pages and clear their browsing history.
* **Human support chat** — Manage conversations from a dedicated support inbox and manually hand off automated conversations.
* **AI-assisted support replies** — Connect a supported AI provider using your own API key while retaining approved answers and human handoff.
* **Returning-user notifications** — Welcome customers back with inactivity rules, cooldowns, role targeting and optional email alerts.
* **Customer feedback campaigns** — Collect ratings and written feedback with targeting, timing and display controls.
* **Device and session insights** — Review browser, device, login and recent page-activity details.
* **Login session controls** — Set simultaneous-login limits and idle logout rules for supported user roles.
* **Scheduled announcements** — Publish timed messages by audience with configurable appearance and dismiss options.
* **Live page viewers** — Display real-time viewing activity for selected pages.
* **CSV and PDF exports** — Export selected user activity and feature summaries for reporting.

**[Upgrade to Online Active Users Pro](https://webizito.com/online-active-users/)** to unlock advanced activity monitoring, WooCommerce self-service, session controls and customer engagement tools.

WooCommerce order and browsing-history chat features require Pro, WooCommerce where applicable, a signed-in customer account and explicit administrator opt-in. Private payment, address, phone, email and transaction data are not included in chat order cards.

**Who can use Online Active Users?**

* WordPress website owners and administrators
* Membership and community websites
* WooCommerce store managers
* Forums, learning sites and customer portals
* Editors, authors and support teams

The Pro add-on is installed separately. The free plugin's online-user tracking and display features remain available without Pro.

AI-assisted replies require your own supported provider account and API key. When enabled, relevant conversation content is sent to the selected provider, subject to its terms and privacy policy. API usage is billed separately by your provider.

**Support**
We are committed to providing ongoing updates and new features based on user feedback. For support, feature requests, or bug reports, please visit our [support forum](https://wordpress.org/support/plugin/online-active-users/ "support forum").


== Installation ==

* Download the latest version of WP Online Active Users.
* Upload plugin files to your plugins folder, or install using WordPress built-in Add New Plugin installer;
* Activate the plugin;
* Go to WP-Admin -> Users
* The column name is User Online Status.

== Frequently Asked Questions ==

= What is an active user on a wordpress site? =

* An active user on a website is a visitor who is currently logging or interacting with the site. 

= How to display Online Active Users in wordpress site using Shortcodes? =

* You can use the ShortCode [webi_active_user] to use in your admin Pages/Templates/Themes.

= How can I see the number of active users on my WordPress website? =

* There are several ways to see the number of active users on your WordPress website. One way is to use a plugin like WP Online Active Users displays the number of active users in real-time and provides additional information about the users.

= Why is it important to know the number of active users on my website? =

* Knowing the number of active users on your WordPress website can help you make informed decisions about your site's content, design, and functionality. By understanding how many users are visiting your site and what they are doing, you can optimize your site to better meet their needs and improve their overall experience. 

= Can I limit simultaneous login sessions? =

* The free plugin does not limit simultaneous logins. The Pro add-on provides configurable session limits and idle logout. Login-attempt protection is a separate function.

= Does the free plugin track visitors who are not logged in? =

The free plugin's online-user count and last-seen features track signed-in WordPress users. They are not a count of all anonymous visitors.

= How do I show the latest blog articles in the dashboard? =

The dashboard always shows the Latest Blog Articles section. Related published articles are requested automatically in the background and cached; there is no settings toggle. The Webizito child theme provides the published-content endpoint. The feed URL can be customized with the `wpoau_updates_feed_url` filter, but it must remain on the trusted `webizito.com` host and use the `/wp-json/wpoau/v1/content` path.

= Can I display the online-user count using the block editor? =

Yes. Add the Online Active Users block to a post or page. You can also use [webi_active_user] inside a Shortcode block.

= Why can a logged-in user appear offline? =

Online status is based on recently recorded activity, not simply whether a login session exists. A user may appear offline after activity stops, even if they have not logged out.

= How do I enable country and timezone information? =

Open the plugin's Active Users screen and enable location lookup. Country and timezone are estimated from the recorded IP address using an external geolocation service. Availability and accuracy can vary.

= Why are country and timezone unavailable on localhost? =

Local addresses such as 127.0.0.1 and ::1 cannot identify a public geographic location. Location details may also be unavailable for private addresses, VPNs or failed lookups.

= How is WP Online Active Users Pro installed? =

Pro is a paid add-on distributed separately from WordPress.org. Keep the free plugin active, install the Pro ZIP separately and activate your license through License & Plan.

= Does support chat require an AI provider? =

No. Pro supports human conversations through the support inbox without an AI connection. AI-assisted replies require a supported provider account and your own API key.

= What information is sent when AI-assisted replies are used? =

Relevant conversation content is sent to the provider configured by the site owner. The provider's terms and privacy policy apply, and API usage is billed separately by the provider.

= Can I export user activity as CSV or PDF? =

Yes, with Pro. Select users using the available filters, choose report columns and download CSV or a locally generated PDF. Reports contain selected activity details and feature summaries, not a complete backup of chat conversations or feedback history.

= Can customers ask for WooCommerce order history in support chat? =

Yes, with Pro and WooCommerce. After the administrator enables the order self-service option, signed-in customers can ask for their latest order, order history or a specific order number. Chat shows a read-only order card with permitted order details and a link to WooCommerce's order page. The chat does not display addresses, phone numbers, email addresses, payment details or transaction data.

= Can customers ask for recently viewed pages and products in chat? =

Yes, with Pro after browsing-history self-service is enabled. Signed-in customers can request recently viewed pages or products and clear their stored browsing history. History excludes cart, checkout, account, private and password-protected pages.

= Can I schedule announcements for specific user roles? =

Yes, with Pro. Set a start and end time, choose the eligible roles and configure whether users can dismiss the announcement.

= Where can I find setup guides and full documentation? =

Visit the [WP Online Active Users documentation](https://webizito.com/docs/) for installation, Free and Pro setup guides, WooCommerce features and troubleshooting.


== Screenshots ==

1. Admin - Users Pages - User Online Status
2. Admin - Dashboard Online Active Users
3. Admin - Display Active User in Admin Bar 
4. Admin - Here You can also add the shortcode where you want the display of Active Users to appear on any WordPress post, page, or widget sidebar
5. Fronted - Here display Online Active Users in pages, post and widget sidebar using shortcode
6. Admin - Here display Online Active Users list Table With User info

== Changelog ==

= 1.0 =
* Initial release

= 1.1 =
* Added Shortcode to display currently active user count anywhere in site
* Minor some bug fixes

= 1.2 =
* Added in admin bar to display currently active users counter
* NEW: Bump to WordPress 6.2
* Minor some bug fixes

= 1.3 =
* Minor some bug fixes

= 1.4 =
* Fixed Two-Factor plugin column content issue

= 1.5 =
* Added uninstall file and improved security 

= 1.6 =
* Minor some bug fixes

= 1.7 =
* Wordpress latest Version bug fixes

= 1.8 =
* Minor some bug fixes

= 1.9 =
* Wordpress latest Version Support

= 2.0 =
* Minor some bug fixes

= 2.1 =
* Wordpress latest Version supported

= 2.2 =
* Wordpress latest Version supported

= 2.3 =
* Wordpress latest Version supported

= 2.4 =
* Wordpress latest Version supported

= 2.5 =
* Add Widget option

= 2.6 =
* Fix bug and Always show last seen

= 2.7 =
* Wordpress latest Version supported

= 2.8 =
* Fix bug

= 2.9 =
* Update User online list table with show user info and Fix bug

= 3.0 =
* Fixed Deprecated id issue.

= 3.1 =
* Wordpress latest Version supported

= 3.2 =
* Fix css issue

= 3.3 =
* Fix css issue.
* Wordpress latest Version supported. Upgrade immediately.

= 3.4 =
* Fix some bug.

= 3.4.1 =
* Fixed the plugin's text domain to match its slug so translations load correctly.
* Escaped all dynamic output and added missing translation wrappers for full translation compatibility.
* Coding standards (WPCS/PHPCS) cleanup: removed dead/duplicate code, added nonce-safety and sanitization fixes on the users list table filters.

= 3.4.2 =
* Fix some bug.

= 3.4.3 =
* Fixed a `Requires PHP` mismatch left over from a botched merge (readme.txt said 7.3, composer.json said 8.0); both the plugin header and readme.txt now correctly state 8.0.
* Added `Requires at least` and `Requires PHP` to the plugin's own header, not just readme.txt.
* Removed calls to ip-api.com (its free tier prohibits commercial use, conflicting with this plugin's WooCommerce support, and was requested over plain HTTP); country, country code, and timezone are now all resolved from a single cached HTTPS call to ipwho.is.
* Added a Third-Party Services section to readme.txt disclosing all external services this plugin calls.
* Verified WordPress 7.1 compatibility; bumped Tested up to.

= 3.4.4 =
* Fix readme.txt file.
* Fix some bug.

= 3.4.5 =
* Added the Online Active Users block to display the active user count in the WordPress block editor.
* Confirmed compatibility with WordPress 7.1. Upgrade immediately.

= 3.5.0 =
* Added country and timezone lookup controls to enable or disable geolocation from the Active Users screen.
* Show country, timezone and country filters only when location lookup is enabled.
* Bundled local country flags and improved handling of unavailable location information.
* Improved geolocation response validation and temporarily cached failed lookups.
* Added integration with WordPress personal-data export and erasure tools.
* Improved the user activity table, filtering and online/offline status handling.
* Updated admin navigation, tab labels and installed edition information.
* Added a Pro add-on overview with feature, documentation and support links.
* Added integration points for the separately installed Pro add-on's device details and activity exports.
* Improved settings layouts, checkbox sizing and upgrade links.
* Removed unsolicited dashboard review prompts.
* Updated FAQs and clarified Free and Pro feature availability.
* Updated external-service and privacy disclosures.
* Confirmed compatibility with WordPress 7.1.4.
* Fixed security issues. Updating immediately is recommended.

= 3.5.1 =
* Fix stable tag mismatch in the previous release.
* Improve release version consistency.

= 3.5.2 =
* Replace the basic dashboard box with a responsive activity overview whose status counts follow the user table's 50-second online and 30-day inactive rules; users without recorded activity are excluded.
* Add an administrator-controlled, opt-in product-content feed with bounded HTTPS requests, background refresh, caching and privacy disclosure.
* Add a dedicated published-content feed route to the Webizito child theme.
* Improve dashboard overview readability with centered status counters, status dots, a title icon and compact footer links.
* Add an administrator-only Pro discovery link with a developer-configured, disabled-by-default offer.
* Keep Latest Blog Articles visible and refresh related published content in the background without a settings toggle.
* Add Pro support-chat self-service for WooCommerce order history, order details and recently viewed pages or products, with explicit administrator controls and privacy-safe data cards.
* Updating immediately is recommended.

== Upgrade Notice ==

= 1.0 =
* Initial Version.

= 1.1 =
* This version Added Shortcode to display currently active user count anywhere in site.

= 1.2 =
* This version Added in admin bar to display currently active users counter. Upgrade immediately.

= 1.3 =
* This version fixed some Minor bugs. Upgrade immediately.

= 1.4 =
* This version fixed Two-Factor plugin column content issue. Upgrade immediately.

= 1.5 =
* This version added uninstall file and improved security. Upgrade immediately.

= 1.6 =
* This version fixed some Minor bugs. Upgrade immediately.

= 1.7 =
* Wordpress latest Version supported. Upgrade immediately.

= 1.8 =
* This version fixed some Minor bugs. Upgrade immediately.

= 1.9 =
* Wordpress latest Version supported. Upgrade immediately.

= 2.0 =
* This version fixed some Minor bugs. Upgrade immediately.

= 2.1 =
* Wordpress latest Version supported. Upgrade immediately.

= 2.2 =
* Wordpress latest Version supported. Upgrade immediately.

= 2.3 =
* Wordpress latest Version supported. Upgrade immediately.

= 2.4 =
* Wordpress latest Version supported. Upgrade immediately.

= 2.5 =
* Add Widget option.

= 2.6 =
* Bug fix and Always show last seen. Upgrade immediately.

= 2.7 =
* Wordpress latest Version supported. Upgrade immediately.

= 2.8 =
* Bug fixed. Upgrade immediately.

= 2.9 =
* Update User online list table with show user info and Bug fixed. Upgrade immediately.

= 3.0 =
* Fixed Deprecated id issue. Upgrade immediately.

= 3.1 =
* Wordpress latest Version supported. Upgrade immediately.

= 3.2 =
* Fix css issue.

= 3.3 =
* Fix css issue.
* Wordpress latest Version supported. Upgrade immediately.

= 3.4 =
* Fix some bug.

= 3.4.1 =
* Text domain and translation fixes, plus WPCS/PHPCS coding standards cleanup. Upgrade recommended.

= 3.4.2 =
* Fix some bug.

= 3.4.3 =
* Fixed Requires PHP mismatch, added Third-Party Services disclosure, and dropped a non-commercial-only geolocation provider. Upgrade recommended.

= 3.4.4 =
* Fix readme.txt file.
* Fix some bug. Upgrade recommended.

= 3.4.5 =
* Added the Online Active Users block to display the active user count in the WordPress block editor.
* Confirmed compatibility with WordPress 7.1. Upgrade immediately.

= 3.5.0 =
* Added country and timezone lookup controls to enable or disable geolocation from the Active Users screen.
* Show country, timezone and country filters only when location lookup is enabled.
* Bundled local country flags and improved handling of unavailable location information.
* Improved geolocation response validation and temporarily cached failed lookups.
* Added integration with WordPress personal-data export and erasure tools.
* Improved the user activity table, filtering and online/offline status handling.
* Updated admin navigation, tab labels and installed edition information.
* Added a Pro add-on overview with feature, documentation and support links.
* Added integration points for the separately installed Pro add-on's device details and activity exports.
* Improved settings layouts, checkbox sizing and upgrade links.
* Removed unsolicited dashboard review prompts.
* Updated FAQs and clarified Free and Pro feature availability.
* Updated external-service and privacy disclosures.
* Confirmed compatibility with WordPress 7.1.4.
* Fixed security issues. Updating immediately is recommended.

= 3.5.1 =
* Fix stable tag mismatch in the previous release.
* Improve release version consistency.

= 3.5.2 =
* Add the responsive activity overview, WooCommerce order and browsing-history chat self-service, and privacy-focused admin controls. Upgrade recommended.
* Improve the dashboard overview, add a developer-configured Pro offer link, and keep the updates section visible. The discount remains disabled until verified.
* Updating immediately is recommended.


