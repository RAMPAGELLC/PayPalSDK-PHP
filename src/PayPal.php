<?php
// Copyright (©) 2023 RAMPAGE Interactive
// PayPal API SDK

namespace RAMPAGELLC\PayPal;
use \Exception;

class PayPal {
    public $environment = "live"; // sandbox or live.
    public $base;
    public $paypalClientId;
    public $paypalClientSecret;
    public $bearerToken;
    
    public function __construct(string $PAYPAL_CLIENT_ID, string $PAYPAL_CLIENT_SECRET) {
        $this->paypalClientId = $PAYPAL_CLIENT_ID;
        $this->paypalClientSecret = $PAYPAL_CLIENT_SECRET;
        $this->base = $this->environment == "live" ? "https://api-m.paypal.com" : "https://api-m.sandbox.paypal.com";
        
        $response = $this->fetch($this->base . "/v1/oauth2/token", [
            "method" => "POST",
            "body" => "grant_type=client_credentials",
            "headers" => [
                "Authorization" => "Basic " . base64_encode("{$this->paypalClientId}:{$this->paypalClientSecret}"),
            ],
        ]);

        $this->bearerToken = json_decode($response, true)["access_token"];
    }

    public static function randomNumber($length)
    {
        $randomNumber = '';

        for ($i = 0; $i < $length; $i++) {
            $randomNumber .= mt_rand(0, 9);
        }

        return $randomNumber;
    }

    public static function randomString($length = 10)
    {
        $numbers = '0123456789';
        $abcs = 'abcdefghijklmnopqrstuvwxyz';
        $characters = $numbers . $abcs . strtoupper($abcs);

        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
    
    public static function fetch(string $url, array $options) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $options["method"]);
        
        if (isset($options["body"])) curl_setopt($ch, CURLOPT_POSTFIELDS, $options["body"]);
        if (isset($options["headers"])) {
            $headers = [];
            
            foreach ($options["headers"] as $key => $value) {
                $headers[] = "{$key}: {$value}";
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) throw new Exception('Curl error: ' . curl_error($ch));

        curl_close($ch);
        return $response;
    }
}