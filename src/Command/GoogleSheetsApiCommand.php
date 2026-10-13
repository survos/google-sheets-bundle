<?php

declare(strict_types=1);

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
        $parsedData = null !== $data ? json_decode($data) : null;
        $service = $this->googleSheetsApiService;

        $response = 'no action has been made';
        if ('token' === $function) {
            $response = $this->clientService->createNewSheetApiAccessToken();
        } else {
            $service->setSheetServices($id);
        }

        if ('get' === $function) {
            $service->setSheetServices($id);
            $response = $service->getGoogleSpreadSheets();
            dd($response);
        } elseif ('create' === $function) {
            $response = $service->createNewSheet($title, $parsedData, $header);
        } elseif ('update' === $function) {
            $response = $service->updateSheet($title, $parsedData, $header);
        } elseif ('clear' === $function) {
            $response = $service->clearSheetByTitle($title);
        } elseif ('delete' === $function) {
            $response = $service->deleteSheetByTitle($title);
        }

        $io->writeln((string) $response);

        return Command::SUCCESS;
    }
}
