<?php

namespace App\Services;

class LineParser
{
    //Define positions and length of each field;
    private const USER_ID_LENGTH = 10;
    private const USER_NAME_LENGTH = 45;
    private const ORDER_ID_LENGTH = 10;
    private const PRODUCT_ID_LENGTH = 10;
    private const VALUE_LENGTH = 12;
    private const DATE_LENGTH = 8;

    public function parseLine(string $line): array
    {
        $line = trim($line);

        if (empty($line)) {
            return [];
        }

        $offset = 0;

        $userId = $this->extractAndCleanNumeric($line, $offset, self::USER_ID_LENGTH);
        $offset += self::USER_ID_LENGTH;

        $userName = $this->extractAndCleanText($line, $offset, self::USER_NAME_LENGTH);
        $offset += self::USER_NAME_LENGTH;

        $orderId = $this->extractAndCleanNumeric($line, $offset, self::ORDER_ID_LENGTH);
        $offset += self::ORDER_ID_LENGTH;

        $productId = $this->extractAndCleanNumeric($line, $offset, self::PRODUCT_ID_LENGTH);
        $offset += self::PRODUCT_ID_LENGTH;

        $value = $this->extractAndCleanDecimal($line, $offset, self::VALUE_LENGTH);
        $offset += self::VALUE_LENGTH;

        $date = $this->extractAndFormatDate($line, $offset, self::DATE_LENGTH);

        return [
            'user_id' => $userId,
            'user_name' => $userName,
            'order_id' => $orderId,
            'product_id' => $productId,
            'value' => $value,
            'date' => $date,
        ];
    }

    private function extractAndCleanNumeric(string $line, int $offset, int $length): int
    {
        $raw = substr($line, $offset, $length);
        return (int) ltrim($raw, '0');
    }

    private function extractAndCleanText(string $line, int $offset, int $length): string
    {
        $raw = substr($line, $offset, $length);
        return trim($raw);
    }

    private function extractAndCleanDecimal(string $line, int $offset, int $length): string
    {
        $raw = substr($line, $offset, $length);
        return number_format((float) trim($raw), 2, '.', '');
    }

    private function extractAndFormatDate(string $line, int $offset, int $length): string
    {
        $raw = substr($line, $offset, $length);
        // Formato: yyyymmdd -> yyyy-mm-dd
        $year = substr($raw, 0, 4);
        $month = substr($raw, 4, 2);
        $day = substr($raw, 6, 2);
        return "{$year}-{$month}-{$day}";
    }
}