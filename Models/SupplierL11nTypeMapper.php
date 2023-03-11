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
final class SupplierL11nTypeMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'suppliermgmt_supplier_l11n_type_id'       => ['name' => 'suppliermgmt_supplier_l11n_type_id',    'type' => 'int',    'internal' => 'id'],
        'suppliermgmt_supplier_l11n_type_title'    => ['name' => 'suppliermgmt_supplier_l11n_type_title', 'type' => 'string', 'internal' => 'title'],
        'suppliermgmt_supplier_l11n_type_required' => ['name' => 'suppliermgmt_supplier_l11n_type_required', 'type' => 'bool', 'internal' => 'isRequired'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'suppliermgmt_supplier_l11n_type';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'suppliermgmt_supplier_l11n_type_id';
}
