<?php

/**
 * @file
 * Hooks provided by Views Slideshow.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Define the type of the slideshow (eg.: cycle, imageflow, ddblock).
 *
 * @return
 *  Associative array of slideshow type and its information.
 */
function hook_views_slideshow_slideshow_info() {
  $options = array(
    'views_slideshow_cycle' => array(
      'name' => t('Cycle'),
      'accepts' => array(
        'goToSlide',
        'nextSlide',
        'pause',
        'play',
        'previousSlide',
      ),
      'calls' => array(
        'transitionBegin',
        'transitionEnd',
        'goToSlide',
        'pause',
        'play',
        'nextSlide',
        'previousSlide',
      ),
    ),
  );
  return $options;
}

/**
 * Define new widgets (pagers, controls, counters).
 *
 * Available events for accepts and calls
 *  - pause
 *  - play
 *  - nextSlide
 *  - previousSlide
 *  - goToSlide
 *  - transitionBegin
 *  - transitionEnd
 *
 * @return
 *  Array keyed by the widget names.
 */
function hook_views_slideshow_widget_info() {
  return array(
    'views_slideshow_pager' => array(
      'name' => t('Pager'),
      'accepts' => array(
        'transitionBegin' => array('required' => TRUE),
        'goToSlide',
        'previousSlide',
        'nextSlide',
      ),
      'calls' => array(
        'goToSlide',
        'pause',
        'play',
      ),
    ),
    'views_slideshow_controls' => array(
      'name' => t('Controls'),
      'accepts' => array(
        'pause' => array('required' => TRUE),
        'play' => array('required' => TRUE),
      ),
      'calls' => array(
        'nextSlide',
        'pause',
        'play',
        'previousSlide',
      ),
    ),
    'views_slideshow_slide_counter' => array(
      'name' => t('Slide Counter'),
      'accepts' => array(
        'transitionBegin' => array('required' => TRUE),
        'goToSlide',
        'previousSlide',
        'nextSlide',
      ),
      'calls' => array(),
    ),
  );
}

/**
 * Set default values for your form fields specified in hook_views_slideshow_type_form
 *
 * @return
 *  Associative array of slideshow type name and options.
 */
function hook_views_slideshow_option_definition() {
  $options['views_slideshow_cycle'] = array(
    'contains' => array(
      // Transition
      'effect' => array('default' => 'fade'),
      'transition_advanced' => array('default' => 0),
      'timeout' => array('default' => 5000),
      'speed' => array('default' => 700), //normal
      'delay' => array('default' => 0),
      'sync' => array('default' => 1),
      'random' => array('default' => 0),
    )
  );
  return $options;
}

/**
 * Form fields to be added for a specific widget type. Example of a widget type would be views_slideshow_pager or views_slideshow_slide_counter.
 */
function INSERT_WIDGET_TYPE_HERE_views_slideshow_widget_form_options(&$form, $form_state, $view, $defaults, $dependency) {
}

/**
 * Hook called by the pager widget to configure it, the fields that should be shown.
 */
function hook_views_slideshow_widget_pager_info($view) {
}

/**
 * Hook called by the pager widget to add form items.
 */
function INSERT_WIDGET_TYPE_HERE_views_slideshow_widget_pager_form_options(&$form, &$form_state, &$view, $defaults, $dependency) {
}

/**
 * Hook called by the controls widget to configure it, the fields that should be shown.
 */
function hook_views_slideshow_widget_controls_info($view) {
}

/**
 * Hook called by the controls widget to add form items.
 */
function INSERT_WIDGET_TYPE_HERE_views_slideshow_widget_controls_form_options(&$form, &$form_state, &$view, $defaults, $dependency) {
}

/**
 * @} End of "addtogroup hooks".
 */