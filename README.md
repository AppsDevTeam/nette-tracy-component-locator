# Nette Tracy Component Locator

Install with the `composer require adt/nette-tracy-component-locator` command.

Add `\ADT\ComponentLocator\ComponentLocator::initializePanel($this);` to your project, for example to `BasePresenter::afterRender` method.

Be sure to have something like this in your neon:

```
tracy:
	editor: "phpstorm://open?file=%file&line=%line"
	editorMapping:
		/var/www/html/: /your/real/path/to/project/
```

On Ubuntu the editor path should be like this: `phpstorm://open?url=file://%file&line=%line`.

Parameter `editorMapping` is needed only if the project runs in Docker.
