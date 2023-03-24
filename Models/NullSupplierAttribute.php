<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\SupplierManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Models;

/**
 * Null model
 *
 * @package Modules\SupplierManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class NullSupplierAttribute extends SupplierAttribute
{
    /**
     * Constructor
     *
     * @param int $id Model id
     *
     * @since 1.0.0
     */
    public function __construct(int $id = 0)
    {
        parent::__construct();
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return ['id' => $this->id];
    }
}
