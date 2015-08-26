<?php
/**
 * @file
 * Provides Drupal\views_slideshow\SlideshowSkinBase.
 */

namespace Drupal\views_slideshow;

use Drupal\Component\Plugin\PluginBase;

abstract class SlideshowSkinBase extends PluginBase implements SlideshowSkinInterface {

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getClass() {
    return $this->pluginDefinition['id'];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries() {
    return array();
  }
}
