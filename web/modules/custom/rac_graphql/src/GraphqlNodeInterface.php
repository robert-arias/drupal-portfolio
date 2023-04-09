<?php

namespace Drupal\rac_graphql;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;

/**
 * Interface for GraphQL node types.
 */
interface GraphqlNodeInterface {

  /**
   * Resolve the default fields all GraphQL node types must have.
   *
   * The default fields are the ones described on the NodeInterface interface in
   * the rac_nodes.base.graphql schema.
   *
   * @param string $type_name
   *   The GraphQL schema type name to resove fields to.
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  public function resolveDefaultNodeFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void;

}
