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
    /** @var ValidateAttributes */
    private $validateAttributes;

    /** @var ValidateChannel */
    private $validateChannel;

    /** @var ValidateLocales */
    private $validateLocales;

    /** @var ValidatePagination */
    private $validatePagination;

    /** @var ValidateCriterion */
    private $validateCriterion;

    /** @var ValidateCategories */
    private $validateCategories;

    /** @var ValidateProperties */
    private $validateProperties;

    /** @var ValidateSearchLocale */
    private $validateSearchLocales;

    /** @var ValidateGrantedSearchLocaleInterface */
    private $validateGrantedSearchLocales;

    /** @var ValidateGrantedCategoriesInterface */
    private $validateGrantedCategories;

    /** @var ValidateGrantedPropertiesInterface */
    private $validateGrantedProperties;

    /** @var ValidateGrantedAttributesInterface */
    private $validateGrantedAttributes;

    /** @var ValidateGrantedLocalesInterface */
    private $validateGrantedLocales;

    public function __construct(
        ValidateAttributes $validateAttributes,
        ValidateChannel $validateChannel,
        ValidateLocales $validateLocales,
        ValidatePagination $validatePagination,
        ValidateCriterion $validateCriterion,
        ValidateCategories $validateCategories,
        ValidateProperties $validateProperties,
        ValidateSearchLocale $validateSearchLocales,
        ValidateGrantedSearchLocaleInterface $validateGrantedSearchLocales,
        ValidateGrantedCategoriesInterface $validateGrantedCategories,
        ValidateGrantedPropertiesInterface $validateGrantedProperties,
        ValidateGrantedAttributesInterface $validateGrantedAttributes,
        ValidateGrantedLocalesInterface $validateGrantedLocales
    ) {
        $this->validateAttributes = $validateAttributes;
        $this->validateChannel = $validateChannel;
        $this->validateLocales = $validateLocales;
        $this->validatePagination = $validatePagination;
        $this->validateCriterion = $validateCriterion;
        $this->validateCategories = $validateCategories;
        $this->validateProperties = $validateProperties;
        $this->validateSearchLocales = $validateSearchLocales;
        $this->validateGrantedSearchLocales = $validateGrantedSearchLocales;
        $this->validateGrantedCategories = $validateGrantedCategories;
        $this->validateGrantedProperties = $validateGrantedProperties;
        $this->validateGrantedAttributes = $validateGrantedAttributes;
        $this->validateGrantedLocales = $validateGrantedLocales;
    }

    /**
     * @param ListProductModelsQuery $query
     *
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
        $this->validateGrantedAttributes->validate($query->attributes);
        $this->validateChannel->validate($query->channel);
        $this->validateLocales->validate($query->locales, $query->channel);
        $this->validateCriterion->validate($query->search);
        $this->validateCategories->validate($query->search);
        $this->validateGrantedCategories->validate($query->search);
        $this->validateProperties->validate($query->search);
        $this->validateGrantedProperties->validate($query->search);
        $this->validateSearchLocales->validate($query->search, $query->searchLocale);
        $this->validateGrantedLocales->validateForLocaleCodes($query->locales);
        $this->validateGrantedSearchLocales->validate($query->search, $query->searchLocale);
    }
}
