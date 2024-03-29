<?php
/**
 * Jingga
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
use Modules\Media\Models\MediaMapper;
use Modules\Media\Models\MediaTypeMapper;
use Modules\SupplierManagement\Models\Attribute\SupplierAttributeTypeL11nMapper;
use Modules\SupplierManagement\Models\Attribute\SupplierAttributeTypeMapper;
use Modules\SupplierManagement\Models\Attribute\SupplierAttributeValueMapper;
use Modules\SupplierManagement\Models\SupplierMapper;
use phpOMS\Asset\AssetType;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\Builder;
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
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewSupplierManagementAttributeTypeList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
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
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewSupplierManagementAttributeValues(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
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
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewSupplierManagementAttributeType(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
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
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewSupplierManagementSupplierList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/supplier-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1003202001, $request, $response);

        $supplier = SupplierMapper::getAll()
            ->with('account')
            ->with('mainAddress')
            ->where('unit', $this->app->unitId)
            ->limit(25)
            ->execute();

        $view->data['supplier'] = $supplier;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewSupplierManagementSupplierCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/supplier-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1003202001, $request, $response);

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewSupplierManagementSupplierView(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $head  = $response->data['Content']->head;
        $nonce = $this->app->appSettings->getOption('script-nonce');

        $head->addAsset(AssetType::CSS, 'Resources/chartjs/chart.css?v=' . $this->app->version);
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/chart.js?v=' . $this->app->version, ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Resources/OpenLayers/OpenLayers.js?v=' . $this->app->version, ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Modules/ClientManagement/Controller.js?v=' . self::VERSION, ['nonce' => $nonce, 'type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/supplier-view');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1003202001, $request, $response);

        /** @var \Modules\SupplierManagement\Models\Supplier $supplier */
        $supplier = SupplierMapper::get()
            ->with('account')
            ->with('contactElements')
            ->with('mainAddress')
            ->with('files')->limit(5, 'files')->sort('files/id', OrderType::DESC)
            ->with('notes')->limit(5, 'notes')->sort('notes/id', OrderType::DESC)
            ->where('id', (int) $request->getData('id'))
            ->execute();

        $view->data['supplier'] = $supplier;

        // Get item profile image
        // @feature Create a new read mapper function that returns relation models instead of its own model
        //      https://github.com/Karaka-Management/phpOMS/issues/320
        $query   = new Builder($this->app->dbPool->get());
        $results = $query->selectAs(SupplierMapper::HAS_MANY['files']['external'], 'file')
            ->from(SupplierMapper::TABLE)
            ->leftJoin(SupplierMapper::HAS_MANY['files']['table'])
                ->on(SupplierMapper::HAS_MANY['files']['table'] . '.' . SupplierMapper::HAS_MANY['files']['self'], '=', SupplierMapper::TABLE . '.' . SupplierMapper::PRIMARYFIELD)
            ->leftJoin(MediaMapper::TABLE)
                ->on(SupplierMapper::HAS_MANY['files']['table'] . '.' . SupplierMapper::HAS_MANY['files']['external'], '=', MediaMapper::TABLE . '.' . MediaMapper::PRIMARYFIELD)
             ->leftJoin(MediaMapper::HAS_MANY['types']['table'])
                ->on(MediaMapper::TABLE . '.' . MediaMapper::PRIMARYFIELD, '=', MediaMapper::HAS_MANY['types']['table'] . '.' . MediaMapper::HAS_MANY['types']['self'])
            ->leftJoin(MediaTypeMapper::TABLE)
                ->on(MediaMapper::HAS_MANY['types']['table'] . '.' . MediaMapper::HAS_MANY['types']['external'], '=', MediaTypeMapper::TABLE . '.' . MediaTypeMapper::PRIMARYFIELD)
            ->where(SupplierMapper::HAS_MANY['files']['self'], '=', $supplier->id)
            ->where(MediaTypeMapper::TABLE . '.' . MediaTypeMapper::getColumnByMember('name'), '=', 'supplier_profile_image');

        $clientImage = MediaMapper::get()
            ->where('id', $results)
            ->limit(1)
            ->execute();

        $view->data['clientImage'] = $clientImage;

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
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewSupplierManagementSupplierAnalysis(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        return new View($this->app->l11nManager, $request, $response);
    }
}
