<?php

    defined( 'ABSPATH' ) or die( 'Keep Silent' );


    function upb_elements_register_action() {
        do_action( 'upb_register_element', upb_elements() );
    }

    add_action( 'wp_loaded', 'upb_elements_register_action' );

    function upb_tabs_register_action() {
        do_action( 'upb_register_tab', upb_tabs() );
    }

    add_action( 'wp_loaded', 'upb_tabs_register_action' );

    function upb_settings_register_action() {
        do_action( 'upb_register_setting', upb_settings() );
    }

    add_action( 'wp_loaded', 'upb_settings_register_action' );


    // AJAX Requests
    add_action( 'wp_ajax__upb_save', function () {

        // Should have edit_pages cap :)
        if ( ! current_user_can( 'edit_pages' ) ) {
            status_header( 403 );
            wp_send_json_error( 'upb_not_allowed' );
        }

        if ( ! check_ajax_referer( '_upb', '_nonce', FALSE ) ) {
            status_header( 400 );
            wp_send_json_error( 'bad_nonce' );
        }

        if ( empty( $_POST[ 'shortcode' ] ) || ! is_array( $_POST[ 'states' ] ) ) {
            status_header( 400 );
            wp_send_json_error( 'missing_contents' );
        }

        // SAVE ON PAGE META :D

        $post_id = absint( $_POST[ 'id' ] );


        update_post_meta( $post_id, '_upb_sections', $_POST[ 'states' ][ 'sections' ] );

        update_post_meta( $post_id, '_upb_shortcodes', $_POST[ 'shortcode' ] );


        upb_settings()->set_settings( $_POST[ 'states' ][ 'settings' ] );

        wp_send_json_success( TRUE );

    } );


    // Section Template Save
    add_action( 'wp_ajax__save_section', function () {

        // Should have manage_options cap :)
        if ( ! current_user_can( 'manage_options' ) ) {
            status_header( 403 );
            wp_send_json_error( 'upb_not_allowed' );
        }

        if ( ! check_ajax_referer( '_upb', '_nonce', FALSE ) ) {
            status_header( 400 );
            wp_send_json_error( 'bad_nonce' );
        }

        if ( empty( $_POST[ 'contents' ] ) || ! is_array( $_POST[ 'contents' ] ) ) {
            status_header( 400 );
            wp_send_json_error( 'missing_contents' );
        }

        $sections   = (array) get_option( '_upb_saved_sections', array() );
        $sections[] = $_POST[ 'contents' ];

        $update = update_option( '_upb_saved_sections', $sections, FALSE );

        wp_send_json_success( $update );
    } );


    // Modify Saved Template
    add_action( 'wp_ajax__save_section_all', function () {

        if ( ! current_user_can( 'manage_options' ) ) {
            status_header( 403 );
            wp_send_json_error( 'upb_not_allowed' );
        }

        if ( ! check_ajax_referer( '_upb', '_nonce', FALSE ) ) {
            status_header( 400 );
            wp_send_json_error( 'bad_nonce' );
        }

        if ( empty( $_POST[ 'contents' ] ) ) {
            $update = update_option( '_upb_saved_sections', array(), FALSE );
        } else {
            $sections = (array) $_POST[ 'contents' ];
            $update   = update_option( '_upb_saved_sections', $sections, FALSE );
        }

        wp_send_json_success( $update );
    } );

    // Section Panel Contents
    add_action( 'wp_ajax__get_upb_sections_panel_contents', function () {

        if ( ! current_user_can( 'customize' ) ) {
            status_header( 403 );
            wp_send_json_error( 'upb_not_allowed' );
        }

        if ( ! check_ajax_referer( '_upb', '_nonce', FALSE ) ) {
            status_header( 400 );
            wp_send_json_error( 'bad_nonce' );
        }

        $post_id = absint( $_POST[ 'id' ] );

        $sections = get_post_meta( $post_id, '_upb_sections', TRUE );

        wp_send_json_success( upb_elements()->set_upb_options_recursive( $sections ) );

    } );

    // Settings Panel Contents
    add_action( 'wp_ajax__get_upb_settings_panel_contents', function () {

        if ( ! current_user_can( 'customize' ) ) {
            status_header( 403 );
            wp_send_json_error( 'upb_not_allowed' );
        }

        if ( ! check_ajax_referer( '_upb', '_nonce', FALSE ) ) {
            status_header( 400 );
            wp_send_json_error( 'bad_nonce' );
        }

        // return get_post_meta( get_the_ID(), '_upb_settings', TRUE );

        wp_send_json_success( upb_settings()->getAll() );
    } );


    add_action( 'wp_ajax__get_upb_elements_panel_contents', function () {

        if ( ! current_user_can( 'customize' ) ) {
            status_header( 403 );
            wp_send_json_error( 'upb_not_allowed' );
        }

        if ( ! check_ajax_referer( '_upb', '_nonce', FALSE ) ) {
            status_header( 400 );
            wp_send_json_error( 'bad_nonce' );
        }

        //wp_send_json_success( upb_elements()->getNonCore() );
        wp_send_json_success( upb_elements()->getAll() );
    } );


    // Get Saved Section
    add_action( 'wp_ajax__get_saved_sections', function () {

        if ( ! current_user_can( 'customize' ) ) {
            status_header( 403 );
            wp_send_json_error( 'upb_not_allowed' );
        }

        if ( ! check_ajax_referer( '_upb', '_nonce', FALSE ) ) {
            status_header( 400 );
            wp_send_json_error( 'bad_nonce' );
        }

        $saved_sections = (array) get_option( '_upb_saved_sections', array() );

        $saved_sections = upb_elements()->set_upb_options_recursive( $saved_sections );

        wp_send_json_success( $saved_sections );
    } );


    add_filter( 'upb-before-contents', function ( $contents, $shortcodes ) {
        ob_start();

        upb_get_template( 'wrapper/before.php', compact( 'contents', 'shortcodes' ) );

        return ob_get_clean();
    }, 10, 2 );

    add_filter( 'upb-on-contents', function ( $contents, $shortcodes ) {
        ob_start();
        upb_get_template( 'wrapper/contents.php', compact( 'contents', 'shortcodes' ) );

        return ob_get_clean();
    }, 10, 2 );

    add_filter( 'upb-after-contents', function ( $contents, $shortcodes ) {
        ob_start();

        upb_get_template( 'wrapper/after.php', compact( 'contents', 'shortcodes' ) );

        return ob_get_clean();
    }, 10, 2 );


    add_filter( 'the_content', function ( $contents ) {
        
        if ( ! upb_is_preview() && (bool) get_post_meta( get_the_ID(), '_upb_settings_page_enable', TRUE ) ):

            $position   = get_post_meta( get_the_ID(), '_upb_settings_page_position', TRUE );
            $shortcodes = get_post_meta( get_the_ID(), '_upb_shortcodes', TRUE );

            return apply_filters( $position, $contents, $shortcodes );
        endif;

        return $contents;


    } );