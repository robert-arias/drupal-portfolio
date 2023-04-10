<?php

namespace Drupal\rac_graphql\Plugin\GraphQL\DataProducer\LayoutBuilder\Sections;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;

/**
 * Resolves the layout builder sections.
 *
 * @DataProducer(
 *   id = "layout_builder_sections",
 *   name = @Translation("Layout Builder Sections"),
 *   description = @Translation("Resolves the layout builder sections of the entity."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Section"),
 *   ),
 *   consumes = {
 *     "entity" = @ContextDefinition("entity",
 *       label = @Translation("Entity")
 *     )
 *   }
 * )
 */
class Sections extends DataProducerPluginBase {

  /**
   * Resolves the node's layout builder sections.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity.
   *
   * @return \Drupal\layout_builder\Section[]
   *   A sequentially and numerically keyed array of section objects.
   */
  public function resolve(FieldableEntityInterface $entity): ?string {
    if (!$entity->hasField('layout_builder__layout')) {
      throw new \InvalidArgumentException(sprintf('The node "%s" does not have layout builder configured', $entity->bundle()));
    }

    // No need to defer these as the section objects are already loaded.
    return $entity->layout_builder__layout->getSections();
  }

}
