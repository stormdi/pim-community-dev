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

    public function __construct(
        ValidatePagination $validatePagination
    ) {
        $this->validatePagination = $validatePagination;
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
    }
}
