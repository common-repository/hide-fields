=== Plugin Name ===
Contributors: brightvesseldev, milardovich
Requires at least: 5.0
Tested up to: 5.4.1
Requires PHP: 7.0
Stable tag: 1.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html


This plugin allows you to hide specific checkout fields on WooCommerce.

== Description ==

A simple plugin to hide the unwanted fields of the checkout depending on the order type. 

This plugin allows the user to define which fields will be displayed in the checkout depending on the order type.
At the moment there are two different scenarios covered: A Free purchase or a Normal purchase(this one is priced).

A Tab has been implemented in the Woocommerce Settings. Inside of this tab the user can find 2 sections, one for each Order type scenario.
Each section is populated dinamically with a set of checkboxes that correspond to the default checkout fields. Here the user can check the unwanted fields to exclude them from the checkout.

The checked fields will be removed from the checkout, order and the email. 

Is very important to notice that if order includes any shipping type, the fields related with the locations (such as country, state, address, etc.) will be displayed.       
     
== Installation ==


* PHP 7.2 or greater is recommended
* MySQL 5.6 or greater is recommended


1. Upload the plugin files to the `/wp-content/plugins/hidefields` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Woocommerce->Settings->Hide Fields tab to configure the plugin



== Changelog ==

There is no changelog yet.

