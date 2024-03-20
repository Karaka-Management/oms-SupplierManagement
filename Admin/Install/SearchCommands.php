<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\SupplierManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\SupplierManagement\Controller\SearchController;
use Modules\SupplierManagement\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^(?!:).+.*?' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\SearchController:searchGeneral',
            'verb'       => RouteVerb::ANY,
            'permission' => [
                'module' => SearchController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
    ],
];
