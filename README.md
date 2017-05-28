API Client For Caldera Forms Pro


# Development Notes:
1) CF pro requires PHP5.4 or later.
2) This is probably going to get included in Caldera Forms. The file bootstrap-cf-pro-bootstrap.php is/will be used as conditional loader for CF API client.
3) Plugin only loads if CF_PRO_VER is not defined. This is useful because:
* You can define that constant earlier to disable CF Pro.
* We probably will add a version check on CF_PRO_VER there so that the CF API client can exist as a separate library for development and latest version will be used.
4) Inside of Caldera Forms Pro the following PHP code rules apply:
* Use the PSR-4ish class autoloader, not Caldera_Forms_Autoloader, which doesn't support nested dirs.
    * Class `container` is in namespace `calderawp\calderaforms\pro` and stored in namespace root (/classes)  in file `container.php`.
    * Class `settings` is in namespace`calderawp\calderaforms\pro\api\local` and is stored in `/classes/api/local` in file settings.php
* Use proper array syntax `[]` not `array()`
* Continue using snake_case function and class names, because WordPress.
5) Translation text domain is still "caldera-forms" because this will ship on dot org inside of Caldera Forms.
6) Make a point to represent errors by throwing an Exception using `calderawp\calderaforms\pro\exceptions\Exception`
    * This is important because Josh intends to build error logging into that.
    * You can use the `to_wp_error` method to convert it into a `WP_Error` object
7) JavaScript for UI is written in ES6 and VueJS. Gulp is used to compile into minified, browser JavaScript.
    * Use /assets/js/admin to write JavaScript
    * /assets/js/ is output of Gulp compiler.
8) CSS is minified using Gulp
    * Use /assets/css/admin to write CSS
    * /assets/css is output of Gulp compiler
9) This is build system is a prototype of how Josh want it to work in all CF add-ons/ Caldera Forms. It's a little wonky. Improvements appreciated.