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

use Modules\SupplierManagement\Models\SupplierAttributeType;
use phpOMS\Localization\BaseStringL11n;

/**
 * @internal
 */
final class SupplierAttributeTypeTest extends \PHPUnit\Framework\TestCase
{
    private SupplierAttributeType $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->type = new SupplierAttributeType();
    }

    /**
     * @covers Modules\SupplierManagement\Models\SupplierAttributeType
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->type->getId());
        self::assertEquals('', $this->type->getL11n());
    }

    /**
     * @covers Modules\SupplierManagement\Models\SupplierAttributeType
     * @group module
     */
    public function testL11nInputOutput() : void
    {
        $this->type->setL11n('Test');
        self::assertEquals('Test', $this->type->getL11n());

        $this->type->setL11n(new BaseStringL11n('NewTest'));
        self::assertEquals('NewTest', $this->type->getL11n());
    }

    /**
     * @covers Modules\SupplierManagement\Models\SupplierAttributeType
     * @group module
     */
    public function testSerialize() : void
    {
        $this->type->name                = 'Title';
        $this->type->fields              = 2;
        $this->type->custom              = true;
        $this->type->validationPattern   = '\d*';
        $this->type->isRequired          = true;

        self::assertEquals(
            [
                'id'                => 0,
                'name'              => 'Title',
                'fields'            => 2,
                'custom'            => true,
                'validationPattern' => '\d*',
                'isRequired'        => true,
            ],
            $this->type->jsonSerialize()
        );
    }
}
