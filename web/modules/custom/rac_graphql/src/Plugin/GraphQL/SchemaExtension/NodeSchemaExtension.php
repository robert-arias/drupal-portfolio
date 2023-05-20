<?php

namespace Drupal\rac_graphql\Plugin\GraphQL\SchemaExtension;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;
use Drupal\node\NodeInterface;
use Drupal\rac_graphql\EntitySchemaResolverInterface;
use GraphQL\Error\Error;

/**
 * Schema extension file for nodes.
 *
 * @SchemaExtension(
 *   id = "rac_nodes",
 *   name = @Translation("Node Schema Extension"),
 *   description = @Translation("Schema extension for nodes"),
 *   schema = "rac_main"
 * )
 */
class NodeSchemaExtension extends SdlSchemaExtensionPluginBase implements EntitySchemaResolverInterface {

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry): void {
    $builder = new ResolverBuilder();

    $this->addNodeInterfaceTypeResolver($registry);
    $this->resolveDefaultEntityFields($registry, $builder);

    $this->addNodeLandingPageFields('NodeLandingPage', $registry, $builder);
    $this->addNodeJobFields('NodeJob', $registry, $builder);
    $this->addNodeEducationFields('NodeEducation', $registry, $builder);
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
    $registry->addTypeResolver('NodeInterface', function ($entity) {
      if ($entity instanceof NodeInterface) {
        switch ($entity->bundle()) {
          case 'landing_page':
            return 'NodeLandingPage';

          case 'job':
            return 'NodeJob';

          case 'education':
            return 'NodeEducation';
        }
      }

      throw new Error('Could not resolve content type.');
    });
  }

  /**
   * Add landing page field resolvers.
   *
   * @param string $type_name
   *   The GraphQL schema type name to resove fields to.
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  protected function addNodeLandingPageFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $registry->addFieldResolver($type_name, 'sections',
      $builder->produce('layout_builder_sections')
        ->map('entity', $builder->fromParent())
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
        ->map('field', $builder->fromValue('field_date_range'))
    );

    $registry->addFieldResolver($type_name, 'description',
      $builder->produce('property_path')
        ->map('path', $builder->fromValue('body.processed'))
        ->map('value', $builder->fromParent())
        ->map('type', $builder->fromValue('entity:node'))
    );
  }

  /**
   * Add education field resolvers.
   *
   * @param string $type_name
   *   The GraphQL schema type name to resove fields to.
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  protected function addNodeEducationFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $registry->addFieldResolver($type_name, 'degreeTitle',
      $builder->produce('entity_label')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver($type_name, 'institution',
      $builder->produce('property_path')
        ->map('path', $builder->fromValue('field_institution.value'))
        ->map('value', $builder->fromParent())
        ->map('type', $builder->fromValue('entity:node'))
    );

    $registry->addFieldResolver($type_name, 'academicPeriod',
      $builder->produce('date_range')
        ->map('entity', $builder->fromParent())
        ->map('field', $builder->fromValue('field_date_range'))
    );

    $registry->addFieldResolver($type_name, 'description',
      $builder->produce('property_path')
        ->map('path', $builder->fromValue('body.processed'))
        ->map('value', $builder->fromParent())
        ->map('type', $builder->fromValue('entity:node'))
    );
  }

  /**
   * {@inheritdoc}
   */
  public function resolveDefaultEntityFields(ResolverRegistry $registry, ResolverBuilder $builder): void {
    $types = [
      'NodeLandingPage',
      'NodeJob',
      'NodeEducation',
    ];

    foreach ($types as $type) {
      $registry->addFieldResolver($type, 'id',
        $builder->produce('entity_id')
          ->map('entity', $builder->fromParent())
      );

      $registry->addFieldResolver($type, 'title',
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      );

      $registry->addFieldResolver($type, 'author',
        $builder->compose(
          $builder->produce('entity_owner')
            ->map('entity', $builder->fromParent()),
          $builder->produce('entity_label')
            ->map('entity', $builder->fromParent())
        )
      );

      $registry->addFieldResolver($type, 'url',
        $builder->compose(
          $builder->produce('entity_url')
            ->map('entity', $builder->fromParent()),
          $builder->produce('url_path')
            ->map('url', $builder->fromParent())
        )
      );
    }

  }

}
