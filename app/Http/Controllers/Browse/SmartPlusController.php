<?php

namespace App\Http\Controllers\Browse;

use AWS;
use Illuminate\Http\Response;
use Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SmartPlusController extends Controller
{
    public function download($encodedFilename)
    {
        // s3のファイル名のエンコードはスペースがプラスなためurlencode
        $filename = urldecode($encodedFilename);

        $bucket = parse_url(config('app.url'))['host'];

        if ($bucket === 'localhost') {
            $bucket = 'dev-smart.switch-m.biz';
        }

        $key = "static/smartplus/download/${filename}";
        $url = "s3://{$bucket}/${key}";

        $client = AWS::createClient('s3');
        $client->registerStreamWrapper();

        try {
            $stream = fopen($url, 'r');
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), '`403 Forbidden`') !== false || strpos($e->getMessage(), '`404 Not Found`') !== false) {
                return response('ファイルが見つかりませんでした。削除された可能性があります。', 404);
            }
            Log::error($e);
            return response('エラーが発生しました。しばらくたってから再度やり直してください。', 500);
        }

        $explodedFilename = array_values(explode('/', $filename));
        $downloadFilename = end($explodedFilename);

        return new StreamedResponse(
            function () use ($stream): void {
                while (!feof($stream)) {
                    echo fread($stream, 1024);
                }
                fclose($stream);
            },
            200,
            [
                'Content-Disposition' => "attachment; filename=\"${downloadFilename}\"",
            ]
        );
    }
}
