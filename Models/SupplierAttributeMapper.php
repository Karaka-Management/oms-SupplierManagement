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

use phpOMS\DataStorage\Database\DataMapperAbstract;

/**
 * Supplier mapper class.
 *
 * @package Modules\SupplierManagement\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class SupplierAttributeMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    protected static array $columns = [
        'suppliermgmt_supplier_attr_id'    => ['name' => 'suppliermgmt_supplier_attr_id',    'type' => 'int', 'internal' => 'id'],
        'suppliermgmt_supplier_attr_supplier'  => ['name' => 'suppliermgmt_supplier_attr_supplier',  'type' => 'int', 'internal' => 'supplier'],
        'suppliermgmt_supplier_attr_type'  => ['name' => 'suppliermgmt_supplier_attr_type',  'type' => 'int', 'internal' => 'type'],
        'suppliermgmt_supplier_attr_value' => ['name' => 'suppliermgmt_supplier_attr_value', 'type' => 'int', 'internal' => 'value'],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    protected static array $ownsOne = [
        'type' => [
            'mapper'            => SupplierAttributeTypeMapper::class,
            'external'          => 'suppliermgmt_supplier_attr_type',
        ],
        'value' => [
            'mapper'            => SupplierAttributeValueMapper::class,
            'external'          => 'suppliermgmt_supplier_attr_value',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $table = 'suppliermgmt_supplier_attr';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $primaryField = 'suppliermgmt_supplier_attr_id';
}
