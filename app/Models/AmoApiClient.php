<?php

namespace App\Models;

use AmoCRM\Client\AmoCRMApiClient;

class AmoApiClient
{
    public static function get():AmoCRMApiClient
    {
        $data=config('amo.client');
        $apiClient = new AmoCRMApiClient($data['client_id'], $data['client_secret'], $data['subdomain']);
        $apiClient->setAccountBaseDomain($data['redirect_uri'].".amocrm.ru");
        return $apiClient;
    }
}
