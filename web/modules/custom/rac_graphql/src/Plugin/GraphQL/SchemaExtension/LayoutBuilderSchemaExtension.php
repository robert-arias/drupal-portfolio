<?php

namespace Drupal\rac_graphql\Plugin\GraphQL\SchemaExtension;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;

/**
 * Schema extension file for layout builder sections and components.
 *
 * @SchemaExtension(
 *   id = "rac_layout_builder",
 *   name = @Translation("Layout Builder Schema Extension"),
 *   description = @Translation("Schema extension for layout builder"),
 *   schema = "rac_main"
 * )
 */
class LayoutBuilderSchemaExtension extends SdlSchemaExtensionPluginBase {

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
   * Maps out the section custom type fields.
   *
   * It maps it out with the schema field name and the Drupal field name.
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
    $this->addLayoutBuilderComponent('Component', $registry, $builder);
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

    $registry->addFieldResolver($type_name, 'content',
      $builder->produce('section_components')
        ->map('section', $builder->fromParent())
    );
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

  /**
   * Add  section content field resolvers.
   *
   * @param string $type_name
   *   The GraphQL schema type name to resove fields to.
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   *   The resolver registry.
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   *   The resolver builder.
   */
  protected function addLayoutBuilderComponent(string $type_name, ResolverRegistry $registry, ResolverBuilder $builder): void {
    $registry->addFieldResolver($type_name, 'uuid',
      $builder->callback(
        fn(SectionComponent $component): string => $component->getUuid()
      )
    );

    $registry->addFieldResolver($type_name, 'region',
      $builder->callback(
        fn(SectionComponent $component): string => $component->getRegion()
      )
    );

    $registry->addFieldResolver($type_name, 'block',
      $builder->produce('entity_load_by_revision_id')
        ->map('type', $builder->fromValue('block_content'))
        ->map('revision_id', $builder->callback(
            fn(SectionComponent $component): string => $component->get('configuration')['block_revision_id']
          )
        )
        ->map('bundles', $builder->callback(
            function (SectionComponent $component): array {
              $id = $component->get('configuration')['id'];
              $bundle = explode(':', $id)[1] ?? NULL;
              return [$bundle];
            }
          )
        )
    );
  }

}
