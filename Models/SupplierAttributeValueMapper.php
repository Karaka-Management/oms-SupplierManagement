<?php
/**
 * Karaka
 *
 * PHP Version 8.0
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
final class SupplierAttributeValueMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'suppliermgmt_attr_value_id'       => ['name' => 'suppliermgmt_attr_value_id',       'type' => 'int',      'internal' => 'id'],
        'suppliermgmt_attr_value_default'  => ['name' => 'suppliermgmt_attr_value_default',  'type' => 'bool',     'internal' => 'isDefault'],
        'suppliermgmt_attr_value_type'     => ['name' => 'suppliermgmt_attr_value_type',     'type' => 'int',      'internal' => 'type'],
        'suppliermgmt_attr_value_valueStr' => ['name' => 'suppliermgmt_attr_value_valueStr', 'type' => 'string',   'internal' => 'valueStr'],
        'suppliermgmt_attr_value_valueInt' => ['name' => 'suppliermgmt_attr_value_valueInt', 'type' => 'int',      'internal' => 'valueInt'],
        'suppliermgmt_attr_value_valueDec' => ['name' => 'suppliermgmt_attr_value_valueDec', 'type' => 'float',    'internal' => 'valueDec'],
        'suppliermgmt_attr_value_valueDat' => ['name' => 'suppliermgmt_attr_value_valueDat', 'type' => 'DateTime', 'internal' => 'valueDat'],
        'suppliermgmt_attr_value_lang'     => ['name' => 'suppliermgmt_attr_value_lang',     'type' => 'string',   'internal' => 'language'],
        'suppliermgmt_attr_value_country'  => ['name' => 'suppliermgmt_attr_value_country',  'type' => 'string',   'internal' => 'country'],
    ];

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
    public const PRIMARYFIELD ='suppliermgmt_attr_value_id';
}
