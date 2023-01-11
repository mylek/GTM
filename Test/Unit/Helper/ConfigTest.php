<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Helper;

use MylSoft\GTM\Helper\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ConfigTest extends TestCase
{
    private Config $object;
    private MockObject $scope;

    protected function setUp(): void {
        $this->scope = $this->getMockForAbstractClass(
            ScopeConfigInterface::class,
            [],
            '',
            false,
            false,
            true,
            ['getValue']
        );

        $this->object = new Config(
            $this->scope
        );
    }

    /**
     * @param bool $expected
     * @param bool $return
     * @return void
     * @dataProvider isEnabledProvider
     */
    public function testIsEnabled(bool $expected, bool $return): void {
        $this->scope
            ->method('getValue')
            ->willReturn($return);
        $this->assertEquals($expected, $this->object->isEnabled());
    }

    /**
     * @return array
     */
    private function isEnabledProvider(): array
    {
        return [
            [true, true],
            [false, false],
        ];
    }

    /**
     * @param bool $expected
     * @param bool $return
     * @return void
     * @dataProvider getCodeProvider
     */
    public function testGetCode(string $expected, string $return): void {
        $this->scope
            ->method('getValue')
            ->willReturn($return);
        $this->assertEquals($expected, $this->object->getCode());
    }

    /**
     * @param string $expected
     * @param string $return
     * @return void
     * @dataProvider getAffiliationProvider
     */
    public function testGetAffiliation(string $expected, string $return): void {
        $this->scope
            ->method('getValue')
            ->willReturn($return);
        $this->assertEquals($expected, $this->object->getAffiliation());
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
     * @return \string[][]
     */
    private function getAffiliationProvider(): array {
        return [
            ['Store Test', 'Store Test'],
            ['', '']
        ];
    }
}