<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\SupplierManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Controller;

use Modules\Billing\Models\PurchaseBillMapper;
use Modules\SupplierManagement\Models\SupplierMapper;
use phpOMS\Asset\AssetType;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\OrderType;
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
 * @link    https://karaka.app
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

        $supplier = SupplierMapper::getAll()
            ->with('profile')
            ->with('profile/account')
            ->with('profile/image')
            ->with('mainAddress')
            ->limit(25)
            ->execute();

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
        /** @var \phpOMS\Model\Html\Head $head */
        $head = $response->get('Content')->getData('head');
        $head->addAsset(AssetType::CSS, 'Resources/chartjs/Chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/Chartjs/chart.js');
        $head->addAsset(AssetType::JSLATE, 'Modules/SupplierManagement/Controller.js', ['type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/supplier-profile');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1003202001, $request, $response));

        /** @var Supplier $supplier */
        $supplier = SupplierMapper::get()
            ->with('profile')
            ->with('profile/account')
            ->with('contactElements')
            ->with('mainAddress')
            ->with('files')->limit(5, 'files')->sort('files/id', OrderType::DESC)
            ->with('notes')->limit(5, 'files')->sort('notes/id', OrderType::DESC)
            ->where('id', (int) $request->getData('id'))
            ->execute();

        $view->setData('supplier', $supplier);

        // stats
        if ($this->app->moduleManager->isActive('Billing')) {
            $ytd                  = PurchaseBillMapper::getPurchaseBySupplierId($supplier->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'));
            $mtd                  = PurchaseBillMapper::getPurchaseBySupplierId($supplier->getId(), new SmartDateTime('Y-m-01'), new SmartDateTime('now'));
            $lastOrder            = PurchaseBillMapper::getLastOrderDateBySupplierId($supplier->getId());
            $newestInvoices       = PurchaseBillMapper::getAll()->with('supplier')->where('supplier', $supplier->getId())->sort('id', OrderType::DESC)->limit(5)->execute();
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
