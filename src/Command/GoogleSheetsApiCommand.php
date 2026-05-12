<?php

namespace Survos\GoogleSheetsBundle\Command;

use Survos\GoogleSheetsBundle\Service\GoogleApiClientService;
use Survos\GoogleSheetsBundle\Service\GoogleSheetsApiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('googlesheets:execute', 'Wrapper for console commands')]
final class GoogleSheetsApiCommand
{
    public function __construct(
        private readonly GoogleApiClientService $clientService,
        private readonly GoogleSheetsApiService $googleSheetsApiService,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option('Sheets API function to execute')] ?string $function = null,
        #[Option('Sheet title')] ?string $title = null,
        #[Option('Spreadsheet ID')] ?string $id = null,
        #[Option('Number of rows for the header')] int $header = 0,
        #[Option('Grid data as JSON')] ?string $data = null,
    ): int {
        $parsedData = $data !== null ? json_decode($data) : null;
        $service = $this->googleSheetsApiService;

        $response = 'no action has been made';
        if ($function === 'token') {
            $response = $this->clientService->createNewSheetApiAccessToken();
        } else {
            $service->setSheetServices($id);
        }

        if ($function === 'get') {
            $service->setSheetServices($id);
            $response = $service->getGoogleSpreadSheets();
            dd($response);
        } elseif ($function === 'create') {
            $response = $service->createNewSheet($title, $parsedData, $header);
        } elseif ($function === 'update') {
            $response = $service->updateSheet($title, $parsedData, $header);
        } elseif ($function === 'clear') {
            $response = $service->clearSheetByTitle($title);
        } elseif ($function === 'delete') {
            $response = $service->deleteSheetByTitle($title);
        }

        $io->writeln((string) $response);

        return Command::SUCCESS;
    }
}
