<?php

namespace Drupal\rac_graphql\Plugin\GraphQL\DataProducer\LayoutBuilder\Sections;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\layout_builder\Section;

/**
 * Resolves the layout builder section field.
 *
 * @DataProducer(
 *   id = "layout_builder_section_field",
 *   name = @Translation("Layout Builder Section Field"),
 *   description = @Translation("Resolves a layout builder section field."),
 *   produces = @ContextDefinition("string",
 *     label = @Translation("Section Field"),
 *   ),
 *   consumes = {
 *     "section" = @ContextDefinition("any",
 *       label = @Translation("Section")
 *     ),
 *     "field" = @ContextDefinition("string",
 *       label = @Translation("Section field name")
 *     )
 *   }
 * )
 */
class SectionField extends DataProducerPluginBase {

  /**
   * Resolves the layout builder section field.
   *
   * @param \Drupal\layout_builder\Section $section
   *   The section object.
   * @param string $field
   *   The field name.
   *
   * @return string|null
   *   The field value or NULL if non existant.
   */
  public function resolve(Section $section, string $field): ?string {
    return $section->getLayoutSettings()[$field] ?? NULL;
  }

}
