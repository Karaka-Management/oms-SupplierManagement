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

use Modules\SupplierManagement\Controller\ApiController;
use Modules\SupplierManagement\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/supplier/find(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiController:apiSupplierFind',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
    ],
    '^.*/supplier(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiController:apiSupplierCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiController:apiSupplierUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
    ],
    '^.*/supplier/attribute(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiAttributeController:apiSupplierAttributeCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiAttributeController:apiSupplierAttributeUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
    ],
    '^.*/supplier/attribute/type(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiAttributeController:apiSupplierAttributeTypeCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::ATTRIBUTE,
            ],
        ],
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiAttributeController:apiSupplierAttributeTypeUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::ATTRIBUTE,
            ],
        ],
    ],
    '^.*/supplier/attribute/type/l11n(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiAttributeController:apiSupplierAttributeTypeL11nCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::ATTRIBUTE,
            ],
        ],
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiAttributeController:apiSupplierAttributeTypeL11nUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::ATTRIBUTE,
            ],
        ],
    ],
    '^.*/supplier/attribute/value(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiAttributeController:apiSupplierAttributeValueCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiAttributeController:apiSupplierAttributeValueUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
    ],
    '^.*/supplier/attribute/value/l11n(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiAttributeController:apiSupplierAttributeValueL11nCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiAttributeController:apiSupplierAttributeValueL11nUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
    ],
    '^.*/supplier/l11n(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiController:apiSupplierL11nCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiController:apiSupplierL11nUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
    ],
    '^.*/supplier/l11n/type(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiController:apiSupplierL11nTypeCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
        [
            'dest'       => '\Modules\SupplierManagement\Controller\ApiController:apiSupplierL11nTypeUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::SUPPLIER,
            ],
        ],
    ],
];
