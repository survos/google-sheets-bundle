<?php

declare(strict_types=1);

namespace Survos\GoogleSheetsBundle\Service;

use Google_Client;
use Google_Service_Sheets;

class GoogleApiClientService
{
    public function __construct(
        private readonly string $applicationName = '',
        private readonly string $credentials = '',
        private readonly string $clientSecret = '',
    ) {}

    public function getClient(string $type = 'offline'): Google_Client
    {
        $client = new Google_Client();

        // $clientSecret is the raw JSON string (decoded by Symfony's base64 env processor)
        // or an array — setAuthConfig accepts both
        $config = json_validate($this->clientSecret)
            ? json_decode($this->clientSecret, true)
            : $this->clientSecret;

        $client->setAuthConfig($config);
        $client->setScopes([
            \Google_Service_Sheets::SPREADSHEETS_READONLY,
            \Google_Service_Drive::DRIVE_READONLY,
        ]);
        $client->setAccessType($type);

        return $client;
    }
}
