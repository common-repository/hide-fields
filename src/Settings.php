<?php

namespace WP_Hide_Fields;

include_once WP_PLUGIN_DIR.'/woocommerce/includes/admin/settings/class-wc-settings-page.php';

class Settings extends \WC_Settings_Page
{
    public function __construct()
    {
        $this->id = 'hide_fields';
        $this->label = __('Hide Fields', 'hidefields');
        add_filter('woocommerce_settings_tabs_array', [$this, 'add_settings_page'], 50);
        add_action('woocommerce_settings_'.$this->id, [$this, 'output']);
        add_action('woocommerce_settings_save_'.$this->id, [$this, 'save']);
        add_action('woocommerce_sections_'.$this->id, [$this, 'output_sections']);
    }

    //Get the sections
    public function get_sections()
    {
        $sections = [
            '' => __('Normal Orders', 'hidefields'),
            'free' => __('Free', 'hidefields'),
        ];

        return apply_filters('woocommerce_get_sections_'.$this->id, $sections);
    }

    //Output the settings
    public function output()
    {
        global $current_section;
        $settings = $this->getSettings($current_section);
        \WC_Admin_Settings::output_fields($settings);
    }

    //Save settings
    public function save()
    {
        global $current_section;

        $settings = $this->getSettings($current_section);
        \WC_Admin_Settings::save_fields($settings);

        if ($current_section) {
            do_action('woocommerce_update_options_'.$this->id.'_'.$current_section);
        }
    }

    public function getSettings($current_section = '')
    {
        //Get Default Billing fields
        $checkout = new DefaultCheckout();
        $billingFields = $checkout->getDefaultBillingFields();
        //If the pluging has been activated, populate DB with default options
        register_activation_hook(WPCC_PLUGIN_DIR.'/main.php', [$this, 'defaultOptions']);
        if ('free' === $current_section) {
            $settings = apply_filters(
                $this->id.'free_orders',
                $this->getFreeSettings($billingFields)
                );
        } else {
            $settings = apply_filters(
                $this->id.'priced_orders',
                $this->getPricedSettings($billingFields)
                )
            ;
        }

        return apply_filters('woocommerce_get_settings_'.$this->id, $settings, $current_section);
    }

    public function getPricedSettings($billingFields)
    {
        $pricedSettings['priced_cart_title'] =
               [
                   'name' => __('Normal Orders', 'hidefields'),
                   'type' => 'title',
                   'desc' => __('Please select which fields you want to hide when a normal(priced) order is made', 'hidefields'),
                   'id' => 'wc_hidefields_settings_tab_hidefields_priced_cart_title',
               ];
        foreach ($billingFields as $fieldName) {
            $field = new Field($fieldName);
            $capitalizedFieldName = ucwords(str_replace('_', ' ', $fieldName));
            $pricedSettings['priced_'.$fieldName] =
                 [
                     'name' => __($capitalizedFieldName, 'hidefields'),
                     'type' => 'checkbox',
                     'std' => 'no',
                     'default' => 'no',
                     'id' => $field->getPricedIdField(),
                 ];
        }
        $pricedSettings['priced_cart_end'] =
            [
                'type' => 'sectionend',
                'id' => 'wc_hidefields_settings_tab_hidefields_priced_cart_title',
            ];

        return $pricedSettings;
    }

    public function getFreeSettings($billingFields)
    {
        $freeSettings['free_cart_title'] =
               [
                   'name' => __('Free Cart Fields', 'hidefields'),
                   'type' => 'title',
                   'desc' => __('Please select which fields you want to hide when a free order is made', 'hidefields'),
                   'id' => 'wc_hidefields_settings_tab_hidefields_free_cart_title',
               ];
        foreach ($billingFields as $fieldName) {
            $field = new Field($fieldName);
            $capitalizedFieldName = ucwords(str_replace('_', ' ', $fieldName));
            $freeSettings['free_'.$fieldName] =
                 [
                     'name' => __($capitalizedFieldName, 'hidefields'),
                     'type' => 'checkbox',
                     'std' => 'no',
                     'default' => 'no',
                     'id' => $field->getFreeIdField(),
                 ];
        }
        $freeSettings['free_cart_end'] =
            [
                'type' => 'sectionend',
                'id' => 'wc_hidefields_settings_tab_hidefields_free_cart_title',
            ];

        return $freeSettings;
    }

    public function defaultOptions()
    {
        $checkout = new DefaultCheckout();
        $billingFields = $checkout->getDefaultBillingFields();
        foreach ($billingFields as $fieldName) {
            $field = new Field($fieldName);
            $pricedIdField = $field->getPricedIdField();
            if (!get_option($pricedIdField) || '' === get_option($pricedIdField)) {
                add_option($pricedIdField, 'no');
            }
            $freeIdField = $field->getFreeIdField();
            if (!get_option($freeIdField) || '' === get_option($freeIdField)) {
                add_option($freeIdField, 'no');
            }
        }
    }
}
