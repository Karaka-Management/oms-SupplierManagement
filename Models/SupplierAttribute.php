<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\SupplierManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Models;

use phpOMS\Contract\ArrayableInterface;

/**
 * Supplier class.
 *
 * @package Modules\SupplierManagement\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class SupplierAttribute implements \JsonSerializable, ArrayableInterface
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
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
