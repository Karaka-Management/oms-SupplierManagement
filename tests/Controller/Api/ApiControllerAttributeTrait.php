<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\tests\Controller\Api;

use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Uri\HttpUri;

trait ApiControllerAttributeTrait
{
    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierAttributeTypeCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('title', 'EN:1');
        $request->setData('name', 'test_name');
        $request->setData('language', ISO639x1Enum::_EN);

        $this->module->apiSupplierAttributeTypeCreate($request, $response);
        self::assertGreaterThan(0, $response->get('')['response']->getId());
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierAttributeTypeL11nCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('title', 'DE:2');
        $request->setData('type', '1');
        $request->setData('language', ISO639x1Enum::_DE);

        $this->module->apiSupplierAttributeTypeL11nCreate($request, $response);
        self::assertGreaterThan(0, $response->get('')['response']->getId());
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierAttributeValueIntCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('default', '1');
        $request->setData('type', '1');
        $request->setData('value', '1');
        $request->setData('language', ISO639x1Enum::_DE);
        $request->setData('country', ISO3166TwoEnum::_DEU);

        $this->module->apiSupplierAttributeValueCreate($request, $response);
        self::assertGreaterThan(0, $response->get('')['response']->getId());
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierAttributeValueStrCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('value', '1');
        $request->setData('type', '1');
        $request->setData('language', ISO639x1Enum::_DE);
        $request->setData('country', ISO3166TwoEnum::_DEU);

        $this->module->apiSupplierAttributeValueCreate($request, $response);
        self::assertGreaterThan(0, $response->get('')['response']->getId());
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierAttributeValueFloatCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('value', '1.1');
        $request->setData('type', '1');
        $request->setData('language', ISO639x1Enum::_DE);
        $request->setData('country', ISO3166TwoEnum::_DEU);

        $this->module->apiSupplierAttributeValueCreate($request, $response);
        self::assertGreaterThan(0, $response->get('')['response']->getId());
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierAttributeValueDatCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('value', '2020-08-02');
        $request->setData('type', '1');
        $request->setData('language', ISO639x1Enum::_DE);
        $request->setData('country', ISO3166TwoEnum::_DEU);

        $this->module->apiSupplierAttributeValueCreate($request, $response);
        self::assertGreaterThan(0, $response->get('')['response']->getId());
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierAttributeCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('supplier', '1');
        $request->setData('value', '1');
        $request->setData('type', '1');

        $this->module->apiSupplierAttributeCreate($request, $response);
        self::assertGreaterThan(0, $response->get('')['response']->getId());
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierAttributeValueCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiSupplierAttributeValueCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierAttributeTypeCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiSupplierAttributeTypeCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierAttributeTypeL11nCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiSupplierAttributeTypeL11nCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierAttributeCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiSupplierAttributeCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }
}
