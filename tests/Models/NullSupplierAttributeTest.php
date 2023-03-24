<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\tests\Models;

use Modules\SupplierManagement\Models\NullSupplierAttribute;

/**
 * @internal
 */
final class NullSupplierAttributeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\SupplierManagement\Models\NullSupplierAttribute
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\SupplierManagement\Models\SupplierAttribute', new NullSupplierAttribute());
    }

    /**
     * @covers Modules\SupplierManagement\Models\NullSupplierAttribute
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullSupplierAttribute(2);
        self::assertEquals(2, $null->getId());
    }
}
