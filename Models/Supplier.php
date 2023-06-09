<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\SupplierManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Models;

use Modules\Admin\Models\Account;
use Modules\Admin\Models\Address;
use Modules\Admin\Models\NullAddress;
use Modules\Editor\Models\EditorDoc;
use Modules\Payment\Models\Payment;
use Modules\Profile\Models\ContactElement;
use Modules\Profile\Models\NullContactElement;

/**
 * Supplier class.
 *
 * @package Modules\SupplierManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class Supplier
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    public string $number = '';

    public string $numberReverse = '';

    public int $status = SupplierStatus::ACTIVE;

    public int $type = 0;

    public string $info = '';

    public \DateTimeImmutable $createdAt;

    public Account $account;

    private array $contactElements = [];

    private array $address = [];

    public Address $mainAddress;

    private array $partners = [];

    /**
     * Payments.
     *
     * @var Payment[]
     * @since 1.0.0
     */
    private array $payments = [];

    /**
     * Unit
     *
     * @var null|int
     * @since 1.0.0
     */
    public ?int $unit = null;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->createdAt   = new \DateTimeImmutable('now');
        $this->account     = new Account();
        $this->mainAddress = new NullAddress();
    }

    /**
     * Get id.
     *
     * @return int Model id
     *
     * @since 1.0.0
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get status.
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * Set status.
     *
     * @param int $status Status
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setStatus(int $status) : void
    {
        $this->status = $status;
    }

    /**
     * Get payments
     *
     * @return Payment[]
     *
     * @since 1.0.0
     */
    public function getPayments() : array
    {
        return $this->payments;
    }

    /**
     * Get payments
     *
     * @param int $type Payment type
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function getPaymentsByType(int $type) : array
    {
        $payments = [];

        foreach ($this->payments as $payment) {
            if ($payment->getType() === $type) {
                $payments[] = $payment;
            }
        }

        return $payments;
    }

    /**
     * Get supplier type.
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getType() : int
    {
        return $this->type;
    }

    /**
     * Set supplier type.
     *
     * @param int $type Type
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setType(int $type) : void
    {
        $this->type = $type;
    }

    /**
     * Add doc to item
     *
     * @param EditorDoc $note Note
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addNote(EditorDoc $note) : void
    {
        $this->notes[] = $note;
    }

    /**
     * Get notes
     *
     * @return EditorDoc[]
     *
     * @since 1.0.0
     */
    public function getNotes() : array
    {
        return $this->notes;
    }

    /**
     * Get addresses.
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function getAddresses() : array
    {
        return $this->address;
    }

    /**
     * Add partner
     *
     * @param Account $partner Partner
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addPartner(Account $partner) : void
    {
        $this->partners[] = $partner;
    }

    /**
     * Get partners
     *
     * @return Account[]
     *
     * @since 1.0.0
     */
    public function getPartners() : array
    {
        return $this->partners;
    }

    /**
     * Get contacts.
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function getContactElements() : array
    {
        return $this->contactElements;
    }

    /**
     * Order contact elements
     *
     * @param ContactElement $a Element
     * @param ContactElement $b Element
     *
     * @return int
     *
     * @since 1.0.0
     */
    private function orderContactElements(ContactElement $a, ContactElement $b) : int
    {
        return $a->order <=> $b->order;
    }

    /**
     * Get the main contact element by type
     *
     * @param int $type Contact element type
     *
     * @return ContactElement
     *
     * @since 1.0.0
     */
    public function getMainContactElement(int $type) : ContactElement
    {
        \uasort($this->contactElements, [$this, 'orderContactElements']);

        foreach ($this->contactElements as $element) {
            if ($element->getType() === $type) {
                return $element;
            }
        }

        return new NullContactElement();
    }

    /**
     * Add contact element
     *
     * @param int|ContactElement $element Contact element
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addContactElement($element) : void
    {
        $this->contactElements[] = $element;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'            => $this->id,
            'number'        => $this->number,
            'numberReverse' => $this->numberReverse,
            'status'        => $this->status,
            'type'          => $this->type,
            'info'          => $this->info,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }

    use \Modules\Media\Models\MediaListTrait;
    use \Modules\Editor\Models\EditorDocListTrait;
    use \Modules\Attribute\Models\AttributeHolderTrait;
}
