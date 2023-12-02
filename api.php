<?php
// Copyright (©) 2023 RAMPAGE Interactive
// PayPal API SDK

require "./src/autoload.php";
require "./modules/BramusRouter.php";

$PayPalApi = new \RAMPAGELLC\PayPal\PayPal(getenv("PP_CID"), getenv("PP_CS"));
$Router = new \Bramus\Router\Router();

$Router->mount('/checkout', function () use ($Router) {
    $Router->mount('/orders', function () use ($Router) {
        $Router->all('/create', function () {
            $PayPalCheckoutApi = new RAMPAGELLC\PayPal\PayPalCheckout("API");
            echo json_encode($PayPalCheckoutApi->processOrder(json_decode(file_get_contents("php://input"), true)));
        });

        $Router->all('/{orderID}/capture', function ($orderID) {
            $PayPalCheckoutApi = new RAMPAGELLC\PayPal\PayPalCheckout("API");
            echo json_encode($PayPalCheckoutApi->captureOrderById($orderID));
        });
    });
});