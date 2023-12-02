<?php
// Copyright (©) 2023 RAMPAGE Interactive
// PayPal API SDK

require "./src/autoload.php";

$PayPalApi = new \RAMPAGELLC\PayPal\PayPal(getenv("PP_CID"), getenv("PP_CS"));
$PayPalCheckoutApi = new RAMPAGELLC\PayPal\PayPalCheckout("API");

$result = $PayPalCheckoutApi->processOrder([
    "id" => 1,
    "price" => "5.00"
]);

$result = $PayPalCheckoutApi->captureOrderById($orderID);

?>