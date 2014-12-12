<?php

namespace Pages;

class PageRenderer {

  static $templates = array();
  static $stylesheets = array();
  static $javascripts = array();

  /**
   * Set this template location as the _only_ location for templates.
   */
  static function setTemplatesLocation($dir) {
    self::$templates = array($dir);
  }

  /**
   * Pushes this template location onto the front of the include queue.
   */
  static function addTemplatesLocation($dir) {
    array_unshift(self::$templates, $dir);
  }

  static function header($arguments = array()) {
    self::requireTemplate("header", $arguments);
  }

  static function footer($arguments = array()) {
    self::requireTemplate("footer", $arguments);
  }

  /**
   * Pushes this stylesheet location (relative to the user) onto the front of the include queue.
   * @param $css a relative (e.g. with {@link url_for()}) or absolute (e.g. JQuery CDN) path
   */
  static function addStylesheet($css) {
    array_unshift(self::$stylesheets, $css);
  }

  /**
   * Pushes this Javascript location (relative to the user) onto the front of the include queue.
   * @param $css a relative (e.g. with {@link url_for()}) or absolute (e.g. JQuery CDN) path
   */
  static function addJavascript($css) {
    array_unshift(self::$javascripts, $css);
  }

  static function includeStylesheets() {
    foreach (self::$stylesheets as $css) {
      echo '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($css) . '">' . "\n";
    }
  }

  static function includeJavascripts() {
    foreach (self::$javascripts as $js) {
      echo '<script type="text/javascript" src="' . htmlspecialchars($js) . '"></script>' . "\n";
    }
  }

  /**
   * Render the given template.
   */
  static function requireTemplate($template, $arguments = array()) {
    if (!is_array($arguments)) {
      throw new \InvalidArgumentException("Arguments '$arguments' need to be an array, not " . gettype($arguments));
    }

    foreach (self::$templates as $dir) {
      if (file_exists($dir . "/" . $template . ".php")) {
        // create locally scoped variables for all arguments
        foreach ($arguments as $key => $value) {
          $$key = $value;
        }

        require($dir . "/" . $template . ".php");
        return;
      }
    }

    throw new TemplateNotFoundException("Could not find template '$template'",
      new TemplateNotFoundException("No template '$template' found in [" . implode(", ", self::$templates) . "]"));
  }

}

PageRenderer::addTemplatesLocation(__DIR__ . "/../templates");
