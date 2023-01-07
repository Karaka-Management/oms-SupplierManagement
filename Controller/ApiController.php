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
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Controller;

use Modules\Admin\Models\Account;
use Modules\Admin\Models\Address;
use Modules\Media\Models\PathSettings;
use Modules\Profile\Models\ContactElementMapper;
use Modules\Profile\Models\Profile;
use Modules\SupplierManagement\Models\NullSupplierAttributeType;
use Modules\SupplierManagement\Models\NullSupplierAttributeValue;
use Modules\SupplierManagement\Models\Supplier;
use Modules\SupplierManagement\Models\SupplierAttribute;
use Modules\SupplierManagement\Models\SupplierAttributeMapper;
use Modules\SupplierManagement\Models\SupplierAttributeType;
use phpOMS\Localization\BaseStringL11n;
use Modules\SupplierManagement\Models\SupplierAttributeTypeL11nMapper;
use Modules\SupplierManagement\Models\SupplierAttributeTypeMapper;
use Modules\SupplierManagement\Models\SupplierAttributeValue;
use Modules\SupplierManagement\Models\SupplierAttributeValueMapper;
use Modules\SupplierManagement\Models\SupplierMapper;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;

/**
 * SupplierManagement class.
 *
 * @package Modules\SupplierManagement
 * @license OMS License 1.0
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
        $account->name1 = (string) ($request->getData('name1') ?? '');
        $account->name2 = (string) ($request->getData('name2') ?? '');

        $profile = new Profile($account);

        $supplier          = new Supplier();
        $supplier->number  = (string) ($request->getData('number') ?? '');
        $supplier->profile = $profile;

        $addr          = new Address();
        $addr->address = (string) ($request->getData('address') ?? '');
        $addr->postal  = (string) ($request->getData('postal') ?? '');
        $addr->city    = (string) ($request->getData('city') ?? '');
        $addr->state   = (string) ($request->getData('state') ?? '');
        $addr->setCountry((string) ($request->getData('country') ?? ''));
        $supplier->mainAddress = $addr;

        $supplier->unit = $request->getData('unit', 'int');

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
        if (($val['number'] = empty($request->getData('number')))
            || ($val['name1'] = empty($request->getData('name1')))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Routing end-point for application behaviour.
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
    public function apiContactElementCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var \Modules\Profile\Controller\ApiController $profileModule */
        $profileModule = $this->app->moduleManager->get('Profile');

        if (!empty($val = $profileModule->validateContactElementCreate($request))) {
            $response->set('contact_element_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $contactElement = $profileModule->createContactElementFromRequest($request);

        $this->createModel($request->header->account, $contactElement, ContactElementMapper::class, 'supplier-contactElement', $request->getOrigin());
        $this->createModelRelation(
            $request->header->account,
            (int) $request->getData('account'),
            $contactElement->getId(),
        SupplierMapper::class, 'contactElements', '', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Contact Element', 'Contact element successfully created', $contactElement);
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
     * @return SupplierAttribute
     *
     * @since 1.0.0
     */
    private function createSupplierAttributeFromRequest(RequestAbstract $request) : SupplierAttribute
    {
        $attribute           = new SupplierAttribute();
        $attribute->supplier = (int) $request->getData('supplier');
        $attribute->type     = new NullSupplierAttributeType((int) $request->getData('type'));
        $attribute->value    = new NullSupplierAttributeValue((int) $request->getData('value'));

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
        if (($val['type'] = empty($request->getData('type')))
            || ($val['value'] = empty($request->getData('value')))
            || ($val['supplier'] = empty($request->getData('supplier')))
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
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute type localization', 'Attribute type localization successfully created', $attrL11n);
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
        $attrL11n       = new BaseStringL11n();
        $attrL11n->ref = (int) ($request->getData('type') ?? 0);
        $attrL11n->setLanguage((string) (
            $request->getData('language') ?? $request->getLanguage()
        ));
        $attrL11n->content = (string) ($request->getData('title') ?? '');

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
        if (($val['title'] = empty($request->getData('title')))
            || ($val['type'] = empty($request->getData('type')))
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
     * @return SupplierAttributeType
     *
     * @since 1.0.0
     */
    private function createSupplierAttributeTypeFromRequest(RequestAbstract $request) : SupplierAttributeType
    {
        $attrType = new SupplierAttributeType($request->getData('name') ?? '');
        $attrType->setL11n((string) ($request->getData('title') ?? ''), $request->getData('language') ?? ISO639x1Enum::_EN);
        $attrType->datatype            = (int) ($request->getData('datatype') ?? 0);
        $attrType->setFields((int) ($request->getData('fields') ?? 0));
        $attrType->custom            = (bool) ($request->getData('custom') ?? false);
        $attrType->isRequired        = (bool) ($request->getData('is_required') ?? false);
        $attrType->validationPattern = (string) ($request->getData('validation_pattern') ?? '');

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
        if (($val['title'] = empty($request->getData('title')))
            || ($val['name'] = empty($request->getData('name')))
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
                $attrValue->getId(),
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
     * @return SupplierAttributeValue
     *
     * @since 1.0.0
     */
    private function createSupplierAttributeValueFromRequest(RequestAbstract $request) : SupplierAttributeValue
    {
        $type = SupplierAttributeTypeMapper::get()
            ->where('id', (int) ($request->getData('type') ?? 0))
            ->execute();

        $attrValue = new SupplierAttributeValue();
        $attrValue->isDefault = (bool) ($request->getData('default') ?? false);
        $attrValue->setValue($request->getData('value'), $type->datatype);

        if ($request->getData('title') !== null) {
            $attrValue->setL11n($request->getData('title'), $request->getData('language') ?? ISO639x1Enum::_EN);
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
        if (($val['type'] = empty($request->getData('type')))
            || ($val['value'] = empty($request->getData('value')))
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
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute type localization', 'Attribute type localization successfully created', $attrL11n);
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
        $attrL11n        = new BaseStringL11n();
        $attrL11n->ref = (int) ($request->getData('value') ?? 0);
        $attrL11n->setLanguage((string) (
            $request->getData('language') ?? $request->getLanguage()
        ));
        $attrL11n->content = (string) ($request->getData('title') ?? '');

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
        if (($val['title'] = empty($request->getData('title')))
            || ($val['value'] = empty($request->getData('value')))
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
            $request->getDataList('names'),
            $request->getDataList('filenames'),
            $uploadedFiles,
            $request->header->account,
            __DIR__ . '/../../../Modules/Media/Files/Modules/SupplierManagement/' . ($request->getData('supplier') ?? '0'),
            '/Modules/SupplierManagement/' . ($request->getData('supplier') ?? '0'),
            $request->getData('type', 'int'),
            pathSettings: PathSettings::FILE_PATH
        );

        $this->createModelRelation(
            $request->header->account,
            (int) $request->getData('supplier'),
            \reset($uploaded)->getId(),
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
        $this->createModelRelation($request->header->account, $request->getData('id'), $model->getId(), SupplierMapper::class, 'notes', '', $request->getOrigin());
    }
}
