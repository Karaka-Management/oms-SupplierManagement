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

use Modules\Admin\Models\Account;
use Modules\Admin\Models\Address;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeType;
use Modules\Attribute\Models\AttributeValue;
use Modules\Attribute\Models\NullAttributeType;
use Modules\Attribute\Models\NullAttributeValue;
use Modules\Media\Models\MediaMapper;
use Modules\Media\Models\PathSettings;
use Modules\SupplierManagement\Models\Supplier;
use Modules\SupplierManagement\Models\SupplierAttributeMapper;
use Modules\SupplierManagement\Models\SupplierAttributeTypeL11nMapper;
use Modules\SupplierManagement\Models\SupplierAttributeTypeMapper;
use Modules\SupplierManagement\Models\SupplierAttributeValueL11nMapper;
use Modules\SupplierManagement\Models\SupplierAttributeValueMapper;
use Modules\SupplierManagement\Models\SupplierL11nMapper;
use Modules\SupplierManagement\Models\SupplierL11nTypeMapper;
use Modules\SupplierManagement\Models\SupplierMapper;
use phpOMS\Localization\BaseStringL11n;
use phpOMS\Localization\BaseStringL11nType;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Localization\NullBaseStringL11nType;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;

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
     * Api method to create news article
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSupplierCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateSupplierCreate($request))) {
            $response->set('supplier_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $supplier = $this->createSupplierFromRequest($request);
        $this->createModel($request->header->account, $supplier, SupplierMapper::class, 'supplier', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Supplier', 'Supplier successfully created', $supplier);
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
        $account        = new Account();
        $account->name1 = $request->getDataString('name1') ?? '';
        $account->name2 = $request->getDataString('name2') ?? '';

        $supplier          = new Supplier();
        $supplier->number  = $request->getDataString('number') ?? '';
        $supplier->account = $account;

        $addr          = new Address();
        $addr->address = $request->getDataString('address') ?? '';
        $addr->postal  = $request->getDataString('postal') ?? '';
        $addr->city    = $request->getDataString('city') ?? '';
        $addr->state   = $request->getDataString('state') ?? '';
        $addr->setCountry($request->getDataString('country') ?? '');
        $supplier->mainAddress = $addr;

        $supplier->unit = $request->getDataInt('unit');

        return $supplier;
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSupplierL11nCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateSupplierL11nCreate($request))) {
            $response->set('supplier_l11n_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $supplierL11n = $this->createSupplierL11nFromRequest($request);
        $this->createModel($request->header->account, $supplierL11n, SupplierL11nMapper::class, 'supplier_l11n', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Localization', 'Localization successfully created', $supplierL11n);
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
            $request->getDataString('language') ?? $request->getLanguage()
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSupplierL11nTypeCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateSupplierL11nTypeCreate($request))) {
            $response->set('supplier_l11n_type_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $supplierL11nType = $this->createSupplierL11nTypeFromRequest($request);
        $this->createModel($request->header->account, $supplierL11nType, SupplierL11nTypeMapper::class, 'supplier_l11n_type', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Localization type', 'Localization type successfully created', $supplierL11nType);
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
        $supplierL11nType->isRequired = (bool) ($request->getData('is_required') ?? false);

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
     * Api method to create supplier attribute
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSupplierAttributeCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateSupplierAttributeCreate($request))) {
            $response->set('attribute_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $attribute = $this->createSupplierAttributeFromRequest($request);
        $this->createModel($request->header->account, $attribute, SupplierAttributeMapper::class, 'attribute', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute', 'Attribute successfully created', $attribute);
    }

    /**
     * Method to create supplier attribute from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Attribute
     *
     * @since 1.0.0
     */
    private function createSupplierAttributeFromRequest(RequestAbstract $request) : Attribute
    {
        $attribute        = new Attribute();
        $attribute->ref   = (int) $request->getData('supplier');
        $attribute->type  = new NullAttributeType((int) $request->getData('type'));
        $attribute->value = new NullAttributeValue((int) $request->getData('value'));

        return $attribute;
    }

    /**
     * Validate supplier attribute create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateSupplierAttributeCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['type'] = !$request->hasData('type'))
            || ($val['value'] = !$request->hasData('value'))
            || ($val['supplier'] = !$request->hasData('supplier'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create supplier attribute l11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSupplierAttributeTypeL11nCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateSupplierAttributeTypeL11nCreate($request))) {
            $response->set('attr_type_l11n_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $attrL11n = $this->createSupplierAttributeTypeL11nFromRequest($request);
        $this->createModel($request->header->account, $attrL11n, SupplierAttributeTypeL11nMapper::class, 'attr_type_l11n', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Localization', 'Localization successfully created', $attrL11n);
    }

    /**
     * Method to create supplier attribute l11n from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    private function createSupplierAttributeTypeL11nFromRequest(RequestAbstract $request) : BaseStringL11n
    {
        $attrL11n      = new BaseStringL11n();
        $attrL11n->ref = $request->getDataInt('type') ?? 0;
        $attrL11n->setLanguage(
            $request->getDataString('language') ?? $request->getLanguage()
        );
        $attrL11n->content = $request->getDataString('title') ?? '';

        return $attrL11n;
    }

    /**
     * Validate supplier attribute l11n create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateSupplierAttributeTypeL11nCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))
            || ($val['type'] = !$request->hasData('type'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create supplier attribute type
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSupplierAttributeTypeCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateSupplierAttributeTypeCreate($request))) {
            $response->set('attr_type_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $attrType = $this->createSupplierAttributeTypeFromRequest($request);
        $this->createModel($request->header->account, $attrType, SupplierAttributeTypeMapper::class, 'attr_type', $request->getOrigin());

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute type', 'Attribute type successfully created', $attrType);
    }

    /**
     * Method to create supplier attribute from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return AttributeType
     *
     * @since 1.0.0
     */
    private function createSupplierAttributeTypeFromRequest(RequestAbstract $request) : AttributeType
    {
        $attrType                    = new AttributeType($request->getDataString('name') ?? '');
        $attrType->datatype          = $request->getDataInt('datatype') ?? 0;
        $attrType->custom            = $request->getDataBool('custom') ?? false;
        $attrType->isRequired        = (bool) ($request->getData('is_required') ?? false);
        $attrType->validationPattern = $request->getDataString('validation_pattern') ?? '';
        $attrType->setL11n($request->getDataString('title') ?? '', $request->getDataString('language') ?? ISO639x1Enum::_EN);
        $attrType->setFields($request->getDataInt('fields') ?? 0);

        return $attrType;
    }

    /**
     * Validate supplier attribute create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateSupplierAttributeTypeCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))
            || ($val['name'] = !$request->hasData('name'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create supplier attribute value
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSupplierAttributeValueCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateSupplierAttributeValueCreate($request))) {
            $response->set('attr_value_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $attrValue = $this->createSupplierAttributeValueFromRequest($request);
        $this->createModel($request->header->account, $attrValue, SupplierAttributeValueMapper::class, 'attr_value', $request->getOrigin());

        if ($attrValue->isDefault) {
            $this->createModelRelation(
                $request->header->account,
                (int) $request->getData('type'),
                $attrValue->id,
                SupplierAttributeTypeMapper::class, 'defaults', '', $request->getOrigin()
            );
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute value', 'Attribute value successfully created', $attrValue);
    }

    /**
     * Method to create supplier attribute value from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return AttributeValue
     *
     * @since 1.0.0
     */
    private function createSupplierAttributeValueFromRequest(RequestAbstract $request) : AttributeValue
    {
        /** @var AttributeType $type */
        $type = SupplierAttributeTypeMapper::get()
            ->where('id', $request->getDataInt('type') ?? 0)
            ->execute();

        $attrValue            = new AttributeValue();
        $attrValue->isDefault = $request->getDataBool('default') ?? false;
        $attrValue->setValue($request->getData('value'), $type->datatype);

        if ($request->hasData('title')) {
            $attrValue->setL11n($request->getDataString('title') ?? '', $request->getDataString('language') ?? ISO639x1Enum::_EN);
        }

        return $attrValue;
    }

    /**
     * Validate supplier attribute value create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateSupplierAttributeValueCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['type'] = !$request->hasData('type'))
            || ($val['value'] = !$request->hasData('value'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create item attribute l11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSupplierAttributeValueL11nCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateSupplierAttributeValueL11nCreate($request))) {
            $response->set('attr_value_l11n_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $attrL11n = $this->createSupplierAttributeValueL11nFromRequest($request);
        $this->createModel($request->header->account, $attrL11n, SupplierAttributeValueL11nMapper::class, 'attr_value_l11n', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Localization', 'Localization successfully created', $attrL11n);
    }

    /**
     * Method to create Supplier attribute l11n from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    private function createSupplierAttributeValueL11nFromRequest(RequestAbstract $request) : BaseStringL11n
    {
        $attrL11n      = new BaseStringL11n();
        $attrL11n->ref = $request->getDataInt('value') ?? 0;
        $attrL11n->setLanguage(
            $request->getDataString('language') ?? $request->getLanguage()
        );
        $attrL11n->content = $request->getDataString('title') ?? '';

        return $attrL11n;
    }

    /**
     * Validate Supplier attribute l11n create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateSupplierAttributeValueL11nCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))
            || ($val['value'] = !$request->hasData('value'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create supplier files
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiFileCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $uploadedFiles = $request->getFiles();

        if (empty($uploadedFiles)) {
            $this->fillJsonResponse($request, $response, NotificationLevel::ERROR, 'Item', 'Invalid supplier image', $uploadedFiles);
            $response->header->status = RequestStatusCode::R_400;

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

        $this->createModelRelation(
            $request->header->account,
            (int) $request->getData('supplier'),
            \reset($uploaded)->id,
            SupplierMapper::class, 'files', '', $request->getOrigin()
        );

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Image', 'Image successfully updated', $uploaded);
    }

    /**
     * Api method to create supplier files
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiNoteCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $request->setData('virtualpath', '/Modules/SupplierManagement/' . $request->getData('id'), true);
        $this->app->moduleManager->get('Editor')->apiEditorCreate($request, $response, $data);

        $data = $response->get($request->uri->__toString());
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
