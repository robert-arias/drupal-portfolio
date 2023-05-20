<?php

namespace Drupal\rac_graphql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;

/**
 * The entry point to the main schema.
 *
 * @Schema(
 *   id = "rac_main",
 *   name = @Translation("Main Schema"),
 *   description = @Translation("The main schema entrypoint")
 * )
 */
class MainSchema extends SdlSchemaPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getResolverRegistry(): ResolverRegistryInterface {
    $builder = new ResolverBuilder();
    $registry = new ResolverRegistry();

    $this->addQueryFields('Query', $registry, $builder);
    $this->addDateRangeFields('DateRange', $registry, $builder);

    return $registry;
  }

  /**
   * Add query field resolvers.
   *
   * @param string $type_name
   *   The GraphQL schema type name to resove fields to.
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  protected function addQueryFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $registry->addFieldResolver($type_name, 'route',
      $builder->compose(
        $builder->produce('route_load')
          ->map('path', $builder->fromArgument('path')),
        $builder->produce('route_entity')
          ->map('url', $builder->fromParent())
      )
    );

    $registry->addFieldResolver($type_name, 'landingPage',
      $builder->produce('entity_load')
        ->map('type', $builder->fromValue('node'))
        ->map('bundles', $builder->fromValue(['landing_page']))
        ->map('id', $builder->fromArgument('id'))
    );

    $registry->addFieldResolver($type_name, 'job',
      $builder->produce('entity_load')
        ->map('type', $builder->fromValue('node'))
        ->map('bundles', $builder->fromValue(['job']))
        ->map('id', $builder->fromArgument('id'))
    );

    $registry->addFieldResolver($type_name, 'education',
      $builder->produce('entity_load')
        ->map('type', $builder->fromValue('node'))
        ->map('bundles', $builder->fromValue(['education']))
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
