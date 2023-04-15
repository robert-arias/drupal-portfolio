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
    'background',
    'margin',
    'padding',
    'container',
  ];

  /**
   * Maps out the section custom type fields with the schema field name and the
   * Drupal field name.
   */
  const SECTION_PROPERTIES_MAP = [
    'GridProperties' => [
      'columnGap' => 'column_gap',
      'rowGap' => 'row_gap',
      'columnBreakpoint' => 'column_breakpoint',
      'columnWidth' => 'column_width',
      'alignItems' => 'align_items',
    ],
    'BackgroundProperties' => [
      'backgroundColor' => 'background',
    ],
    'MarginProperties' => [
      'top' => 'top_margin',
      'bottom' => 'bottom_margin',
      'left' => 'left_margin',
      'right' => 'right_margin',
      'topBottom' => 'equal_top_bottom_margins',
      'leftRight' => 'equal_left_right_margins',
    ],
    'PaddingProperties' => [
      'top' => 'top_padding',
      'bottom' => 'bottom_padding',
      'left' => 'left_padding',
      'right' => 'right_padding',
      'topBottom' => 'equal_top_bottom_paddings',
      'leftRight' => 'equal_left_right_paddings',
    ],
    'ContainerProperties' => [
      'width' => 'container',
      'height' => 'height',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry): void {
    $builder = new ResolverBuilder();

    $this->addLayoutBuilderSectionFields('Section', $registry, $builder);
    $this->addLayoutBuilderPropertyFields($registry, $builder);
  }

  /**
   * Add layout builder section field resolvers.
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
  }

  /**
   * Add section field type resolvers.
   *
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  protected function addLayoutBuilderPropertyFields(ResolverRegistry $registry, ResolverBuilder $builder): void {
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
