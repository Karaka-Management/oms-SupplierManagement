<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\SupplierManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Models;

use phpOMS\Localization\ISO639x1Enum;

/**
 * Localization of the supplier class.
 *
 * @package Modules\SupplierManagement\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class SupplierL11n implements \JsonSerializable
{
    /**
     * Article ID.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * Supplier ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $supplier = 0;

    /**
     * Supplier ID.
     *
     * @var SupplierL11nType
     * @since 1.0.0
     */
    public SupplierL11nType $type;

    /**
     * Language.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $language = ISO639x1Enum::_EN;

    /**
     * Title.
     *
     * @var string
     * @since 1.0.0
     */
    public string $description = '';

    /**
     * Constructor.
     *
     * @param SupplierL11nType $type        Supplier localization type
     * @param string           $description Description/content
     * @param string           $language    Language
     *
     * @since 1.0.0
     */
    public function __construct(SupplierL11nType $type = null, string $description = '', string $language = ISO639x1Enum::_EN)
    {
        $this->type        = $type ?? new SupplierL11nType();
        $this->description = $description;
        $this->language    = $language;
    }

    /**
     * Get id
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get language
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getLanguage() : string
    {
        return $this->language;
    }

    /**
     * Set language
     *
     * @param string $language Language
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setLanguage(string $language) : void
    {
        $this->language = $language;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'             => $this->id,
            'description'    => $this->description,
            'supplier'           => $this->supplier,
            'language'       => $this->language,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }
}
