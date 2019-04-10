<?php

declare(strict_types=1);

namespace AkeneoTest\Pim\Enrichment\Integration\Product\Query\Sql;

use Akeneo\Pim\Enrichment\Bundle\Product\Query\Sql\GetProductAssociationsByProductIdentifiers;
use Akeneo\Pim\Structure\Component\AttributeTypes;
use Akeneo\Test\Integration\TestCase;
use AkeneoTest\Pim\Enrichment\Integration\Fixture\EntityBuilder;
use Webmozart\Assert\Assert;

/**
 * @author    Anael Chardan <anael.chardan@akeneo.com>
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GetProductAssociationsByProductIdentifiersIntegration extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $entityBuilder = new EntityBuilder($this->testKernel->getContainer());
        $this->givenBooleanAttributes(['first_yes_no', 'second_yes_no']);
        $this->givenFamilies([['code' => 'aFamily', 'attribute_codes' => ['first_yes_no', 'second_yes_no']]]);
        $entityBuilder->createFamilyVariant(
            [
                'code' => 'familyVariantWithTwoLevels',
                'family' => 'aFamily',
                'variant_attribute_sets' => [
                    [
                        'level' => 1,
                        'axes' => ['first_yes_no'],
                        'attributes' => [],
                    ],
                    [
                        'level' => 2,
                        'axes' => ['second_yes_no'],
                        'attributes' => [],
                    ],
                ],
            ]
        );

        $entityBuilder->createProduct('productA', 'aFamily', []);
        $entityBuilder->createProduct('productB', 'aFamily', []);
        $entityBuilder->createProduct('productC', 'aFamily', $this->getAssociationsFormated([], [], [], ['productA']));
        $entityBuilder->createProduct('productD', 'aFamily', $this->getAssociationsFormated(['productA', 'productB'], ['productC']));
        $entityBuilder->createProduct('productE', 'aFamily', []);
        $entityBuilder->createProduct('productF', 'aFamily', []);
        $entityBuilder->createProduct('productG', 'aFamily', []);
        $rootProductModel = $entityBuilder->createProductModel('root_product_model', 'familyVariantWithTwoLevels', null, $this->getAssociationsFormated(['productF'], ['productA', 'productC']));
        $subProductModel1 = $entityBuilder->createProductModel('sub_product_model_1', 'familyVariantWithTwoLevels', $rootProductModel, $this->getAssociationsFormated(['productD'], [], ['productB']));
        $entityBuilder->createVariantProduct('variant_product_1', 'aFamily', 'familyVariantWithTwoLevels', $subProductModel1, $this->getAssociationsFormated([], ['productG'], [], ['productE']));
    }

    private function getAssociationsFormated(array $crossSell = [], array $pack = [], array $substitutions = [], array $upsell = [])
    {
        return ['associations' => [
            'X_SELL' => ['products' => $crossSell],
            'PACK' => ['products' => $pack],
            'SUBSTITUTION' => ['products' => $substitutions],
            'UPSELL' => ['products' => $upsell]
        ]];
    }

    public function testWithAProductContainingNoAssociation()
    {
        $expected = ['productE' => ['X_SELL' => [], 'PACK' => [], 'SUBSTITUTION' => [], 'UPSELL' => []]];
        $actual = $this->getQuery()->fetchByProductIdentifiers(['productE']);

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    public function testOnASingleProduct()
    {
        $expected = [
            'productD' => ['X_SELL' => ['productA', 'productB'], 'PACK' => ['productC'], 'SUBSTITUTION' => [], 'UPSELL' => []],
        ];
        $actual = $this->getQuery()->fetchByProductIdentifiers(['productD']);

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    public function testOnMultipleSimpleProduct()
    {
        $expected = [
            'productE' => ['X_SELL' => [], 'PACK' => [], 'SUBSTITUTION' => [], 'UPSELL' => []],
            'productD' => ['X_SELL' => ['productA', 'productB'], 'PACK' => ['productC'], 'SUBSTITUTION' => [], 'UPSELL' => []],
            'productC' => ['X_SELL' => [], 'PACK' => [], 'SUBSTITUTION' => [], 'UPSELL' => ['productA']],
        ];
        $actual = $this->getQuery()->fetchByProductIdentifiers(['productE', 'productC', 'productD']);

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    public function testOnMultipleWithProductModels()
    {
        $expected = [
            'productE' => ['X_SELL' => [], 'PACK' => [], 'SUBSTITUTION' => [], 'UPSELL' => []],
            'productD' => ['X_SELL' => ['productA', 'productB'], 'PACK' => ['productC'], 'SUBSTITUTION' => [], 'UPSELL' => []],
            'productC' => ['X_SELL' => [], 'PACK' => [], 'SUBSTITUTION' => [], 'UPSELL' => ['productA']],
            'variant_product_1' => ['X_SELL' => ['productF', 'productD'], 'PACK' => ['productA', 'productC', 'productG'], 'SUBSTITUTION' => ['productB'], 'UPSELL' => ['productE']]
        ];
        $actual = $this->getQuery()->fetchByProductIdentifiers(['productE', 'productC', 'productD', 'variant_product_1']);

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    private function getQuery(): GetProductAssociationsByProductIdentifiers
    {
        return $this->testKernel->getContainer()->get('akeneo.pim.enrichment.product.query.get_product_associations_by_product_identifiers');
    }

    private function givenBooleanAttributes(array $codes): void
    {
        $attributes = array_map(function (string $code) {
            $data = [
                'code' => $code,
                'type' => AttributeTypes::BOOLEAN,
                'localizable' => false,
                'scopable' => false,
                'group' => 'other'
            ];
            $attribute = $this->get('pim_catalog.factory.attribute')->create();
            $this->get('pim_catalog.updater.attribute')->update($attribute, $data);
            $constraints = $this->get('validator')->validate($attribute);

            Assert::count($constraints, 0);

            return $attribute;
        }, $codes);
        $this->get('pim_catalog.saver.attribute')->saveAll($attributes);
    }

    private function givenFamilies(array $familiesData): void
    {
        $families = array_map(function ($data) {
            $family = $this->get('pim_catalog.factory.family')->create();
            $this->get('pim_catalog.updater.family')->update($family, [
                'code' => $data['code'],
                'attributes'  => array_merge(['sku'], $data['attribute_codes']),
                'attribute_requirements' => ['ecommerce' => ['sku']]
            ]);

            $errors = $this->get('validator')->validate($family);

            Assert::count($errors, 0);

            return $family;
        }, $familiesData);

        $this->get('pim_catalog.saver.family')->saveAll($families);
    }

    protected function getConfiguration()
    {
        return $this->catalog->useMinimalCatalog();
    }
}
