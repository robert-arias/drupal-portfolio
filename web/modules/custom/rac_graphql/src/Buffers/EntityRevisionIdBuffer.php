<?php

namespace Drupal\rac_graphql\Buffers;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\graphql\GraphQL\Buffers\BufferBase;
use Drupal\Tests\Core\Entity\RevisionableEntity;

/**
 * Collects entity revision IDs per entity type and loads them all at once in the end.
 */
class EntityRevisionIdBuffer extends BufferBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * EntityBuffer constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Add an item to the buffer.
   *
   * @param string $type
   *   The entity type of the given entity ids.
   * @param array|string $revision_id
   *   The entity revision ID to load.
   *
   * @return \Closure
   *   The callback to invoke to load the result for this buffer item.
   */
  public function add(string $type, array|string $revision_id): \Closure {
    $item = new \ArrayObject([
      'type' => $type,
      'revision_id' => $revision_id,
    ]);

    return $this->createBufferResolver($item);
  }

  /**
   * {@inheritdoc}
   */
  protected function getBufferId($item): string {
    return $item['type'];
  }

  /**
   * {@inheritdoc}
   */
  public function resolveBufferArray(array $buffer): array {
    $type = reset($buffer)['type'];

    $entityType = $this->entityTypeManager->getDefinition($type);

    if (!$revision_id_key = $entityType->getKey('revision_id')) {
      throw new EntityStorageException("Entity type $type does not support reviion IDs.");
    }

    $revision_ids = array_map(function (\ArrayObject $item) {
      return (array) $item['revision_id'];
    }, $buffer);

    $revision_ids = call_user_func_array('array_merge', $revision_ids);
    $revision_ids = array_values(array_unique($revision_ids));

    $entities = $this->entityTypeManager
      ->getStorage($type)->loadByProperties([$revision_id_key => $revision_ids]);

    $entities = array_reduce(array_map(function (RevisionableEntity $entity) {
      return [$entity->getRevisionId() => $entity];
    }, $entities), 'array_merge', []);

    return array_map(function ($item) use ($entities) {
      if (is_array($item['revision_id'])) {
        return array_reduce($item['revision_id'], function ($carry, $current) use ($entities) {
          if (!empty($entities[$current])) {
            return $carry + [$current => $entities[$current]];
          }

          return $carry;
        }, []);
      }

      return $entities[$item['revision_id']] ?? NULL;
    }, $buffer);
  }

}
