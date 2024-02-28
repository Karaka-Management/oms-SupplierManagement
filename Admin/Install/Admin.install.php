<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\SupplierManagement\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\SupplierManagement\Controller\ApiController;
use Modules\SupplierManagement\Models\SettingsEnum;

return [
    [
        "description" => "Default item segmentation (segment, section, sales group, product group)",
        'type'    => 'setting',
        'name'    => SettingsEnum::DEFAULT_SEGMENTATION,
        'content' => '{"segment":1, "section":1, "supplier_group":1}',
        'pattern' => '',
        'module'  => ApiController::NAME,
    ],
];
