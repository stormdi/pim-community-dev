<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Component\Product\Connector\UseCase\Validator;

use Akeneo\Pim\Enrichment\Component\Product\Connector\UseCase\ListProductModelsQuery;
use Akeneo\Tool\Component\Api\Exception\InvalidQueryException;

/**
 * @author Pierre Allard <pierre.allard@akeneo.com>
 */
class ListProductModelsQueryValidator
{
    /** @var ValidatePagination */
    private $validatePagination;

    /** @var ValidateChannel */
    private $validateChannel;

    /** @var ValidateLocales */
    private $validateLocales;

    /** @var ValidateSearchLocale */
    private $validateSearchLocales;

    /** @var ValidateAttributes */
    private $validateAttributes;

    public function __construct(
        ValidatePagination $validatePagination,
        ValidateChannel $validateChannel,
        ValidateLocales $validateLocales,
        ValidateSearchLocale $validateSearchLocales,
        ValidateAttributes $validateAttributes
    ) {
        $this->validatePagination = $validatePagination;
        $this->validateChannel = $validateChannel;
        $this->validateLocales = $validateLocales;
        $this->validateSearchLocales = $validateSearchLocales;
        $this->validateAttributes = $validateAttributes;
    }

    /**
     * @throws InvalidQueryException
     */
    public function validate(ListProductModelsQuery $query): void
    {
        $this->validatePagination->validate(
            $query->paginationType,
            $query->page,
            $query->limit,
            $query->withCount
        );
        $this->validateAttributes->validate($query->attributes);
        $this->validateChannel->validate($query->channel);
        $this->validateLocales->validate($query->locales, $query->channel);
        $this->validateSearchLocales->validate($query->search, $query->searchLocale);
    }
}
