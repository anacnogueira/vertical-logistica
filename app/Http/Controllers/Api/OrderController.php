<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use Illuminate\Http\JsonResponse;
use App\Services\LineParser;
use App\Mappers\OrderNormalizationMapper;
use App\Http\Resources\UserOrderResource;

class OrderController extends Controller
{
    public function normalize(FileUploadRequest $request, LineParser $parser, OrderNormalizationMapper $mapper): JsonResponse
    {
        $fileContents = file_get_contents($request->file('file')->getRealPath());
        $lines = explode("\n", $fileContents);

        $normalizedData = [];

        foreach ($lines as $line) {
            $parsedData = $parser->parseLine($line);

            if (!empty($parsedData)) {
                $mapper->processLineData($parsedData);
            }
        }

        return UserOrderResource::collection($mapper->getNormalizedData())->response();
    }
}
