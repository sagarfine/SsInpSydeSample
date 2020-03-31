=== InpSyde sample ===

Contributors: Sagar Shinde

Tags: API, HTTP REQUEST

Requires at least: 5.0

Tested up to: 5.3.2

Requires PHP: 7.0

License: GPLv2 or later

License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

This plugin demonstrates, sending HTTP requests to third party API, process the received data and display.  

== Description ==

This is demonstration plugin, which sends HTTP request to third party API and received the JSON data. The plugin process this data and display in tabular format in custom page. It also sends an HTTP request through AJAX to display user details. 

**The following are major features of the plugin ....**
### Admin Panel
* The plugin create a separate interface in admin panel to manage the plugin settings.
* It adds the separate sub menu named "InpSyde" under the "Settings"
* The Admin can define the API URL, custom slug and time duration for cache expiry. 
* API Endpoint (Users): The URL of API where users data are available. The URL should be a valid URL.
* Custom Slug : Admin can add a custom slug. This slug will be used at the front end to open the custom page. This slug will be used as a suffix to the site URL to access custom endpoint. 
If admin add valid slug then, you can find a link to open the custom page. 
* Cache Expiry (Seconds): The plugin maintain the cache to avoid recurring requests to the API. Admin can add cache expiry duration in seconds.

### Front End
* Visitors can access front end custom endpoint page by adding a custom slug to the end of the site URL. 
* In this page, visitors can see the list of all the users. 
* Clicking on User Id, Username and Name of user will open the popup to show the details of that respective user. The plugin is using AJAX technique to show these details to avoid page refresh. 
* The front end page is responsive in nature and compatible with all latest browsers and mobile devices.



== Installation ==
### Method 1 :
* Download the plugin
* Upload the folder “SsInpSydeSample” to wp-content/plugins (or upload a zip through the WordPress admin)
Activate and enjoy! 

### Method 2 : Composer Install
* If you don't have composer then install it. 
* Open Command Prompt/Terminal and go to the wp-content/plugins directory.
* Run Command "composer require sagarfine/ssinpsydesample"
* Activate Plugin and Enjoy. 

== Frequently Asked Questions ==
### 1. Can we use any API URL/endpoint? 
#### No, you can not use any API URL/endpoint for this plugin. You can use https://jsonplaceholder.typicode.com/users this API sample URL only. 

### 2. Can we add any slug?
#### Yes, you can add any slug,. The slug should contain characters only. 

### 3. The custom slug is not working?
#### Please try to flush the rewrite rules. You can flush rules by changing the permalinks from the settings. 



