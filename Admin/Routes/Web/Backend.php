<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\SupplierManagement\Controller\BackendController;
use Modules\SupplierManagement\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/purchase/supplier/attribute/type/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\BackendController:viewSupplierManagementAttributeTypeList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
    ],
    '^.*/purchase/supplier/attribute/type(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\BackendController:viewSupplierManagementAttributeType',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
    ],
    '^.*/purchase/supplier/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\BackendController:viewSupplierManagementSupplierList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
    ],
    '^.*/purchase/supplier/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\BackendController:viewSupplierManagementSupplierCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
    ],
    '^.*/purchase/supplier/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\BackendController:viewSupplierManagementSupplierView',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
    ],
    '^.*/purchase/analysis/supplier(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\BackendController:viewSupplierManagementSupplierAnalysis',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::ANALYSIS,
            ],
        ],
    ],
];
