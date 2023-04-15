<?php

namespace Drupal\rac_graphql\Plugin\GraphQL\SchemaExtension;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\rac_graphql\EntitySchemaResolverInterface;
use GraphQL\Error\Error;

/**
 * Schema extension file for layout builder sections and components.
 *
 * @SchemaExtension(
 *   id = "rac_paragraphs",
 *   name = @Translation("Paragraphs Schema Extension"),
 *   description = @Translation("Schema extension for paragraph entities"),
 *   schema = "rac_main"
 * )
 */
class ParagraphSchemaExtension extends SdlSchemaExtensionPluginBase implements EntitySchemaResolverInterface {

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry): void {
    $builder = new ResolverBuilder();

    $this->addParagraphInterfaceTypeResolver($registry);

    $this->addParagraphButtonFields('ParagraphButton', $registry, $builder);
    $this->addParagraphLinkFields('ParagraphLink', $registry, $builder);
    $this->addParagraphDocumentFields('ParagraphDocument', $registry, $builder);
  }

  /**
   * Resolves the type of paragraph passed down from the parent.
   *
   * This needs to be resolved in order to know which data resolver to use.
   *
   * @param \Drupal\graphql\GraphQL\ResolverRegistryInterface $registry
   *   The registry interface.
   *
   * @throws \GraphQL\Error\Error
   *   Exception when the node interface couldn't be resolved.
   */
  protected function addParagraphInterfaceTypeResolver(ResolverRegistryInterface $registry): void {
    $registry->addTypeResolver('ParagraphInterface', function (EntityInterface $entity): string {
      if ($entity instanceof ParagraphInterface) {
        switch ($entity->bundle()) {
          case 'cta':
            return 'ParagraphButton';

          case 'link':
            return 'ParagraphLink';

          case 'document':
            return 'ParagraphDocument';
        }
      }

      throw new Error('Could not resolve paragraph type.');
    });
  }

  /**
   * Add paragraph button field resolvers.
   *
   * @param string $type_name
   *   The GraphQL schema type name to resove fields to.
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  protected function addParagraphButtonFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $this->resolveDefaultEntityFields($type_name, $registry, $builder);

    $registry->addFieldResolver($type_name, 'title',
      $builder->produce('property_path')
        ->map('path', $builder->fromValue('field_title.value'))
        ->map('value', $builder->fromParent())
        ->map('type', $builder->fromValue('entity:paragraph'))
    );

    $registry->addFieldResolver($type_name, 'type',
      $builder->produce('property_path')
        ->map('path', $builder->fromValue('field_button_type.value'))
        ->map('value', $builder->fromParent())
        ->map('type', $builder->fromValue('entity:paragraph'))
    );

    $registry->addFieldResolver($type_name, 'content',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:paragraph'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_cta_content.entity'))
    );
  }

  /**
   * Add paragraph link field resolvers.
   *
   * @param string $type_name
   *   The GraphQL schema type name to resove fields to.
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  protected function addParagraphLinkFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $this->resolveDefaultEntityFields($type_name, $registry, $builder);

    $registry->addFieldResolver($type_name, 'url',
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:paragraph'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_link.uri')),
        $builder->callback(
          fn(string $uri): Url => Url::fromUri($uri)
        ),
        $builder->produce('url_path')
          ->map('url', $builder->fromParent())
      )
    );
  }

  /**
   * Add paragraph document field resolvers.
   *
   * @param string $type_name
   *   The GraphQL schema type name to resove fields to.
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  protected function addParagraphDocumentFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $this->resolveDefaultEntityFields($type_name, $registry, $builder);
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
        fn(EntityInterface $entity): string => $entity->bundle()
      )
    );
  }

}
