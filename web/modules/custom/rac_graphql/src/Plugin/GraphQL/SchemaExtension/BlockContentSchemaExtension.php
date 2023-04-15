<?php

namespace Drupal\rac_graphql\Plugin\GraphQL\SchemaExtension;

use Drupal\block_content\BlockContentInterface;
use Drupal\block_content\Entity\BlockContent;
use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;
use Drupal\rac_graphql\EntitySchemaResolverInterface;
use GraphQL\Error\Error;

/**
 * Schema extension file for layout builder sections and components.
 *
 * @SchemaExtension(
 *   id = "rac_blocks",
 *   name = @Translation("Block Content Schema Extension"),
 *   description = @Translation("Schema extension for block content entities"),
 *   schema = "rac_main"
 * )
 */
class BlockContentSchemaExtension extends SdlSchemaExtensionPluginBase implements EntitySchemaResolverInterface {

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry): void {
    $builder = new ResolverBuilder();

    $this->addBlockContentInterfaceTypeResolver($registry);

    $this->addBlockLandingContentFields('BlockLandingContent', $registry, $builder);
    $this->addBlockTitleTextFields('BlockTitleText', $registry, $builder);
  }

  /**
   * Resolves the type of node passed down from the parent.
   *
   * This needs to be resolved in order to know which data resolver to use.
   *
   * @param \Drupal\graphql\GraphQL\ResolverRegistryInterface $registry
   *   The registry interface.
   *
   * @throws \GraphQL\Error\Error
   *   Exception when the node interface couldn't be resolved.
   */
  protected function addBlockContentInterfaceTypeResolver(ResolverRegistryInterface $registry): void {
    $registry->addTypeResolver('BlockContentInterface', function ($entity) {
      if ($entity instanceof BlockContentInterface) {
        switch ($entity->bundle()) {
          case 'landing_content':
            return 'BlockLandingContent';

          case 'title_and_text':
            return 'BlockTitleText';
        }
      }

      throw new Error('Could not resolve block type.');
    });
  }

  /**
   * Add block landing content field resolvers.
   *
   * @param string $type_name
   *   The GraphQL schema type name to resove fields to.
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  protected function addBlockLandingContentFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $this->resolveDefaultEntityFields($type_name, $registry, $builder);

    $registry->addFieldResolver($type_name, 'title',
      $builder->produce('property_path')
        ->map('path', $builder->fromValue('field_title.value'))
        ->map('value', $builder->fromParent())
        ->map('type', $builder->fromValue('entity:block_content'))
    );

    $registry->addFieldResolver($type_name, 'text',
      $builder->produce('property_path')
        ->map('path', $builder->fromValue('field_text.processed'))
        ->map('value', $builder->fromParent())
        ->map('type', $builder->fromValue('entity:block_content'))
    );

    $registry->addFieldResolver($type_name, 'buttons',
      $builder->produce('entity_reference_revisions')
        ->map('entity', $builder->fromParent())
        ->map('field', $builder->fromValue('field_buttons'))
    );
  }

  /**
   * Add block title and text resolvers.
   *
   * @param string $type_name
   *   The GraphQL schema type name to resove fields to.
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  protected function addBlockTitleTextFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $this->resolveDefaultEntityFields($type_name, $registry, $builder);

    $registry->addFieldResolver($type_name, 'headingLevel',
      $builder->produce('property_path')
        ->map('path', $builder->fromValue('field_heading_level.value'))
        ->map('value', $builder->fromParent())
        ->map('type', $builder->fromValue('entity:block_content'))
    );

    $registry->addFieldResolver($type_name, 'title',
      $builder->produce('property_path')
        ->map('path', $builder->fromValue('field_title.value'))
        ->map('value', $builder->fromParent())
        ->map('type', $builder->fromValue('entity:block_content'))
    );

    $registry->addFieldResolver($type_name, 'text',
      $builder->produce('property_path')
        ->map('path', $builder->fromValue('field_text.processed'))
        ->map('value', $builder->fromParent())
        ->map('type', $builder->fromValue('entity:block_content'))
    );
  }

  /**
   * {@inheritdoc}
   */
  public function resolveDefaultEntityFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $registry->addFieldResolver($type_name, 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver($type_name, 'bundle',
      $builder->callback(
        fn(BlockContent $block): string => $block->bundle()
      )
    );
  }

}
