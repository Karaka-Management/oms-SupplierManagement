<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\SupplierManagement\Admin\Install
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Admin\Install;

use phpOMS\Application\ApplicationAbstract;

/**
 * Admin class.
 *
 * @package Modules\SupplierManagement\Admin\Install
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 */
class Admin
{
    /**
     * Install Admin providing
     *
     * @param ApplicationAbstract $app  Application
     * @param string              $path Module path
     *
     * @return void
     *
     * @since 1.0.0
     */
    public static function install(ApplicationAbstract $app, string $path) : void
    {
        // We are creating default items in the SupplierManagement installer.
        // This requires that these settings are already available
        // However, this install script runs AFTER the primary installer runs.
        // This causes problems for the item installation and is therefore moved to the "Installer".
        // \Modules\Admin\Admin\Installer::installExternal($app, ['path' => __DIR__ . '/Admin.install.php']);
    }
}
