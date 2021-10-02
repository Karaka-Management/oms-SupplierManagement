<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\SupplierManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Controller;

use Modules\Billing\Models\BillTypeL11n;
use Modules\Billing\Models\PurchaseBillMapper;
use Modules\Media\Models\Media;
use Modules\SupplierManagement\Models\SupplierMapper;
use phpOMS\Asset\AssetType;
use phpOMS\Contract\RenderableInterface;
use phpOMS\Localization\Money;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Stdlib\Base\SmartDateTime;
use phpOMS\Views\View;

/**
 * SupplierManagement controller class.
 *
 * @package Modules\SupplierManagement
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 * @codeCoverageIgnore
 */
final class BackendController extends Controller
{
    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewSupplierManagementSupplierList(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/supplier-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1003202001, $request, $response));

        $supplier = SupplierMapper::with('notes', models: null)
            ::with('contactElements', models: null)
            //::with('type', 'backend_image', models: [Media::class]) // @todo: it would be nicer if I coult say files:type or files/type and remove the models parameter?
            ::getAfterPivot(0, null, 25);

        $view->addData('supplier', $supplier);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewSupplierManagementSupplierCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/supplier-create');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1003202001, $request, $response));

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewSupplierManagementSupplierProfile(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $head = $response->get('Content')->getData('head');
        $head->addAsset(AssetType::CSS, 'Resources/chartjs/Chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/Chartjs/chart.js');
        $head->addAsset(AssetType::JSLATE, 'Modules/SupplierManagement/Controller.js', ['type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/supplier-profile');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1003202001, $request, $response));

        $supplier = SupplierMapper
            ::with('files', limit: 5)::orderBy('createdAt', 'ASC')
            ::with('notes', limit: 5)::orderBy('id', 'ASC')
            ::get((int) $request->getData('id'));

        $view->setData('supplier', $supplier);

        // stats
        if ($this->app->moduleManager->isActive('Billing')) {
            $ytd                  = PurchaseBillMapper::getPurchaseBySupplierId($supplier->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'));
            $mtd                  = PurchaseBillMapper::getPurchaseBySupplierId($supplier->getId(), new SmartDateTime('Y-m-01'), new SmartDateTime('now'));
            $lastOrder            = PurchaseBillMapper::getLastOrderDateBySupplierId($supplier->getId());
            $newestInvoices       = PurchaseBillMapper::with('language', $response->getLanguage(), [BillTypeL11n::class])::getNewestSupplierInvoices($supplier->getId(), 5);
            $monthlyPurchaseCosts = PurchaseBillMapper::getSupplierMonthlyPurchaseCosts($supplier->getId(), (new SmartDateTime('now'))->createModify(-1), new SmartDateTime('now'));
        } else {
            $ytd                  = new Money();
            $mtd                  = new Money();
            $lastOrder            = null;
            $newestInvoices       = [];
            $monthlyPurchaseCosts = [];
        }

        $view->addData('ytd', $ytd);
        $view->addData('mtd', $mtd);
        $view->addData('lastOrder', $lastOrder);
        $view->addData('newestInvoices', $newestInvoices);
        $view->addData('monthlyPurchaseCosts', $monthlyPurchaseCosts);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewSupplierManagementSupplierAnalysis(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        return $view;
    }
}
