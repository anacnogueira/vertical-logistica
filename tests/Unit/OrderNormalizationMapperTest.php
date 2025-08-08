<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Mappers\OrderNormalizationMapper;

class OrderNormalizationMapperTest extends TestCase
{
    /** @var OrderNormalizationMapper */
    protected $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new OrderNormalizationMapper();
    }

    #[Test]
    public function test_it_normalizes_data_correctly_for_single_user_and_order(): void
    {
        $data1 = [
            'user_id' => 1,
            'user_name' => 'Zarelli',
            'order_id' => 123,
            'product_id' => 111,
            'value' => '512.24',
            'date' => '2021-12-01',
        ];

        $this->mapper->processLineData($data1);

        $expected = [
            [
                'user_id' => 1,
                'name' => 'Zarelli',
                'orders' => [
                    [
                        'order_id' => 123,
                        'total' => '512.24',
                        'date' => '2021-12-01',
                        'products' => [
                            [
                                'product_id' => 111,
                                'value' => '512.24',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->mapper->getNormalizedData());
    }

    #[Test]
    public function test_it_aggregates_products_within_the_same_order(): void
    {
        $data1 = [
            'user_id' => 1,
            'user_name' => 'Zarelli',
            'order_id' => 123,
            'product_id' => 111,
            'value' => '512.24',
            'date' => '2021-12-01',
        ];
        $data2 = [
            'user_id' => 1,
            'user_name' => 'Zarelli',
            'order_id' => 123,
            'product_id' => 122,
            'value' => '512.24',
            'date' => '2021-12-01',
        ];

        $this->mapper->processLineData($data1);
        $this->mapper->processLineData($data2);

        $expected = [
            [
                'user_id' => 1,
                'name' => 'Zarelli',
                'orders' => [
                    [
                        'order_id' => 123,
                        'total' => '1024.48', // Sum of 512.24 + 512.24
                        'date' => '2021-12-01',
                        'products' => [
                            [
                                'product_id' => 111,
                                'value' => '512.24',
                            ],
                            [
                                'product_id' => 122,
                                'value' => '512.24',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->mapper->getNormalizedData());
    }

    #[Test]
    public function test_it_aggregates_multiple_orders_for_the_same_user(): void
    {
        $data1 = [
            'user_id' => 1,
            'user_name' => 'Zarelli',
            'order_id' => 123,
            'product_id' => 111,
            'value' => '512.24',
            'date' => '2021-12-01',
        ];
        $data2 = [
            'user_id' => 1,
            'user_name' => 'Zarelli',
            'order_id' => 456,
            'product_id' => 222,
            'value' => '100.00',
            'date' => '2021-12-02',
        ];

        $this->mapper->processLineData($data1);
        $this->mapper->processLineData($data2);

        $expected = [
            [
                'user_id' => 1,
                'name' => 'Zarelli',
                'orders' => [
                    [
                        'order_id' => 123,
                        'total' => '512.24',
                        'date' => '2021-12-01',
                        'products' => [
                            [
                                'product_id' => 111,
                                'value' => '512.24',
                            ],
                        ],
                    ],
                    [
                        'order_id' => 456,
                        'total' => '100.00',
                        'date' => '2021-12-02',
                        'products' => [
                            [
                                'product_id' => 222,
                                'value' => '100.00',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->mapper->getNormalizedData());
    }

    #[Test]
    public function test_it_aggregates_data_for_multiple_users(): void
    {
        $data1 = [
            'user_id' => 1,
            'user_name' => 'Zarelli',
            'order_id' => 123,
            'product_id' => 111,
            'value' => '512.24',
            'date' => '2021-12-01',
        ];
        $data2 = [
            'user_id' => 2,
            'user_name' => 'Medeiros',
            'order_id' => 456,
            'product_id' => 222,
            'value' => '100.00',
            'date' => '2021-12-02',
        ];

        $this->mapper->processLineData($data1);
        $this->mapper->processLineData($data2);

        $expected = [
            [
                'user_id' => 1,
                'name' => 'Zarelli',
                'orders' => [
                    [
                        'order_id' => 123,
                        'total' => '512.24',
                        'date' => '2021-12-01',
                        'products' => [
                            [
                                'product_id' => 111,
                                'value' => '512.24',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'user_id' => 2,
                'name' => 'Medeiros',
                'orders' => [
                    [
                        'order_id' => 456,
                        'total' => '100.00',
                        'date' => '2021-12-02',
                        'products' => [
                            [
                                'product_id' => 222,
                                'value' => '100.00',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->mapper->getNormalizedData());
    }

    #[Test]
    public function test_it_handles_empty_parsed_data_without_error(): void
    {
        $this->mapper->processLineData([]);
        $this->assertEmpty($this->mapper->getNormalizedData());
    }
}
