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

use Modules\Admin\Models\Account;
use Modules\Admin\Models\NullAccount;
use Modules\Media\Models\Collection;
use Modules\Media\Models\CollectionMapper;
use Modules\Media\Models\PathSettings;
use Modules\SupplierManagement\Models\Attribute\SupplierAttributeTypeMapper;
use Modules\SupplierManagement\Models\SettingsEnum;
use Modules\SupplierManagement\Models\Supplier;
use Modules\SupplierManagement\Models\SupplierL11nMapper;
use Modules\SupplierManagement\Models\SupplierL11nTypeMapper;
use Modules\SupplierManagement\Models\SupplierMapper;
use phpOMS\Localization\BaseStringL11n;
use phpOMS\Localization\BaseStringL11nType;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Localization\NullBaseStringL11nType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;

/**
 * SupplierManagement class.
 *
 * @package Modules\SupplierManagement
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ApiController extends Controller
{
    /**
     * Api method to create Supplier
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSupplierCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateSupplierCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $supplier = $this->createSupplierFromRequest($request);
        $this->createModel($request->header->account, $supplier, SupplierMapper::class, 'supplier', $request->getOrigin());

        // Create media dir
        // @todo should this collection get added to the parent collection?
        $this->createMediaDirForSupplier($supplier->id, $request->header->account);

        // Create stock
        if ($this->app->moduleManager->isActive('WarehouseManagement')) {
            $internalResponse = new HttpResponse();
            $internalRequest  = new HttpRequest($request->uri);

            $internalRequest->header->account = $request->header->account;
            $internalRequest->setData('name', $supplier->number);
            $internalRequest->setData('supplier', $supplier->id);

            $this->app->moduleManager->get('WarehouseManagement', 'Api')
                ->apiStockCreate($internalRequest, $internalResponse);
        }

        $this->createSupplierSegmentation($request, $response, $supplier);

        $this->createStandardCreateResponse($request, $response, $supplier);
    }

    /**
     * Create directory for a supplier
     *
     * @param int $id        Item number
     * @param int $createdBy Creator of the directory
     *
     * @return Collection
     *
     * @since 1.0.0
     */
    private function createMediaDirForSupplier(int $id, int $createdBy) : Collection
    {
        $collection       = new Collection();
        $collection->name = (string) $id;
        $collection->setVirtualPath('/Modules/SupplierManagement/Suppliers');
        $collection->setPath('/Modules/Media/Files/Modules/SupplierManagement/Suppliers/' . $id);
        $collection->createdBy = new NullAccount($createdBy);

        CollectionMapper::create()->execute($collection);

        return $collection;
    }

    /**
     * Method to create Supplier from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Supplier
     *
     * @since 1.0.0
     */
    private function createSupplierFromRequest(RequestAbstract $request) : Supplier
    {
        $account = null;
        if (!$request->hasData('account')) {
            $account        = new Account();
            $account->name1 = $request->getDataString('name1') ?? '';
            $account->name2 = $request->getDataString('name2') ?? '';
        } else {
            $account = new NullAccount((int) $request->getData('account'));
        }

        $supplier          = new Supplier();
        $supplier->number  = $request->getDataString('number') ?? '';
        $supplier->account = $account;
        $supplier->unit    = $request->getDataInt('unit') ?? $this->app->unitId;

        $request->setData('name', null, true);
        $supplier->mainAddress = $this->app->moduleManager->get('Admin', 'Api')->createAddressFromRequest($request);

        return $supplier;
    }

    /**
     * Create supplier segmentation.
     *
     * Default: segment->section->sales_group and to the side supplier_type
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param Supplier         $supplier Supplier
     *
     * @return void
     *
     * @since 1.0.0
     */
    private function createSupplierSegmentation(RequestAbstract $request, ResponseAbstract $response, Supplier $supplier) : void
    {
        /** @var \Model\Setting $settings */
        $settings = $this->app->appSettings->get(null, SettingsEnum::DEFAULT_SEGMENTATION);

        /** @var array $segmentation */
        $segmentation = \json_decode($settings->content, true);
        if ($segmentation === false || $segmentation === null) {
            return;
        }

        /** @var \Modules\Attribute\Models\AttributeType[] $types */
        $types = SupplierAttributeTypeMapper::getAll()
            ->where('name', \array_keys($segmentation), 'IN')
            ->executeGetArray();

        foreach ($types as $type) {
            $internalResponse = clone $response;
            $internalRequest  = new HttpRequest();

            $internalRequest->header->account = $request->header->account;
            $internalRequest->setData('ref', $supplier->id);
            $internalRequest->setData('type', $type->id);
            $internalRequest->setData('value_id', $segmentation[$type->name]);

            $this->app->moduleManager->get('SupplierManagement', 'ApiAttribute')->apiSupplierAttributeCreate($internalRequest, $internalResponse);
        }
    }

    /**
     * Validate Supplier create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateSupplierCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['number'] = !$request->hasData('number'))
            || ($val['name1'] = !$request->hasData('name1'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create supplier l11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSupplierL11nCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateSupplierL11nCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $supplierL11n = $this->createSupplierL11nFromRequest($request);
        $this->createModel($request->header->account, $supplierL11n, SupplierL11nMapper::class, 'supplier_l11n', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $supplierL11n);
    }

    /**
     * Method to create supplier l11n from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    private function createSupplierL11nFromRequest(RequestAbstract $request) : BaseStringL11n
    {
        $supplierL11n           = new BaseStringL11n();
        $supplierL11n->ref      = $request->getDataInt('supplier') ?? 0;
        $supplierL11n->type     = new NullBaseStringL11nType($request->getDataInt('type') ?? 0);
        $supplierL11n->language = ISO639x1Enum::tryFromValue($request->getDataString('language')) ?? $request->header->l11n->language;
        $supplierL11n->content  = $request->getDataString('description') ?? '';

        return $supplierL11n;
    }

    /**
     * Validate supplier l11n create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateSupplierL11nCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['supplier'] = !$request->hasData('supplier'))
            || ($val['type'] = !$request->hasData('type'))
            || ($val['description'] = !$request->hasData('description'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create supplier l11n type
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSupplierL11nTypeCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateSupplierL11nTypeCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $supplierL11nType = $this->createSupplierL11nTypeFromRequest($request);
        $this->createModel($request->header->account, $supplierL11nType, SupplierL11nTypeMapper::class, 'supplier_l11n_type', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $supplierL11nType);
    }

    /**
     * Method to create supplier l11n type from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11nType
     *
     * @since 1.0.0
     */
    private function createSupplierL11nTypeFromRequest(RequestAbstract $request) : BaseStringL11nType
    {
        $supplierL11nType             = new BaseStringL11nType();
        $supplierL11nType->title      = $request->getDataString('title') ?? '';
        $supplierL11nType->isRequired = $request->getDataBool('is_required') ?? false;

        return $supplierL11nType;
    }

    /**
     * Validate supplier l11n type create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateSupplierL11nTypeCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create supplier files
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiFileCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (empty($request->files)) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidAddResponse($request, $response, $request->files);

            return;
        }

        $uploaded = $this->app->moduleManager->get('Media', 'Api')->uploadFiles(
            names: $request->getDataList('names'),
            fileNames: $request->getDataList('filenames'),
            files: $request->files,
            account: $request->header->account,
            basePath: __DIR__ . '/../../../Modules/Media/Files/Modules/SupplierManagement/Suppliers/' . ($request->getData('supplier') ?? '0'),
            virtualPath: '/Modules/SupplierManagement/Suppliers/' . ($request->getData('supplier') ?? '0'),
            pathSettings: PathSettings::FILE_PATH,
            type: $request->getDataInt('type'),
            rel: (int) $request->getData('supplier'),
            mapper: SupplierMapper::class,
            field: 'files'
        );

        if (empty($uploaded->sources)) {
            $this->createInvalidAddResponse($request, $response, []);

            return;
        }

        $this->createStandardAddResponse($request, $response, $uploaded->sources);
    }

    /**
     * Api method to create Note
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiNoteCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        $request->setData('virtualpath', '/Modules/SupplierManagement/Suppliers/' . $request->getData('id'), true);
        $this->app->moduleManager->get('Editor')->apiEditorCreate($request, $response, $data);

        $data = $response->getDataArray($request->uri->__toString());
        if (!\is_array($data)) {
            return;
        }

        $model = $data['response'];
        $this->createModelRelation(
            $request->header->account,
            $request->getData('id'),
            $model->id,
            SupplierMapper::class,
            'notes',
            '',
            $request->getOrigin()
        );
    }
}
