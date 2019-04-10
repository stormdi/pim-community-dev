<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Component\Product\Connector\UseCase;

use Akeneo\Tool\Component\Api\Pagination\PaginationTypes;

/**
 * @author    Pierre Allard <pierre.allard@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ListProductModelsQuery
{
    /** @var string */
    public $paginationType = PaginationTypes::OFFSET;

    /** @var int */
    public $page = 1;

    /** @var int */
    public $limit;

    /** @var string */
    public $withCount = 'false';

    /** @var string */
    public $channel;

    /** @var string */
    public $searchAfter;

    /** @var string[] */
    public $locales;

    /** @var array */
    public $search = [];

    /** @var string */
    public $searchLocale;

    /** @var string[] */
    public $attributes;

    /** @var string */
    public $searchScope;

    /**
     * Returns the parameter 'with_count' typed as a boolean
     *
     * @return bool
     */
    public function withCountAsBoolean(): bool
    {
        return $this->withCount === 'true';
    }
}
