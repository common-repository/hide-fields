<?php

use WP_Hide_Fields\DefaultCheckout;
use WP_Hide_Fields\Field;

require_once __DIR__.'/vendor/autoload.php';

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$checkout = new DefaultCheckout();

//remove all settings options
foreach ($checkout->getDefaultBillingFields() as $fieldName) {
    $field = new Field($fieldName);
    $pricedIdField = $field->getPricedIdField();

    //delete normal(priced) options
    if (get_option($pricedIdField)) {
        delete_option($pricedIdField);
    }

    //delete free options
    $freeIdField = $field->getFreeIdField();
    if (get_option($freeIdField)) {
        delete_option($freeIdField);
    }
}
