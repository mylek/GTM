<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Controller\Cart;

use Exception;
use Magento\Checkout\Model\Cart;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MylSoft\GTM\Controller\Cart\Add;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Model\Quote;
use Magento\Framework\Controller\Result\Json;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Quote\Model\Quote\Item as ItemQuote;

class AddTest extends TestCase
{
    private Add $object;

    private MockObject $jsonResultFactory;
    private MockObject $cart;
    private MockObject $resultJson;
    private MockObject $configuration;
    private MockObject $item;

    protected function setUp(): void
    {
        $this->jsonResultFactory = $this->getMockBuilder(JsonFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultJson = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->jsonResultFactory->method('create')->willReturn($this->resultJson);
        $this->configuration = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->item = $this->getMockBuilder(ItemQuote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cart = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new Add($this->jsonResultFactory, $this->cart, $this->configuration);
    }

    /**
     * @param array $expected
     * @param array $return
     * @return void
     * @throws Exception
     * @dataProvider getLastAddItemProvider
     */
    public function testGetLastAddItem(array $expected, array $return): void {
        $quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $items = [];
        foreach ($return['items'] as $itemData) {
            $item = $this->getMockBuilder(ItemQuote::class)
                ->disableOriginalConstructor()
                ->getMock();
            $item->method('getData')->willReturn($itemData);
            $items[] = $item;
        }

        $quote->method('getItems')->willReturn($items);
        $this->cart->method('getQuote')->willReturn($quote);
        var_dump($this->object->getLastAddItem()->getData());
        $this->assertEquals($expected, $this->object->getLastAddItem()->getData());
    }

    /**
     * @param string $expected
     * @param array $return
     * @return void
     * @dataProvider executeProvider
     */
    /*public function testExecute(string $expected, array $return): void {
        $quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $quote->method('getItems')->willReturn($return['items']);
        $this->cart->method('getQuote')->willReturn($quote);
        $this->resultJson->method('setJsonData')->willReturn('');
        $json = $this->object->execute();
        $this->assertJson($expected, $json->render());
    }*/

    /**
     * @return void
     * @throws Exception
     */
    public function testGetLastAddItemException(): void {
        $this->expectException(Exception::class);
        $quote = false;
        $this->cart->method('getQuote')->willReturn($quote);
        $this->object->getLastAddItem();
    }

    private function getLastAddItemProvider(): array {
        return [
            [
                [],
                [
                    'items' => []
                ]
            ],
            [
                [
                    'item_id' => 2
                ],
                [
                    'items' => [
                        [
                            'item_id' => 1
                        ],
                        [
                            'item_id' => 2
                        ]
                    ]
                ]
            ]
        ];
    }

    private function executeProvider(): array {
        return [
            [
                '{"gtm_product": []}',
                [
                    'items' => [
                        []
                    ]
                ]
            ]
        ];
    }
}
