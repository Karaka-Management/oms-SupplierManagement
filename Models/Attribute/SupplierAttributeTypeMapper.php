<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\SupplierManagement\Models\Attribute
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Models\Attribute;

use Modules\Attribute\Models\AttributeType;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Supplier mapper class.
 *
 * @package Modules\SupplierManagement\Models\Attribute
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of AttributeType
 * @extends DataMapperFactory<T>
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
        'suppliermgmt_attr_type_id'         => ['name' => 'suppliermgmt_attr_type_id',       'type' => 'int',    'internal' => 'id'],
        'suppliermgmt_attr_type_name'       => ['name' => 'suppliermgmt_attr_type_name',     'type' => 'string', 'internal' => 'name', 'autocomplete' => true],
        'suppliermgmt_attr_type_datatype'   => ['name' => 'suppliermgmt_attr_type_datatype',   'type' => 'int',    'internal' => 'datatype'],
        'suppliermgmt_attr_type_fields'     => ['name' => 'suppliermgmt_attr_type_fields',   'type' => 'int',    'internal' => 'fields'],
        'suppliermgmt_attr_type_custom'     => ['name' => 'suppliermgmt_attr_type_custom',   'type' => 'bool',   'internal' => 'custom'],
        'suppliermgmt_attr_type_repeatable' => ['name' => 'suppliermgmt_attr_type_repeatable',   'type' => 'bool',   'internal' => 'repeatable'],
        'suppliermgmt_attr_type_internal'   => ['name' => 'suppliermgmt_attr_type_internal',   'type' => 'bool',   'internal' => 'isInternal'],
        'suppliermgmt_attr_type_pattern'    => ['name' => 'suppliermgmt_attr_type_pattern',  'type' => 'string', 'internal' => 'validationPattern'],
        'suppliermgmt_attr_type_required'   => ['name' => 'suppliermgmt_attr_type_required', 'type' => 'bool',   'internal' => 'isRequired'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'l11n' => [
            'mapper'   => SupplierAttributeTypeL11nMapper::class,
            'table'    => 'suppliermgmt_attr_type_l11n',
            'self'     => 'suppliermgmt_attr_type_l11n_type',
            'column'   => 'content',
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
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = AttributeType::class;

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
    public const PRIMARYFIELD = 'suppliermgmt_attr_type_id';
}
