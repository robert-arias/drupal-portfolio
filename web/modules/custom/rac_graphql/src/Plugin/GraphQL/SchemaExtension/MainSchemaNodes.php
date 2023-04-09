<?php

namespace Drupal\rac_graphql\Plugin\GraphQL\SchemaExtension;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;
use Drupal\node\NodeInterface;
use GraphQL\Error\Error;

/**
 * Schema extension file for nodes.
 *
 * @SchemaExtension(
 *   id = "rac_nodes",
 *   name = @Translation("Main Schema Nodes"),
 *   description = @Translation("Schema extension for nodes"),
 *   schema = "rac_main"
 * )
 */
class MainSchemaNodes extends SdlSchemaExtensionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry): void {
    $builder = new ResolverBuilder();

    $this->addNodeInterfaceTypeResolver($registry);

    $this->addQueryFields($registry, $builder);
    $this->addNodeJobFields('NodeJob', $registry, $builder);
    $this->addDateRangeFields('DateRange', $registry, $builder);
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
  protected function addNodeInterfaceTypeResolver(ResolverRegistryInterface $registry): void {
    $registry->addTypeResolver('NodeInterface', function ($value) {
      if ($value instanceof NodeInterface) {
        switch ($value->bundle()) {
          case 'job':
            return 'NodeJob';
        }
      }
      throw new Error('Could not resolve content type.');
    });
  }

  /**
   * Add Query field resolvers.
   *
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  protected function addQueryFields(ResolverRegistry $registry, ResolverBuilder $builder): void {
    $registry->addFieldResolver('Query', 'job',
      $builder->produce('entity_load')
        ->map('type', $builder->fromValue('node'))
        ->map('bundles', $builder->fromValue(['job']))
        ->map('id', $builder->fromArgument('id'))
    );
  }

  /**
   * Add job field resolvers.
   *
   * @param string $type_name
   *   The GraphQL schema type name to resove fields to.
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  protected function addNodeJobFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $registry->addFieldResolver($type_name, 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver($type_name, 'title',
      $builder->produce('entity_label')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver($type_name, 'jobTitle',
      $builder->produce('entity_label')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver($type_name, 'company',
      $builder->produce('property_path')
        ->map('path', $builder->fromValue('field_company.value'))
        ->map('value', $builder->fromParent())
        ->map('type', $builder->fromValue('entity:node'))
    );

    $registry->addFieldResolver($type_name, 'jobPeriod',
      $builder->produce('date_range')
        ->map('entity', $builder->fromParent())
        ->map('field_name', $builder->fromValue('field_job_period'))
    );

    $registry->addFieldResolver($type_name, 'description',
      $builder->produce('property_path')
        ->map('path', $builder->fromValue('body.processed'))
        ->map('value', $builder->fromParent())
        ->map('type', $builder->fromValue('entity:node'))
    );

    $registry->addFieldResolver($type_name, 'author',
      $builder->compose(
        $builder->produce('entity_owner')
          ->map('entity', $builder->fromParent()),
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );
  }

  /**
   * Add job field resolvers.
   *
   * @param string $type_name
   *   The GraphQL schema type name to resove fields to.
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  protected function addDateRangeFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $registry->addFieldResolver($type_name, 'start',
      $builder->callback(function (array $date_range) {
        return $date_range['start'];
      })
    );

    $registry->addFieldResolver($type_name, 'end',
      $builder->callback(function (array $date_range) {
        return $date_range['end'];
      })
    );
  }

}
