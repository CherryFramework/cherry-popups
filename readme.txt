=== Cherry PopUps ===

Contributors: TemplateMonster 2002
Tags: custom post type, popup, subscribe, mailchimp, cherry framework, login, sign in, custom popup styles, custom open events
Requires at least: 4.5
Tested up to: 4.8.1
Stable tag: 1.1.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Cherry PopUps is a powerful and extremely useful plugin, which allows you to create customizable pop-up windows and manage them effectively.

== Description ==

Cherry PopUps is a standalone plugin, however it depends on the Cherry Framework package that comes with the plugin by default. You don't need to download any additional modules manually, just be aware of this dependency.

= Flexible Settings =
When it comes to customization, Cherry PopUp offers many options to choose from. You can freely modify the look of your pop-up windows by defining its shape, color scheme, background type, etc.
Here is a list of options that you can use to reshape your pop-ups:

* Popup layout type: allows you to choose between center, center & full width and bottom & full width views.
* Show/Hide animation: allows you to apply fade, scale or move up animations;
* Base style preset: offers several styles for a better compatibility of your pop-ups with the design of your website;
* Container background type and color/image: lets you choose, whether to use an image-based or a single-color background.
* Container opacity, width and height: allows you to modify the basic visual parameters of the pop-up container.

You can also enable, disable or customize the overlay that will cover the content of your page when the pop-up is on.
Open and Close settings define the conditions under which pop-up windows show up and hide.

= Easy integration with Mailchimp =
One of the best ways of using pop-ups is for collecting emails via integrated newsletter subscription forms. Cherry PopUp is insegrated with Mailchimp out of the box, so you can leverage its functionality to increase traffic and user engagement of your website.
In order to connect Mailchimp to your website, just input your Mailchimp API Key and List ID.

= Customizable templates =
The plugin offers simplified templating system for .tmpl files. 13 templates are available by default:

* default-popup.tmpl - Main template for displaying popup with all available macros.
* default-simple-popup.tmpl - Template for displaying popup without subscribe form macros.
* default-subscribe-popup.tmpl - Template for displaying popup without content macros.

Cherry PopUp plugin supports templates, which can be quickly modified using macros:

* TITLE - Title of your pop-up window;
* CONTENT - General content;
* SUBSCRIBEFORM - To place a Mailchimp email subscription form;

All you need is to create a template file with the macros listed above, and upload it to the templates folder.

Standard templates can be rewritten in the theme. For that you need to create cherry-popups folder in the root catalog of the theme and copy the necessary templates in there. You can also add your own templates. For that you need to create a file with .tmpl extension in the same folder.

= Double pop-ups =
You can enable two pop-up windows simultaneously: for example, one at the beginning, and one at the end of the page.

= Popup Identification on static page =
If standard settings are not enough for identifying visible section, there is a metablock that allows you to add a particular popup to any static page.

== Installation ==

1. Upload "Cherry Popups" folder to the "/wp-content/plugins/" directory
2. Activate the plugin through the "Plugins" menu in WordPress
3. Navigate to the "Cherry Popups" page available through the left menu

== Screenshots ==
1. Settings page - general settings.
2. Settings page - "Open page" settings.
3. Settings page - "Close page" settings.
4. Settings page - Mailing List manager settings(MailChimp).
5. Popup Settings - General.
6. Popup Settings - Overlay.
7. Popup Settings - "Open page" settings.
8. Popup Settings - "Close page" settings.
9. Popup Settings - Advanced settings.
10. Popup Identification on static page

== Configuration ==

= Plugin Options =
All plugin options are gathered in PopUps -> Settings

= General settings =
* Enable popups - Enable/disable plugin functions globally for the site
* Enable Plugin on Mobile Devices - Show/hide popups on mobile devices
 *Enable for logged users - Show/hide popups for logged in users

= "Open page" settings =
* Default Open Page Popup - Default  open page popup identity
* Open page popup display in - Pages identification for the default popup

= "Close page" settings =
* Default Close Page Popup - Default close page popup identity
* Close page popup display in - Pages identification for the default popup

= Mailing List manager settings(MailChimp) =
* MailChimp API key - Add MailChimp Api key
* MailChimp list ID - Profile list id

= Popup Settings =
Each popup has its own settings which are gathered in Popup settings

= General =
* Popup layout type - Choose popup layout type (center, fullwidth center, fullwidth bottom)
* Show/Hide animation - Choose show/hide animation effects(fade, scale, move up)
* Base style preset - Popup controls base color styles(default, light, dark, blue, red)
* Container background type - Container background type (fill-color, image)
* Container background color - Popup container background color
* Container background image - Choose container background image
* Container opacity - Container opacity (active for fill-color type)
* Popup width - Popup container width
* Popup height - Popup container height

= Overlay =
* Type of overlay - Overlay type
* Overlay background color - Choose overlay background color
* Overlay opacity - Set overlay background opacity
* Overlay background image - Set overlay background image
* Overlay close clicking- Clicking on the overlay closes the popup

= "Open" page settings =
* "Open page" popup appear event - Set an event to which a popup will be opened
* Open delay - Set the time delay when the page loads
* User inactivity time - User inactivity time. Option for Inactivity time after
* Page scrolling value - Page scrolling progress(%). Option for On page scrolling event

= "Close" page settings =
* Outside viewport -  Set top border for mouse cursor
* Page unfocus - User sets focus on another page or app in the system

= Advanced settings =
*Template - Template file for content

== Changelog ==

= 1.0.0 =

* Initial release

= 1.0.1 =

* Hot fixes

= 1.0.2 =

* Hot fixes

= 1.1.0 =

* Add styles settings tab and new avaliable styles options
* Add new fullwidth popup layout type
* Add login form popup
* Add sign up form popup
* Add subscribe form popup
* Add custom opening event(click, hover)
* Hot fixes

= 1.1.1 =

* Hot fixes

= 1.1.2 =

* Hot fixes

= 1.1.3 =

* Fixes
