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

namespace Modules\SupplierManagement\tests\Models;

use Modules\Editor\Models\EditorDoc;
use Modules\Media\Models\Media;
use Modules\Profile\Models\ContactElement;
use Modules\SupplierManagement\Models\Supplier;
use Modules\SupplierManagement\Models\SupplierStatus;

/**
 * @internal
 */
final class SupplierTest extends \PHPUnit\Framework\TestCase
{
    private Supplier $supplier;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->supplier = new Supplier();
    }

    /**
     * @covers Modules\SupplierManagement\Models\Supplier
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->supplier->getId());
        self::assertEquals('', $this->supplier->number);
        self::assertEquals('', $this->supplier->numberReverse);
        self::assertEquals('', $this->supplier->info);
        self::assertEquals(SupplierStatus::ACTIVE, $this->supplier->getStatus());
        self::assertEquals(0, $this->supplier->getType());
        self::assertEquals([], $this->supplier->getNotes());
        self::assertEquals([], $this->supplier->getFiles());
        self::assertEquals([], $this->supplier->getAddresses());
        self::assertEquals([], $this->supplier->getContactElements());
        self::assertEquals((new \DateTime('now'))->format('Y-m-d'), $this->supplier->createdAt->format('Y-m-d'));
        self::assertInstanceOf('\Modules\Admin\Models\Account', $this->supplier->account);
        self::assertInstanceOf('\Modules\Admin\Models\Address', $this->supplier->mainAddress);
        self::assertInstanceOf('\Modules\Profile\Models\NullContactElement', $this->supplier->getMainContactElement(0));
    }

    /**
     * @covers Modules\SupplierManagement\Models\Supplier
     * @group module
     */
    public function testStatusInputOutput() : void
    {
    	$this->supplier->setStatus(SupplierStatus::INACTIVE);
    	self::assertEquals(SupplierStatus::INACTIVE, $this->supplier->getStatus());
    }

    /**
     * @covers Modules\SupplierManagement\Models\Supplier
     * @group module
     */
    public function testTypeInputOutput() : void
    {
        $this->supplier->setType(2);
        self::assertEquals(2, $this->supplier->getType());
    }

    /**
     * @covers Modules\SupplierManagement\Models\Supplier
     * @group module
     */
    public function testFileInputOutput() : void
    {
        $this->supplier->addFile($temp = new Media());
        self::assertCount(1, $this->supplier->getFiles());
    }

    /**
     * @covers Modules\SupplierManagement\Models\Supplier
     * @group module
     */
    public function testContactElementInputOutput() : void
    {
        $this->supplier->addContactElement($temp = new ContactElement());
        self::assertCount(1, $this->supplier->getContactElements());
        self::assertEquals($temp, $this->supplier->getMainContactElement(0));
    }

    /**
     * @covers Modules\SupplierManagement\Models\Supplier
     * @group module
     */
    public function testNoteInputOutput() : void
    {
        $this->supplier->addNote(new EditorDoc());
        self::assertCount(1, $this->supplier->getNotes());
    }

    /**
     * @covers Modules\SupplierManagement\Models\Supplier
     * @group module
     */
    public function testSerialize() : void
    {
        $this->supplier->number        = '123456';
        $this->supplier->numberReverse = '654321';
        $this->supplier->setStatus(SupplierStatus::INACTIVE);
        $this->supplier->setType(2);
        $this->supplier->info = 'Test info';

        self::assertEquals(
            [
                'id'            => 0,
                'number'        => '123456',
                'numberReverse' => '654321',
                'status'        => SupplierStatus::INACTIVE,
                'type'          => 2,
                'info'          => 'Test info',
            ],
            $this->supplier->jsonSerialize()
        );
    }
}
