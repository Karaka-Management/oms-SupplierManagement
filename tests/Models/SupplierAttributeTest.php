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
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\tests\Models;

use Modules\SupplierManagement\Models\SupplierAttribute;

/**
 * @internal
 */
final class SupplierAttributeTest extends \PHPUnit\Framework\TestCase
{
    private SupplierAttribute $attribute;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->attribute = new SupplierAttribute();
    }

    /**
     * @covers Modules\SupplierManagement\Models\SupplierAttribute
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->attribute->getId());
        self::assertInstanceOf('\Modules\SupplierManagement\Models\SupplierAttributeType', $this->attribute->type);
        self::assertInstanceOf('\Modules\SupplierManagement\Models\SupplierAttributeValue', $this->attribute->value);
    }

    /**
     * @covers Modules\SupplierManagement\Models\SupplierAttribute
     * @group module
     */
    public function testSerialize() : void
    {
        $serialized = $this->attribute->jsonSerialize();

        self::assertEquals(
            [
                'id',
                'supplier',
                'type',
                'value',
            ],
            \array_keys($serialized)
        );
    }
}
