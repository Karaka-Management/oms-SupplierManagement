<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\ItemManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Views\View $this */
$items = $this->data['items'] ?? [];

?>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Items'); ?><i class="g-icon download btn end-xs">download</i></div>
            <table id="iPurchaseItemList" class="default sticky">
                <thead>
                <tr>
                    <td><label class="checkbox" for="iPurchaseItemSelect-">
                            <input type="checkbox" id="iPurchaseItemSelect-" name="itemselect">
                            <span class="checkmark"></span>
                        </label>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                        <label for="iPurchaseItemList-sort-1">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-1">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-2">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-2">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                        <label for="iPurchaseItemList-sort-3">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-3">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-4">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-4">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Quantity'); ?>
                        <label for="iPurchaseItemList-sort-5">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-5">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-6">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-6">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('UnitPrice'); ?>
                        <label for="iPurchaseItemList-sort-7">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-7">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-8">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-8">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Discount'); ?>
                        <label for="iPurchaseItemList-sort-11">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-11">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-12">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-12">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Discount%'); ?>
                        <label for="iPurchaseItemList-sort-13">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-13">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-14">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-14">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('DiscountBonus'); ?>
                        <label for="iPurchaseItemList-sort-15">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-15">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-16">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-16">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('TotalPrice'); ?>
                        <label for="iPurchaseItemList-sort-9">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-9">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-10">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-10">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                <tbody>
                <?php
                $count = 0;
                foreach ($items as $key => $value) :
                    if ($value->itemNumber === '') {
                        continue;
                    }

                    ++$count;
                    $url = UriFactory::build('{/base}/purchase/item/view?{?}&id=' . $value->id);
                ?>
                <tr data-href="<?= $url; ?>">
                    <td><label class="checkbox" for="iPurchaseItemSelect-<?= $key; ?>">
                            <input type="checkbox" id="iPurchaseItemSelect-<?= $key; ?>" name="itemselect">
                            <span class="checkmark"></span>
                        </label>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->itemNumber); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->itemName); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->quantity->getAmount()); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->getCurrency($value->singlePurchasePriceNet); ?></a>
                    <td>
                    <td>
                    <td>
                    <td><a href="<?= $url; ?>"><?= $this->getCurrency($value->totalPurchasePriceNet); ?></a>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                <tr><td colspan="9" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
        </section>
    </div>
</div>
