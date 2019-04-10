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

    public function __construct(
        ValidatePagination $validatePagination,
        ValidateChannel $validateChannel
    ) {
        $this->validatePagination = $validatePagination;
        $this->validateChannel = $validateChannel;
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
        $this->validateChannel->validate($query->channel);
    }
}
