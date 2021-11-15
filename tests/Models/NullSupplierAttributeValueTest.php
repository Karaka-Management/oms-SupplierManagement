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

use Modules\SupplierManagement\Models\NullSupplierAttributeValue;

/**
 * @internal
 */
final class NullSupplierAttributeValueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\SupplierManagement\Models\NullSupplierAttributeValue
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\SupplierManagement\Models\SupplierAttributeValue', new NullSupplierAttributeValue());
    }

    /**
     * @covers Modules\SupplierManagement\Models\NullSupplierAttributeValue
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullSupplierAttributeValue(2);
        self::assertEquals(2, $null->getId());
    }
}
