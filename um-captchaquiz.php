<?php

/*
   Plugin Name: Ultimate Member Captcha Quiz
   Description: Adds a custom captcha quiz query to UM.
   Version: 1.0
   Author: Hans Morbach
   Author URI: https://github.com/hansmorb/
*/



class UMCaptchaQuizPlugin {
    function __construct()
    {
        add_action('admin_menu', array($this,'adminPage'));
        add_action('admin_init', array($this, 'settings'));
        add_action( 'um_submit_form_errors_hook__registration', array($this, 'customValidation'), 99 );
    }

    function adminPage(){
        add_options_page('UM Captcha Quiz Einstellungen','UM Captcha Quiz', 'manage_options', 'umcaptchaquiz-settings-page', array($this, 'SettingsPage' ));
    }

    /* Add settings page to Settings
     */
    function settings(){
        add_settings_section('umc_section', null, null, 'umcaptchaquiz-settings-page' );

        add_settings_field('umc_metakey', 'Metaschlüssel des Feldes', array($this, 'metakeyHTML'), 'umcaptchaquiz-settings-page', 'umc_section' );
        register_setting('umcaptchaquiz', 'umc_metakey', array('sanitize_callback' => 'sanitize_text_field', 'default' => ''));

        add_settings_field('umc_value', 'Vergleichswert', array($this, 'valueHTML'), 'umcaptchaquiz-settings-page', 'umc_section' );
        register_setting('umcaptchaquiz', 'umc_value', array('sanitize_callback' => 'sanitize_text_field', 'default' => ''));
    }

    function SettingsPage() {
        ?>
       <div class="wrap">
            <h1>UM Captcha Quiz Einstellungen</h1>
            <form action="options.php" method="POST">
                <?php
                    settings_fields('umcaptchaquiz');
                    do_settings_sections('umcaptchaquiz-settings-page');
                    submit_button();
                ?>
            </form>
        </div>
    <?php }

    function metakeyHTML() {?>
        <input type="text" name="umc_metakey" value="<?php echo esc_attr(get_option('umc_metakey'))?>">
    <?php }

    function valueHTML() {?>
        <input type="text" name="umc_value" value="<?php echo esc_attr(get_option('umc_value'))?>">
    <?php }


    /* 
    * Quiz custom validierung für Ultimate Member 
    * (https://wordpress.org/plugins/ultimate-member/)
    * Basierend auf: https://wordpress.org/support/topic/how-to-create-quiz/
    * In Ultimate Member hat das entsprechende Element den gesetzten Meta Schlüssel.
    * Wird als barrierefreie Alternative zu Captchas eingesetzt, in den meisten Fällen völlig ausreichend.
    * (Bis auf koordinierte Bot Attacken)
    * 
    * 100% fertig
    * 
    */
    function customValidation( $args) {

        $metakey = get_option('umc_metakey',null);
        $value = get_option('umc_value', null);

        if (empty($metakey) or empty($value) ){
            return false;
        }
        if ( isset( $args[$metakey] ) ) {
            if (  strcasecmp($args[$metakey],$value) != 0) {
                $message = sprintf( __( 'Falscher Validierungswert', 'ultimate-member' ), $mystring );
                UM()->form()->add_error( $metakey, $message );
            }
        }
    }
}

$UMCaptchaQuizPlugin = new UMCaptchaQuizPlugin();

