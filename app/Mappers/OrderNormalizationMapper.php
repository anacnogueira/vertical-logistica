<?php

namespace App\Mappers;

class OrderNormalizationMapper
{
    private array $normalizedData = [];

    public function processLineData(array $parsedData): void
    {
        if (empty($parsedData)) {
            return;
        }

        $userId = $parsedData['user_id'];
        $userName = $parsedData['user_name'];
        $orderId = $parsedData['order_id'];
        $productId = $parsedData['product_id'];
        $value = $parsedData['value'];
        $date = $parsedData['date'];

        $userKey = $this->findUserKey($userId);

        if ($userKey === false) {
            $this->normalizedData[] = [
                'user_id' => $userId,
                'name' => $userName,
                'orders' => []
            ];
            $userKey = count($this->normalizedData) - 1;
        }

        $orderKey = $this->findOrderKey($userKey, $orderId);

        if ($orderKey === false) {
            $this->normalizedData[$userKey]['orders'][] = [
                'order_id' => $orderId,
                'total' => '0.00',
                'date' => $date,
                'products' => []
            ];
            $orderKey = count($this->normalizedData[$userKey]['orders']) - 1;
        }

        $this->normalizedData[$userKey]['orders'][$orderKey]['products'][] = [
            'product_id' => $productId,
            'value' => $value
        ];

        $currentTotal = (float) $this->normalizedData[$userKey]['orders'][$orderKey]['total'];
        $total = $currentTotal + (float) $value;
        $this->normalizedData[$userKey]['orders'][$orderKey]['total'] = number_format($total, 2, '.', '');
    }

    public function getNormalizedData(): array
    {
        return $this->normalizedData;
    }

    private function findUserKey(int $userId)
    {
        foreach ($this->normalizedData as $key => $user) {
            if ($user['user_id'] === $userId) {
                return $key;
            }
        }
        return false;
    }

    private function findOrderKey(int $userKey, int $orderId)
    {
        foreach($this->normalizedData[$userKey]['orders'] as $key => $order) {
            if ($order['order_id'] === $orderId) {
                return $key;
            }
        }

        return false;
    }
}