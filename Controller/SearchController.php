<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\SupplierManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SupplierManagement\Controller;

use Modules\Admin\Models\ContactType;
use Modules\Media\Models\MediaMapper;
use Modules\SupplierManagement\Models\SupplierMapper;
use Modules\Tag\Models\TagMapper;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\System\MimeType;

/**
 * Search class.
 *
 * @package Modules\SupplierManagement
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class SearchController extends Controller
{
    /**
     * Api method to search for tags
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function searchGeneral(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        $names   = \explode(' ', ($request->getDataString('search') ?? ''));
        $names[] = ($request->getDataString('search') ?? '');

        $mapper = SupplierMapper::getAll()
            ->with('account')
            ->with('mainAddress')
            ->with('account/contacts')
            ->limit(8);

        foreach ($names as $name) {
            $mapper->where('account/login', '%' . $name . '%', 'LIKE', 'OR')
                ->where('account/name1', '%' . $name . '%', 'LIKE', 'OR')
                ->where('account/name2', '%' . $name . '%', 'LIKE', 'OR')
                ->where('account/name3', '%' . $name . '%', 'LIKE', 'OR');
        }

        /** @var \Modules\SupplierManagement\Models\Supplier[] $accounts */
        $accounts = $mapper->execute();

        $results = [];
        foreach ($accounts as $account) {
            // Get item profile image
            // @feature Create a new read mapper function that returns relation models instead of its own model
            //      https://github.com/Karaka-Management/phpOMS/issues/320
            $query    = new Builder($this->app->dbPool->get());
            $iResults = $query->selectAs(SupplierMapper::HAS_MANY['files']['external'], 'file')
                ->from(SupplierMapper::TABLE)
                ->leftJoin(SupplierMapper::HAS_MANY['files']['table'])
                    ->on(SupplierMapper::HAS_MANY['files']['table'] . '.' . SupplierMapper::HAS_MANY['files']['self'], '=', SupplierMapper::TABLE . '.' . SupplierMapper::PRIMARYFIELD)
                ->leftJoin(MediaMapper::TABLE)
                    ->on(SupplierMapper::HAS_MANY['files']['table'] . '.' . SupplierMapper::HAS_MANY['files']['external'], '=', MediaMapper::TABLE . '.' . MediaMapper::PRIMARYFIELD)
                ->leftJoin(MediaMapper::HAS_MANY['tags']['table'])
                    ->on(MediaMapper::TABLE . '.' . MediaMapper::PRIMARYFIELD, '=', MediaMapper::HAS_MANY['tags']['table'] . '.' . MediaMapper::HAS_MANY['tags']['self'])
                ->leftJoin(TagMapper::TABLE)
                    ->on(MediaMapper::HAS_MANY['tags']['table'] . '.' . MediaMapper::HAS_MANY['tags']['external'], '=', TagMapper::TABLE . '.' . TagMapper::PRIMARYFIELD)
                ->where(SupplierMapper::HAS_MANY['files']['self'], '=', $account->id)
                ->where(TagMapper::TABLE . '.' . TagMapper::getColumnByMember('name'), '=', 'profile_image');

            $image = MediaMapper::get()
                ->where('id', $iResults)
                ->limit(1)
                ->execute();

            $results[] = [
                'title' => $account->account->name1 . ' ' . $account->account->name2,
                'link'  => '{/base}/purchase/supplier/view?id=' . $account->id,
                'email' => $account->account->getContactByType(ContactType::EMAIL)->content,
                'phone' => $account->account->getContactByType(ContactType::PHONE)->content,
                'city'  => $account->mainAddress->city,
                'image' => $image->id === 0
                    ? 'Web/Backend/img/logo_grey.png'
                    : $image->getPath(),
                'tags'   => [],
                'type'   => 'list_accounts',
                'module' => 'Supplier Management',
            ];
        }

        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);
        $response->add($request->uri->__toString(), $results);
    }
}
