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

namespace Modules\SupplierManagement\Controller;

use Modules\Auditor\Models\AuditMapper;
use Modules\Media\Models\MediaMapper;
use Modules\Media\Models\MediaTypeMapper;
use Modules\Organization\Models\Attribute\UnitAttributeMapper;
use Modules\SupplierManagement\Models\Attribute\SupplierAttributeTypeL11nMapper;
use Modules\SupplierManagement\Models\Attribute\SupplierAttributeTypeMapper;
use Modules\SupplierManagement\Models\Attribute\SupplierAttributeValueL11nMapper;
use Modules\SupplierManagement\Models\Attribute\SupplierAttributeValueMapper;
use Modules\SupplierManagement\Models\PermissionCategory;
use Modules\SupplierManagement\Models\SupplierMapper;
use phpOMS\Account\PermissionType;
use phpOMS\Asset\AssetType;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\DataStorage\Database\Query\OrderType;
use phpOMS\DataStorage\Database\Query\Where;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Utils\StringUtils;
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
        $view              = new \Modules\Attribute\Theme\Backend\Components\AttributeTypeListView($this->app->l11nManager, $request, $response);
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1003203001, $request, $response);

        $view->attributes = SupplierAttributeTypeMapper::getAll()
            ->with('l11n')
            ->where('l11n/language', $response->header->l11n->language)
            ->executeGetArray();

        $view->path = 'purchase/supplier';

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
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1003203001, $request, $response);

        /** @var \Modules\Attribute\Models\AttributeValue[] $attributes */
        $attributes = SupplierAttributeValueMapper::getAll()
            ->with('l11n')
            ->where('l11n/language', $response->header->l11n->language)
            ->executeGetArray();

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
        $view              = new \Modules\Attribute\Theme\Backend\Components\AttributeTypeView($this->app->l11nManager, $request, $response);
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1003203001, $request, $response);

        $view->attribute = SupplierAttributeTypeMapper::get()
            ->with('l11n')
            ->with('defaults')
            ->with('defaults/l11n')
            ->where('id', (int) $request->getData('id'))
            ->where('l11n/language', $response->header->l11n->language)
            ->where('defaults/l11n/language', [$response->header->l11n->language, null])
            ->execute();

        $view->l11ns = SupplierAttributeTypeL11nMapper::getAll()
            ->where('ref', $view->attribute->id)
            ->executeGetArray();

            $view->path = 'purchase/supplier';

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
    public function viewSupplierManagementAttributeValue(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view              = new \Modules\Attribute\Theme\Backend\Components\AttributeValueView($this->app->l11nManager, $request, $response);
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1003203001, $request, $response);

        $view->attribute = SupplierAttributeValueMapper::get()
            ->with('l11n')
            ->where('id', (int) $request->getData('id'))
            ->where('l11n/language', [$response->header->l11n->language, null])
            ->execute();

        $view->l11ns = SupplierAttributeValueL11nMapper::getAll()
            ->where('ref', $view->attribute->id)
            ->executeGetArray();

        // @todo Also find the ItemAttributeType

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
            ->executeGetArray();

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
        $view->setTemplate('/Modules/SupplierManagement/Theme/Backend/supplier-view');
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

        $pkType  = 'id';
        $pkValue = $request->getDataInt('id');
        if ($pkValue === null) {
            $pkType  = 'number';
            $pkValue = $request->getDataString('number');
        }

        $view->data['supplier'] = SupplierMapper::get()
            ->with('account')
            ->with('account/addresses')
            ->with('account/contacts')
            ->with('mainAddress')
            ->with('files')->limit(5, 'files')->sort('files/id', OrderType::DESC)
            ->with('notes')->limit(5, 'notes')->sort('notes/id', OrderType::DESC)
            ->with('attributes')
            ->with('attributes/type')
            ->with('attributes/type/l11n')
            ->with('attributes/value')
            ->with('attributes/value/l11n')
            ->where($pkType, $pkValue)
            ->where('attributes/type/l11n/language', $response->header->l11n->language)
            ->where('attributes/value/l11n/language', [$response->header->l11n->language, null])
            /*
            ->where('attributes/value/l11n', (new Where($this->app->dbPool->get()))
                ->where(SupplierAttributeValueL11nMapper::getColumnByMember('ref'), '=', null)
                ->orWhere(SupplierAttributeValueL11nMapper::getColumnByMember('language'), '=', $response->header->l11n->language))
            */
            ->execute();

        $view->data['attributeView']                               = new \Modules\Attribute\Theme\Backend\Components\AttributeView($this->app->l11nManager, $request, $response);
        $view->data['attributeView']->data['default_localization'] = $this->app->l11nServer;

        $view->data['attributeTypes'] = SupplierAttributeTypeMapper::getAll()
            ->with('l11n')
            ->where('l11n/language', $response->header->l11n->language)
            ->executeGetArray();

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
            ->where(SupplierMapper::HAS_MANY['files']['self'], '=', $view->data['supplier']->id)
            ->where(MediaTypeMapper::TABLE . '.' . MediaTypeMapper::getColumnByMember('name'), '=', 'supplier_profile_image');

        $view->data['supplierImage'] = MediaMapper::get()
            ->where('id', $results)
            ->limit(1)
            ->execute();

        $businessStart = UnitAttributeMapper::get()
            ->with('type')
            ->with('value')
            ->where('ref', $this->app->unitId)
            ->where('type/name', 'business_year_start')
            ->execute();

        $view->data['business_start'] = $businessStart->id === 0 ? 1 : $businessStart->value->getValue();

        $view->data['hasBilling']    = $this->app->moduleManager->isActive('Billing');
        $view->data['hasAccounting'] = $this->app->moduleManager->isActive('Accounting');

        $view->data['prices'] = $view->data['hasBilling']
        ? \Modules\Billing\Models\Price\PriceMapper::getAll()
            ->where('supplier', $view->data['supplier']->id)
            ->where('type', \Modules\Billing\Models\Price\PriceType::PURCHASE)
            ->executeGetArray()
        : [];

        /** @var \Modules\Attribute\Models\AttributeType[] $tmp */
        $tmp = SupplierAttributeTypeMapper::getAll()
        ->with('defaults')
        ->with('defaults/l11n')
        ->where('name', [
            'segment', 'section', 'sales_group', 'product_group', 'product_type',
            'sales_tax_code', 'purchase_tax_code',
        ], 'IN')
        ->where('defaults/l11n', (new Where($this->app->dbPool->get()))
            ->where(SupplierAttributeValueL11nMapper::getColumnByMember('ref') ?? '', '=', null)
            ->orWhere(SupplierAttributeValueL11nMapper::getColumnByMember('language') ?? '', '=', $response->header->l11n->language))
        ->executeGetArray();

        $defaultAttributeTypes = [];
        foreach ($tmp as $t) {
            $defaultAttributeTypes[$t->name] = $t;
        }

        $view->data['defaultAttributeTypes'] = $defaultAttributeTypes;

        /** @var \Modules\Attribute\Models\AttributeType[] $tmp */
        $tmp = SupplierAttributeTypeMapper::getAll()
        ->with('defaults')
        ->with('defaults/l11n')
        ->where('name', [
            'segment', 'section', 'supplier_group', 'supplier_type',
            'sales_tax_code',
        ], 'IN')
        ->where('defaults/l11n', (new Where($this->app->dbPool->get()))
            ->where(SupplierAttributeValueL11nMapper::getColumnByMember('ref') ?? '', '=', null)
            ->orWhere(SupplierAttributeValueL11nMapper::getColumnByMember('language') ?? '', '=', $response->header->l11n->language))
        ->executeGetArray();

        $supplierSegmentationTypes = [];
        foreach ($tmp as $t) {
            $supplierSegmentationTypes[$t->name] = $t;
        }

        $view->data['supplierSegmentationTypes'] = $supplierSegmentationTypes;

        $logs = [];
        if ($this->app->accountManager->get($request->header->account)->hasPermission(
            PermissionType::READ,
            $this->app->unitId,
            null,
            self::NAME,
            PermissionCategory::SUPPLIER_LOG,
        )
        ) {
            /** @var \Modules\Auditor\Models\Audit[] */
            $logs = AuditMapper::getAll()
            ->where('type', StringUtils::intHash(SupplierMapper::class))
            ->where('module', 'SupplierManagement')
            ->where('ref', (string) $view->data['supplier']->id)
            ->executeGetArray();
        }

        $view->data['logs'] = $logs;

        // @todo join audit with files, attributes, localization, prices, notes, ...

        $view->data['files'] = MediaMapper::getAll()
        ->with('types')
        ->join('id', SupplierMapper::class, 'files') // id = media id, files = supplier relations
            ->on('id', $view->data['supplier']->id, relation: 'files') // id = item id
        ->executeGetArray();

        $view->data['media-upload'] = new \Modules\Media\Theme\Backend\Components\Upload\BaseView($this->app->l11nManager, $request, $response);
        $view->data['note']         = new \Modules\Editor\Theme\Backend\Components\Note\BaseView($this->app->l11nManager, $request, $response);

        $view->data['address-component'] = new \Modules\Admin\Theme\Backend\Components\AddressEditor\AddressView($this->app->l11nManager, $request, $response);
        $view->data['contact-component'] = new \Modules\Admin\Theme\Backend\Components\ContactEditor\ContactView($this->app->l11nManager, $request, $response);

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
