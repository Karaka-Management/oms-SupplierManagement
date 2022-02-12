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

use Modules\SupplierManagement\Models\NullSupplier;

/**
 * @internal
 */
final class NullSupplierTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\SupplierManagement\Models\NullSupplier
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\SupplierManagement\Models\Supplier', new NullSupplier());
    }

    /**
     * @covers Modules\SupplierManagement\Models\NullSupplier
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullSupplier(2);
        self::assertEquals(2, $null->getId());
    }
}