<?php

namespace Drupal\rac_graphql\Plugin\GraphQL\DataProducer\LayoutBuilder\Components;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use GraphQL\Deferred;

/**
 * Gets the list of the LayoutBuilder section components.
 *
 * @DataProducer(
 *   id = "section_components",
 *   name = @Translation("Section Components"),
 *   description = @Translation("Resolves the components of the section."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Component"),
 *   ),
 *   consumes = {
 *     "section" = @ContextDefinition("any",
 *       label = @Translation("Section")
 *     )
 *   }
 * )
 */
class Components extends DataProducerPluginBase {

  /**
   * Resolves the components of the section sort by weight.
   *
   * @param \Drupal\layout_builder\Section $section
   *   The section entity.
   *
   * @return \GraphQL\Deferred
   *   The components attached to the section.
   */
  public function resolve(Section $section): Deferred {
    return new Deferred(function () use ($section) {
      $components = $section->getComponents();

      uasort($components,
        fn(SectionComponent $a, SectionComponent $b): int => $a->getWeight() - $b->getWeight()
      );

      return $components;
    });
  }

}
