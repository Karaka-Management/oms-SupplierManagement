<?php
/**
 * Jingga
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
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\System\MimeType;
use phpOMS\Utils\TestUtils;

trait ApiControllerSupplierTrait
{
    public static function tearDownAfterClass() : void
    {
        if (\is_file(__DIR__ . '/m_icon_tmp.png')) {
            \unlink(__DIR__ . '/m_icon_tmp.png');
        }

        if (\is_file(__DIR__ . '/Test file_tmp.txt')) {
            \unlink(__DIR__ . '/Test file_tmp.txt');
        }
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('number', '123456');
        $request->setData('name1', 'Name1');
        $request->setData('name2', 'Name2');
        $request->setData('info', 'Info text');
        $request->setData('address', 'Address');
        $request->setData('postal', 'Postal');
        $request->setData('city', 'City');
        $request->setData('country', ISO3166TwoEnum::_USA);

        $this->module->apiSupplierCreate($request, $response);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiSupplierCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierProfileImageCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        \copy(__DIR__ . '/m_icon.png', __DIR__ . '/m_icon_tmp.png');

        $request->header->account = 1;
        $request->setData('name', '123456 backend');
        $request->setData('supplier', 1);
        $request->setData('type', '1');

        TestUtils::setMember($request, 'files', [
            'file1' => [
                'name'     => '123456.png',
                'type'     => MimeType::M_PNG,
                'tmp_name' => __DIR__ . '/m_icon_tmp.png',
                'error'    => \UPLOAD_ERR_OK,
                'size'     => \filesize(__DIR__ . '/m_icon_tmp.png'),
            ],
        ]);

        $this->module->apiFileCreate($request, $response);
        $file = $response->getDataArray('')['response'];
        self::assertGreaterThan(0, \reset($file)->id);
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierFileCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        \copy(__DIR__ . '/Test file.txt', __DIR__ . '/Test file_tmp.txt');

        $request->header->account = 1;
        $request->setData('name', 'test file backend');
        $request->setData('supplier', 1);

        TestUtils::setMember($request, 'files', [
            'file1' => [
                'name'     => 'Test file.txt',
                'type'     => MimeType::M_TXT,
                'tmp_name' => __DIR__ . '/Test file_tmp.txt',
                'error'    => \UPLOAD_ERR_OK,
                'size'     => \filesize(__DIR__ . '/Test file_tmp.txt'),
            ],
        ]);

        $this->module->apiFileCreate($request, $response);
        $file = $response->getDataArray('')['response'];
        self::assertGreaterThan(0, \reset($file)->id);
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiSupplierNoteCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;

        $MARKDOWN = "# Test Title\n\nThis is **some** text.";

        $request->setData('id', 1);
        $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
        $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));

        $this->module->apiNoteCreate($request, $response);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers Modules\SupplierManagement\Controller\ApiController
     * @group module
     */
    public function testApiFileCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiFileCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }
}
