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

use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Views\View $this */
$suppliers = $this->getData('supplier');

echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Suppliers'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <table class="default">
                <thead>
                <tr>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                    <td><?= $this->getHtml('Name1'); ?>
                    <td class="wf-100"><?= $this->getHtml('Name2'); ?>
                    <td><?= $this->getHtml('City'); ?>
                    <td><?= $this->getHtml('Zip'); ?>
                    <td><?= $this->getHtml('Address'); ?>
                    <td><?= $this->getHtml('Country'); ?>
                <tbody>
                <?php $count = 0; foreach ($suppliers as $key => $value) : ++$count;
                 $url        = UriFactory::build('{/prefix}purchase/supplier/profile?{?}&id=' . $value->getId()); ?>
                <tr data-href="<?= $url; ?>">
                    <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->number); ?></a>
                    <td data-label="<?= $this->getHtml('Name1'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->profile->account->name1); ?></a>
                    <td data-label="<?= $this->getHtml('Name2'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->profile->account->name2); ?></a>
                    <td data-label="<?= $this->getHtml('City'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->getMainAddress()->city); ?></a>
                    <td data-label="<?= $this->getHtml('Zip'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->getMainAddress()->postal); ?></a>
                    <td data-label="<?= $this->getHtml('Address'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->getMainAddress()->address); ?></a>
                    <td data-label="<?= $this->getHtml('Country'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->getMainAddress()->getCountry()); ?></a>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                    <tr><td colspan="8" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
        </section>
    </div>
</div>
