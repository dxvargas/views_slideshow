<?php

/**
 * @file
 * Contains Drupal\views_slideshow\Annotation\SlideshowSkin.
 */

namespace Drupal\views_slideshow\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a slideshow skin annotation object.
 *
 * @Annotation
 */
class SlideshowSkin extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the slideshow skin.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

}
