<?php
/**
 * @file
 * Contains Drupal\views_slideshow\SlideshowSkinInterface.
 */

namespace Drupal\views_slideshow;

/**
 * Interface SlideshowSkinInterface
 * @package Drupal\views_slideshow
 */
interface SlideshowSkinInterface {

  /**
   * Returns a array of libraries to attach when the skin is used.
   * @return array
   */
  public function getLibraries();

  /**
   * Returns a class to be added to templates.
   * @return string
   */
  public function getClass();

}
