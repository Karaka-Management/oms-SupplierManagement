<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\tests\Models;

use Modules\SupplierManagement\Models\SupplierAttributeValue;
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Localization\ISO639x1Enum;

/**
 * @internal
 */
final class SupplierAttributeValueTest extends \PHPUnit\Framework\TestCase
{
    private SupplierAttributeValue $value;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->value = new SupplierAttributeValue();
    }

    /**
     * @covers Modules\SupplierManagement\Models\SupplierAttributeValue
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->value->getId());
        self::assertNull($this->value->getValue());
    }

    /**
     * @covers Modules\SupplierManagement\Models\SupplierAttributeValue
     * @group module
     */
    public function testLanguageInputOutput() : void
    {
        $this->value->setLanguage(ISO639x1Enum::_DE);
        self::assertEquals(ISO639x1Enum::_DE, $this->value->getLanguage());
    }

    /**
     * @covers Modules\SupplierManagement\Models\SupplierAttributeValue
     * @group module
     */
    public function testCountryInputOutput() : void
    {
        $this->value->setCountry(ISO3166TwoEnum::_DEU);
        self::assertEquals(ISO3166TwoEnum::_DEU, $this->value->getCountry());
    }

    /**
     * @covers Modules\SupplierManagement\Models\SupplierAttributeValue
     * @group module
     */
    public function testValueIntInputOutput() : void
    {
        $this->value->setValue(1);
        self::assertEquals(1, $this->value->getValue());
    }

    /**
     * @covers Modules\SupplierManagement\Models\SupplierAttributeValue
     * @group module
     */
    public function testValueFloatInputOutput() : void
    {
        $this->value->setValue(1.1);
        self::assertEquals(1.1, $this->value->getValue());
    }

    /**
     * @covers Modules\SupplierManagement\Models\SupplierAttributeValue
     * @group module
     */
    public function testValueStringInputOutput() : void
    {
        $this->value->setValue('test');
        self::assertEquals('test', $this->value->getValue());
    }

    /**
     * @covers Modules\SupplierManagement\Models\SupplierAttributeValue
     * @group module
     */
    public function testValueDateInputOutput() : void
    {
        $this->value->setValue($dat = new \DateTime('now'));
        self::assertEquals($dat->format('Y-m-d'), $this->value->getValue()->format('Y-m-d'));
    }

    /**
     * @covers Modules\SupplierManagement\Models\SupplierAttributeValue
     * @group module
     */
    public function testSerialize() : void
    {
        $this->value->type = 1;
        $this->value->setValue('test');
        $this->value->isDefault = true;
        $this->value->setLanguage(ISO639x1Enum::_DE);
        $this->value->setCountry(ISO3166TwoEnum::_DEU);

        self::assertEquals(
            [
                'id'           => 0,
                'type'         => 1,
                'valueInt'     => null,
                'valueStr'     => 'test',
                'valueDec'     => null,
                'valueDat'     => null,
                'isDefault'    => true,
                'language'     => ISO639x1Enum::_DE,
                'country'      => ISO3166TwoEnum::_DEU,
            ],
            $this->value->jsonSerialize()
        );
    }
}
