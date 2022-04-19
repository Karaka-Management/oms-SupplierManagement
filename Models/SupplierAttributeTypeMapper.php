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
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Models;

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Supplier mapper class.
 *
 * @package Modules\SupplierManagement\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class SupplierAttributeTypeMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'suppliermgmt_attr_type_id'       => ['name' => 'suppliermgmt_attr_type_id',       'type' => 'int',    'internal' => 'id'],
        'suppliermgmt_attr_type_name'     => ['name' => 'suppliermgmt_attr_type_name',     'type' => 'string', 'internal' => 'name', 'autocomplete' => true],
        'suppliermgmt_attr_type_fields'   => ['name' => 'suppliermgmt_attr_type_fields',   'type' => 'int',    'internal' => 'fields'],
        'suppliermgmt_attr_type_custom'   => ['name' => 'suppliermgmt_attr_type_custom',   'type' => 'bool',   'internal' => 'custom'],
        'suppliermgmt_attr_type_pattern'  => ['name' => 'suppliermgmt_attr_type_pattern',  'type' => 'string', 'internal' => 'validationPattern'],
        'suppliermgmt_attr_type_required' => ['name' => 'suppliermgmt_attr_type_required', 'type' => 'bool',   'internal' => 'isRequired'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'l11n' => [
            'mapper'   => SupplierAttributeTypeL11nMapper::class,
            'table'    => 'suppliermgmt_attr_type_l11n',
            'self'     => 'suppliermgmt_attr_type_l11n_type',
            'column'   => 'title',
            'external' => null,
        ],
        'defaults' => [
            'mapper'   => SupplierAttributeValueMapper::class,
            'table'    => 'suppliermgmt_supplier_attr_default',
            'self'     => 'suppliermgmt_supplier_attr_default_type',
            'external' => 'suppliermgmt_supplier_attr_default_value',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'suppliermgmt_attr_type';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='suppliermgmt_attr_type_id';
}
