<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\SupplierManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Admin\Models\ContactType;
use Modules\Billing\Models\BillMapper;
use Modules\Billing\Models\Price\PriceType;
use Modules\Billing\Models\PurchaseBillMapper;
use Modules\Media\Models\NullMedia;
use Modules\SupplierManagement\Models\NullSupplier;
use Modules\SupplierManagement\Models\SupplierStatus;
use phpOMS\DataStorage\Database\Query\OrderType;
use phpOMS\Localization\ISO3166CharEnum;
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Localization\ISO4217CharEnum;
use phpOMS\Localization\ISO639Enum;
use phpOMS\Localization\RegionEnum;
use phpOMS\Message\Http\HttpHeader;
use phpOMS\Stdlib\Base\FloatInt;
use phpOMS\Stdlib\Base\SmartDateTime;
use phpOMS\System\File\ExtensionType;
use phpOMS\System\File\FileUtils;
use phpOMS\Uri\UriFactory;

$countryCodes = ISO3166TwoEnum::getConstants();
$regions      = RegionEnum::getConstants();
$countries    = ISO3166CharEnum::getConstants();
$currencies   = ISO4217CharEnum::getConstants();
$languages    = ISO639Enum::getConstants();

/**
 * @var \Modules\SupplierManagement\Models\Supplier $supplier
 */
$supplier = $this->data['supplier'] ?? new NullSupplier();
$isNew    = $supplier->id === 0;

$supplierImage = $this->getData('supplierImage') ?? new NullMedia();

$supplierStatus = SupplierStatus::getConstants();

$monthlySalesCosts = $this->data['monthlySalesCosts'] ?? [];

// @performance The client, supplier and item views should not use actual tabs but individual pages for better performance
//      Tabs require too many models to be loaded. Implement and then use a tab navigation if it doesn't already exist.
//      https://github.com/Karaka-Management/oms-ItemManagement/issues/13

/**
 * @var \phpOMS\Views\View $this
 */
echo $this->data['nav']->render();
?>
<div class="tabview tab-2">
    <?php if (!$isNew) : ?>
    <div class="box">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('Profile'); ?></label>
            <li><label for="c-tab-2"><?= $this->getHtml('Addresses'); ?></label>
            <li><label for="c-tab-3"><?= $this->getHtml('Payment'); ?></label>
            <li><label for="c-tab-4"><?= $this->getHtml('Prices'); ?></label>
            <li><label for="c-tab-5"><?= $this->getHtml('Attributes', 'Attribute', 'Backend'); ?></label>
            <li><label for="c-tab-6"><?= $this->getHtml('Accounting'); ?></label>
            <li><label for="c-tab-7"><?= $this->getHtml('Notes'); ?></label>
            <li><label for="c-tab-8"><?= $this->getHtml('Files'); ?></label>
            <li><label for="c-tab-9"><?= $this->getHtml('Bills'); ?></label>
            <li><label for="c-tab-10"><?= $this->getHtml('Items'); ?></label>
            <?php if (!empty($logs)) : ?><li><label for="c-tab-17"><?= $this->getHtml('Logs'); ?></label><?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= $isNew || $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-lg-3 last-lg">
                    <?php if (!$isNew && (($this->data['hasBilling'] ?? false) || ($this->data['hasAccounting'] ?? false))) : ?>
                    <div class="box">
                        <?php if ($this->data['hasBilling'] ?? false) : ?>
                        <a class="button" href="<?= UriFactory::build('{/base}/purchase/bill/create?supplier=' . $supplier->id); ?>"><?= $this->getHtml('CreateBill'); ?></a>
                        <?php endif; ?>
                        <?php if ($this->data['hasAccounting'] ?? false) : ?>
                            <a class="button" href="<?= UriFactory::build('{/base}/accounting/account?number=' . $supplier->number); ?>"><?= $this->getHtml('ViewAccount'); ?></a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <section class="portlet">
                        <form id="supplierForm"
                            method="<?= $isNew ? 'PUT' : 'POST'; ?>"
                            action="<?= UriFactory::build('{/api}supplier?csrf={$CSRF}'); ?>"
                            <?= $isNew ? 'data-redirect="' . UriFactory::build('{/base}/purchase/supplier/view') . '?id={/0/response/id}"' : ''; ?>>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iId"><?= $this->getHtml('ID', '0', '0'); ?></label>
                                    <span class="input"><button type="button" formaction=""><i class="g-icon">book</i></button><input type="text" id="iId" min="1" name="id" value="<?= $this->printHtml($supplier->number); ?>"<?= $isNew ? ' required' : ' disabled'; ?>></span>
                                </div>

                                <div class="form-group">
                                    <label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                                    <select id="iStatus" name="status">
                                        <?php foreach ($supplierStatus as $status) : ?>
                                            <option value="<?= $status; ?>"<?= $supplier->status === $status ? ' selected': ''; ?>><?= $this->getHtml(':status-' . $status); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iName1"><?= $this->getHtml('Name1'); ?></label>
                                    <input type="text" id="iName1" name="name1" value="<?= $this->printHtml($supplier->account->name1); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="iName2"><?= $this->getHtml('Name2'); ?></label>
                                    <input type="text" id="iName2" name="name2" value="<?= $this->printHtml($supplier->account->name2); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="iName3"><?= $this->getHtml('Name3'); ?></label>
                                    <input type="text" id="iName3" name="name3" value="<?= $this->printHtml($supplier->account->name3); ?>">
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <?php if ($isNew) : ?>
                                    <input type="submit" value="<?= $this->getHtml('Create', '0', '0'); ?>" name="create-supplier">
                                <?php else : ?>
                                    <input type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>" name="save-supplier-profile">
                                    <input class="cancel end-xs" type="submit" value="<?= $this->getHtml('Delete', '0', '0'); ?>" name="delete-supplier-profile">
                                <?php endif; ?>
                            </div>
                        </form>
                    </section>

                    <section class="portlet">
                        <div class="portlet-head">
                            <?= $this->getHtml('Contact'); ?>
                            <a class="end-xs" href=""><i class="g-icon btn">mail</i></a>
                        </div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iPhone"><?= $this->getHtml('Phone'); ?></label>
                                <input type="text" id="iPhone" form="supplierForm" name="phone" value="<?= $this->printHtml($supplier->account->getContactByType(ContactType::PHONE)->content); ?>">
                            </div>

                            <div class="form-group">
                                <label for="iEmail"><?= $this->getHtml('Email'); ?></label>
                                <input type="text" id="iEmail" form="supplierForm" name="email" value="<?= $this->printHtml($supplier->account->getContactByType(ContactType::EMAIL)->content); ?>">
                            </div>

                            <div class="form-group">
                                <label for="iWebsite"><?= $this->getHtml('Website'); ?></label>
                                <input type="text" id="iWebsite" form="supplierForm" name="website" value="<?= $this->printHtml($supplier->account->getContactByType(ContactType::WEBSITE)->content); ?>">
                            </div>
                        </div>
                    </section>

                    <section class="portlet map-small">
                        <div class="portlet-head">
                            <?= $this->getHtml('Address'); ?>
                            <span class="clickPopup end-xs">
                                <label for="addressDropdown"><i class="g-icon btn">print</i></label>
                                <input id="addressDropdown" name="addressDropdown" type="checkbox">
                                <div class="popup">
                                    <ul>
                                        <li>
                                            <input id="id1" type="checkbox">
                                            <ul>
                                                <li>
                                                    <label for="id1">
                                                        <a href="" class="button">Word</a>
                                                        <span></span>
                                                        <i class="g-icon expand">chevron_right</i>
                                                    </label>
                                                <li>Letter
                                            </ul>
                                        <li><label class="button cancel" for="addressDropdown">Cancel</label>
                                    </ul>
                                </div>
                            </span>
                        </div>
                        <div class="portlet-body">
                            <?php if (!empty($supplier->mainAddress->fao)) : ?>
                            <div class="form-group">
                                <label for="iFAO"><?= $this->getHtml('FAO'); ?></label>
                                <input type="text" id="iFAO" form="supplierForm" name="fao" value="<?= $this->printHtml($supplier->mainAddress->fao); ?>">
                            </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="iAddress"><?= $this->getHtml('Address'); ?></label>
                                <input type="text" id="iAddress" form="supplierForm" name="address" value="<?= $this->printHtml($supplier->mainAddress->address); ?>" required>
                            </div>

                            <?php if (!empty($supplier->mainAddress->addressAddition)) : ?>
                            <div class="form-group">
                                <label for="iAddition"><?= $this->getHtml('Addition'); ?></label>
                                <input type="text" id="iAddition" form="supplierForm" name="addition" value="<?= $this->printHtml($supplier->mainAddress->addressAddition); ?>">
                            </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="iPostal"><?= $this->getHtml('Postal'); ?></label>
                                <input type="text" id="iPostal" form="supplierForm" name="postal" value="<?= $this->printHtml($supplier->mainAddress->postal); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="iCity"><?= $this->getHtml('City'); ?></label>
                                <input type="text" id="iCity" form="supplierForm" name="city" value="<?= $this->printHtml($supplier->mainAddress->city); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="iCountry"><?= $this->getHtml('Country'); ?></label>
                                <select id="iCountry" form="supplierForm" name="country">
                                    <?php foreach ($countryCodes as $code3 => $code2) : ?>
                                    <option value="<?= $this->printHtml($code2); ?>"<?= $this->printHtml($code2 === $supplier->mainAddress->country ? ' selected' : ''); ?>><?= $this->printHtml($countries[$code3]); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if (!$isNew) : ?>
                            <div class="form-group">
                                <label for="iSupplierMap"><?= $this->getHtml('Map'); ?></label>
                                <div id="iSupplierMap" class="map" data-lat="<?= $supplier->mainAddress->lat; ?>" data-lon="<?= $supplier->mainAddress->lon; ?>"></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <?php if (!$isNew) : ?>
                    <section class="portlet">
                        <div class="portlet-body">
                            <img alt="<?= $this->printHtml($supplierImage->name); ?>" width="100%" loading="lazy" class="item-image"
                                src="<?= $supplierImage->id === 0
                                    ? 'Web/Backend/img/logo_grey.png'
                                    : UriFactory::build($supplierImage->getPath()); ?>">
                        </div>
                    </section>

                    <section class="portlet hl-4">
                        <div class="portlet-body">
                            <textarea class="undecorated"><?= $this->printTextarea($supplier->info); ?></textarea>
                        </div>
                    </section>
                    <?php endif; ?>
                </div>

                <?php if (!$isNew) : ?>
                <div class="col-xs-12 col-lg-9 plain-grid">
                    <?php if (!empty($supplier->notes) && ($warning = $supplier->getEditorDocByTypeName('supplier_backend_warning'))->id !== 0) : ?>
                    <!-- If note warning exists -->
                    <div class="row">
                        <div class="col-xs-12">
                            <section class="portlet hl-1">
                                <div class="portlet-body"><?= $this->printHtml($warning->plain); ?></div>
                            </section>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($this->data['hasBilling'] ?? false) : ?>
                    <div class="row">
                        <div class="col-xs-12 col-lg-4">
                            <section class="portlet hl-7">
                                <div class="portlet-body">
                                    <table class="wf-100">
                                        <tr><td><?= $this->getHtml('YTDSales'); ?>:
                                            <td><?= PurchaseBillMapper::getSupplierNetSales($supplier->id, SmartDateTime::startOfYear($this->data['business_start']), new \DateTime('now'))->getAmount(); ?>
                                        <tr><td><?= $this->getHtml('MTDSales'); ?>:
                                            <td><?= PurchaseBillMapper::getSupplierNetSales($supplier->id, SmartDateTime::startOfMonth(), new \DateTime('now'))->getAmount(); ?>
                                        <tr><td><?= $this->getHtml('SLV'); ?>:
                                            <td><?= PurchaseBillMapper::getSLVHistoric($supplier->id)->getAmount(); ?>
                                    </table>
                                </div>
                            </section>
                        </div>

                        <div class="col-xs-12 col-lg-4">
                            <section class="portlet hl-2">
                                <div class="portlet-body">
                                    <table class="wf-100">
                                        <tr><td><?= $this->getHtml('LastContact'); ?>:
                                            <td><?= PurchaseBillMapper::getSupplierLastOrder($supplier->id)?->format('Y-m-d'); ?>
                                        <tr><td><?= $this->getHtml('LastOrder'); ?>:
                                            <td><?= PurchaseBillMapper::getSupplierLastOrder($supplier->id)?->format('Y-m-d'); ?>
                                        <tr><td><?= $this->getHtml('Created'); ?>:
                                            <td><?= $supplier->createdAt->format('Y-m-d H:i'); ?>
                                    </table>
                                </div>
                            </section>
                        </div>

                        <div class="col-xs-12 col-lg-4">
                            <section class="portlet hl-3">
                                <div class="portlet-body">
                                    <table class="wf-100">
                                        <tr><td><?= $this->getHtml('DSO'); ?>:
                                            <td>TBD
                                        <tr><td><?= $this->getHtml('Due'); ?>:
                                            <td>TBD
                                        <tr><td><?= $this->getHtml('Balance'); ?>:
                                            <td>TBD
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <section class="portlet">
                                <div class="portlet-head"><?= $this->getHtml('Notes'); ?></div>
                                <div class="slider">
                                <table id="iNotesItemList" class="default sticky">
                                    <thead>
                                    <tr>
                                        <td class="wf-100"><?= $this->getHtml('Title'); ?>
                                        <td><?= $this->getHtml('CreatedAt'); ?>
                                    <tbody>
                                    <?php
                                    $count = 0;
                                    foreach ($supplier->notes as $note) :
                                        ++$count;
                                        $url = UriFactory::build('{/base}/editor/view?{?}&id=' . $note->id);
                                    ?>
                                    <tr data-href="<?= $url; ?>">
                                        <td><a href="<?= $url; ?>"><?= $note->title; ?></a>
                                        <td><a href="<?= $url; ?>"><?= $note->createdAt->format('Y-m-d'); ?></a>
                                    <?php endforeach; ?>
                                    <?php if ($count === 0) : ?>
                                    <tr><td colspan="3" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                    <?php endif; ?>
                                </table>
                                </div>
                            </section>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <section class="portlet">
                                <div class="portlet-head"><?= $this->getHtml('Documents'); ?></div>
                                <div class="slider">
                                <table id="iFilesSupplierList" class="default sticky">
                                    <thead>
                                    <tr>
                                        <td class="wf-100"><?= $this->getHtml('Title'); ?>
                                        <td>
                                        <td><?= $this->getHtml('CreatedAt'); ?>
                                    <tbody>
                                    <?php
                                    $count = 0;
                                    foreach ($supplier->files as $file) :
                                        ++$count;
                                        $url           = UriFactory::build('{/base}/media/view?{?}&id=' . $file->id);
                                        $extensionType = FileUtils::getExtensionType($file->extension);
                                    ?>
                                    <tr data-href="<?= $url; ?>"
                                        <?= \in_array($extensionType, [ExtensionType::IMAGE, ExtensionType::PDF]) ? 'data-preview="' . UriFactory::build('{/api}media/export?id=' . $file->id . '&type=html&csrf={$CSRF}') . '"' : ''; ?>>
                                        <td><a href="<?= $url; ?>"><?= $file->name; ?></a>
                                        <td><a href="<?= $url; ?>"><?= $file->extension; ?></a>
                                        <td><a href="<?= $url; ?>"><?= $file->createdAt->format('Y-m-d'); ?></a>
                                    <?php endforeach; ?>
                                    <?php if ($count === 0) : ?>
                                    <tr><td colspan="3" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                    <?php endif; ?>
                                </table>
                                </div>
                            </section>
                        </div>
                    </div>

                    <?php if ($this->data['hasBilling'] ?? false) : ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <section class="portlet">
                                <div class="portlet-head"><?= $this->getHtml('RecentInvoices'); ?></div>
                                <table id="iSalesItemList" class="default sticky">
                                    <thead>
                                    <tr>
                                        <td><?= $this->getHtml('Number'); ?>
                                        <td><?= $this->getHtml('Type'); ?>
                                        <td class="wf-100"><?= $this->getHtml('Name'); ?>
                                        <td><?= $this->getHtml('Net'); ?>
                                        <td><?= $this->getHtml('Date'); ?>
                                    <tbody>
                                    <?php
                                    $newestInvoices = PurchaseBillMapper::getAll()
                                        ->with('type')
                                        ->with('type/l11n')
                                        ->with('supplier')
                                        ->where('supplier', $supplier->id)
                                        ->where('type/l11n/language', $this->response->header->l11n->language)
                                        ->sort('id', OrderType::DESC)
                                        ->limit(5)
                                        ->executeGetArray();

                                    $count = 0;

                                    /** @var \Modules\Billing\Models\Bill $invoice */
                                    foreach ($newestInvoices as $invoice) :
                                        ++$count;
                                        $url         = UriFactory::build('{/base}/purchase/bill/view?{?}&id=' . $invoice->id);
                                        $supplierUrl = UriFactory::build('{/base}/purchase/supplier/view?{?}&id=' . $invoice->supplier->id);
                                    ?>
                                    <tr data-href="<?= $url; ?>">
                                        <td><a href="<?= $url; ?>"><?= $invoice->getNumber(); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $invoice->type->getL11n(); ?></a>
                                        <td><a class="content" href="<?= $supplierUrl; ?>"><?= $invoice->billTo; ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->getCurrency($invoice->netSales, symbol: ''); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $invoice->performanceDate->format('Y-m-d'); ?></a>
                                    <?php endforeach; ?>
                                    <?php if ($count === 0) : ?>
                                    <tr><td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                    <?php endif; ?>
                                </table>
                            </section>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($this->data['hasBilling'] ?? false) :
                        $monthlySalesCosts = PurchaseBillMapper::getSupplierMonthlySalesCosts($supplier->id, (new SmartDateTime('now'))->createModify(-1), new SmartDateTime('now'));

                        // @bug I don't think this is correct.
                        //      The margin requires to calculate the sales for all items owned by the supplier
                        //      Even that is not 100% correct and more complicated due to possible supplier switches
                        if (!empty($monthlySalesCosts)) :
                    ?>
                    <div class="row">
                        <?php $segmentSales = PurchaseBillMapper::getSupplierAttributeNetSales($supplier->id, 'segment', 'en', (new SmartDateTime('now'))->createModify(-1), new SmartDateTime('now')); ?>
                        <div class="col-xs-12 col-md-6">
                            <section class="portlet">
                                <div class="portlet-head"><?= $this->getHtml('Segments'); ?></div>
                                <div class="portlet-body">
                                <div style="position: relative; width: 100%; height: 100%; aspect-ratio: 2;">
                                    <canvas id="sales-region" data-chart='{
                                            "type": "bar",
                                            "data": {
                                                "labels": [
                                                    <?php
                                                        $temp = [];
                                                        foreach ($segmentSales as $segment) {
                                                            $temp[] = $segment['title'];
                                                        }
                                                    ?>
                                                    <?= '"' . \implode('", "', $temp) . '"'; ?>
                                                ],
                                                "datasets": [
                                                    {
                                                        "label": "<?= $this->getHtml('Purchase'); ?>",
                                                        "type": "bar",
                                                        "data": [
                                                            <?php
                                                                $temp = [];
                                                                foreach ($segmentSales as $segment) {
                                                                    $temp[] = (float) (((int) $segment['net_sales']) / FloatInt::DIVISOR);
                                                                }
                                                            ?>
                                                            <?= \implode(',', $temp); ?>
                                                        ],
                                                        "yAxisID": "axis1",
                                                        "backgroundColor": "rgb(54, 162, 235)"
                                                    }
                                                ]
                                            },
                                            "options": {
                                                "responsive": true,
                                                "scales": {
                                                    "axis1": {
                                                        "id": "axis1",
                                                        "display": true,
                                                        "position": "left",
                                                        "ticks": {
                                                            "precision": 0
                                                        }
                                                    }
                                                },
                                                "plugins": {
                                                    "legend": {
                                                        "display": false
                                                    }
                                                }
                                            }
                                    }'></canvas>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div class="col-xs-12 col-lg-6">
                            <section class="portlet">
                                <div class="portlet-head"><?= $this->getHtml('Purchase'); ?></div>
                                <div class="portlet-body">
                                    <div style="position: relative; width: 100%; height: 100%; aspect-ratio: 2;">
                                    <canvas id="sales-region" data-chart='{
                                            "type": "bar",
                                            "data": {
                                                "labels": [
                                                    <?php
                                                        $temp = [];
                                                        foreach ($monthlySalesCosts as $monthly) {
                                                            $temp[] = $monthly['month'] . '/' . \substr((string) $monthly['year'], -2);
                                                        }
                                                    ?>
                                                    <?= '"' . \implode('", "', $temp) . '"'; ?>
                                                ],
                                                "datasets": [
                                                    {
                                                        "label": "<?= $this->getHtml('Margin'); ?>",
                                                        "type": "line",
                                                        "data": [
                                                            <?php
                                                                $temp = [];
                                                                foreach ($monthlySalesCosts as $monthly) {
                                                                    $temp[] = \round(((int) $monthly['net_sales']) === 0 ? 0 : ((((int) $monthly['net_sales']) - ((int) $monthly['net_costs'])) / ((int) $monthly['net_sales'])) * 100, 2);
                                                                }
                                                            ?>
                                                            <?= \implode(',', $temp); ?>
                                                        ],
                                                        "yAxisID": "axis2",
                                                        "fill": false,
                                                        "borderColor": "rgb(255, 99, 132)",
                                                        "backgroundColor": "rgb(255, 99, 132)"
                                                    },
                                                    {
                                                        "label": "<?= $this->getHtml('Purchase'); ?>",
                                                        "type": "bar",
                                                        "data": [
                                                            <?php
                                                                $temp = [];
                                                                foreach ($monthlySalesCosts as $monthly) {
                                                                    $temp[] = (float) (((int) $monthly['net_sales']) / FloatInt::DIVISOR);
                                                                }
                                                            ?>
                                                            <?= \implode(',', $temp); ?>
                                                        ],
                                                        "yAxisID": "axis1",
                                                        "backgroundColor": "rgb(54, 162, 235)"
                                                    }
                                                ]
                                            },
                                            "options": {
                                                "responsive": true,
                                                "scales": {
                                                    "axis1": {
                                                        "id": "axis1",
                                                        "display": true,
                                                        "position": "left",
                                                        "ticks": {
                                                            "precision": 0
                                                        }
                                                    },
                                                    "axis2": {
                                                        "id": "axis2",
                                                        "display": true,
                                                        "position": "right",
                                                        "title": {
                                                            "display": true,
                                                            "text": "<?= $this->getHtml('Margin'); ?> %"
                                                        },
                                                        "grid": {
                                                            "display": false
                                                        },
                                                        "beginAtZero": true,
                                                        "ticks": {
                                                            "min": 0,
                                                            "max": 100,
                                                            "stepSize": 10
                                                        }
                                                    }
                                                }
                                            }
                                    }'></canvas>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!$isNew) : ?>
        <input type="radio" id="c-tab-2" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-2' ? ' checked' : ''; ?>>
        <div class="tab">
            <?= $this->data['contact-component']->render('supplier-contact', 'contacts', $supplier->account->contacts); ?>
            <?= $this->data['address-component']->render('supplier-address', 'addresses', $supplier->account->addresses); ?>
        </div>

        <input type="radio" id="c-tab-3" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-3' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6 col-lg-4">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Payment'); ?></div>
                        <div class="portlet-body">
                            <form>
                                <table class="layout wf-100">
                                    <tr><td><label for="iACType"><?= $this->getHtml('Type'); ?></label>
                                    <tr><td><select id="iACType" name="actype">
                                                <option><?= $this->getHtml('Wire'); ?>
                                                <option><?= $this->getHtml('Creditcard'); ?>
                                            </select>
                                    <tr><td colspan="2"><input type="submit" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                                </table>
                            </form>
                        </div>
                    </section>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6 col-lg-4">
                    <section class="portlet">
                        <form>
                            <div class="portlet-head"><?= $this->getHtml('PaymentTerm'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iSource"><?= $this->getHtml('ID', '0', '0'); ?></label>
                                    <span class="input"><button type="button" formaction=""><i class="g-icon">book</i></button><input id="iSource" name="source" type="text"></span>
                                </div>

                                <div class="form-group">
                                    <label for="iSegment"><?= $this->getHtml('Segment'); ?></label>
                                    <input id="iSegment" name="segment" type="text">
                                </div>

                                <div class="form-group">
                                    <label for="iProductgroup"><?= $this->getHtml('Productgroup'); ?></label>
                                    <input id="iProductgroup" name="productgroup" type="text">
                                </div>

                                <div class="form-group">
                                    <label for="iGroup"><?= $this->getHtml('Group'); ?></label>
                                    <input id="iGroup" name="group" type="text">
                                </div>

                                <div class="form-group">
                                    <label for="iArticlegroup"><?= $this->getHtml('Articlegroup'); ?></label>
                                    <input id="iArticlegroup" name="articlegroup" type="text">
                                </div>

                                <div class="form-group">
                                    <label for="iTerm"><?= $this->getHtml('Type'); ?></label>
                                    <select id="iTerm" name="term" required>
                                                <option>
                                            </select>
                                </div>
                                <div class="form-group">
                                    <span class="check"><input type="checkbox" id="iFreightage" name="freightage"><label for="iFreightage"><?= $this->getHtml('Freightage'); ?></label></span>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input type="submit" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-4" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-4' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="supplierSalesPriceForm" action="<?= UriFactory::build('{/api}bill/price?csrf={$CSRF}'); ?>" method="post"
                            data-ui-container="#supplierSalesPriceTable tbody"
                            data-add-form="supplierSalesPriceForm"
                            data-add-tpl="#supplierSalesPriceTable tbody .oms-add-tpl-supplierSalesPrice">
                            <div class="portlet-head"><?= $this->getHtml('Pricing'); ?></div>
                            <div class="portlet-body">
                                <input id="iPriceId" class="vh" name="id" type="number" data-tpl-text="/id" data-tpl-value="/id">
                                <input id="iPriceSupplierId" class="vh" name="supplier" type="text" value="<?= $supplier->id; ?>">
                                <input id="iPriceItemType" class="vh" name="type" type="text" value="<?= PriceType::PURCHASE; ?>">

                                <div class="form-group">
                                    <label for="iPriceName"><?= $this->getHtml('Name'); ?></label>
                                    <input id="iPriceName" name="name" type="text" data-tpl-text="/name" data-tpl-value="/name">
                                </div>
                            </div>
                            <div class="portlet-separator"></div>
                            <div class="portlet-body">
                                <div class="flex-line">
                                    <div>
                                        <div class="form-group">
                                            <label for="iPricePrice"><?= $this->getHtml('Price'); ?></label>
                                            <div class="flex-line wf-100">
                                                <div class="fixed">
                                                    <select id="iPriceCurrency" name="currency" data-tpl-text="/currency" data-tpl-value="/currency">
                                                        <?php foreach ($currencies as $currency) : ?>
                                                        <option value="<?= $currency; ?>"<?= $this->data['attributeView']->data['default_localization']->currency === $currency ? ' selected' : ''; ?>><?= $this->printHtml($currency); ?>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div>
                                                    <input id="iPricePrice" class="wf-100" name="price_new" type="number" step="any" data-tpl-text="/price" data-tpl-value="/price">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="form-group">
                                            <label for="iPriceQuantity"><?= $this->getHtml('Quantity'); ?></label>
                                            <input id="iPriceQuantity" name="quantity" type="number" step="any" data-tpl-text="/quantity" data-tpl-value="/quantity">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-line">
                                    <div>
                                        <div class="form-group">
                                            <label for="iPriceDiscount"><?= $this->getHtml('Discount'); ?></label>
                                            <input id="iPriceDiscount" name="discount" type="number" step="any" data-tpl-text="/discount" data-tpl-value="/discount">
                                        </div>
                                    </div>

                                    <div>
                                        <div class="form-group">
                                            <label for="iPriceDiscountR"><?= $this->getHtml('DiscountP'); ?></label>
                                            <input id="iPriceDiscountR" name="discountPercentage" type="number" step="any" data-tpl-text="/discountr" data-tpl-value="/discountr">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="iPriceBonus"><?= $this->getHtml('Bonus'); ?></label>
                                    <input id="iPriceBonus" name="bonus" type="number" step="any" data-tpl-text="/bonus" data-tpl-value="/bonus">
                                </div>
                            </div>
                            <div class="portlet-separator"></div>
                            <div class="portlet-body">
                                <div class="flex-line">
                                    <div>
                                        <div class="form-group">
                                            <label for="iPriceItemItem"><?= $this->getHtml('Item'); ?></label>
                                            <input id="iPriceItemItem" name="item" type="text" data-tpl-text="/item_item" data-tpl-value="/item_item">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-separator"></div>
                            <div class="portlet-body">
                                <div class="flex-line">
                                    <div>
                                        <div class="form-group">
                                            <label for="iPriceItemStart"><?= $this->getHtml('Start'); ?></label>
                                            <input id="iPriceItemStart" name="start" type="datetime-local" data-tpl-text="/item_start" data-tpl-value="/item_start">
                                        </div>
                                    </div>

                                    <div>
                                        <div class="form-group">
                                            <label for="iPriceItemEnd"><?= $this->getHtml('End'); ?></label>
                                            <input id="iPriceItemEnd" name="end" type="datetime-local" data-tpl-text="/item_end" data-tpl-value="/item_end">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input id="bPriceItemAdd" formmethod="put" type="submit" class="add-form" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                                <input id="bPriceItemSave" formmethod="post" type="submit" class="save-form vh button save" value="<?= $this->getHtml('Update', '0', '0'); ?>">
                                <input id="bPriceItemCancel" type="submit" class="cancel-form vh button close" value="<?= $this->getHtml('Cancel', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Prices'); ?><i class="g-icon download btn end-xs">download</i></div>
                        <div class="slider">
                        <table id="supplierSalesPriceTable" class="default sticky"
                            data-tag="form"
                            data-ui-element="tr"
                            data-add-tpl=".oms-add-tpl-supplierSalesPrice"
                            data-update-form="supplierSalesPriceForm">
                            <thead>
                                <tr>
                                    <td>
                                    <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                    <td><?= $this->getHtml('Name'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                    <td><?= $this->getHtml('Price'); ?>
                                    <td>
                                    <td><?= $this->getHtml('Quantity'); ?>
                                    <td><?= $this->getHtml('Discount'); ?>
                                    <td><?= $this->getHtml('DiscountP'); ?>
                                    <td><?= $this->getHtml('Bonus'); ?>
                                    <td><?= $this->getHtml('Item'); ?>
                                    <td><?= $this->getHtml('Start'); ?>
                                    <td><?= $this->getHtml('End'); ?>
                            <tbody>
                                <template class="oms-add-tpl-supplierSalesPrice">
                                    <tr class="animated medium-duration greenCircleFade" data-id="" draggable="false">
                                        <td>
                                            <i class="g-icon btn update-form">settings</i>
                                            <input id="supplierSalesPriceTable-remove-0" type="checkbox" class="vh">
                                            <label for="supplierSalesPriceTable-remove-0" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                            <span class="checked-visibility">
                                                <label for="supplierSalesPriceTable-remove-0" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                                <label for="supplierSalesPriceTable-remove-0" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                            </span>
                                        <td data-tpl-text="/id" data-tpl-value="/id"></td>
                                        <td data-tpl-text="/name" data-tpl-value="/name" data-value=""></td>
                                        <td data-tpl-text="/price" data-tpl-value="/price"></td>
                                        <td data-tpl-text="/currency" data-tpl-value="/currency"></td>
                                        <td data-tpl-text="/quantity" data-tpl-value="/quantity"></td>
                                        <td data-tpl-text="/discount" data-tpl-value="/discount"></td>
                                        <td data-tpl-text="/discountr" data-tpl-value="/discountr"></td>
                                        <td data-tpl-text="/bonus" data-tpl-value="/bonus"></td>
                                        <td data-tpl-text="/item_item" data-tpl-value="/item_item"></td>
                                        <td data-tpl-text="/item_start" data-tpl-value="/item_start"></td>
                                        <td data-tpl-text="/item_end" data-tpl-value="/item_end"></td>
                                    </tr>
                                </template>
                                <?php
                                $c      = 0;
                                $prices = $this->data['prices'];
                                foreach ($prices as $key => $value) : ++$c;
                                ?>
                                    <tr data-id="<?= $value->id; ?>">
                                        <td>
                                            <i class="g-icon btn update-form">settings</i>
                                            <?php if ($value->name !== 'default') : ?>
                                            <input id="supplierSalesPriceTable-remove-<?= $value->id; ?>" type="checkbox" class="vh">
                                            <label for="supplierSalesPriceTable-remove-<?= $value->id; ?>" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                            <span class="checked-visibility">
                                                <label for="supplierSalesPriceTable-remove-<?= $value->id; ?>" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                                <label for="supplierSalesPriceTable-remove-<?= $value->id; ?>" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                            </span>
                                            <?php endif; ?>
                                        <td data-tpl-text="/id" data-tpl-value="/id"><?= $value->id; ?>
                                        <td data-tpl-text="/name" data-tpl-value="/name"><?= $this->printHtml($value->name); ?>
                                        <td data-tpl-text="/price" data-tpl-value="/price"><?= $value->price->getAmount(); ?>
                                        <td data-tpl-text="/currency" data-tpl-value="/currency"><?= $this->printHtml($value->currency); ?>
                                        <td data-tpl-text="/quantity" data-tpl-value="/quantity"><?= $value->quantity->getAmount(); ?>
                                        <td data-tpl-text="/discount" data-tpl-value="/discount"><?= $value->discount->getAmount(); ?>
                                        <td data-tpl-text="/discountr" data-tpl-value="/discountr"><?= $this->getPercentage($value->discountPercentage); ?>
                                        <td data-tpl-text="/bonus" data-tpl-value="/bonus"><?= $value->bonus->getAmount(); ?>
                                        <td data-tpl-text="/item_item" data-tpl-value="/item_item"><?= $this->printHtml((string) $value->item->id); ?>
                                        <td data-tpl-text="/item_start" data-tpl-value="/item_start"><?= $value->start?->format('Y-m-d'); ?>
                                        <td data-tpl-text="/item_end" data-tpl-value="/item_end"><?= $value->end?->format('Y-m-d'); ?>
                                <?php endforeach; ?>
                                <?php if ($c === 0) : ?>
                                    <tr>
                                        <td colspan="23" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                <?php endif; ?>
                        </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-5" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-5' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <?= $this->data['attributeView']->render(
                    $supplier->attributes,
                    $this->data['attributeTypes'] ?? [],
                    $this->data['units'] ?? [],
                    '{/api}supplier/attribute?csrf={$CSRF}',
                    $supplier->id
                );
                ?>
            </div>
        </div>

        <input type="radio" id="c-tab-8" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-8' ? ' checked' : ''; ?>>
        <div class="tab col-simple">
            <?= $this->data['media-upload']->render('supplier-file', 'files', '', $supplier->files, '{/api}supplier/file?csrf={$CSRF}', (string) $item->id); ?>
        </div>

        <input type="radio" id="c-tab-9" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-9' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('RecentInvoices'); ?></div>
                        <div class="slider">
                        <table id="iSalesItemList" class="default sticky">
                            <thead>
                            <tr>
                                <td><?= $this->getHtml('Number'); ?>
                                <td><?= $this->getHtml('Type'); ?>
                                <td class="wf-100"><?= $this->getHtml('Name'); ?>
                                <td><?= $this->getHtml('Net'); ?>
                                <td><?= $this->getHtml('Date'); ?>
                                <td><?= $this->getHtml('Created'); ?>
                            <tbody>
                            <?php
                            $allInvoices = BillMapper::getAll()
                                ->with('type')
                                ->with('type/l11n')
                                ->where('supplier', $supplier->id)
                                ->where('type/l11n/language', $this->response->header->l11n->language)
                                ->where('billDate', SmartDateTime::startOfYear($this->data['business_start']), '>=')
                                ->executeGetArray();

                            $count = 0;
                            /** @var \Modules\Billing\Models\Bill $invoice */
                            foreach ($allInvoices as $invoice) :
                                ++$count;
                                $url = UriFactory::build('{/base}/sales/bill/view?{?}&id=' . $invoice->id);
                                ?>
                            <tr data-href="<?= $url; ?>">
                                <td><a href="<?= $url; ?>"><?= $invoice->getNumber(); ?></a>
                                <td><a href="<?= $url; ?>"><?= $invoice->type->getL11n(); ?></a>
                                <td><a href="<?= $url; ?>"><?= $invoice->billTo; ?></a>
                                <td><a href="<?= $url; ?>"><?= $this->getCurrency($invoice->netSales, symbol: ''); ?></a>
                                <td><a href="<?= $url; ?>"><?= $invoice->performanceDate->format('Y-m-d'); ?></a>
                                <td><a href="<?= $url; ?>"><?= $invoice->createdAt->format('Y-m-d'); ?></a>
                            <?php endforeach; ?>
                            <?php if ($count === 0) : ?>
                            <tr><td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                            <?php endif; ?>
                        </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-6" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-6' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="itemAccounting" action="<?= UriFactory::build('{/api}item/accounting?csrf={$CSRF}'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Accounting'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iSupplierAccountingAccount"><?= $this->getHtml('Account'); ?></label>
                                    <input type="text" name="account" value="<?= $this->printHtml(\Modules\Accounting\Models\AccountAbstractMapper::get()->where('account', $supplier->account->id)->execute()->code); ?>" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="iSupplierCostIndicator"><?= $this->getHtml('CostIndicator'); ?></label>
                                    <select id="iSupplierCostIndicator" name="costindicator">
                                        <option>
                                        <?php
                                            $attr = $this->data['supplierSegmentationTypes']['sales_tax_code'] ?? null;
                                            foreach ($attr?->defaults ?? [] as $value) : ?>
                                            <option value="<?= $value->id; ?>"><?= $this->printHtml((string) $value->getValue()); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-10" name="tabular-2" checked>
        <div class="tab col-simple">
            <?php $billElements = PurchaseBillMapper::getSupplierItem($supplier->id, SmartDateTime::startOfYear($this->data['business_start']), new SmartDateTime('now')); ?>
            <div class="row">
                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Items'); ?><i class="g-icon download btn end-xs">download</i></div>
                        <div class="slider">
                        <table id="iSalesItemList" class="default sticky">
                            <thead>
                            <tr>
                                <td><label class="checkbox" for="iSalesItemSelect-">
                                        <input type="checkbox" id="iSalesItemSelect-" name="itemselect">
                                        <span class="checkmark"></span>
                                    </label>
                                <td><?= $this->getHtml('Date'); ?>
                                    <label for="iSalesItemList-sort-3">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-3">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="iSalesItemList-sort-4">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-4">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                                <td><?= $this->getHtml('Bill'); ?>
                                    <label for="iSalesItemList-sort-3">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-3">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="iSalesItemList-sort-4">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-4">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                                <td><?= $this->getHtml('ID', '0', '0'); ?>
                                    <label for="iSalesItemList-sort-1">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-1">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="iSalesItemList-sort-2">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-2">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                                <td class="wf-100"><?= $this->getHtml('Name'); ?>
                                    <label for="iSalesItemList-sort-3">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-3">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="iSalesItemList-sort-4">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-4">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                                <td><?= $this->getHtml('Quantity'); ?>
                                    <label for="iSalesItemList-sort-5">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-5">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="iSalesItemList-sort-6">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-6">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                                <td><?= $this->getHtml('UnitPrice'); ?>
                                    <label for="iSalesItemList-sort-7">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-7">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="iSalesItemList-sort-8">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-8">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                                <td><?= $this->getHtml('Discount'); ?>
                                    <label for="iSalesItemList-sort-11">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-11">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="iSalesItemList-sort-12">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-12">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                                <td><?= $this->getHtml('DiscountP'); ?>
                                    <label for="iSalesItemList-sort-13">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-13">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="iSalesItemList-sort-14">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-14">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                                <td><?= $this->getHtml('Bonus'); ?>
                                    <label for="iSalesItemList-sort-15">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-15">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="iSalesItemList-sort-16">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-16">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                                <td><?= $this->getHtml('TotalPrice'); ?>
                                    <label for="iSalesItemList-sort-9">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-9">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="iSalesItemList-sort-10">
                                        <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-10">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                            <tbody>
                            <?php
                                $count = 0;
                                foreach ($billElements as $key => $value) :
                                    ++$count;
                                    $url     = UriFactory::build('{/base}/sales/bill/view?{?}&id=' . $value->bill->id);
                                    $itemUrl = UriFactory::build('{/base}/sales/item/view?{?}&id=' . $value->item->id);
                            ?>
                            <tr data-href="<?= $url; ?>">
                                <td><label class="checkbox" for="iSalesItemSelect-<?= $key; ?>">
                                        <input type="checkbox" id="iSalesItemSelect-<?= $key; ?>" name="itemselect">
                                        <span class="checkmark"></span>
                                    </label>
                                <td><?= $value->bill->performanceDate?->format('Y-m-d'); ?>
                                <td><?= $this->printHtml($value->bill->getNumber()); ?>
                                <td><a class="content" href="<?= $itemUrl; ?>"><?= $this->printHtml($value->itemNumber); ?></a>
                                <td><a class="content" href="<?= $itemUrl; ?>"><?= $this->printHtml($value->itemName); ?></a>
                                <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->quantity->getAmount()); ?></a>
                                <td><a href="<?= $url; ?>"><?= $this->getCurrency($value->singleSalesPriceNet, symbol: ''); ?></a>
                                <td><a href="<?= $url; ?>"><?= $this->getCurrency($value->singleDiscountP, symbol: ''); ?></a>
                                <td><a href="<?= $url; ?>"><?= $this->getPercentage($value->singleDiscountR); ?></a>
                                <td><a href="<?= $url; ?>"><?= $this->getNumeric($value->discountQ?->value); ?></a>
                                <td><a href="<?= $url; ?>"><?= $this->getCurrency($value->totalSalesPriceNet, symbol: ''); ?></a>
                            <?php endforeach; ?>
                            <?php if ($count === 0) : ?>
                                <tr><td colspan="11" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                            <?php endif; ?>
                        </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-7" name="tabular-2" checked>
        <div class="tab col-simple">
            <?= $this->data['note']->render('supplier-note', 'notes', $supplier->notes, '{/api}supplier/note?csrf={$CSRF}', (string) $supplier->id); ?>
        </div>

        <?php if (!empty($logs)) : ?>
        <input type="radio" id="c-tab-17" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Logs', 'Auditor'); ?><i class="g-icon download btn end-xs">download</i></div>
                        <div class="slider">
                        <table class="default sticky">
                            <colgroup>
                                <col style="width: 75px">
                                <col style="width: 150px">
                                <col style="width: 100px">
                                <col>
                                <col>
                                <col style="width: 125px">
                                <col style="width: 75px">
                                <col style="width: 150px">
                            </colgroup>
                            <thead>
                            <tr>
                                <td><?= $this->getHtml('ID', '0', '0'); ?>
                                <td><?= $this->getHtml('Module', 'Auditor'); ?>
                                <td><?= $this->getHtml('Type', 'Auditor'); ?>
                                <td><?= $this->getHtml('Trigger', 'Auditor'); ?>
                                <td><?= $this->getHtml('Content', 'Auditor'); ?>
                                <td><?= $this->getHtml('By', 'Auditor'); ?>
                                <td><?= $this->getHtml('Ref', 'Auditor'); ?>
                                <td><?= $this->getHtml('Date', 'Auditor'); ?>
                            <tbody>
                            <?php
                                $count    = 0;
                                $previous = empty($logs) ? HttpHeader::getAllHeaders()['Referer'] ?? 'admin/module/settings?id={?id}#{\#}' : 'admin/module/settings?{?}&audit=' . \reset($logs)->id . '&ptype=p#{\#}';
                                $next     = empty($logs) ? HttpHeader::getAllHeaders()['Referer'] ?? 'admin/module/settings?id={?id}#{\#}' : 'admin/module/settings?{?}&audit=' . \end($logs)->id . '&ptype=n#{\#}';

                                foreach ($logs as $key => $audit) : ++$count;
                                    $url = UriFactory::build('{/base}/admin/audit/view?{?}&id=' . $audit->id); ?>
                                <tr tabindex="0" data-href="<?= $url; ?>">
                                    <td><?= $audit->id; ?>
                                    <td><?= $this->printHtml($audit->module); ?>
                                    <td><?= $audit->type; ?>
                                    <td><?= $this->printHtml($audit->trigger); ?>
                                    <td><?= $this->printHtml((string) $audit->content); ?>
                                    <td><?= $this->printHtml($audit->createdBy->login); ?>
                                    <td><?= $this->printHtml((string) $audit->ref); ?>
                                    <td><?= $audit->createdAt->format('Y-m-d H:i'); ?>
                            <?php endforeach; ?>
                            <?php if ($count === 0) : ?>
                                <tr><td colspan="8" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                            <?php endif; ?>
                        </table>
                        </div>
                        <!--
                        <div class="portlet-foot">
                            <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                            <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
                        </div>
                        -->
                    </section>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
