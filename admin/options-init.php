<?php

    /**
     * For full documentation, please visit: http://docs.reduxframework.com/
     * For a more extensive sample-config file, you may look at:
     * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }

    // This is your option name where all the Redux data is stored.
    $opt_name = "woocommerce_packing_slips_options";

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        'opt_name' => 'woocommerce_packing_slips_options',
        'use_cdn' => TRUE,
        'dev_mode' => FALSE,
        'display_name' => 'WooCommerce Packing Slips',
        'display_version' => '1.0.5',
        'page_title' => 'WooCommerce Packing Slips',
        'update_notice' => TRUE,
        'intro_text' => '',
        'footer_text' => '&copy; '.date('Y').' weLaunch',
        'admin_bar' => TRUE,
        'menu_type' => 'submenu',
        'menu_title' => 'Packing Slips',
        'allow_sub_menu' => TRUE,
        'page_parent' => 'woocommerce',
        'page_parent_post_type' => 'your_post_type',
        'customizer' => FALSE,
        'default_mark' => '*',
        'hints' => array(
            'icon_position' => 'right',
            'icon_color' => 'lightgray',
            'icon_size' => 'normal',
            'tip_style' => array(
                'color' => 'light',
            ),
            'tip_position' => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect' => array(
                'show' => array(
                    'duration' => '500',
                    'event' => 'mouseover',
                ),
                'hide' => array(
                    'duration' => '500',
                    'event' => 'mouseleave unfocus',
                ),
            ),
        ),
        'output' => TRUE,
        'output_tag' => TRUE,
        'settings_api' => TRUE,
        'cdn_check_time' => '1440',
        'compiler' => TRUE,
        'page_permissions' => 'manage_options',
        'save_defaults' => TRUE,
        'show_import_export' => TRUE,
        'database' => 'options',
        'transient_time' => '3600',
        'network_sites' => TRUE,
    );

    Redux::setArgs( $opt_name, $args );

    /*
     * ---> END ARGUMENTS
     */

    /*
     * ---> START HELP TABS
     */

    $tabs = array(
        array(
            'id'      => 'help-tab',
            'title'   => __( 'Information', 'woocommerce-packing-slips' ),
            'content' => __( '<p>Need support? Please use the comment function on codecanyon.</p>', 'woocommerce-packing-slips' )
        ),
    );
    Redux::setHelpTab( $opt_name, $tabs );

    // Set the help sidebar
    // $content = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'woocommerce-packing-slips' );
    // Redux::setHelpSidebar( $opt_name, $content );


    /*
     * <--- END HELP TABS
     */
    
    global $woocommerce;
    $mailer = $woocommerce->mailer();
    $wc_emails = $mailer->get_emails();

    $non_order_emails = array(
        'customer_note',
        'customer_reset_password',
        'customer_new_account'
    );

    $emails = array();
    foreach ($wc_emails as $class => $email) {
        if ( !in_array( $email->id, $non_order_emails ) ) {
            switch ($email->id) {
                case 'new_order':
                    $emails[$email->id] = sprintf('%s (%s)', $email->title, __( 'Admin email', 'woocommerce-pdf-invoices-packing-slips' ) );
                    break;
                case 'customer_invoice':
                    $emails[$email->id] = sprintf('%s (%s)', $email->title, __( 'Manual email', 'woocommerce-pdf-invoices-packing-slips' ) );
                    break;
                default:
                    $emails[$email->id] = $email->title;
                    break;
            }
        }
    }

    /*
     *
     * ---> START SECTIONS
     *
     */

    Redux::setSection( $opt_name, array(
        'title'  => __( 'Packing Slips', 'woocommerce-packing-slips' ),
        'id'     => 'general',
        'desc'   => __( 'Need support? Please use the comment function on codecanyon.', 'woocommerce-packing-slips' ),
        'icon'   => 'el el-home',
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'General', 'woocommerce-packing-slips' ),
        // 'desc'       => __( '', 'woocommerce-packing-slips' ),
        'id'         => 'general-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enable',
                'type'     => 'checkbox',
                'title'    => __( 'Enable', 'woocommerce-packing-slips' ),
                'subtitle' => __( 'Enable Packing Slips to use the options below', 'woocommerce-packing-slips' ),
                'default' => 1,
            ),
            array(
                'id'       => 'generalAutomatic',
                'type'     => 'checkbox',
                'title'    => __( 'Create Packing Slips Automatically', 'woocommerce-packing-slips' ),
                'subtitle' => __( 'Packing Slips will be created for each order automatically.', 'woocommerce-packing-slips' ),
                'default' => 1,
            ),
            array(
                'id'       => 'generalAttachToMail',
                'type'     => 'checkbox',
                'title'    => __( 'Attach Packing Slip to Email', 'woocommerce-packing-slips' ),
                'subtitle' => __( 'Attach Packing Slips automatically to admin mails.', 'woocommerce-packing-slips' ),
                'default' => 1,
            ),
            array(
                'id'     =>'generalAttachToMailStatus',
                'type'  => 'select',
                'title' => __('Attach to Email order Statuses', 'woocommerce-pdf-invoices'), 
                'multi' => true,
                'options' => $emails,
                'default' => array(
                    'new_order',
                ),
                'required' => array('generalAttachToMail','equals','1'),
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Layout', 'woocommerce-packing-slips' ),
        // 'desc'       => __( '', 'woocommerce-packing-slips' ),
        'id'         => 'layout',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'             => 'layoutPadding',
                'type'           => 'spacing',
                // 'output'         => array('.site-header'),
                'mode'           => 'padding',
                'units'          => array('px'),
                'units_extended' => 'false',
                'title'          => __('Padding', 'woocommerce-pdf-catalog'),
                'default'            => array(
                    'padding-top'     => '50px', 
                    'padding-right'   => '60px', 
                    'padding-bottom'  => '10px', 
                    'padding-left'    => '60px',
                    'units'          => 'px', 
                ),
            ),
            array(
                'id'     =>'layoutTextColor',
                'type'  => 'color',
                'title' => __('Text Color', 'woocommerce-packing-slips'), 
                'validate' => 'color',
                'default'   => '#333333',
            ),
            array(
                'id'     =>'layoutFontFamily',
                'type'  => 'select',
                'title' => __('Default Font', 'woocommerce-packing-slips'), 
                'options'  => array(
                    'dejavusans' => __('Sans', 'woocommerce-packing-slips' ),
                    'dejavuserif' => __('Serif', 'woocommerce-packing-slips' ),
                    'dejavusansmono' => __('Mono', 'woocommerce-packing-slips' ),
                    'droidsans' => __('Droid Sans', 'woocommerce-packing-slips'),
                    'droidserif' => __('Droid Serif', 'woocommerce-packing-slips'),
                    'lato' => __('Lato', 'woocommerce-packing-slips'),
                    'lora' => __('Lora', 'woocommerce-packing-slips'),
                    'merriweather' => __('Merriweather', 'woocommerce-packing-slips'),
                    'montserrat' => __('Montserrat', 'woocommerce-packing-slips'),
                    'opensans' => __('Open sans', 'woocommerce-packing-slips'),
                    'opensanscondensed' => __('Open Sans Condensed', 'woocommerce-packing-slips'),
                    'oswald' => __('Oswald', 'woocommerce-packing-slips'),
                    'ptsans' => __('PT Sans', 'woocommerce-packing-slips'),
                    'sourcesanspro' => __('Source Sans Pro', 'woocommerce-packing-slips'),
                    'slabo' => __('Slabo', 'woocommerce-packing-slips'),
                    'raleway' => __('Raleway', 'woocommerce-packing-slips'),
                ),
                'default'   => 'dejavusans',
            ),
            array(
                'id'     =>'layoutFontSize',
                'type'     => 'spinner', 
                'title'    => __('Default font size', 'woocommerce-packing-slips'),
                'default'  => '9',
                'min'      => '1',
                'step'     => '1',
                'max'      => '40',
            ),
            array(
                'id'     =>'layoutFontLineHeight',
                'type'     => 'spinner', 
                'title'    => __('Default line height', 'woocommerce-packing-slips'),
                'default'  => '12',
                'min'      => '1',
                'step'     => '1',
                'max'      => '40',
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Header', 'woocommerce-packing-slips' ),
        // 'desc'       => __( '', 'woocommerce-packing-slips' ),
        'id'         => 'header',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableHeader',
                'type'     => 'checkbox',
                'title'    => __( 'Enable', 'woocommerce-packing-slips' ),
                'subtitle' => __( 'Enable header', 'woocommerce-packing-slips' ),
                'default' => '1',
            ),

            array(
                'id'     =>'headerBackgroundColor',
                'type' => 'color',
                'title' => __('Header background color', 'woocommerce-packing-slips'), 
                'validate' => 'color',
                'default' => '#333333',
                'required' => array('enableHeader','equals','1'),
            ),
            array(
                'id'     =>'headerTextColor',
                'type'  => 'color',
                'title' => __('Header text color', 'woocommerce-packing-slips'), 
                'validate' => 'color',
                'default' => '#FFFFFF',
                'required' => array('enableHeader','equals','1'),
            ),
            array(
                'id'     =>'headerFontSize',
                'type'     => 'spinner', 
                'title'    => __('Header font size', 'woocommerce-packing-slips'),
                'default'  => '8',
                'min'      => '1',
                'step'     => '1',
                'max'      => '40',
            ),
            array(
                'id'     =>'headerLayout',
                'type'  => 'select',
                'title' => __('Header Layout', 'woocommerce-packing-slips'), 
                'required' => array('enableHeader','equals','1'),
                'options'  => array(
                    'oneCol' => __('1/1', 'woocommerce-packing-slips' ),
                    'twoCols' => __('1/2 + 1/2', 'woocommerce-packing-slips' ),
                    'threeCols' => __('1/3 + 1/3 + 1/3', 'woocommerce-packing-slips' ),
                ),
                'default' => 'twoCols',
            ),
            array(
                'id'     =>'headerMargin',
                'type'     => 'spinner', 
                'title'    => __('Header Margin', 'woocommerce-packing-slips'),
                'default'  => '10',
                'min'      => '1',
                'step'     => '1',
                'max'      => '200',
                'required' => array('enableHeader','equals','1'),
            ),
            array(
                'id'     =>'headerHeight',
                'type'     => 'spinner', 
                'title'    => __('Header Height', 'woocommerce-packing-slips'),
                'default'  => '30',
                'min'      => '1',
                'step'     => '1',
                'max'      => '200',
                'required' => array('enableHeader','equals','1'),
            ),
            array(
                'id'     =>'headerVAlign',
                'type'  => 'select',
                'title' => __('Vertical Align', 'woocommerce-packing-slips'), 
                'required' => array('enableHeader','equals','1'),
                'options'  => array(
                    'top' => __('Top', 'woocommerce-packing-slips' ),
                    'middle' => __('Middle', 'woocommerce-packing-slips' ),
                    'bottom' => __('Bottom', 'woocommerce-packing-slips' ),
                ),
                'default' => 'middle',
            ),
            array(
                'id'     =>'headerTopLeft',
                'type'  => 'select',
                'title' => __('Top Left Header', 'woocommerce-packing-slips'), 
                'required' => array('enableHeader','equals','1'),
                'options'  => array(
                    'none' => __('None', 'woocommerce-packing-slips' ),
                    'bloginfo' => __('Blog information', 'woocommerce-packing-slips' ),
                    'text' => __('Custom text', 'woocommerce-packing-slips' ),
                    'pagenumber' => __('Pagenumber', 'woocommerce-packing-slips' ),
                    'image' => __('Image', 'woocommerce-packing-slips' ),
                    'exportinfo' => __('Export Information', 'woocommerce-packing-slips' ),
                    'qr' => __('QR-Code', 'woocommerce-packing-slips' ),
                ),
                'default' => 'bloginfo',
            ),
            array(
                'id'     =>'headerTopLeftText',
                'type'  => 'editor',
                'title' => __('Top Left Header Text', 'woocommerce-packing-slips'), 
                'required' => array('headerTopLeft','equals','text'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'headerTopLeftImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Top Left Header Image', 'woocommerce-packing-slips'), 
                'required' => array('headerTopLeft','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'headerTopMiddle',
                'type'  => 'select',
                'title' => __('Top Middle Header', 'woocommerce-packing-slips'), 
                'required' => array('headerLayout','equals','threeCols'),
                'options'  => array(
                    'none' => __('None', 'woocommerce-packing-slips' ),
                    'bloginfo' => __('Blog information', 'woocommerce-packing-slips' ),
                    'text' => __('Custom text', 'woocommerce-packing-slips' ),
                    'pagenumber' => __('Pagenumber', 'woocommerce-packing-slips' ),
                    'image' => __('Image', 'woocommerce-packing-slips' ),
                    'exportinfo' => __('Export Information', 'woocommerce-packing-slips' ),
                    'qr' => __('QR-Code', 'woocommerce-packing-slips' ),
                ),
            ),
            array(
                'id'     =>'headerTopMiddleText',
                'type'  => 'editor',
                'title' => __('Top Middle Header Text', 'woocommerce-packing-slips'), 
                'required' => array('headerTopMiddle','equals','text'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'headerTopMiddleImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Top Middle Header Image', 'woocommerce-packing-slips'), 
                'required' => array('headerTopMiddle','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'headerTopRight',
                'type'  => 'select',
                'title' => __('Top Right Header', 'woocommerce-packing-slips'), 
                'required' => array('headerLayout','equals',array('threeCols','twoCols')),
                'options'  => array(
                    'none' => __('None', 'woocommerce-packing-slips' ),
                    'bloginfo' => __('Blog information', 'woocommerce-packing-slips' ),
                    'text' => __('Custom text', 'woocommerce-packing-slips' ),
                    'pagenumber' => __('Pagenumber', 'woocommerce-packing-slips' ),
                    'image' => __('Image', 'woocommerce-packing-slips' ),
                    'exportinfo' => __('Export Information', 'woocommerce-packing-slips' ),
                    'qr' => __('QR-Code', 'woocommerce-packing-slips' ),
                ),
                'default' => 'pagenumber',
            ),
            array(
                'id'     =>'headerTopRightText',
                'type'  => 'editor',
                'title' => __('Top Right Header Text', 'woocommerce-packing-slips'), 
                'required' => array('headerTopRight','equals','text'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'headerTopRightImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Top Right Header Image', 'woocommerce-packing-slips'), 
                'required' => array('headerTopRight','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Address', 'woocommerce-packing-slips' ),
        // 'desc'       => __( '', 'woocommerce-packing-slips' ),
        'id'         => 'address',
        'subsection' => true,
        'fields'     => array(
            // array(
            //     'id'       => 'addressLayout',
            //     'type'     => 'image_select',
            //     'title'    => __( 'Select Layout', 'woocommerce-packing-slips' ),
            //     'options'  => array(
            //         '1'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/1.png'),
            //         '2'      => array('img'   => plugin_dir_url( __FILE__ ). 'img/2.png'),
            //         '3'      => array('img'   => plugin_dir_url( __FILE__ ). 'img/3.png'),
            //     ),
            //     'default' => '1'
            // ),
            array(
                'id'     =>'addressTextLeft',
                'type'  => 'editor',
                'title' => __('Address Text Left', 'woocommerce-packing-slips'),
                'args'   => array(
                    'teeny'            => false,
                ),
                'default' => '<strong>Ship from:</strong>
                Company XYZ
                Street 123
                1234 City
                E: info@domain.com'
            ),
            array(
                'id'     =>'addressTextCenter',
                'type'  => 'editor',
                'title' => __('Address Text Center', 'woocommerce-packing-slips'),
                'args'   => array(
                    'teeny'            => false,
                ),
                'default' => '<strong>Ship to:</strong>
                {{shipping_company}}
                {{shipping_first_name}} {{shipping_last_name}}
                {{shipping_address_1}} {{shipping_address_2}}
                {{shipping_postcode}} {{shipping_city}}
                {{shipping_state}} {{shipping_country}}'
            ),
            array(
                'id'     =>'addressTextRight',
                'type'  => 'editor',
                'title' => __('Address Text Right', 'woocommerce-packing-slips'),
                'args'   => array(
                    'teeny'            => false,
                ),
                'default' => 
                    '<strong>Bill To</strong>
                    {{billing_company}}
                    {{billing_first_name}} {{billing_last_name}}
                    {{billing_address_1}} {{billing_address_2}}
                    {{billing_postcode}} {{billing_city}}
                    {{billing_state}} {{billing_country}}'
            ),
        )
    ) );
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Content', 'woocommerce-packing-slips' ),
        // 'desc'       => __( '', 'woocommerce-packing-slips' ),
        'id'         => 'content',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'     =>'contentTextIntro',
                'type'  => 'editor',
                'title' => __('Content Intro Text', 'woocommerce-packing-slips'),
                'args'   => array(
                    'teeny'            => false,
                ),
                'default' => 
                    '<h4>Packing Slip No {{id}}</h4>
                    Dear {{shipping_first_name}} {{shipping_last_name}},

                    thank you very much for your order and the trust you have placed in!'
            ),
            array(
                'id'       => 'contentItemsShowPos',
                'type'     => 'checkbox',
                'title'    => __( 'Show Position Field', 'woocommerce-packing-slips' ),
                'default' => 1,
            ),
            array(
                'id'       => 'contentItemsShowProduct',
                'type'     => 'checkbox',
                'title'    => __( 'Show Product Field', 'woocommerce-packing-slips' ),
                'default' => 1,
            ),
            array(
                'id'       => 'contentItemsShowSKU',
                'type'     => 'checkbox',
                'title'    => __( 'Show SKU Field', 'woocommerce-packing-slips' ),
                'default' => 1,
            ),
            array(
                'id'       => 'contentItemsShowWeight',
                'type'     => 'checkbox',
                'title'    => __( 'Show Weight Field', 'woocommerce-packing-slips' ),
                'default' => 1,
            ),
            array(
                'id'       => 'contentItemsShowQty',
                'type'     => 'checkbox',
                'title'    => __( 'Show Quantity Field', 'woocommerce-packing-slips' ),
                'default' => 1,
            ),
            array(
                'id'     =>'contentItemsEvenBackgroundColor',
                'type' => 'color',
                'url'      => true,
                'title' => __('Even Items Background Color', 'woocommerce-packing-slips'), 
                'validate' => 'color',
                'default' => '#FFFFFF',
            ),
            array(
                'id'     =>'contentItemsEvenTextColor',
                'type' => 'color',
                'url'      => true,
                'title' => __('Even Items Text Color', 'woocommerce-packing-slips'), 
                'validate' => 'color',
                'default' => '#333333',
            ),
            array(
                'id'     =>'contentItemsOddBackgroundColor',
                'type' => 'color',
                'url'      => true,
                'title' => __('Odd Items Background Color', 'woocommerce-packing-slips'), 
                'validate' => 'color',
                'default' => '#ebebeb',
            ),
            array(
                'id'     =>'contentItemsOddTextColor',
                'type' => 'color',
                'url'      => true,
                'title' => __('Odd Items Text Color', 'woocommerce-packing-slips'), 
                'validate' => 'color',
                'default' => '#333333',
            ),
            array(
                'id'     =>'contentTextOutro',
                'type'  => 'editor',
                'title' => __('Content Outro Text', 'woocommerce-packing-slips'),
                'args'   => array(
                    'teeny'            => false,
                ),
                'default' => 
                    'Thank you for shopping with us.<br>
                    <br>
                    Yours sincerely<br>
                    WeLaunch'
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Footer', 'woocommerce-packing-slips' ),
        // 'desc'       => __( '', 'woocommerce-packing-slips' ),
        'id'         => 'footer',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableFooter',
                'type'     => 'checkbox',
                'title'    => __( 'Enable', 'woocommerce-packing-slips' ),
                'subtitle' => __( 'Enable footer', 'woocommerce-packing-slips' ),
                'default' => '1',
            ),
            array(
                'id'     =>'footerBackgroundColor',
                'type' => 'color',
                'url'      => true,
                'title' => __('Footer background color', 'woocommerce-packing-slips'), 
                'validate' => 'color',
                'default' => '#F7F7F7',
                'required' => array('enableFooter','equals','1'),
            ),
            array(
                'id'     =>'footerTextColor',
                'type'  => 'color',
                'url'      => true,
                'title' => __('Footer text color', 'woocommerce-packing-slips'), 
                'validate' => 'color',
                'default' => '#333333',
                'required' => array('enableFooter','equals','1'),
            ),
            array(
                'id'     =>'footerFontSize',
                'type'     => 'spinner', 
                'title'    => __('Footer font size', 'woocommerce-packing-slips'),
                'default'  => '8',
                'min'      => '1',
                'step'     => '1',
                'max'      => '40',
            ),
            array(
                'id'     =>'footerLayout',
                'type'  => 'select',
                'title' => __('Footer Layout', 'woocommerce-packing-slips'), 
                'required' => array('enableFooter','equals','1'),
                'options'  => array(
                    'oneCol' => __('1/1', 'woocommerce-packing-slips' ),
                    'twoCols' => __('1/2 + 1/2', 'woocommerce-packing-slips' ),
                    'threeCols' => __('1/3 + 1/3 + 1/3', 'woocommerce-packing-slips' ),
                    'fourCols' => __('1/4 + 1/4 + 1/4 + 1/4', 'woocommerce-packing-slips' ),
                ),
                'default' => 'fourCols',
                'required' => array('enableFooter','equals','1'),
            ),
            array(
                'id'     =>'footerMargin',
                'type'     => 'spinner', 
                'title'    => __('Footer Margin', 'woocommerce-packing-slips'),
                'default'  => '10',
                'min'      => '1',
                'step'     => '1',
                'max'      => '200',
                'required' => array('enableFooter','equals','1'),
            ),
            array(
                'id'     =>'footerHeight',
                'type'     => 'spinner', 
                'title'    => __('Footer Height', 'woocommerce-packing-slips'),
                'default'  => '30',
                'min'      => '1',
                'step'     => '1',
                'max'      => '200',
                'required' => array('enableFooter','equals','1'),
            ),
            array(
                'id'     =>'footerVAlign',
                'type'  => 'select',
                'title' => __('Vertical Align', 'woocommerce-packing-slips'), 
                'required' => array('enableFooter','equals','1'),
                'options'  => array(
                    'top' => __('Top', 'woocommerce-packing-slips' ),
                    'middle' => __('Middle', 'woocommerce-packing-slips' ),
                    'bottom' => __('Bottom', 'woocommerce-packing-slips' ),
                ),
                'default' => 'top',
            ),
            array(
                'id'     =>'footerTopLeft',
                'type'  => 'select',
                'title' => __('Top Left Footer', 'woocommerce-packing-slips'), 
                'required' => array('enableFooter','equals','1'),
                'options'  => array(
                    'none' => __('None', 'woocommerce-packing-slips' ),
                    'bloginfo' => __('Blog information', 'woocommerce-packing-slips' ),
                    'text' => __('Custom text', 'woocommerce-packing-slips' ),
                    'pagenumber' => __('Pagenumber', 'woocommerce-packing-slips' ),
                    'image' => __('Image', 'woocommerce-packing-slips' ),
                    'exportinfo' => __('Export Information', 'woocommerce-packing-slips' ),
                    'qr' => __('QR-Code', 'woocommerce-packing-slips' ),
                ),
                'default' => 'text',
            ),
            array(
                'id'     =>'footerTopLeftText',
                'type'  => 'editor',
                'title' => __('Top Left Footer Text', 'woocommerce-packing-slips'), 
                'required' => array('footerTopLeft','equals','text'),
                'args'   => array(
                    'teeny'            => false,
                ),
                'default' => 
                    'Company<br>
                    Address 123<br>
                    1234 City<br>
                    Country'
            ),
            array(
                'id'     =>'footerTopLeftImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Top Left Footer Image', 'woocommerce-packing-slips'), 
                'required' => array('footerTopLeft','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'footerTopMiddleLeft',
                'type'  => 'select',
                'title' => __('Top Middle Left Footer', 'woocommerce-packing-slips'), 
                'required' => array('footerLayout','equals', array('fourCols')),
                'options'  => array(
                    'none' => __('None', 'woocommerce-packing-slips' ),
                    'bloginfo' => __('Blog information', 'woocommerce-packing-slips' ),
                    'text' => __('Custom text', 'woocommerce-packing-slips' ),
                    'pagenumber' => __('Pagenumber', 'woocommerce-packing-slips' ),
                    'image' => __('Image', 'woocommerce-packing-slips' ),
                    'exportinfo' => __('Export Information', 'woocommerce-packing-slips' ),
                    'qr' => __('QR-Code', 'woocommerce-packing-slips' ),
                ),
                'default' => 'text',
            ),
            array(
                'id'     =>'footerTopMiddleLeftText',
                'type'  => 'editor',
                'title' => __('Top Middle Left Footer Text', 'woocommerce-packing-slips'), 
                'required' => array('footerTopMiddleLeft','equals','text'),
                'args'   => array(
                    'teeny'            => false,
                ),
                'default' => 
                    'Tel.: 0160 123 1534<br>
                    E-Mail: info@yourdomain.com<br>
                    Web: https://yourdomain.com'
            ),
            array(
                'id'     =>'footerTopMiddleLeftImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Top Middle Left Footer Image', 'woocommerce-packing-slips'), 
                'required' => array('footerTopMiddleLeft','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'footerTopMiddleRight',
                'type'  => 'select',
                'title' => __('Top Middle Right Footer', 'woocommerce-packing-slips'), 
                'required' => array('footerLayout','equals', array('fourCols','threeCols')),
                'options'  => array(
                    'none' => __('None', 'woocommerce-packing-slips' ),
                    'bloginfo' => __('Blog information', 'woocommerce-packing-slips' ),
                    'text' => __('Custom text', 'woocommerce-packing-slips' ),
                    'pagenumber' => __('Pagenumber', 'woocommerce-packing-slips' ),
                    'image' => __('Image', 'woocommerce-packing-slips' ),
                    'exportinfo' => __('Export Information', 'woocommerce-packing-slips' ),
                    'qr' => __('QR-Code', 'woocommerce-packing-slips' ),
                ),
                'default' => 'text',
            ),
            array(
                'id'     =>'footerTopMiddleRightText',
                'type'  => 'editor',
                'title' => __('Top Middle Right Footer Text', 'woocommerce-packing-slips'), 
                'required' => array('footerTopMiddleRight','equals','text'),
                'args'   => array(
                    'teeny'            => false,
                ),
                'default' => 
                    'VAT-ID: 123 435 456<br>
                    Managing Director: Your Name'
            ),
            array(
                'id'     =>'footerTopMiddleRightImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Top Middle Right Footer Image', 'woocommerce-packing-slips'), 
                'required' => array('footerTopMiddleRight','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'footerTopRight',
                'type'  => 'select',
                'title' => __('Top Right Footer', 'woocommerce-packing-slips'), 
                'required' => array('footerLayout','equals', array('fourCols','threeCols','twoCols')),
                'options'  => array(
                    'none' => __('None', 'woocommerce-packing-slips' ),
                    'bloginfo' => __('Blog information', 'woocommerce-packing-slips' ),
                    'text' => __('Custom text', 'woocommerce-packing-slips' ),
                    'pagenumber' => __('Pagenumber', 'woocommerce-packing-slips' ),
                    'image' => __('Image', 'woocommerce-packing-slips' ),
                    'exportinfo' => __('Export Information', 'woocommerce-packing-slips' ),
                    'qr' => __('QR-Code', 'woocommerce-packing-slips' ),
                ),
                'default' => 'text',
            ),
            array(
                'id'     =>'footerTopRightText',
                'type'  => 'editor',
                'title' => __('Top Right Footer Text', 'woocommerce-packing-slips'), 
                'required' => array('footerTopRight','equals','text'),
                'args'   => array(
                    'teeny'            => false,
                ),
                'default' => 
                    'Bank: Deutsche Bank<br>
                    IBAN: DE 123345 3 456<br>
                    BIC: GEN0123'
            ),
            array(
                'id'     =>'footerTopRightImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Top Right Footer Image', 'woocommerce-packing-slips'), 
                'required' => array('footerTopRight','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Advanced settings', 'woocommerce-packing-slips' ),
        'desc'       => __( 'Custom stylesheet / javascript.', 'woocommerce-packing-slips' ),
        'id'         => 'advanced',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'debugMode',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Debug Mode', 'woocommerce-packing-slips' ),
                'subtitle' => __( 'This stops creating the PDF and shows the plain HTML.', 'woocommerce-packing-slips' ),
                'default'   => 0,
            ),
            array(
                'id'       => 'debugMPDF',
                'type'     => 'checkbox',
                'title'    => __( 'Enable MPDF Debug Mode', 'woocommerce-packing-slips' ),
                'subtitle' => __( 'Show image , font or other errors in the PDF Rendering engine.', 'woocommerce-packing-slips' ),
                'default'   => 0,
            ),
            array(
                'id'       => 'customCSS',
                'type'     => 'ace_editor',
                'mode'     => 'css',
                'title'    => __( 'Custom CSS', 'woocommerce-packing-slips' ),
                'subtitle' => __( 'Add some stylesheet if you want.', 'woocommerce-packing-slips' ),
            ),
        )
    ));


    Redux::setSection( $opt_name, array(
        'title'  => __( 'Preview', 'woocommerce-packing-slips' ),
        'id'     => 'preview',
        'desc'   => __( 'Need support? Please use the comment function on codecanyon.', 'woocommerce-packing-slips' ),
        'icon'   => 'el el-eye-open',
    ) );

    /*
     * <--- END SECTIONS
     */
