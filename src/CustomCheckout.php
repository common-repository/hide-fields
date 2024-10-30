<?php

namespace WP_Hide_Fields;

class CustomCheckout
{
    public function __construct()
    {
        add_action('wp', [$this, 'billingFieldsDisplay']);
    }

    public function billingFieldsDisplay()
    {
        //Check if is checkout
        if (function_exists('is_checkout') && (is_checkout() || is_ajax())) {
            add_filter('woocommerce_checkout_fields', function ($fields) {
                $fieldsToRemove = $this->getFieldsToRemove();
                if (!empty($fieldsToRemove)) {
                    // unset each of those unwanted fields
                    foreach ($fieldsToRemove as $fieldName) {
                        unset($fields['billing'][$fieldName]);
                    }
                }

                return $fields;
            });
        } else {
            return;
        }
    }

    public function fieldSelected($fieldId, $allOptions)
    {
        if ('yes' == $allOptions[$fieldId]) {
            return true;
        }

        return false;
    }

    public function getFieldsToRemove()
    {
        //get all setting values (options)
        $allOptions = wp_load_alloptions();
        //get all billing fields
        $checkout = new DefaultCheckout();
        $billingFieldsNames = $checkout->getDefaultBillingFields();
        //If the purchase is normal (priced)
        if (WC()->cart && WC()->cart->needs_payment()) {
            $fieldsToRemove = $this->getNormalOrderSettings($billingFieldsNames, $allOptions);
        }
        //If the purchase is free
        else {
            $fieldsToRemove = $this->getFreeOrderSettings($billingFieldsNames, $allOptions);
        }

        if (WC()->cart->needs_shipping() && !empty($fieldsToRemove)) {
            $fieldsToRemove = $this->removeAddressFields($fieldsToRemove);
        }

        return $fieldsToRemove;
    }

    public function getNormalOrderSettings($billingFieldsNames, $allOptions)
    {
        foreach ($billingFieldsNames as $fieldName) {
            $field = new Field($fieldName);
            //If this field has been selected in the settings panel, will be included in the array fields to remove
            if ($this->fieldSelected($field->getPricedIdField(), $allOptions)) {
                $fieldsToRemove[] = $fieldName;
            }
        }

        return $fieldsToRemove;
    }

    public function getFreeOrderSettings($billingFieldsNames, $allOptions)
    {
        foreach ($billingFieldsNames as $fieldName) {
            $field = new Field($fieldName);
            //If this field has been selected in the settings panel, will be included in the array fields to remove
            if ($this->fieldSelected($field->getFreeIdField(), $allOptions)) {
                $fieldsToRemove[] = $fieldName;
            }
        }

        return $fieldsToRemove;
    }

    public function removeAddressFields($fieldsToRemove)
    {
        $fieldsToRemove = array_filter($fieldsToRemove, function ($fieldToRemove) {
            $addressFields = [
                'billing_address_1',
                'billing_address_2',
                'billing_city',
                'billing_country',
                'billing_state',
            ];

            return in_array($fieldToRemove, $addressFields);
        });
    }
}
