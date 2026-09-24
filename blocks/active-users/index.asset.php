<?php
// phpcs:ignoreFile WordPress.Files.FileName.NotHyphenatedLowercase -- WordPress requires this filename for index.js dependencies.
/**
 * Editor script dependencies for the active users block.
 *
 * @package Online_Active_Users
 */

return array(
	'dependencies' => array(
		'wp-blocks',
		'wp-element',
		'wp-block-editor',
		'wp-components',
		'wp-server-side-render',
	),
	'version'      => '3.4.5',
);
