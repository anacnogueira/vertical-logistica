<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderNormalizeRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function normalize(OrderNormalizeRequest $request)
    {
        $file = $request->file('file');
        $contents = file_get_contents($file->getRealPath());
        $lines = explode("\n", $contents);
        $data = array_map('trim', $lines);

        $normalizedData = [];

        foreach ($data as $line) {
            if (empty($line)) {
                continue;
            }

            $userIdLength = 10;
            $userNameLength = 45;
            $orderIdLength = 10;
            $productIdLength = 10;
            $valueLength = 12;
            $dateLength = 8;

            $userId     = substr($line, 0, $userIdLength);
            $userName   = substr($line, $userIdLength, $userNameLength);
            $orderId    = substr($line, $userIdLength + $userNameLength, $orderIdLength);
            $productId  = substr($line, $userIdLength + $userNameLength + $orderIdLength, $productIdLength);
            $value      = substr($line, $userIdLength + $userNameLength + $orderIdLength + $productIdLength, $valueLength);
            $date       = substr($line, $userIdLength + $userNameLength + $orderIdLength + $productIdLength + $valueLength, $dateLength);

            $userId = (int) ltrim($userId, '0');
            $orderId = (int) ltrim($orderId, '0');
            $productId = (int) ltrim($productId, '0');
            $userName = trim($userName);
            $value = number_format((float) trim($value), 2, '.', '');

            $year = substr($date, 0, 4);
            $month = substr($date, 4, 2);
            $day = substr($date, 6, 2);
            $formattedDate = "{$year}-{$month}-{$day}";

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

        return Response::json($normalizedData);
    }
}
