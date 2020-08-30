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

/** @var \phpOMS\Views\View $this */
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
                    <td><?= $this->getHtml('Name2'); ?>
                    <td class="wf-100"><?= $this->getHtml('Name3'); ?>
                    <td><?= $this->getHtml('City'); ?>
                    <td><?= $this->getHtml('Zip'); ?>
                    <td><?= $this->getHtml('Address'); ?>
                    <td><?= $this->getHtml('Country'); ?>
                <tbody>
                <?php $count = 0; foreach ([] as $key => $value) : ++$count; ?>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                    <tr><td colspan="8" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
        </section>
    </div>
</div>
