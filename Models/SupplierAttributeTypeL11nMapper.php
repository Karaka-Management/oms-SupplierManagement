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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\Localization\BaseStringL11n;

/**
 * Supplier mapper class.
 *
 * @package Modules\SupplierManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class SupplierAttributeTypeL11nMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'suppliermgmt_attr_type_l11n_id'    => ['name' => 'suppliermgmt_attr_type_l11n_id',    'type' => 'int',    'internal' => 'id'],
        'suppliermgmt_attr_type_l11n_title' => ['name' => 'suppliermgmt_attr_type_l11n_title', 'type' => 'string', 'internal' => 'content', 'autocomplete' => true],
        'suppliermgmt_attr_type_l11n_type'  => ['name' => 'suppliermgmt_attr_type_l11n_type',  'type' => 'int',    'internal' => 'ref'],
        'suppliermgmt_attr_type_l11n_lang'  => ['name' => 'suppliermgmt_attr_type_l11n_lang',  'type' => 'string', 'internal' => 'language'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'suppliermgmt_attr_type_l11n';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'suppliermgmt_attr_type_l11n_id';

    /**
     * Model to use by the mapper.
     *
     * @var class-string
     * @since 1.0.0
     */
    public const MODEL = BaseStringL11n::class;
}
