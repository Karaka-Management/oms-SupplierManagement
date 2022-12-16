<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\SupplierManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Models;

/**
 * Supplier class.
 *
 * @package Modules\SupplierManagement\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class SupplierAttribute implements \JsonSerializable
{
    /**
     * Id.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * Supplier this attribute belongs to
     *
     * @var int
     * @since 1.0.0
     */
    public int $supplier = 0;

    /**
     * Attribute type the attribute belongs to
     *
     * @var SupplierAttributeType
     * @since 1.0.0
     */
    public SupplierAttributeType $type;

    /**
     * Attribute value the attribute belongs to
     *
     * @var SupplierAttributeValue
     * @since 1.0.0
     */
    public SupplierAttributeValue $value;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->type  = new SupplierAttributeType();
        $this->value = new SupplierAttributeValue();
    }

    /**
     * Get id
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'          => $this->id,
            'supplier'    => $this->supplier,
            'type'        => $this->type,
            'value'       => $this->value,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }
}
