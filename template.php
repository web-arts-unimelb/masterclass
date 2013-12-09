<?php
/**
 * @file template.php
 *
 * Thanks to Aaron Tan and team at the Faculty of Architecture, Building and
 * Planning, University of Melbourne, and Paul Tagell and team at Marketing
 * and Communications, University of Melbourne - Media Insights 2011
 */

/**
 * Implements hook_preprocess_html().
 */
function masterclass_preprocess_html(&$variables) {
  $variables['classes_array'][] = 'masterclasses';

  // Check if we're looking at a masterclass page. Inject an extra class if so, can be used for theming.
  $object = menu_get_object();
  if (isset($object->type) && !empty($object->field_shared_masterclass)) {
    $variables['classes_array'][] = 'masterclass-' . $object->field_shared_masterclass[LANGUAGE_NONE][0]['tid'];
  }
}

/**
 * Implements hook_preprocess_page().
 *
 * Use as a wrapper function. This runs on each request anyway and this way
 * I can test for syntax errors via the CLI without getting a bunch of
 * undefined function errors.
 */
function masterclass_preprocess_page(&$variables) {

  /**
   * If looking at a node with a masterclasses taxonomy...
   */
  if (isset($variables['node']) && !empty($variables['node']->field_shared_masterclass)) {
    $masterclass = taxonomy_term_load($variables['node']->field_shared_masterclass[LANGUAGE_NONE][0]['tid']);
    $url = file_create_url($masterclass->field_mc_background[LANGUAGE_NONE][0]['uri']);
    drupal_add_css(".masterclasses #main-content.main { background-image: url({$url}); background-position: center top; background-repeat: repeat-y; }", 'inline');
  }

  $variables['masterclass_meta_parent_org'] = theme_get_setting("masterclass_settings_parent-org");
  $variables['masterclass_meta_parent_org_url'] = theme_get_setting("masterclass_settings_parent-org-url");

  if (!empty($variables['masterclass_meta_parent_org_url'])) {
    $variables['home_page_url'] = $variables['masterclass_meta_parent_org_url'];
  }

  $search_form = theme_get_setting('masterclass_settings_site_search_box');
  if (!empty($search_form)) {
    if (module_exists('search')) {
      $variables['site_search_box'] = drupal_get_form('search_block_form');
    }
    // @TODO: Do not hardcode this to this search form!
    elseif (function_exists('intranet_searchapi_form')) {
      $variables['site_search_box'] = drupal_get_form('intranet_searchapi_form');
    }
    else {
      $variables['site_search_box'] = FALSE;
    }
  }
  else {
    $variables['site_search_box'] = FALSE;
  }

  // Force a re-sort of the page contents.
  $variables['page']['content']['#sorted'] = FALSE;
}

/**
 * Implements hook_preprocess_views_view_grid().
 *
 * Our own implementation removes the complete.css grid class names from
 * the views grid and uses different ones instead.
 *
 * Specifically: col-N => view-col-N
 */
function masterclass_preprocess_views_view_grid(&$vars) {
  $columns = isset($vars['options']['columns']) ? $vars['options']['columns'] : $vars['view']->style_options['columns'];
  $replace = array();
  for ($i = 1; $i <= $columns; $i++) {
    $replace['col-' . $i . ' '] = 'view-col-' . $i . ' ';
    $replace['col-' . $i] = 'view-col-' . $i;
  }

  foreach ($vars['column_classes'] as &$row) {
    foreach ($row as $column => &$classes) {
      $classes = strtr($classes, $replace);
    }
  }
}
