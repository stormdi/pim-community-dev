<?php
namespace Pim\Bundle\CatalogBundle\Entity;

use Oro\Bundle\FlexibleEntityBundle\Entity\EntityAttributeOption as AbstractEntityAttributeOption;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Attribute options
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @ORM\Table(name="akeneo_catalog_product_attributeoption")
 * @ORM\Entity
 * @Gedmo\TranslationEntity(class="Pim\Bundle\CatalogBundle\Entity\ProductTranslation")
 */
class ProductAttributeOption extends AbstractEntityAttributeOption
{

    /**
     * Overrided to change target entity name
     *
     * @var Attribute $attribute
     *
     * @ORM\ManyToOne(targetEntity="ProductAttribute")
     * @ORM\JoinColumn(name="attribute_id", nullable=false, onDelete="CASCADE", referencedColumnName="id")
     */
    protected $attribute;

}
