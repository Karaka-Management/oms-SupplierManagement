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

use phpOMS\Stdlib\Base\Enum;

/**
 * Supplier status enum.
 *
 * @package Modules\SupplierManagement\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
abstract class SupplierStatus extends Enum
{
    public const ACTIVE = 1;

    public const INACTIVE = 2;

    public const BANNED = 4;
}
