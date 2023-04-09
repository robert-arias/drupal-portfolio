<?php

namespace Drupal\rac_graphql;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;

/**
 * Base class for schema extensions that will resolve GraphQL node types.
 */
abstract class GraphqlNodeBase extends SdlSchemaExtensionPluginBase implements GraphqlNodeInterface {

  /**
   * {@inheritdoc}
   */
  public function resolveDefaultNodeFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $registry->addFieldResolver($type_name, 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver($type_name, 'title',
      $builder->produce('entity_label')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver($type_name, 'author',
      $builder->compose(
        $builder->produce('entity_owner')
          ->map('entity', $builder->fromParent()),
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );

    $registry->addFieldResolver($type_name, 'url',
      $builder->compose(
        $builder->produce('entity_url')
          ->map('entity', $builder->fromParent()),
        $builder->produce('url_path')
          ->map('url', $builder->fromParent())
      )
    );
  }

}
