<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\SupplierManagement\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Admin;

use phpOMS\Application\ApplicationAbstract;
use phpOMS\Config\SettingsInterface;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Module\InstallerAbstract;
use phpOMS\Module\ModuleInfo;
use phpOMS\Uri\HttpUri;

/**
 * Installer class.
 *
 * @package Modules\SupplierManagement\Admin
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
    /**
     * Path of the file
     *
     * @var string
     * @since 1.0.0
     */
    public const PATH = __DIR__;

    /**
     * {@inheritdoc}
     */
    public static function install(ApplicationAbstract $app, ModuleInfo $info, SettingsInterface $cfgHandler) : void
    {
        parent::install($app, $info, $cfgHandler);

        /* Attributes */
        $fileContent = \file_get_contents(__DIR__ . '/Install/attributes.json');
        if ($fileContent === false) {
            return;
        }

        $attributes = \json_decode($fileContent, true);
        if (!\is_array($attributes)) {
            return;
        }

        $attrTypes  = self::createSupplierAttributeTypes($app, $attributes);
        $attrValues = self::createSupplierAttributeValues($app, $attrTypes, $attributes);

        /* Localizations */
        $fileContent = \file_get_contents(__DIR__ . '/Install/localizations.json');
        if ($fileContent === false) {
            return;
        }

        $localizations = \json_decode($fileContent, true);
        if (!\is_array($localizations)) {
            return;
        }

        $l11nTypes = self::createSupplierL11nTypes($app, $localizations);
    }

    /**
     * Install default l11n types
     *
     * @param ApplicationAbstract $app   Application
     * @param array               $l11ns Attribute definition
     *
     * @return array
     *
     * @since 1.0.0
     */
    private static function createSupplierL11nTypes(ApplicationAbstract $app, array $l11ns) : array
    {
        /** @var array<string, array> $l11nTypes */
        $l11nTypes = [];

        /** @var \Modules\SupplierManagement\Controller\ApiController $module */
        $module = $app->moduleManager->getModuleInstance('SupplierManagement');

        foreach ($l11ns as $l11n) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = 1;
            $request->setData('title', $l11n['name']);
            $request->setData('is_required', $l11n['is_required'] ?? false);

            $module->apiSupplierL11nTypeCreate($request, $response);

            $responseData = $response->get('');
            if (!\is_array($responseData)) {
                continue;
            }

            $l11nTypes[] = \is_array($responseData['response'])
                ? $responseData['response']
                : $responseData['response']->toArray();
        }

        return $l11nTypes;
    }

    /**
     * Install default attribute types
     *
     * @param ApplicationAbstract $app        Application
     * @param array               $attributes Attribute definition
     *
     * @return array
     *
     * @since 1.0.0
     */
    private static function createSupplierAttributeTypes(ApplicationAbstract $app, array $attributes) : array
    {
        /** @var array<string, array> $supplierAttrType */
        $supplierAttrType = [];

        /** @var \Modules\SupplierManagement\Controller\ApiController $module */
        $module = $app->moduleManager->getModuleInstance('SupplierManagement');

        /** @var array $attribute */
        foreach ($attributes as $attribute) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = 1;
            $request->setData('name', $attribute['name'] ?? '');
            $request->setData('title', \reset($attribute['l11n']));
            $request->setData('language', \array_keys($attribute['l11n'])[0] ?? 'en');
            $request->setData('is_required', $attribute['is_required'] ?? false);
            $request->setData('custom', $attribute['is_custom_allowed'] ?? false);
            $request->setData('validation_pattern', $attribute['validation_pattern'] ?? '');
            $request->setData('datatype', (int) $attribute['value_type']);

            $module->apiSupplierAttributeTypeCreate($request, $response);

            $responseData = $response->get('');
            if (!\is_array($responseData)) {
                continue;
            }

            $supplierAttrType[$attribute['name']] = \is_array($responseData['response'])
                ? $responseData['response']
                : $responseData['response']->toArray();

            $isFirst = true;
            foreach ($attribute['l11n'] as $language => $l11n) {
                if ($isFirst) {
                    $isFirst = false;
                    continue;
                }

                $response = new HttpResponse();
                $request  = new HttpRequest(new HttpUri(''));

                $request->header->account = 1;
                $request->setData('title', $l11n);
                $request->setData('language', $language);
                $request->setData('type', $supplierAttrType[$attribute['name']]['id']);

                $module->apiSupplierAttributeTypeL11nCreate($request, $response);
            }
        }

        return $supplierAttrType;
    }

    /**
     * Create default attribute values for types
     *
     * @param ApplicationAbstract $app              Application
     * @param array               $supplierAttrType Attribute types
     * @param array               $attributes       Attribute definition
     *
     * @return array
     *
     * @since 1.0.0
     */
    private static function createSupplierAttributeValues(ApplicationAbstract $app, array $supplierAttrType, array $attributes) : array
    {
        /** @var array<string, array> $supplierAttrValue */
        $supplierAttrValue = [];

        /** @var \Modules\SupplierManagement\Controller\ApiController $module */
        $module = $app->moduleManager->getModuleInstance('SupplierManagement');

        foreach ($attributes as $attribute) {
            $supplierAttrValue[$attribute['name']] = [];

            /** @var array $value */
            foreach ($attribute['values'] as $value) {
                $response = new HttpResponse();
                $request  = new HttpRequest(new HttpUri(''));

                $request->header->account = 1;
                $request->setData('value', $value['value'] ?? '');
                $request->setData('unit', $value['unit'] ?? '');
                $request->setData('default', true);
                $request->setData('type', $supplierAttrType[$attribute['name']]['id']);

                if (isset($value['l11n']) && !empty($value['l11n'])) {
                    $request->setData('title', \reset($value['l11n']));
                    $request->setData('language', \array_keys($value['l11n'])[0] ?? 'en');
                }

                $module->apiSupplierAttributeValueCreate($request, $response);

                $responseData = $response->get('');
                if (!\is_array($responseData)) {
                    continue;
                }

                $attrValue = \is_array($responseData['response'])
                    ? $responseData['response']
                    : $responseData['response']->toArray();

                $supplierAttrValue[$attribute['name']][] = $attrValue;

                $isFirst = true;
                foreach (($value['l11n'] ?? []) as $language => $l11n) {
                    if ($isFirst) {
                        $isFirst = false;
                        continue;
                    }

                    $response = new HttpResponse();
                    $request  = new HttpRequest(new HttpUri(''));

                    $request->header->account = 1;
                    $request->setData('title', $l11n);
                    $request->setData('language', $language);
                    $request->setData('value', $attrValue['id']);

                    $module->apiSupplierAttributeValueL11nCreate($request, $response);
                }
            }
        }

        return $supplierAttrValue;
    }
}
