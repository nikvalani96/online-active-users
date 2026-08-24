=== Online Active Users ===
Contributors: valani9099, alkesh7
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=APRNBJUZHRP7G
Tags: online users, active users, user tracking, user status, user activity
Requires at least: 6.3
Tested up to: 7.1
Stable tag: 3.4.4
Requires PHP: 8.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Plugin Name: Online Active Users
Plugin Title: Online Active Users Plugin
Plugin URI: https://wordpress.org/plugins/online-active-users/
Author: Webizito
Author URI: http://webizito.com/
Text Domain: online-active-users
Version: 3.4.4

Online Active Users is a lightweight, powerful plugin to monitor and display how many users are currently online active on your WordPress website.

== Description ==


**WP Online Active Users** is a lightweight and powerful WordPress plugin that lets you monitor and display how many users are currently online active on your website. Easily view real-time user activity and last seen status directly on the Users page in your WordPress admin dashboard.

This plugin is the best solution to show live **online active users** on your WordPress site, providing instant insights into user engagement and site activity. Simply login and check the **Users** menu to see all currently logged-in users.

Ideal for website owners who want to enhance site security, track active sessions, and improve user interaction. With a simple, user-friendly interface, **WP Online Active Users** is easy to install and configure. This plugin is ideal for all types of websites including blogs, membership sites, forums, and WooCommerce stores.

**WooCommerce compatible**, it also displays the number of logged-in customers on your online store, helping you monitor customer activity in real-time.

**WP Online Active Users** also provides a user-friendly shortcode that lets you display live user counts anywhere on your site — whether on posts, pages, widgets, or within compatible with popular page builders.

`[webi_active_user]`


**KEY FEATURES:**

* View real-time online active user status in WordPress admin dashboard.
* Green dot indicator for online users and red dot with last seen for offline users.
* Display active users count in the WordPress admin bar.
* WooCommerce support to track logged-in customers.
* Shortcode support for displaying active users on posts, pages, or widgets.
* Display online active users list table with user info.

**Who Should Use WP Online Active Users?**

* Website Owners and Administrators
* Membership and Community Sites
* WooCommerce Store Managers
* Editors, Authors, and Subscribers

**Why Use WP Online Active Users on Your Site?**

* **Real-Time User Monitoring:** Instantly see how many users are active on your website to better understand engagement and traffic patterns.
* **Improve Security:** Detect unusual login activity and monitor user sessions to enhance your site’s security.
* **Boost User Engagement:** Showcase live user activity as social proof to encourage interaction and trust.
* **WooCommerce Friendly:** Track active customers in your online store to optimize marketing and sales strategies.
* **Easy to Use & Lightweight:** Minimal setup with a clean interface that won’t slow down your website.
* **Flexible Display Options:** Use shortcode to show online user counts anywhere on your site, compatible with all page builders.

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

= How can I limit the number of active users on my WordPress website? =

* There are several ways to limit the number of active users on your WordPress website, such as using plugins like WP Limit Login Attempts or Limit Login Attempts Reloaded. These plugins allow you to set limits on the number of login attempts, which can help prevent brute-force attacks and limit the number of active users on your site. 


== Third-Party Services ==

This plugin connects to external services to enrich the "Online Active Users" admin list table with an approximate country, timezone, and country flag for each currently-online user, based on their IP address.

**IP geolocation (country, timezone, country code)**
* Service: ipwho.is (by IPWHOIS.io)
* When: once per unique IP address, cached for 24 hours, whenever a logged-in user's status is recorded.
* Data sent: the visiting user's IP address, sent as part of the request URL. No other personal data is sent.
* Terms of Service: https://ipwhois.io/terms
* Privacy Policy: https://ipwhois.io/privacy

**Public IP lookup (local/development environments only)**
* Service: ipify (https://www.ipify.org)
* When: only as a fallback, when the site is running on localhost (IP resolves to 127.0.0.1 or ::1), to resolve a public IP for display purposes.
* Data sent: no parameters are sent; the request has no request body or query data.

**Country flag icons**
* Service: flagcdn.com
* When: whenever the "Online Active Users" admin list table is displayed, to load a small flag image per country.
* Data sent: the resolved two-letter country code, sent as part of the image URL requested by the administrator's browser.

None of these services are used for advertising, tracking, or analytics; they are used only to display geolocation context to site administrators.

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
