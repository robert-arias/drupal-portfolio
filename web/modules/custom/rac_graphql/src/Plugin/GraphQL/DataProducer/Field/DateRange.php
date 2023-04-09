<?php

namespace Drupal\rac_graphql\Plugin\GraphQL\DataProducer\Field;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;

/**
 * Resolves a date range field from an entity.
 *
 * @DataProducer(
 *   id = "date_range",
 *   name = @Translation("Date Range Field"),
 *   description = @Translation("Resolves a date range field from an entity."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Date Range value")
 *   ),
 *   consumes = {
 *     "entity" = @ContextDefinition("entity",
 *       label = @Translation("Entity")
 *     ),
 *     "field_name" = @ContextDefinition("string",
 *       label = @Translation("The field name to retrieve the date range value")
 *     )
 *   }
 * )
 */
class DateRange extends DataProducerPluginBase {

  /**
   * Resolver.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity node.
   * @param \Drupal\Core\Cache\RefinableCacheableDependencyInterface $metadata
   *   The metadata object.
   *
   * @return string
   *   Array of metatags items.
   */
  public function resolve(FieldableEntityInterface $entity, string $field_name, RefinableCacheableDependencyInterface $metadata) {
    if (!$entity->hasField($field_name)) {
      throw new \InvalidArgumentException(sprintf('The field "%s" does not exist on the %s entity', $field_name, $entity->bundle()));
    }

    $date_range = $entity->get($field_name)->first()->getValue();
    return [
      'start' => $date_range['value'],
      'end' => $date_range['end_value'],
    ];
  }

}
