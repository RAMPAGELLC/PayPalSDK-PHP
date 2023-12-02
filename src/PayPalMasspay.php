<?php
// Copyright (©) 2023 RAMPAGE Interactive
// PayPal API SDK

namespace RAMPAGELLC\PayPal;

use \Exception;

class PayPalMasspay extends PayPal
{
    /**
     * Dataset example:
     * [
     *   [
     *      "email" => "hello@paypal.com",
     *      "amount" => "50.00",
     *      "currency" => "USD"
     *   ]
     * ]
     */
    public function Send(array $list, string $email_subject = "You have money!", string $email_message = "You received a payment. Thanks for using our service!")
    {
        $sender_batch_id = PayPal::randomNumber(13);
        $Payload = [
            "sender_batch_header" => [
                "sender_batch_id" => $sender_batch_id,
                "recipient_type" => "EMAIL",
                "email_subject" => $email_subject,
                "email_message" => $email_message
            ],
            "items" => []
        ];

        foreach ($list as $Item) {
            $Payload["items"][] = [
                "receiver" => $Item["email"] ?? "billing@paypal.com",
                "recipient_type" => $Item["recipient_type"] ?? "EMAIL",
                "note" => $Item["note"] ?? "Thanks for your patronage!",
                "sender_item_id" => $Item["sender_item_id"] ?? PayPal::randomNumber(13),
                "recipient_wallet" => "RECIPIENT_SELECTED",
                "amount" => [
                    "currency" => $Item["currency"] ?? "USD",
                    "value" => $Item["amount"] ?? "00.00"
                ]
            ];
        }

        return $this->fetch($this->base . "/v1/payments/payouts", [
            "method" => "POST",
            "headers" => [
                "Content-Type" => "application/json",
                "Authorization" => "Bearer " . $this->bearerToken,
            ],
            "body" => json_encode($Payload),
        ]);
    }
}