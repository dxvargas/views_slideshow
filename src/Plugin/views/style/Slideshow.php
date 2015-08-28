<?php

/**
 * @file
 * Contains \Drupal\views_slideshow\Plugin\views\style\Slideshow.
 */

namespace Drupal\views_slideshow\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Core\Url;
use Drupal\views_slideshow\ViewsSlideshowSkinPluginManager;

/**
 * Style plugin to render each item in a slideshow.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "slideshow",
 *   title = @Translation("Slideshow"),
 *   help = @Translation("Display the results as a slideshow."),
 *   theme = "views_view_slideshow",
 *   display_types = {"normal"}
 * )
 */
class Slideshow extends StylePluginBase {

  /**
   * Does the style plugin allows to use style plugins.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Does the style plugin support custom css class for the rows.
   *
   * @var bool
   */
  protected $usesRowClass = TRUE;

  /**
   * Does the style plugin support grouping of rows.
   *
   * @var bool
   */
  protected $usesGrouping = FALSE;

  /**
   * Does the style plugin for itself support to add fields to it's output.
   *
   * This option only makes sense on style plugins without row plugins, like
   * for example table.
   *
   * @var bool
   */
  protected $usesFields = TRUE;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['row_class_custom'] = array('default' => '');
    $options['row_class_default'] = array('default' => TRUE);

    // @todo: check if it really required as there is no differnce when this block is commented.
    $typeManager = \Drupal::service('plugin.manager.views_slideshow.slideshow_type');
    $types = $typeManager->getDefinitions();
    foreach ($types as $id => $definition) {
      $instance = $typeManager->createInstance($id, []);
      $options[$id] = $instance->defaultConfiguration();
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    // Wrap all the form elements to help style the form.
    $form['views_slideshow_wrapper'] = array(
      '#markup' => '<div id="views-slideshow-form-wrapper">',
    );

    /**
     * Style.
     */
    $form['slideshow_skin_header'] = array(
      '#markup' => '<h2>' . t('Style') . '</h2>',
    );

    $skins = [];

    $skinManager = \Drupal::service('plugin.manager.views_slideshow.slideshow_skin');
    foreach ($skinManager->getDefinitions() as $id => $definition) {
      $skins[$id] = $definition['label'];
    }
    
    asort($skins);

    // Create the drop down box so users can choose an available skin.
    $form['slideshow_skin'] = array(
      '#type' => 'select',
      '#title' => t('Skin'),
      '#options' => $skins,
      '#default_value' => $this->options['slideshow_skin'],
      '#description' => t('Select the skin to use for this display.  Skins allow for easily swappable layouts of things like next/prev links and thumbnails.  Note that not all skins support all widgets, so a combination of skins and widgets may lead to unpredictable results in layout.'),
    );

    /**
     * Slides
     */
    $form['slides_header'] = array(
      '#markup' => '<h2>' . t('Slides') . '</h2>',
    );

    // Get all slideshow types.
    $typeManager = \Drupal::service('plugin.manager.views_slideshow.slideshow_type');
    $types = $typeManager->getDefinitions();

    if ($types) {

      // Build our slideshow options for the form.
      $slideshow_options = array();
      foreach ($types as $id => $definition) {
        $slideshow_options[$id] = $definition['label'];
      }

      $form['slideshow_type'] = array(
        '#type' => 'select',
        '#title' => t('Slideshow Type'),
        '#options' => $slideshow_options,
        '#default_value' => $this->options['slideshow_type'],
      );

      // @todo: check if default values are properly passed to the buildConfigurationForm().
      foreach ($types as $id => $definition) {
        $configuration = [];
        if (!empty($this->options[$id])) {
          $configuration = $this->options[$id];
        }
        $instance = $typeManager->createInstance($id, $configuration);

        $form[$id] = array(
          '#type' => 'fieldset',
          '#title' => t('!module options', array('!module' => $definition['label'])),
          '#collapsible' => TRUE,
          '#attributes' => array('class' => array($id)),
          '#states' => array(
            'visible' => array(
              ':input[name="style_options[slideshow_type]"]' => array('value' => $id),
            ),
          ),
        );
        $form[$id]['instance'] = array(
          '#type' => 'value',
          '#value' => $instance
      );

        $form = $instance->buildConfigurationForm($form, $form_state);
      }
    }
    else {
      $form['enable_module'] = array(
        '#markup' => t('There is no Views Slideshow plugin enabled. Go to the !modules and enable a Views Slideshow plugin module. For example Views Slideshow Singleframe.', array('!modules' => \Drupal::l(t('Modules Page'), Url::fromRoute('system.modules_list')))),
      );
    }

    /**
     * Widgets
     */
    /*$form['widgets_header'] = array(
      '#markup' => '<h2>' . t('Widgets') . '</h2>',
    );

    // Loop through all locations so we can add header for each location.
    $location = array('top' => t('Top'), 'bottom' => t('Bottom'));
    foreach ($location as $location_id => $location_name) {
      // Widget Header
      $form['widgets'][$location_id]['header'] = array(
        '#markup' => '<h3>' . t('!location Widgets', array('!location' => $location_name)) . '</h3>',
      );
    }

    // Get all widgets that are registered.
    // If we have widgets then build it's form fields.
    $widgets = \Drupal::moduleHandler()->invokeAll('views_slideshow_widget_info');
    if (!empty($widgets)) {

      // Build our weight values by number of widgets
      $weights = array();
      for ($i = 1; $i <= count($widgets); $i++) {
        $weights[$i] = $i;
      }

      // Loop through our widgets and locations to build our form values for
      // each widget.
      foreach ($widgets as $widget_id => $widget_info) {
        foreach ($location as $location_id => $location_name) {
          $widget_dependency = 'style_options[widgets][' . $location_id . '][' . $widget_id . ']';

          // Determine if a widget is compatible with a slideshow.
          $compatible_slideshows = array();
          foreach ($slideshows as $slideshow_id => $slideshow_info) {
            $is_compatible = 1;
            // Check if every required accept value in the widget has a
            // corresponding calls value in the slideshow.
            foreach($widget_info['accepts'] as $accept_key => $accept_value) {
              if (is_array($accept_value) && !empty($accept_value['required']) && !in_array($accept_key, $slideshow_info['calls'])) {
                $is_compatible = 0;
                break;
              }
            }

            // No need to go through this if it's not compatible.
            if ($is_compatible) {
              // Check if every required calls value in the widget has a
              // corresponding accepts call.
              foreach($widget_info['calls'] as $calls_key => $calls_value) {
                if (is_array($calls_value) && !empty($calls_value['required']) && !in_array($calls_key, $slideshow_info['accepts'])) {
                  $is_compatible = 0;
                  break;
                }
              }
            }

            // If it passed all those tests then they are compatible.
            if ($is_compatible) {
              $compatible_slideshows[] = $slideshow_id;
            }
          }

          // Use Widget Checkbox
          $form['widgets'][$location_id][$widget_id]['enable'] = array(
            '#type' => 'checkbox',
            '#title' => t($widget_info['name']),
            '#default_value' => $this->options['widgets'][$location_id][$widget_id]['enable'],
            '#description' => t('Should !name be rendered at the !location of the slides.', array('!name' => $widget_info['name'], '!location' => $location_name)),
          );

          $form['widgets'][$location_id][$widget_id]['enable']['#dependency']['edit-style-options-slideshow-type'] = !empty($compatible_slideshows) ? $compatible_slideshows : array('none');

          // Need to wrap this so it indents correctly.
          $form['widgets'][$location_id][$widget_id]['wrapper'] = array(
            '#markup' => '<div class="vs-dependent">',
          );

          // Widget weight
          // We check to see if the default value is greater than the number of
          // widgets just in case a widget has been removed and the form hasn't
          // been saved again.
          $weight = (isset($this->options['widgets'][$location_id][$widget_id]['weight'])) ? $this->options['widgets'][$location_id][$widget_id]['weight'] : 0;
          if ($weight > count($widgets)) {
            $weight = count($widgets);
          }
          $form['widgets'][$location_id][$widget_id]['weight'] = array(
            '#type' => 'select',
            '#title' => t('Weight of the !name', array('!name' => $widget_info['name'])),
            '#default_value' => $weight,
            '#options' => $weights,
            '#description' => t('Determines in what order the !name appears.  A lower weight will cause the !name to be above higher weight items.', array('!name' => $widget_info['name'])),
            '#prefix' => '<div class="vs-dependent">',
            '#suffix' => '</div>',
            '#states' => array(
              'visible' => array(
                ':input[name="style_options[widgets][' . $location_id . '][' . $widget_id . '][enable]"]' => array('checked' => TRUE),
              ),
            ),
          );

          // Add all the widget settings.
          if (function_exists($widget_id . '_views_slideshow_widget_form_options')) {
            $arguments = array(
              &$form['widgets'][$location_id][$widget_id],
              &$form_state,
              &$this,
              $this->options['widgets'][$location_id][$widget_id],
              $widget_dependency,
            );
            call_user_func_array($widget_id . '_views_slideshow_widget_form_options', $arguments);
          }

          $form['widgets'][$location_id][$widget_id]['wrapper_close'] = array(
            '#markup' => '</div>',
          );
        }
      }
    }*/

    $form['views_slideshow_wrapper_close'] = array(
      '#markup' => '</div>',
    );

    $form['#attached']['library'] = array('views_slideshow/form');
  }

  /**
   * {@inheritdoc}
   */
  public function validateOptionsForm(&$form, FormStateInterface $form_state) {
    // Validate all slideshow type plugins values.
    $typeManager = \Drupal::service('plugin.manager.views_slideshow.slideshow_type');
    foreach ($typeManager->getDefinitions() as $id => $definition) {
      if (!empty($form_state->getValue(array('style_options', $id, 'instance')))) {
        $form_state->getValue(array('style_options', $id, 'instance'))->validateConfigurationForm($form, $form_state);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state) {
    // Submit all slideshow type plugins values.
    $typeManager = \Drupal::service('plugin.manager.views_slideshow.slideshow_type');
    foreach ($typeManager->getDefinitions() as $id => $definition) {
      if (!empty($form_state->getValue(array('style_options', $id, 'instance')))) {
        $form_state->getValue(array('style_options', $id, 'instance'))->submitConfigurationForm($form, $form_state);
      }
    }
  }

}
