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

use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Views\View $this */
$suppliers = $this->data['supplier'];

echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Suppliers'); ?><i class="lni lni-download download btn end-xs"></i></div>
            <div class="slider">
            <table id="iPurchaseSupplierList" class="default sticky">
                <thead>
                <tr>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                        <label for="iPurchaseSupplierList-sort-1">
                            <input type="radio" name="iPurchaseSupplierList-sort" id="iPurchaseSupplierList-sort-1">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iPurchaseSupplierList-sort-2">
                            <input type="radio" name="iPurchaseSupplierList-sort" id="iPurchaseSupplierList-sort-2">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                        <label for="iPurchaseSupplierList-sort-3">
                            <input type="radio" name="iPurchaseSupplierList-sort" id="iPurchaseSupplierList-sort-3">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iPurchaseSupplierList-sort-4">
                            <input type="radio" name="iPurchaseSupplierList-sort" id="iPurchaseSupplierList-sort-4">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('City'); ?>
                        <label for="iPurchaseSupplierList-sort-5">
                            <input type="radio" name="iPurchaseSupplierList-sort" id="iPurchaseSupplierList-sort-5">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iPurchaseSupplierList-sort-6">
                            <input type="radio" name="iPurchaseSupplierList-sort" id="iPurchaseSupplierList-sort-6">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Zip'); ?>
                        <label for="iPurchaseSupplierList-sort-7">
                            <input type="radio" name="iPurchaseSupplierList-sort" id="iPurchaseSupplierList-sort-7">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iPurchaseSupplierList-sort-8">
                            <input type="radio" name="iPurchaseSupplierList-sort" id="iPurchaseSupplierList-sort-8">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Address'); ?>
                        <label for="iPurchaseSupplierList-sort-9">
                            <input type="radio" name="iPurchaseSupplierList-sort" id="iPurchaseSupplierList-sort-9">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iPurchaseSupplierList-sort-10">
                            <input type="radio" name="iPurchaseSupplierList-sort" id="iPurchaseSupplierList-sort-10">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Country'); ?>
                        <label for="iPurchaseSupplierList-sort-11">
                            <input type="radio" name="iPurchaseSupplierList-sort" id="iPurchaseSupplierList-sort-11">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iPurchaseSupplierList-sort-12">
                            <input type="radio" name="iPurchaseSupplierList-sort" id="iPurchaseSupplierList-sort-12">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                <tbody>
                <?php $count = 0; foreach ($suppliers as $key => $value) : ++$count;
                $url         = UriFactory::build('{/base}/purchase/supplier/profile?{?}&id=' . $value->id);
                ?>
                <tr data-href="<?= $url; ?>">
                    <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->number); ?></a>
                    <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->account->name1); ?> <?= $this->printHtml($value->account->name2); ?></a>
                    <td data-label="<?= $this->getHtml('City'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->mainAddress->city); ?></a>
                    <td data-label="<?= $this->getHtml('Zip'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->mainAddress->postal); ?></a>
                    <td data-label="<?= $this->getHtml('Address'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->mainAddress->address); ?></a>
                    <td data-label="<?= $this->getHtml('Country'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->mainAddress->getCountry()); ?></a>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                    <tr><td colspan="8" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
        </section>
    </div>
</div>
