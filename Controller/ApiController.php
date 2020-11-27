<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\SupplierManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Controller;

use Modules\Admin\Models\Account;
use Modules\Admin\Models\Address;
use Modules\Profile\Models\Profile;
use Modules\Profile\Models\ContactElementMapper;
use Modules\SupplierManagement\Models\Supplier;
use Modules\SupplierManagement\Models\SupplierMapper;
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
 * @link    https://orange-management.org
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
    public function apiSupplierCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
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
        $account->name1 = $request->getData('name1') ?? '';
        $account->name2 = $request->getData('name2') ?? '';

        $profile = new Profile($account);

        $supplier          = new Supplier();
        $supplier->number  = $request->getData('number') ?? '';
        $supplier->profile = $profile;

        $addr          = new Address();
        $addr->address = $request->getData('address') ?? '';
        $addr->postal  = $request->getData('postal') ?? '';
        $addr->city    = $request->getData('city') ?? '';
        $addr->state   = $request->getData('state') ?? '';
        $addr->setCountry($request->getData('country') ?? '');
        $supplier->mainAddress = $addr;

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
    public function apiContactElementCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $profileModule  = $this->app->moduleManager->get('Profile');

        if (!empty($val = $profileModule->validateContactElementCreate($request))) {
            $response->set('contact_element_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $contactElement = $profileModule->createContactElementFromRequest($request);

        $this->createModel($request->header->account, $contactElement, ContactElementMapper::class, 'supplier-contactElement', $request->getOrigin());
        $this->createModelRelation(
            $request->header->account,
            (int) $request->getData('supplier'),
            $contactElement->getId(),
        SupplierMapper::class, 'contactElements', '', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Contact Element', 'Contact element successfully created', $contactElement);
    }
}
