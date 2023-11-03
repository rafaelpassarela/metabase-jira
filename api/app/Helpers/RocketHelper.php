<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class Rocket
{
    private $alias;
    private $channel;
    private $icon;
    private $web_hook;

    public function __construct() {
        $this->alias    = env('ALERTS_ALIAS');
        $this->channel  = env('ALERTS_CHANNEL');
        $this->icon     = env('ALERTS_ICON');
        $this->web_hook = env('ALERTS_WEBHOOK');
    }

    public function isConfigurated() {
        return (trim($this->channel) != '')
            && (trim($this->web_hook) != '');
    }

    public function sendMessage($message)
    {
        $url = $this->web_hook;

        $body = json_encode(
            array(
                "channel"     => $this->channel,
                "text"        => "```\n" . $message . "\n```",
                "apiToken"    => $this->getToken(),
                "displayUser" => $this->alias,
                "iconURL"     => $this->icon
            )
        );

        $response = Http::withBody(
            $body
        )->post($url);

        if ($response->ok()) {
            return array(
                "status" => true,
                "message" => $response->body()
            );
        } else {
            return array(
                "status" => false,
                "message" => $response->status() . ' ' . $response->body()
            );
        }
    }

    function getToken() {
        // {{now.endOfMonth.as("dd")}} 31
        // {{now.as("yy")}}            23
        // {{now.as("MM")}}            10
        // {{now.as("yyyy")}}          2023
        // {{now.as("dd")}}            24
        // {{now.as("yy")}}            23
        // {{now.as("dd")}}            24
        // {{now.as("MM")}}            10

        $day   = date('d'); // 00
        $month = date('m'); // 00
        $year  = date('y'); // 00
        $fullYear = date('Y'); // 0000
        $maxDays  = Carbon::now()->month($month)->daysInMonth;

        $token = $maxDays . $year . $month . $fullYear . $day . $year . $day . $month;
        return $token;

    }
}
