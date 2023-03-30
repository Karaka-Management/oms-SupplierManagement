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

use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\AddressMapper;
use Modules\Editor\Models\EditorDocMapper;
use Modules\Media\Models\MediaMapper;
use Modules\Profile\Models\ContactElementMapper;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Supplier mapper class.
 *
 * @package Modules\SupplierManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class SupplierMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'suppliermgmt_supplier_id'         => ['name' => 'suppliermgmt_supplier_id',         'type' => 'int',               'internal' => 'id'],
        'suppliermgmt_supplier_no'         => ['name' => 'suppliermgmt_supplier_no',         'type' => 'string',            'internal' => 'number'],
        'suppliermgmt_supplier_no_reverse' => ['name' => 'suppliermgmt_supplier_no_reverse', 'type' => 'string',            'internal' => 'numberReverse'],
        'suppliermgmt_supplier_status'     => ['name' => 'suppliermgmt_supplier_status',     'type' => 'int',               'internal' => 'status'],
        'suppliermgmt_supplier_type'       => ['name' => 'suppliermgmt_supplier_type',       'type' => 'int',               'internal' => 'type'],
        'suppliermgmt_supplier_info'       => ['name' => 'suppliermgmt_supplier_info',       'type' => 'string',            'internal' => 'info'],
        'suppliermgmt_supplier_created_at' => ['name' => 'suppliermgmt_supplier_created_at', 'type' => 'DateTimeImmutable', 'internal' => 'createdAt', 'readonly' => true],
        'suppliermgmt_supplier_account'    => ['name' => 'suppliermgmt_supplier_account',    'type' => 'int',               'internal' => 'account'],
        'suppliermgmt_supplier_address'    => ['name' => 'suppliermgmt_supplier_address',    'type' => 'int',               'internal' => 'mainAddress'],
        'suppliermgmt_supplier_unit'       => ['name' => 'suppliermgmt_supplier_unit',    'type' => 'int',               'internal' => 'unit'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'suppliermgmt_supplier';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'suppliermgmt_supplier_id';

    /**
     * Created at column
     *
     * @var string
     * @since 1.0.0
     */
    public const CREATED_AT = 'suppliermgmt_supplier_created_at';

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:class-string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    public const OWNS_ONE = [
        'account' => [
            'mapper'   => AccountMapper::class,
            'external' => 'suppliermgmt_supplier_account',
        ],
        'mainAddress' => [
            'mapper'   => AddressMapper::class,
            'external' => 'suppliermgmt_supplier_address',
        ],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'files'           => [
            'mapper'   => MediaMapper::class, /* mapper of the related object */
            'table'    => 'suppliermgmt_supplier_media', /* table of the related object, null if no relation table is used (many->1) */
            'external' => 'suppliermgmt_supplier_media_dst',
            'self'     => 'suppliermgmt_supplier_media_src',
        ],
        'notes'           => [
            'mapper'   => EditorDocMapper::class, /* mapper of the related object */
            'table'    => 'suppliermgmt_supplier_note', /* table of the related object, null if no relation table is used (many->1) */
            'external' => 'suppliermgmt_supplier_note_dst',
            'self'     => 'suppliermgmt_supplier_note_src',
        ],
        'contactElements' => [
            'mapper'   => ContactElementMapper::class,
            'table'    => 'suppliermgmt_supplier_contactelement',
            'external' => 'suppliermgmt_supplier_contactelement_dst',
            'self'     => 'suppliermgmt_supplier_contactelement_src',
        ],
        'attributes' => [
            'mapper'      => SupplierAttributeMapper::class,
            'table'       => 'suppliermgmt_supplier_attr',
            'self'        => 'suppliermgmt_supplier_attr_supplier',
            'external'    => null,
        ],
    ];
}
