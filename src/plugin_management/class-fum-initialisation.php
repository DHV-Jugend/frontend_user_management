<?php

/**
 * @author Christoph Bessei
 * @version
 */
class Fum_Initialisation
{

    const ADMIN_BAR_CAP_FILTER = Fum_Conf::FUM_NAME_PREFIX . 'admin_bar_cap_filter';

    public static function initiate_plugin()
    {
        //Removes 'next' link in head because this could cause SEO problems and firefox is fetching the link in background which causes more traffic
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

        self::add_action_hooks();
        self::add_filter_hooks();

        add_shortcode(
            Fum_Conf::$fum_register_login_page_name,
            ['Fum_Register_Login_Form_Controller', 'create_register_login_form']
        );
        add_shortcode(Fum_Conf::$fum_edit_page_name, ['Fum_Edit_Form_Controller', 'create_edit_form']);
        add_shortcode(
            Fum_Conf::$fum_event_registration_page,
            ['Fum_Event_Registration_Controller', 'create_event_registration_form']
        );
        add_shortcode('ems_eventverwaltung', ['Fum_Registered_Event_list', 'create_applied_event_form']);
        add_shortcode('recent_posts', ['Fum_Initialisation', 'my_recent_posts_shortcode']);
        add_shortcode('contact_form', ['Fum_Contact_Form_Controller', 'create_contact_form']);
    }

    public static function my_recent_posts_shortcode($atts)
    {
        $q = new WP_Query(
            ['orderby' => 'date', 'posts_per_page' => '1']
        );

        $list = '<div class="latest-post">';

        while ($q->have_posts()) : $q->the_post();
            $heading = '<h3><a href="'.get_post_permalink(get_the_ID()) .'">' . get_the_title() . '</a></h3>';
            $list .= $heading . '<i>' . get_the_date() . '</i>' . '<br/><p>' . get_the_excerpt() . '</p>';
        endwhile;

        wp_reset_query();

        return $list . '</div>';
    }


    protected static function add_action_hooks()
    {

        //Action hook for changing
        add_action('get_header', ['Fum_Initialisation', 'check_shortcode']);
        //Register plugin settings
        add_action('admin_init', ['Fum_Option_Page_Controller', 'register_settings']);
        //Create plugin admin menu page
        add_action('admin_menu', ['Fum_Option_Page_Controller', 'create_menu']);

        add_action('init', ['Fum_Post', 'fum_register_post_type']);
        add_action('init', [new Fum_Front_End_Form(), 'buffer_content_if_front_end_form']);

        add_action('plugins_loaded', ['Fum_Initialisation', 'hideAdminBar']);

        //Create activation code on user_register and add it to the user meta
        add_action('user_register', ['Fum_Activation_Email', 'new_user_registered']);

        //Check if url contains activation key and if yes, prepend "You have successfully your account etc.."
        add_filter('the_content', ['Fum_Activation_Email', 'activate_user']);

        if (get_option(Fum_Conf::$fum_register_form_use_activation_mail_option)) {
            //Check on login  if user is activated
            add_filter('wp_authenticate_user', ['Fum_Activation_Email', 'authenticate'], 10, 1);
        }

        //Delete not activated users, if the home url changes, because the activation link may returns a 404 then
        add_action('update_option_home', ['Fum_Activation_Email', 'delete_not_activated_users']);
        add_action('update_option_siteurl', ['Fum_Activation_Email', 'delete_not_activated_users']);


        //Redirect wp-admin/profile.php (Only redirect if the user edits his OWN profile!)
        add_action('show_user_profile', ['Fum_Redirect', 'redirect_own_profile_edit']);

        if (get_option(Fum_Conf::$fum_general_option_group_hide_wp_login_php)) {
            //Redirect wp-login.php
            add_action('login_init', ['Fum_Redirect', 'redirect_wp_login_php']);
        }

        if (get_option(Fum_Conf::$fum_general_option_group_hide_dashboard_from_non_admin)) {
            add_action('wp_dashboard_setup', ['Fum_Redirect', 'redirect_to_home_if_user_cannot_manage_options']);
        }
    }

    private static function add_filter_hooks()
    {
        add_filter('force_ssl', [new Fum_Front_End_Form(), 'use_ssl_on_front_end_form'], 1, 3);
        add_filter('logout_url', ['Fum_Redirect', 'redirect_wp_logout'], 10, 2);
    }

    /**
     * @param null $neededCapabilities
     * @return bool
     */
    public static function hideAdminBar($neededCapabilities = null)
    {
        // Admin bar is always hidden for guests, do nothing
        if (!is_user_logged_in()) {
            return true;
        }

        if (empty($neededCapabilities)) {
            // Admin bar is shown if user has at least one of these capabilities
            $neededCapabilities = ['upload_files', 'edit_posts'];
        }
        $neededCapabilities = apply_filters(static::ADMIN_BAR_CAP_FILTER, $neededCapabilities);

        if (is_array($neededCapabilities)) {
            foreach ($neededCapabilities as $capability) {
                if (current_user_can($capability)) {
                    show_admin_bar(true);
                    return false;
                }
            }
        }

        show_admin_bar(false);
        return true;
    }

    /**
     * Calls shortcode callback_header function in wp_head, useful for add styles,scripts, change title, etc.
     *
     *
     * <p>Checks if the current post (it checks the complete WP_Post object) contains a shortcode with the FUM_NAME_PREFIX
     * If a shortcode is found it takes the callback functionname adds _header and calls it (if it's callable)</p>
     *
     * <p><b>Example:</b></p>
     * <code>add_shortcode('FUM_NAME_PREFIX_test',array('Classname','functionname'));</code>
     * then the following is called:<br>
     * <code>call_user_func(array('Classname','functionname_header'));</code>
     *
     * <code>add_shortcode('FUM_NAME_PREFIX_test','functionname');</code>
     * then the following is called:
     * <code>call_user_func('functionname_header');</code>
     */
    public static function check_shortcode()
    {
        global $shortcode_tags;
        foreach ($shortcode_tags as $shortcode_tag => $callback) {
            if (0 === stripos($shortcode_tag, Fum_Conf::FUM_NAME_PREFIX)) {
                $post = get_post();
                //Maybe the current site is not a post, not sure when this happens
                if (null === $post) {
                    continue;
                }
                //If there are no object vars, then it's not possible that there is a shortcode tag
                if (!has_shortcode(implode(' ', get_object_vars($post)), $shortcode_tag)) {
                    continue;
                }

                switch (count($callback)) {
                    case 1:
                        $function = (string)$callback;
                        $function = $function . '_header';
                        if (is_callable($function)) {
                            call_user_func($function);
                        }
                        break;
                    case 2:
                        $class = $callback[0];
                        $function = $callback[1] . '_header';
                        $callback = [$class, $function];
                        if (is_callable($callback)) {
                            call_user_func($callback);
                        }
                        break;
                }
            }
        }
    }
}
