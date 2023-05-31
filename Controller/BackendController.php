<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\SupplierManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Controller;

use Modules\Billing\Models\PurchaseBillMapper;
use Modules\SupplierManagement\Models\SupplierAttributeTypeL11nMapper;
use Modules\SupplierManagement\Models\SupplierAttributeTypeMapper;
use Modules\SupplierManagement\Models\SupplierAttributeValueMapper;
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
 * @license OMS License 2.0
 * @link    https://jingga.app
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
    public function viewSupplierManagementAttributeTypeList(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/attribute-type-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004801001, $request, $response);

        /** @var \Modules\Attribute\Models\AttributeType[] $attributes */
        $attributes = SupplierAttributeTypeMapper::getAll()
            ->with('l11n')
            ->where('l11n/language', $response->header->l11n->language)
            ->execute();

        $view->data['attributes'] = $attributes;

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
    public function viewSupplierManagementAttributeValues(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/attribute-value-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004801001, $request, $response);

        /** @var \Modules\Attribute\Models\AttributeValue[] $attributes */
        $attributes = SupplierAttributeValueMapper::getAll()
            ->with('l11n')
            ->where('l11n/language', $response->header->l11n->language)
            ->execute();

        $view->data['attributes'] = $attributes;

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
    public function viewSupplierManagementAttributeType(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/attribute-type');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004801001, $request, $response);

        /** @var \Modules\Attribute\Models\AttributeType $attribute */
        $attribute = SupplierAttributeTypeMapper::get()
            ->with('l11n')
            ->where('id', (int) $request->getData('id'))
            ->where('l11n/language', $response->header->l11n->language)
            ->execute();

        $l11ns = SupplierAttributeTypeL11nMapper::getAll()
            ->where('ref', $attribute->id)
            ->execute();

        $view->data['attribute'] = $attribute;
        $view->data['l11ns']     = $l11ns;

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
    public function viewSupplierManagementSupplierList(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/supplier-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1003202001, $request, $response);

        $supplier = SupplierMapper::getAll()
            ->with('account')
            ->with('mainAddress')
            ->limit(25)
            ->execute();

        $view->data['supplier'] = $supplier;

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
    public function viewSupplierManagementSupplierCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/supplier-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1003202001, $request, $response);

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
    public function viewSupplierManagementSupplierProfile(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        /** @var \phpOMS\Model\Html\Head $head */
        $head = $response->get('Content')->head;
        $head->addAsset(AssetType::CSS, 'Resources/chartjs/Chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/Chartjs/chart.js');
        $head->addAsset(AssetType::JSLATE, 'Modules/SupplierManagement/Controller.js', ['type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/supplier-profile');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1003202001, $request, $response);

        /** @var \Modules\SupplierManagement\Models\Supplier $supplier */
        $supplier = SupplierMapper::get()
            ->with('account')
            ->with('contactElements')
            ->with('mainAddress')
            ->with('files')->limit(5, 'files')->sort('files/id', OrderType::DESC)
            ->with('notes')->limit(5, 'files')->sort('notes/id', OrderType::DESC)
            ->where('id', (int) $request->getData('id'))
            ->execute();

        $view->data['supplier'] = $supplier;

        // stats
        if ($this->app->moduleManager->isActive('Billing')) {
            $ytd                  = PurchaseBillMapper::getPurchaseBySupplierId($supplier->id, new SmartDateTime('Y-01-01'), new SmartDateTime('now'));
            $mtd                  = PurchaseBillMapper::getPurchaseBySupplierId($supplier->id, new SmartDateTime('Y-m-01'), new SmartDateTime('now'));
            $lastOrder            = PurchaseBillMapper::getLastOrderDateBySupplierId($supplier->id);
            $newestInvoices       = PurchaseBillMapper::getAll()->with('supplier')->where('supplier', $supplier->id)->sort('id', OrderType::DESC)->limit(5)->execute();
            $monthlyPurchaseCosts = PurchaseBillMapper::getSupplierMonthlyPurchaseCosts($supplier->id, (new SmartDateTime('now'))->createModify(-1), new SmartDateTime('now'));
        } else {
            $ytd                  = new Money();
            $mtd                  = new Money();
            $lastOrder            = null;
            $newestInvoices       = [];
            $monthlyPurchaseCosts = [];
        }

        $view->data['ytd']                  = $ytd;
        $view->data['mtd']                  = $mtd;
        $view->data['lastOrder']            = $lastOrder;
        $view->data['newestInvoices']       = $newestInvoices;
        $view->data['monthlyPurchaseCosts'] = $monthlyPurchaseCosts;

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
    public function viewSupplierManagementSupplierAnalysis(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        return $view;
    }
}
