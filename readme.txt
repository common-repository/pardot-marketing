=== Pardot Marketing ===
Contributors: bmarshall511
Tags: pardot, marketing, elementor, elementor widget
Donate link: https://benmarshall.me/donate/?utm_source=pardot_marketing&utm_medium=wordpress_repo&utm_campaign=donate
Requires at least: 5.2
Tested up to: 5.8.1
Requires PHP: 7.3
Stable tag: 1.1.4
License: GNU GPLv3
License URI: https://choosealicense.com/licenses/gpl-3.0/

Annoyed with Pardot's constraining form handler embeds? The Pardot Marketing WordPress plugin allows you to easily add styled forms to your site — and more.

== Description ==

Pardot's [form handler embeds](https://www.pardot.com/training/form-handlers-15-introduction-to-form-handlers/) make it difficult to match your site's look and feel. That's where the Pardot Marketing WordPress comes in. Quickly add site-matching forms with custom validation rules using an Elementor widget. Say 'good-bye' to those annoying & ugly Form Handler embeds.

= Features Include =

* Control access with Pardot-specific user roles
* View Pardot prospects from your WordPress dashboard
* Customize form handlers to match the look of your site
* Easily add forms via an Elementor widget
* Provides real-time field validation
* Create custom field validation rules
* Easy-to-add pre-configured form fields
* Create dynamic, pre-populated form fields
* Customize success & error messages
* Ability to integrate into any theme or plugin

== Installation ==

1. Upload the entire pardot-marketing folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the Plugins screen (Plugins > Installed Plugins).

For more information, see the [plugin’s website](https://www.highfivery.com/projects/pardot-wordpress-plugin/).

== Frequently Asked Questions ==

= What user roles are available? =

* Pardot Administrator (<code>pardotmarketing_admin</code>) - Inherits WP admin & all Pardot Marketing capabilities

= What user capabilites are there? =

* <code>pardotmarketing_read_prospects</code> - Allows access to the Pardot Prospects admin screen

= How do I add my own field validation rules? =

Pardot Marketing uses the [jQuery Validation plugin](https://jqueryvalidation.org/) to handle & add custom valdations. You can add your own by using the `pardotmarketing_form_handler_scripts_filter` to add your own JS that can extend the form rules or inject your own JS rules via the `pardotmarketing_form_handler_validation_options_filter_[form_id]` filter.

= What action hooks are available? =

* `pardotmarketing_before_form_handler` - Fires before output of a Form Handler Elementor widget.
* `pardotmarketing_pre_form` - Fires right before the opening `form` element in the Form Handler Elementor widgets.
* `pardotmarketing_form` - Fires right after the opening `form` element in the Form Handler Elementor widgets.
* `pardotmarketing_pre_error_msg` - Fires before output of the error message notification.
* `pardotmarketing_error_msg` - Fires right after the opening tag of the error message notification.
* `pardotmarketing_error_post_msg` - Fires at the end of the error message notification, before the closing tag.

= What filters are available? =

* `pardotmarketing_form_handler_styles_filter` - Modifies what registered styles are used when a Form Handler Elementor widget is on the page.
* `pardotmarketing_form_handler_scripts_filter` - Modifies what registered scripts are used when a Form Handler Elementor widget is on the page.
* `pardotmarketing_form_handler_scripts_filter` - Modifies what registered scripts are used when a Form Handler Elementor widget is on the page.
* `pardotmarketing_form_handler_validation_options_filter_[form_id]` - Modifies/adds to the default [jQuery Validation](https://jqueryvalidation.org/) form options. [form_id] should be the value from the form `Form ID` field.

== Changelog ==

= 1.1.4 =

* [Fix] Missing Elementor widget icon
* [Feature] reCAPTCHA v3 integration

= 1.1.3 =

* [Resolves #5](https://github.com/bmarshall511/pardot-marketing/issues/5). Added border styling options to the form handler submit button.
* [Resolves #4](https://github.com/bmarshall511/pardot-marketing/issues/4). Added ability to hide field labels.
* [Resolves #3](https://github.com/bmarshall511/pardot-marketing/issues/3). Added radio field option to the form handlers.

= 1.1.2 =

* Enhancement - Ability to select the submitted value for country select fields (i.e. country name or abbr.)
* Enhancement - Developers now have access to the `pardot_marketing_country_options_WIDGET_ID` filter, see [#1](https://github.com/bmarshall511/pardot-marketing/issues)

= 1.1.1 =

* Fix - Bug when multiple Pardot Form Handler widgets are on the page.

= 1.1.0 =

* Added Prospects & Forms admin dashboard with data pulled from the Pardot API

= 1.0.0 =

* Initial commit
