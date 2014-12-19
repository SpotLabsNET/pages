## Asset pipeline

The asset pipeline is designed so that you include all assets on every page.
There is no automation for including multiple assets together.

## Using

Create a new folder `templates\` and create `templates\header.php` and `templates\footer.php` optionally
(or else the framework will use the default supplied templates).

```php
// inc/global.php
\Pages\PageRenderer::addTemplatesLocation(__DIR__ . "/../templates");
\Pages\PageRenderer::addStylesheet(\Openclerk\Router::urlFor("css/default.css"));
\Pages\PageRenderer::addJavascript(\Openclerk\Router::urlFor("js/default.js"));
```

```php
<?php
// templates/index.php

if ($user) {
  echo "<h2>Logged in successfully as $user</h2>";
} else {
  echo "<h2>Could not log in</a>";
}
?>
```

```php
// site/index.php

$user = get_user();

\Pages\PageRenderer::header(array("title" => "My page title"));
\Pages\PageRenderer::requireTemplate("index", array('user' => $user));
\Pages\PageRenderer::footer();
```

## HAML

You can also define templates with HAML syntax:

```haml
/ templates/index.haml

- if($user)
  %h2 Logged in successfully as #{$user}
- else
  %h2 Could not log in

:php
  echo link_to(url_for("index"), "Back home")
```
