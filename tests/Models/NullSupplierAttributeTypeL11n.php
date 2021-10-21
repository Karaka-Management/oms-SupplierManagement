<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\tests\Models;

use Modules\SupplierManagement\Models\NullSupplierAttributeTypeL11n;

/**
 * @internal
 */
final class Null extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\SupplierManagement\Models\NullSupplierAttributeTypeL11n
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\SupplierManagement\Models\SupplierAttributeTypeL11n', new NullSupplierAttributeTypeL11n());
    }

    /**
     * @covers Modules\SupplierManagement\Models\NullSupplierAttributeTypeL11n
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullSupplierAttributeTypeL11n(2);
        self::assertEquals(2, $null->getId());
    }
}
