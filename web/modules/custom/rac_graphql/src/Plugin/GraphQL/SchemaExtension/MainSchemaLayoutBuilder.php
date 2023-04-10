<?php

namespace Drupal\rac_graphql\Plugin\GraphQL\SchemaExtension;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;
use Drupal\layout_builder\Section;

/**
 * Schema extension file for layout builder sections and components.
 *
 * @SchemaExtension(
 *   id = "rac_layout_builder",
 *   name = @Translation("Main Schema Layout Builder"),
 *   description = @Translation("Schema extension for layout builder"),
 *   schema = "rac_main"
 * )
 */
class MainSchemaLayoutBuilder extends SdlSchemaExtensionPluginBase {

  /**
   * The section type fields that resolve another type.
   */
  const SECTION_TYPE_FIELDS = [
    'grid',
    // 'background',
    // 'margin',
    // 'padding',
    // 'container',
  ];

  const SECTION_PROPERTIES_MAP = [
    'GridProperties' => [
      'columnGap' => 'column_gap',
      'rowGap' => 'row_gap',
      'columnBreakpoint' => 'column_breakpoint',
      'columnWidth' => 'column_width',
      'alignItems' => 'align_items',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry): void {
    $builder = new ResolverBuilder();

    $this->addLayoutBuilderSectionFields('Section', $registry, $builder);
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
  protected function addLayoutBuilderSectionFields(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $registry->addFieldResolver($type_name, 'sectionId',
      $builder->callback(
        fn(Section $section): string => $section->getLayoutId()
      )
    );

    $registry->addFieldResolver($type_name, 'label',
      $builder->produce('layout_builder_section_field')
        ->map('section', $builder->fromParent())
        ->map('field', $builder->fromValue('label'))
    );

    foreach (self::SECTION_TYPE_FIELDS as $field) {
      $registry->addFieldResolver($type_name, $field,
        $builder->callback(
          fn(Section $section): Section => $section
        )
      );
    }

    foreach (self::SECTION_PROPERTIES_MAP as $type => $fields) {
      foreach ($fields as $schema_field => $section_field) {
        $registry->addFieldResolver($type, $schema_field,
        $builder->produce('layout_builder_section_field')
          ->map('section', $builder->fromParent())
          ->map('field', $builder->fromValue($section_field))
        );
      }
    }
  }

}
