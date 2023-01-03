<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\ViewModel;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MylSoft\GTM\ViewModel\InitGTM;
use MylSoft\GTM\Helper\Config;

class InitGTMTest extends TestCase
{
    private InitGTM $object;
    private MockObject $config;

    protected function setUp(): void {
        $this->config = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new InitGTM($this->config);
    }

    /**
     * @param string $expected
     * @param string $return
     * @return void
     * @dataProvider getCodeProvider
     */
    public function testGetCode(string $expected, string $return): void {
        $this->config
            ->method('getCode')
            ->willReturn($return);

        $this->assertEquals($expected, $this->object->getCode());
    }

    /**
     * @return \string[][]
     */
    private function getCodeProvider(): array {
        return [
            ['GTM-111111', 'GTM-111111'],
            ['GTM-999999', 'GTM-999999']
        ];
    }

    /**
     * @param string $expected
     * @param string $code
     * @param bool $enabled
     * @return void
     * @dataProvider isShowProvider
     */
    public function testIsShow(bool $expected, string $code, bool $enabled): void {
        $this->config
            ->method('getCode')
            ->willReturn($code);
        $this->config
            ->method('isEnabled')
            ->willReturn($enabled);

        $this->assertEquals($expected, $this->object->isShow());
    }

    /**
     * @return array[]
     */
    private function isShowProvider(): array {
        return [
            [true, 'GTM-111111', true],
            [false, '', true],
            [false, 'GTM-111111', false],
        ];
    }
}