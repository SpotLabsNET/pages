<?php

namespace Pages;

use \Openclerk\Events;

class PageRenderer {

  static $templates = array();
  static $stylesheets = array();
  static $javascripts = array();

  static $haml_options = array();

  static function setHamlOptions($options) {
    self::$haml_options = $options;
  }

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
    Events::trigger('pages_header_start', $arguments);
    self::requireTemplate("header", $arguments);
    Events::trigger('pages_header_end', $arguments);
  }

  static function footer($arguments = array()) {
    Events::trigger('pages_footer_start', $arguments);
    self::requireTemplate("footer", $arguments);
    Events::trigger('pages_footer_end', $arguments);
  }

  /**
   * Pushes this stylesheet location (relative to the user) onto the back of the include queue.
   * @param $css a relative (e.g. with {@link url_for()}) or absolute (e.g. JQuery CDN) path
   */
  static function addStylesheet($css) {
    array_push(self::$stylesheets, $css);
  }

  /**
   * Pushes this Javascript location (relative to the user) onto the back of the include queue.
   * @param $css a relative (e.g. with {@link url_for()}) or absolute (e.g. JQuery CDN) path
   */
  static function addJavascript($css) {
    array_push(self::$javascripts, $css);
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
   * Cached {@link MtHaml\Support\Php\Executor} instance, as necessary.
   */
  static $executor = null;

  /**
   * Render the given template.
   *
   * Searches for templates within each template directory, with the following
   * search order: {@code .php}, {@code .haml.php}, {@code .haml}.
   */
  static function requireTemplate($template, $arguments = array()) {
    Events::trigger('pages_template_start', $arguments + array('template' => $template));

    if (!is_array($arguments)) {
      throw new \InvalidArgumentException("Arguments '$arguments' need to be an array, not " . gettype($arguments));
    }

    foreach (self::$templates as $dir) {
      // either include templates as direct PHP files...
      $file = $dir . "/" . $template . ".php";
      if (file_exists($file)) {
        // create locally scoped variables for all arguments
        foreach ($arguments as $key => $value) {
          $$key = $value;
        }

        require($file);
        Events::trigger('pages_template_end', $arguments + array('template' => $template, 'filetype' => 'php'));
        return;
      }

      // ... or as HAML templates
      foreach (array($dir . "/" . $template . ".php.haml", $dir . "/" . $template . ".haml") as $file) {
        if (file_exists($file)) {
          if (self::$executor === null) {
            $haml = new \MtHaml\Environment('php', self::$haml_options);
            self::$executor = new \MtHaml\Support\Php\Executor($haml, array(
                'cache' => sys_get_temp_dir().'/haml',
            ));
          }

          // Compiles and executes the HAML template, with variables given as second argument
          self::$executor->display($file, $arguments);
          Events::trigger('pages_template_end', $arguments + array('template' => $template, 'filetype' => 'haml'));
          return;
        }
      }
    }

    throw new TemplateNotFoundException("Could not find template '$template'",
      new TemplateNotFoundException("No template '$template' found in [" . implode(", ", self::$templates) . "]"));
  }

}

PageRenderer::addTemplatesLocation(__DIR__ . "/../templates");
