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

use Modules\Admin\Models\Account;
use Modules\Admin\Models\NullAccount;
use Modules\Media\Models\MediaMapper;
use Modules\Media\Models\PathSettings;
use Modules\SupplierManagement\Models\SettingsEnum;
use Modules\SupplierManagement\Models\Supplier;
use Modules\SupplierManagement\Models\Attribute\SupplierAttributeTypeMapper;
use Modules\SupplierManagement\Models\SupplierL11nMapper;
use Modules\SupplierManagement\Models\SupplierL11nTypeMapper;
use Modules\SupplierManagement\Models\SupplierMapper;
use phpOMS\Api\Geocoding\Nominatim;
use phpOMS\Localization\BaseStringL11n;
use phpOMS\Localization\BaseStringL11nType;
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Localization\NullBaseStringL11nType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Stdlib\Base\Address;
use phpOMS\Uri\HttpUri;

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
    use \Modules\Attribute\Controller\ApiAttributeTraitController;

    /**
     * Api method to create news article
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

        $this->createSupplierSegmentation($request, $response, $supplier);

        $this->createStandardCreateResponse($request, $response, $supplier);
    }

    /**
     * Method to create news article from request.
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
        $supplier->unit = $request->getDataInt('unit') ?? 1;

        // Handle main address
        $addr          = new Address();
        $addr->address = $request->getDataString('address') ?? '';
        $addr->postal  = $request->getDataString('postal') ?? '';
        $addr->city    = $request->getDataString('city') ?? '';
        $addr->state   = $request->getDataString('state') ?? '';
        $addr->setCountry($request->getDataString('country') ?? ISO3166TwoEnum::_XXX);
        $supplier->mainAddress = $addr;

        // Try to find lat/lon through external API
        $geocoding = Nominatim::geocoding($addr->country, $addr->city, $addr->address);
        if ($geocoding === ['lat' => 0.0, 'lon' => 0.0]) {
            $geocoding = Nominatim::geocoding($addr->country, $addr->city);
        }

        $supplier->mainAddress->lat = $geocoding['lat'];
        $supplier->mainAddress->lon = $geocoding['lon'];

        return $supplier;
    }

    private function createSupplierSegmentation(RequestAbstract $request, ResponseAbstract $response, Supplier $supplier) : void
    {
        /** @var \Model\Setting $settings */
        $settings = $this->app->appSettings->get(null, SettingsEnum::DEFAULT_SEGMENTATION);

        $segmentation = \json_decode($settings->content, true);
        if ($segmentation === false || $segmentation === null) {
            return;
        }

        $types = SupplierAttributeTypeMapper::get()
            ->where('name', \array_keys($segmentation), 'IN')
            ->execute();

        foreach ($types as $type) {
            $internalResponse = clone $response;
            $internalRequest  = new HttpRequest(new HttpUri(''));

            $internalRequest->header->account = $request->header->account;
            $internalRequest->setData('ref', $supplier->id);
            $internalRequest->setData('type', $type->id);
            $internalRequest->setData('value_id', $segmentation[$type->name]);

            $this->app->moduleManager->get('SupplierManagement', 'ApiAttribute')->apiSupplierAttributeCreate($internalRequest, $internalResponse);
        }
    }

    /**
     * Validate news create request
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
        $supplierL11n       = new BaseStringL11n();
        $supplierL11n->ref  = $request->getDataInt('supplier') ?? 0;
        $supplierL11n->type = new NullBaseStringL11nType($request->getDataInt('type') ?? 0);
        $supplierL11n->setLanguage(
            $request->getDataString('language') ?? $request->header->l11n->language
        );
        $supplierL11n->content = $request->getDataString('description') ?? '';

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
        $uploadedFiles = $request->files;

        if (empty($uploadedFiles)) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidAddResponse($request, $response, $uploadedFiles);

            return;
        }

        $uploaded = $this->app->moduleManager->get('Media')->uploadFiles(
            names: $request->getDataList('names'),
            fileNames: $request->getDataList('filenames'),
            files: $uploadedFiles,
            account: $request->header->account,
            basePath: __DIR__ . '/../../../Modules/Media/Files/Modules/SupplierManagement/' . ($request->getData('supplier') ?? '0'),
            virtualPath: '/Modules/SupplierManagement/' . ($request->getData('supplier') ?? '0'),
            pathSettings: PathSettings::FILE_PATH
        );

        if ($request->hasData('type')) {
            foreach ($uploaded as $file) {
                $this->createModelRelation(
                    $request->header->account,
                    $file->id,
                    $request->getDataInt('type'),
                    MediaMapper::class,
                    'types',
                    '',
                    $request->getOrigin()
                );
            }
        }

        if (empty($uploaded)) {
            $this->createInvalidAddResponse($request, $response, []);

            return;
        }

        $this->createModelRelation(
            $request->header->account,
            (int) $request->getData('supplier'),
            \reset($uploaded)->id,
            SupplierMapper::class, 'files', '', $request->getOrigin()
        );

        $this->createStandardAddResponse($request, $response, $uploaded);
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
    public function apiNoteCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        $request->setData('virtualpath', '/Modules/SupplierManagement/' . $request->getData('id'), true);
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
