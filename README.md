# Nette Tracy Component Locator

Install with the `composer require --dev adt/nette-tracy-component-locator` command.

Add this to your project, for example to `BasePresenter::afterRender` method:

```php

if (class_exists('\ADT\ComponentLocator\ComponentLocator')) {
	\ADT\ComponentLocator\ComponentLocator::initializePanel($this);
}
```

Be sure to have something like this in your local neon:

```
tracy:
	editor: "phpstorm://open?file=%file&line=%line"
	editorMapping:
		/var/www/html/: /your/real/path/to/project/

	bar:
		- \ADT\ComponentLocator\ComponentLocator
```

On Ubuntu the editor path should be like this: `phpstorm://open?url=file://%file&line=%line`.

Parameter `editorMapping` is needed only if the project runs in Docker.
