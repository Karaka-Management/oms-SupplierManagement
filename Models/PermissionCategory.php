<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\SupplierManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * Permission category enum.
 *
 * @package Modules\SupplierManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class PermissionCategory extends Enum
{
    public const SUPPLIER = 1;

    public const ANALYSIS = 2;

    public const ATTRIBUTE = 4;

    public const SUPPLIER_LOG = 5;
}
