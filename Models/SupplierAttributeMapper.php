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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Supplier mapper class.
 *
 * @package Modules\SupplierManagement\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class SupplierAttributeMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'suppliermgmt_supplier_attr_id'       => ['name' => 'suppliermgmt_supplier_attr_id',       'type' => 'int', 'internal' => 'id'],
        'suppliermgmt_supplier_attr_supplier' => ['name' => 'suppliermgmt_supplier_attr_supplier', 'type' => 'int', 'internal' => 'supplier'],
        'suppliermgmt_supplier_attr_type'     => ['name' => 'suppliermgmt_supplier_attr_type',     'type' => 'int', 'internal' => 'type'],
        'suppliermgmt_supplier_attr_value'    => ['name' => 'suppliermgmt_supplier_attr_value',    'type' => 'int', 'internal' => 'value'],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    public const OWNS_ONE = [
        'type' => [
            'mapper'   => SupplierAttributeTypeMapper::class,
            'external' => 'suppliermgmt_supplier_attr_type',
        ],
        'value' => [
            'mapper'   => SupplierAttributeValueMapper::class,
            'external' => 'suppliermgmt_supplier_attr_value',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'suppliermgmt_supplier_attr';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='suppliermgmt_supplier_attr_id';
}
