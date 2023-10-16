<?php
/**
 * Jingga
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

use Modules\SupplierManagement\Models\NullSupplier;

/**
 * @internal
 */
final class NullSupplierTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\SupplierManagement\Models\NullSupplier
     * @group module
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\SupplierManagement\Models\Supplier', new NullSupplier());
    }

    /**
     * @covers Modules\SupplierManagement\Models\NullSupplier
     * @group module
     */
    public function testId() : void
    {
        $null = new NullSupplier(2);
        self::assertEquals(2, $null->id);
    }

    /**
     * @covers Modules\SupplierManagement\Models\NullSupplier
     * @group module
     */
    public function testJsonSerialize() : void
    {
        $null = new NullSupplier(2);
        self::assertEquals(['id' => 2], $null);
    }
}
