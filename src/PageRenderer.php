<?php

namespace Pages;

class PageRenderer {

  static $templates = array(__DIR__ . "/../templates");

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
    array_unshift(self::$templates, array($dir));
  }

  static function header($arguments = array()) {
    self::requireTemplate("header", $arguments);
  }

  static function footer($arguments = array()) {
    self::requireTemplate("footer", $arguments);
  }

  /**
   * Render the given template.
   */
  static function requireTemplate($template, $arguments = array()) {
    foreach (self::$templates as $dir) {
      if (file_exists($dir . $template . ".php")) {
        // create locally scoped variables for all arguments
        foreach ($arguments as $key => $value) {
          $$key = $value;
        }

        require($dir . $template . ".php");
        return;
      }
    }

    throw new TemplateNotFoundException("Could not find template '$template'",
      new TemplateNotFoundException("No template '$template' found in " . implode("; ", self::$templates)));
  }

}