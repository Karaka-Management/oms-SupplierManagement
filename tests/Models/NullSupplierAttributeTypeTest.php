<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\tests\Models;

use Modules\SupplierManagement\Models\NullSupplierAttributeType;

/**
 * @internal
 */
final class NullSupplierAttributeTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\SupplierManagement\Models\NullSupplierAttributeType
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\SupplierManagement\Models\SupplierAttributeType', new NullSupplierAttributeType());
    }

    /**
     * @covers Modules\SupplierManagement\Models\NullSupplierAttributeType
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullSupplierAttributeType(2);
        self::assertEquals(2, $null->getId());
    }
}
