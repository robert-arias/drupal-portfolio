<?php

namespace Drupal\rac_graphql\Plugin\GraphQL\DataProducer\Field;

use Drupal\Component\Serialization\Exception\InvalidDataTypeException;
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
 *       label = @Translation("Field Name"),
 *       description = @Translation("The field name to retrieve the date range value")
 *     ),
 *    "date_format" = @ContextDefinition("string",
 *       label = @Translation("Date Format"),
 *       description = @Translation("A valid date format to transform both start and end date values"),
 *       required = FALSE,
 *       default_value = NULL
 *     )
 *   }
 * )
 */
class DateRange extends DataProducerPluginBase {

  /**
   * Resolves the start and end date of a date range field.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity to retrieve the date range from.
   * @param string $field_name
   *   The name of the date range field.
   * @param string|null $date_format
   *   (Optional) The format to convert the dates to.
   * @param \Drupal\Core\Cache\RefinableCacheableDependencyInterface $metadata
   *   The cache metadata object.
   *
   * @return array
   *   An array containing the start and end dates of the field.
   */
  public function resolve(FieldableEntityInterface $entity, string $field_name, ?string $date_format = NULL, RefinableCacheableDependencyInterface $metadata): array {
    if (!$entity->hasField($field_name)) {
      throw new \InvalidArgumentException(sprintf('The field "%s" does not exist on %s entity.', $field_name, $entity->bundle()));
    }

    if ($entity->getFieldDefinition($field_name)->getType() !== 'daterange') {
      throw new InvalidDataTypeException(
        sprintf(
          'The field must be of type "daterange", %s given for %s field.',
          $entity->getFieldDefinition($field_name)->getType(),
          $field_name
        )
      );
    }

    // The field might be optional.
    $date_range = $entity->get($field_name)->first()?->getValue();

    if ($date_format && $date_range) {
      foreach ($date_range as &$date) {
        $immutableDate = new \DateTimeImmutable($date);
        $date = $immutableDate->format($date_format);
      }
    }

    return [
      'start' => $date_range['value'] ?? NULL,
      'end' => $date_range['end_value'] ?? NULL,
    ];
  }

}
