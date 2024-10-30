<?php

namespace WP_Hide_Fields;

class DefaultCheckout
{
    public function __construct()
    {
    }

    public function getDefaultBillingFields()
    {
        $countries = new \WC_Countries();
        //Get all billing fields corresponding to the base country
        return array_keys($countries->get_address_fields($countries->get_base_country(), 'billing_'));
    }
}
