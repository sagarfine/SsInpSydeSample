<?php
/*
 * Class Name : SsClassInpSyde
 * Description : This class contains all the properties and methods which are required
 *  to fetch the data from external API and display on custom page.
 * Date : 24 March 2020
 * Author : Sagar Shinde
 */
declare(strict_types=1);
namespace SsClassInpSyde;

use WP_Http;

class SsClassInpSyde
{
    protected string $ssApiEndPoint; // The API endpoint URL.
    private string $ssCustomSlug; // The slug of custom endpoint
    private float $ssCacheExpiry; // Cache Expiry

    /*
     * Function name : ssFnInitialization
     * Parameters : none
     * Return Type : void
     * Description : This function is used to initialize required properties.
     */

    public function ssFnInitialization()
    {
        $endPoint=get_option('ssApiEndPoint', 'https://jsonplaceholder.typicode.com/users');
        $customSlug=get_option('ssCustomSlug', 'ssinpsyde');
        $cacheExpiry=get_option('ssCacheExpiry', 43200);
        $this->ssApiEndPoint=strval($endPoint);
        $this->ssCustomSlug=strval($customSlug);
        $this->ssCacheExpiry=floatval($cacheExpiry);
    }

    /*
     * Function name : fnLoadTextDomain
     * Parameters : none
     * Return Type : void
     * Description : This function load the text domain which is useful for translation.
     */

    public function fnLoadTextDomain()
    {
        load_plugin_textdomain(
            'ssinpsyde',
            false,
            basename(dirname(__FILE__)) . '/languages/'
        );
    }

    /*
     * Function name : fnCallHooks
     * Parameters : none
     * Return Type : void
     * Description : This function call all the hooks which are used in the plugin.
     */

    public function fnCallHooks()
    {
        // Load Text Domain
        add_action('plugins_loaded', [$this, 'fnLoadTextDomain']);
        //Action to add menu in Setting page.
        add_action('admin_menu', [$this, 'fnAddSettingsMenu']);
        add_action('init', [$this, 'ssFnRewriteRules'], 10, 0);
        add_filter('query_vars', [$this, 'fnSsRewriteFilterRequest']);
        add_action('template_redirect', [$this, 'fnTemplateRedirect']);
        add_action('admin_init', [$this, 'fnAdminInitActions']);
        add_action('wp_ajax_ssFnGetUserPosts', [$this, 'ssFnGetUserDetailsById']);
        add_action('wp_ajax_nopriv_ssFnGetUserPosts', [$this, 'ssFnGetUserDetailsById']);
    }

    /*
     * Function name : ssFnRewriteRules
     * Parameters : None
     * Return Type : None
     * Description : This action function add custom rewrite rule.
     */

    public function ssFnRewriteRules()
    {
        add_rewrite_rule('^'.$this->ssCustomSlug.'/?$', 'index.php?param='.$this->ssCustomSlug, 'top');
        flush_rewrite_rules();
    }

     /*
     * Function name : ssFnHttpRequest
     * Parameters : None
     * Return Type : String = The valid data received from the API call.
     * Description : This function sends HTTP request to the API endpoint and return response.
     */

    protected function ssFnHttpRequest(string $suffix = ''):string
    {
        $endpoint=$this->ssFnGetEndpoint();
        if ($suffix!=='') {
            $endpoint.=$suffix;
        }
        if (!class_exists('WP_Http')) {
            require_once ABSPATH . WPINC . '/class-http.php';
        }
        $http = new WP_Http();
        $ssRemoteData = $http->request($endpoint, ['reject_unsafe_urls'=>true, 'blocking'=>true]);
        if (is_wp_error($ssRemoteData)) {
            return '';
        }
        $ssRemoteData = wp_remote_retrieve_body($ssRemoteData);

        return $ssRemoteData;
    }

    /*
     * Function name : ssFnSendRequest
     * Parameters : 1. String $variable = This string constant which is required
     *                 to get/store values from/in the cache
     * Return Type : String = The valid data received from the API call.
     * Description : This function sends HTTP request to the API endpoint and return response.
     */

    protected function ssFnSendRequest(string $variable, string $suffix = ''):array
    {
        $ssArrUsersPostDetails=get_transient($variable);
        if ($ssArrUsersPostDetails!==false &&
            $ssArrUsersPostDetails!=='' && $ssArrUsersPostDetails!=='{}') {
            $ssArrUsersPostDetails= json_decode($ssArrUsersPostDetails, true);
            return $ssArrUsersPostDetails;
        }
        if (is_null($ssArrUsersPostDetails) || $ssArrUsersPostDetails===''
            || !isset($ssArrUsersPostDetails) || !is_array($ssArrUsersPostDetails
            ||$ssArrUsersPostDetails==='{}')) {
            $ssArrUsersPostDetails = $this->ssFnHttpRequest($suffix);
            if (is_null($ssArrUsersPostDetails)) {
                return [];
            }
            set_transient($variable, $ssArrUsersPostDetails, $this->ssCacheExpiry);
            $ssArrUsersPostDetails= json_decode($ssArrUsersPostDetails, true);
        }
        if (is_null($ssArrUsersPostDetails)) {
            $ssArrUsersPostDetails=[];
        }
        return $ssArrUsersPostDetails;
    }

    /*
     * Function name : ssFnGetEndpoint
     * Parameters : none
     * Return Type : String = It will return the valid URL.
     * Description : This function check if the URL is having valid protocol or not based on
     *              server protocol.
     */

    protected function ssFnGetEndpoint():string
    {
        $ssApiEndPoint=$this->ssApiEndPoint;
        $url = parse_url($ssApiEndPoint);
        if (is_ssl()) {
            if ($url['scheme']==='http') {
                $ssApiEndPoint=str_replace('http', 'https', $ssApiEndPoint);
            }
        }
        if (!is_ssl()) {
            if ($url['scheme']==='https') {
                $ssApiEndPoint=str_replace('https', 'http', $ssApiEndPoint);
            }
        }
        return $ssApiEndPoint;
    }

    /*
     * Function name : ssFnGetUserDetailsById
     * Parameters : none
     * Return Type : none
     * Description : This function sends request and received data from the API
     *               and send back to AJAX call.
     */

    public function ssFnGetUserDetailsById()
    {
        $ssUserId=isset($_POST['user_id'])?sanitize_text_field($_POST['user_id']):0; // input var okay
        if ($ssUserId!==0){
            $ssArrUsersPostDetails=$this->ssFnSendRequest(
                'ssArrUsersDetails-'.$ssUserId,
                '/'.$ssUserId
                );
            if ((is_array($ssArrUsersPostDetails) && count($ssArrUsersPostDetails)===0)
                || $ssArrUsersPostDetails===''){
                echo esc_html__('It seems like problem in API connection, Please try again.', 'ssinpsyde');
                exit(0);
            }
            $ssModalHtml=$this->ssFnGetUserDetails($ssArrUsersPostDetails);
            echo $ssModalHtml;
            exit(0);
        }
    }

    /*
     * Function name : ssFnGetUserDetails
     * Parameters : array = $ssPostData = Data coming from the API.
     * Return Type : None
     * Description : It display users details in well format.
     */

    protected function ssFnGetUserDetails(array $ssPostData):string
    {
        $ssModalHtml='';
        if (is_array($ssPostData) && count($ssPostData)>0) {
            $ssModalHtml.='<div class="row" >';
            $ssModalHtml.='<div class="col-md-4 col-sm-1"><h6>';
            $ssModalHtml.= esc_html__('User Name','ssinpsyde').'</h6></div>';
            $ssModalHtml.='<div class="col-md-8 col-sm-1">'.esc_html($ssPostData['username']).'</div>';
            $ssModalHtml.='</div>';
            $ssModalHtml.='<div class="row" >';
            $ssModalHtml.='<div class="col-md-4 col-sm-1"><h6>'.esc_html__('Email','ssinpsyde').'</h6></div>';
            $ssModalHtml.='<div class="col-md-8 col-sm-1">
                                <a class="badge badge-light" href="mailto:'.esc_html($ssPostData['email']).'" target="_blank">
                                '.esc_html($ssPostData['email']).'</a></div>';
            $ssModalHtml.='</div>';
            $ssModalHtml.='<div class="row" >';
            $ssModalHtml.='<div class="col-md-4 col-sm-1"><h6>';
            $ssModalHtml.= esc_html__('Address','ssinpsyde').'</h6></div>';
            $ssModalHtml.='<div class="col-md-8 col-sm-1">';
            $ssModalHtml.=esc_html($ssPostData['address']['suite']).',';
            $ssModalHtml.=esc_html($ssPostData['address']['street']).'<br>';
            $ssModalHtml.=esc_html($ssPostData['address']['city']).',';
            $ssModalHtml.=esc_html($ssPostData['address']['zipcode']).'<br>';
            $ssGeo='//maps.google.com/maps?z=14&q=';
            $ssGeo.=$ssPostData['address']['geo']['lng'].','.$ssPostData['address']['geo']['lat'];
            $ssModalHtml.='<a class="badge badge-info" href="'.esc_html($ssGeo).'" target="_blank">
                                '.esc_html__('Find on Map', 'ssinpsyde').'</a></div>';
            $ssModalHtml.='</div>';
            $ssModalHtml.='<div class="row" >';
            $ssModalHtml.='<div class="col-md-4 col-sm-1"><h6>'.esc_html__('Phone','ssinpsyde').'</h6></div>';
            $ssModalHtml.='<div class="col-md-8 col-sm-1">
                            <a class="badge badge-light" href="tel:'.esc_html($ssPostData['phone']).'" target="_blank">
                                '.esc_html($ssPostData['phone']).'</a></div>';
            $ssModalHtml.='</div>';
            $ssModalHtml.='</div>';
            $ssModalHtml.='<div class="row" >';
            $ssModalHtml.='<div class="col-md-4 col-sm-1"><h6>';
            $ssModalHtml.= esc_html__('Website','ssinpsyde').'</h6></div>';
            $ssModalHtml.='<div class="col-md-8 col-sm-1">
<a class="badge badge-light" href="//'.esc_html($ssPostData['website']).'" target="_blank">
                                '.esc_html($ssPostData['website']).'</a></div>';
            $ssModalHtml.='</div>';
            $ssModalHtml.='<div class="row" >';
            $ssModalHtml.='<div class="col-md-4 col-sm-1"><h6>';
            $ssModalHtml.= esc_html__('Company','ssinpsyde').'</h6></div>';
            $ssModalHtml.='<div class="col-md-8 col-sm-1">';
            $ssModalHtml.='<strong>'.esc_html__('Name','ssinpsyde').': </strong>';
            $ssModalHtml.=esc_html($ssPostData['company']['name']).'<br>';
            $ssModalHtml.='<strong>'.esc_html__('Catch Phrase','ssinpsyde').': </strong>';
            $ssModalHtml.=esc_html($ssPostData['company']['catchPhrase']).'<br>';
            $ssModalHtml.='<strong>'.esc_html__('Bs','ssinpsyde').': </strong>';
            $ssModalHtml.=esc_html($ssPostData['company']['bs']);
            $ssModalHtml.='</div>';
            $ssModalHtml.='</div>';

        }
        echo $ssModalHtml;
        exit(0);
    }

    /*
     * Function name : fnTemplateRedirect
     * Parameters : None
     * Return Type : None
     * Description : This function add required CSS and JS files
     *               in the custom page created for the plugin.
     */

    public function fnTemplateRedirect()
    {
        if (get_query_var('param') === $this->ssCustomSlug) {
            add_filter('show_admin_bar', '__return_false');
            add_filter('style_loader_src', [$this, 'ssFnStyleLoader']);
            add_filter('script_loader_src', [$this, 'ssFnScriptLoader']);
            wp_enqueue_style(
                'ssBootstrap',
                '//stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css',
                [],
                '1.0.0'
            );
            wp_enqueue_style(
                'ssCustomCss',
                plugins_url('', __FILE__).'/css/ssCustom.css',
                [],
                '1.0.0'
            );
            wp_enqueue_script(
                'ssJQuerySlim',
                //'//code.jquery.com/jquery-3.4.1.slim.min.js',
                '//code.jquery.com/jquery-3.4.1.min.js',
                [],
                '1.0.0',
                true
            );
            wp_enqueue_script(
                'ssPopper',
                '//cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js',
                [],
                '1.0.0',
                true
            );
            wp_enqueue_script(
                'ssBoostrap',
                '//stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js',
                [],
                '1.0.0',
                true
            );
            wp_enqueue_script(
                'ssCustom',
                plugins_url('', __FILE__).'/js/ssCustom.js',
                [],
                '1.0.0',
                true
            );
            wp_localize_script(
                'ssCustom',
                'ssCustomAjax',
                ['ajax_url' => admin_url('admin-ajax.php')]
            );
            include_once('templates/frontListing.php');
            exit();
        }
    }

    /*
     * Function name : ssFnScriptLoader
     * Parameters : String $href= URL of CSS/JS file
     * Return Type : None
     * Description : This function will remove all the JS files which are not required .
     */

    public function ssFnScriptLoader(string $href):string
    {
        $ssUrlArray=['code.jquery.com/jquery-3.4.1.min.js',
            'cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js',
            'stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js',
            'ssCustom.js',
        ];
        foreach ($ssUrlArray as $url) {
            if (strpos(
                    $href,
                    $url
                ) !== false) {
                return $href;
            }
        }
        return '';
    }

    /*
     * Function name : ssFnStyleLoader
     * Parameters : String $href= URL of CSS file
     * Return Type : None
     * Description : This function will remove all the CSS files which are not required .
     */

    public function ssFnStyleLoader(string $href):string
    {
        if (strpos(
                $href,
                "stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
            ) !== false) {
            return $href;
        }
        if (strpos(
                $href,
                "ssCustom.css"
            ) !== false) {
            return $href;
        }
        return '';
    }

    /*
     * Function name : fnAdminInitActions
     * Parameters : None
     * Return Type : None
     * Description : This function is storing the values in the database
     *               which added through admin interface.
     */

    public function fnAdminInitActions()
    {
        $isError=false;
        $errorMessage='';
        if (isset($_POST['ssApiEndPoint'])
            && wp_verify_nonce($_POST['ffApiNounce'], 'ffApiNounce')
        ) { //
            $ssApiEndPoint = sanitize_text_field(wp_unslash($_POST['ssApiEndPoint']));
            if ($ssApiEndPoint!=='' && wp_http_validate_url($ssApiEndPoint)) {
                update_option('ssApiEndPoint', $ssApiEndPoint, true);
            }
            else{
                $isError=true;
                $errorMessage.=__('The URL is not valid or it is blank,Please enter valid URL','ssinpsyde').'[ss]';
            }
            $ssCustomSlug = sanitize_text_field(wp_unslash($_POST['ssCustomSlug']));
            if ($ssCustomSlug!=='') {
                $ssCustomSlug=strtolower(str_replace(' ', '', $ssCustomSlug));
                update_option('ssCustomSlug', $ssCustomSlug, true);
            }
            else{
                $isError=true;
                $errorMessage.=__('The slug should not be blank, Please add valid slug.','ssinpsyde').'[ss]';
            }
            $ssCacheExpiry = sanitize_text_field(wp_unslash($_POST['ssCacheExpiry']));
            if ($ssCacheExpiry!=='' && is_numeric($ssCacheExpiry)) {
                update_option('ssCacheExpiry', $ssCacheExpiry, true);
            }
            else{
                $isError=true;
                $errorMessage.=__('The cache expiry should be required (in seconds).','ssinpsyde').'[ss]';
            }
            if ($isError) {
                set_transient(
                    'ssError',
                    $errorMessage,
                    60
                );
            }
            else{
                set_transient(
                    'ssSuccess',
                    esc_html(__('All the information saved successfully.','ssinpsyde')),
                    60
                );
            }
        }
        $this->ssFnInitialization();
        flush_rewrite_rules();
    }

    /*
     * Function name : fnSsRewriteFilterRequest
     * Parameters : array $vars = The array of whitelisted query variable names.
     * Return Type : None
     * Description : Allows custom rewrite rules using your own arguments to work,
     *               or any other custom query variables you want to be publicly available.
     */

    public function fnSsRewriteFilterRequest(array $vars):array
    {
        $vars[]='param';
        return $vars;
    }

    /*
     * Function name : fnAddSettingsMenu
     * Parameters : None
     * Return Type : None
     * Description : It adds option in Setting menu in admin panel.
     */

    public function fnAddSettingsMenu()
    {
        add_options_page(
            __('InpSyde', 'ssinpsyde'),
            __('InpSyde', 'ssinpsyde'),
            'manage_options',
            'ssinpsyde',
            [$this, 'fnAddMenuPage']
        );
    }

    /*
    * Function name : fnAddMenuPage
    * Parameters : None
    * Return Type : None
    * Description : It adds Page in the admin panel for custom option.
    */

    public function fnAddMenuPage()
    {
        echo '<h1>'.esc_html(__('InpSyde Settings', 'ssinpsyde')).'</h1>';
        echo '<div id="ssRoot">';
        include_once('templates/adminForm.php');
        echo '</div>';
    }
}
