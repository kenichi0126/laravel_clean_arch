<?php

namespace Switchm\SmartApi\Components\Common;

use stdClass;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait OutputCsvTrait
{
    public function outputCsv(string $filename, array $header, array $body, bool $footerFlag = true): StreamedResponse
    {
        return new StreamedResponse(function () use ($filename, $header, $body, $footerFlag): void {
            $stream = fopen('php://output', 'w');

            foreach ($header as $line) {
                fputcsv($stream, $line);
            }

            foreach ($body as $line) {
                fputcsv($stream, json_decode(json_encode($line), true));
            }
            fclose($stream);
        }, 200, [
            'Content-Type' => 'text/csv;charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function outputCsvGenerator(string $filename, array $header, array $generator, stdClass $data): StreamedResponse
    {
        return new StreamedResponse(function () use ($filename, $header, $generator, $data): void {
            $stream = fopen('php://output', 'w');

            foreach ($header as $line) {
                fputcsv($stream, $line);
            }

            foreach (call_user_func($generator, $data) as $body) {
                foreach ($body as $line) {
                    fputcsv($stream, json_decode(json_encode($line), true));
                }
            }
            fclose($stream);
        }, 200, [
            'Content-Type' => 'text/csv;charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
