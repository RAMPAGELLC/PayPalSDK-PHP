<?php
// Copyright (©) 2023 RAMPAGE Interactive
// PayPal API SDK

namespace RAMPAGELLC\PayPal;
use \Exception;

class PayPalCheckout extends PayPal {
    private $sdkType;

    public function __construct(string $SDK_TYPE) {
        $this->sdkType = $SDK_TYPE;
    }

    private function createOrder(array $cart) {
        $payload = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $cart["price"],
                    ],
                ],
            ],
        ];

        $response = $this->fetch($this->base . "/v2/checkout/orders", [
            "method" => "POST",
            "headers" => [
                "Content-Type" => "application/json",
                "Authorization" => "Bearer " . $this->bearerToken,
            ],
            "body" => json_encode($payload),
        ]);

        return $this->handleResponse($response);
    }

    private function captureOrder(string $orderID) {
        $response = $this->fetch($this->base . "/v2/checkout/orders/$orderID/capture", [
            "method" => "POST",
            "headers" => [
                "Content-Type" => "application/json",
                "Authorization" => "Bearer " . $this->bearerToken,
            ],
        ]);

        return $this->handleResponse($response);
    }

    private function handleResponse(mixed $response) {
        return [
            "jsonResponse" => json_decode($response, true),
            "httpStatusCode" => http_response_code(),
        ];
    }

    public function processOrder(array $cart) {
        try {
            if (empty($cart) || empty($cart["id"]) || empty($cart["price"])) throw new Exception("Failed to create order.");
            $result = $this->createOrder($cart);
            
            if ($this->sdkType == "JSON") {
                http_response_code($result["httpStatusCode"]);
                echo json_encode($result["jsonResponse"]);
                return true;
            }
            
            if ($this->sdkType == "API") return $result["jsonResponse"];
        } catch (Exception $error) {
            if ($this->sdkType == "JSON") {
                echo json_encode(["error" => "Failed to create order."]);
                return false;
            }
            
            if ($this->sdkType == "API") return false;
        }
    }

    public function captureOrderById(string $orderID) {
        try {
            $result = $this->captureOrder($orderID);
            
            if ($this->sdkType == "JSON") {
                http_response_code($result["httpStatusCode"]);
                echo json_encode($result["jsonResponse"]);
                return true;
            }
            
            if ($this->sdkType == "API") return $result["jsonResponse"];
        } catch (Exception $error) {
            if ($this->sdkType == "JSON") {
                echo json_encode(["error" => "Failed to create order."]);
                return false;
            }
            
            if ($this->sdkType == "API") return false;
        }
    }
}