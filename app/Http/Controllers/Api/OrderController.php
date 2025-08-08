<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use Illuminate\Support\Facades\Storage;
use App\Services\LineParser;

class OrderController extends Controller
{
    public function normalize(FileUploadRequest $request, LineParser $parser)
    {
        $fileContents = file_get_contents($request->file('file')->getRealPath());
        $lines = explode("\n", $fileContents);

        $normalizedData = [];

        foreach ($lines as $line) {
            $parsedData = $parser->parseLine($line);

            $userKey = array_search($userId, array_column($normalizedData, 'user_id'));

            if ($userKey === false) {
                $normalizedData[] = [
                    'user_id' => $userId,
                    'name' => $userName,
                    'orders' => []
                ];
                $userKey = count($normalizedData) - 1;
            }

            // Encontra o pedido existente para o usuário ou cria um novo
            $orderKey = array_search($orderId, array_column($normalizedData[$userKey]['orders'], 'order_id'));

            if ($orderKey === false) {
                $normalizedData[$userKey]['orders'][] = [
                    'order_id' => $orderId,
                    'total' => '0.00', // Será atualizado abaixo
                    'date' => $formattedDate, // Usando a data formatada
                    'products' => []
                ];
                $orderKey = count($normalizedData[$userKey]['orders']) - 1;
            }

            // Adiciona o produto ao pedido
            $normalizedData[$userKey]['orders'][$orderKey]['products'][] = [
                'product_id' => $productId,
                'value' => $value
            ];

             // Atualiza o total do pedido
             $currentTotal = (float) $normalizedData[$userKey]['orders'][$orderKey]['total'];
             $normalizedData[$userKey]['orders'][$orderKey]['total'] = number_format($currentTotal + (float) $value, 2, '.', '');

        }

        return response()->json($normalizedData);
    }
}
