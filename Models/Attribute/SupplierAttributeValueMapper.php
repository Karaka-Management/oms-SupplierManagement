<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\SupplierManagement\Models\Attribute
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Models\Attribute;

use Modules\Attribute\Models\AttributeValue;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Supplier mapper class.
 *
 * @package Modules\SupplierManagement\Models\Attribute
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of AttributeValue
 * @extends DataMapperFactory<T>
 */
final class SupplierAttributeValueMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'suppliermgmt_attr_value_id'                => ['name' => 'suppliermgmt_attr_value_id',       'type' => 'int',      'internal' => 'id'],
        'suppliermgmt_attr_value_default'           => ['name' => 'suppliermgmt_attr_value_default',  'type' => 'bool',     'internal' => 'isDefault'],
        'suppliermgmt_attr_value_valueStr'          => ['name' => 'suppliermgmt_attr_value_valueStr', 'type' => 'string',   'internal' => 'valueStr'],
        'suppliermgmt_attr_value_valueInt'          => ['name' => 'suppliermgmt_attr_value_valueInt', 'type' => 'int',      'internal' => 'valueInt'],
        'suppliermgmt_attr_value_valueDec'          => ['name' => 'suppliermgmt_attr_value_valueDec', 'type' => 'float',    'internal' => 'valueDec'],
        'suppliermgmt_attr_value_valueDat'          => ['name' => 'suppliermgmt_attr_value_valueDat', 'type' => 'DateTime', 'internal' => 'valueDat'],
        'suppliermgmt_attr_value_unit'              => ['name' => 'suppliermgmt_attr_value_unit', 'type' => 'string', 'internal' => 'unit'],
        'suppliermgmt_attr_value_deptype'           => ['name' => 'suppliermgmt_attr_value_deptype', 'type' => 'int', 'internal' => 'dependingAttributeType'],
        'suppliermgmt_attr_value_depvalue'          => ['name' => 'suppliermgmt_attr_value_depvalue', 'type' => 'int', 'internal' => 'dependingAttributeValue'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'l11n' => [
            'mapper'   => SupplierAttributeValueL11nMapper::class,
            'table'    => 'suppliermgmt_attr_value_l11n',
            'self'     => 'suppliermgmt_attr_value_l11n_value',
            'external' => null,
        ],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = AttributeValue::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'suppliermgmt_attr_value';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'suppliermgmt_attr_value_id';
}
