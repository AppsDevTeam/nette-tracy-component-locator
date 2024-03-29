<?php

namespace ADT\ComponentLocator;

use Nette\Application\UI\Presenter;
use Nette\Utils\Strings;
use Tracy;

class ComponentLocator implements Tracy\IBarPanel
{
	public static $components = [];

	public static function initializePanel(Presenter $presenter): void
	{
		foreach ((new \ReflectionClass(get_class($presenter)))->getMethods() as $methodReflection) {
			if (Strings::startsWith($methodReflection->getName(), 'createComponent')) {
				$reflection = new \ReflectionMethod($presenter, $methodReflection->getName());
				$fileName = $reflection->getFileName();
				foreach (Tracy\Debugger::$editorMapping as $from => $to) {
					$fileName = str_replace($from, $to, $fileName);
				}

				self::$components[] = [
					'name' => lcfirst(str_replace('createComponent', '', $methodReflection->getName())),
					'file' => $fileName,
					'line' => $reflection->getStartLine()
				];
			}
		}
	}

	public function getTab()
	{
		return '
		<span title="Find a component" class="js-tracy-component-locator" style="cursor: pointer;">' .
			'<svg version="1.1" id="Icons" width="16" height="16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
			 viewBox="0 0 32 32" style="enable-background:new 0 0 32 32;" xml:space="preserve">
				<style type="text/css">
					.st0{fill:none;stroke:#000000;stroke-width:2;stroke-miterlimit:10;}
					.st1{fill:none;stroke:#000000;stroke-width:2;stroke-linejoin:round;stroke-miterlimit:10;}
					.st2{fill:none;stroke:#000000;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;}
					.st3{fill:none;stroke:#000000;stroke-width:2;stroke-linecap:round;stroke-miterlimit:10;}
					.st4{fill:none;stroke:#000000;stroke-width:2;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:3;}
				</style>
				<line class="st3" x1="10" y1="12" x2="10" y2="19"/>
				<line class="st3" x1="14" y1="11" x2="14" y2="14"/>
				<line class="st3" x1="18" y1="12" x2="18" y2="14"/>
				<line class="st3" x1="22" y1="13" x2="22" y2="15"/>
				<path class="st3" d="M24.3,12c-1.3-0.2-2.3,0.8-2.3,2v-0.9c0-1-0.7-1.9-1.7-2.1c-1.3-0.2-2.3,0.8-2.3,2v-0.9c0-1-0.7-1.9-1.7-2.1
					c-1.3-0.2-2.3,0.8-2.3,2l0-6.9c0-1-0.7-1.9-1.7-2.1C11.1,2.8,10,3.8,10,5v8.5c0-1.5-1.3-2.6-2.8-2.5c-0.5,0-0.9,0.2-1.2,0.5v7.7
					c0,5.1,3.9,9.6,9,9.8c0.3,0,0.6,0,0.8,0C21.6,28.8,26,23.9,26,18.1V18l0-3.9C26,13.1,25.3,12.2,24.3,12z"/>
			</svg>' . " Find a component" .
			"</span>" .

			"<style>
        	.js-component-locator-pointer {
        		cursor: pointer;
        	} 
        	.backgroundColorImportant {
        		background: lightgrey !important;
        	}
    	</style>" .

			"<script>
			$(function () {
				var componentLocator = $('.js-tracy-component-locator');
				var componentLocatorTab = componentLocator.parent();
				
				componentLocatorTab.removeAttr('rel');
				componentLocatorTab.hover(showHover, hideHover)
							
				function capitalizeFirstLetter(string) {
					return string.charAt(0).toUpperCase() + string.slice(1);
				}
				
				const components = " . json_encode(self::$components) . ";
				
				const findElement = function(e) {
					e.preventDefault();
					e.stopPropagation();

					let found = false;
					$(e.target).parents().each(function () { 
						if (this.getAttribute('id')) {
							const id = this.getAttribute('id').replace('frm-', '').replace('snippet-', '').split('-')[0];
							try {
								components.forEach(function(component) {
									if (component.name === id) {
										found = true;
										window.location.href = '" . Tracy\Debugger::$editor . "'.replace('%file', component.file).replace('%line', component.line);
										throw true;
									}
								});
							} catch (e) {}
						}
						
						if (found) {
							componentLocatorTab.css('backgroundColor', 'limegreen');
							makeLocatorTransparent();
							return false;
						}
						else {
							if (componentLocator[0] === e.target) {
								componentLocatorTab.css('backgroundColor', 'transparent'); 
							}
							else {
								componentLocatorTab.css('backgroundColor', 'orange'); 
								makeLocatorTransparent();
							}
						}
					});
					
					$('*').each(function() {
						this.removeEventListener('click', findElement, true);
						$(this).removeClass('js-component-locator-pointer');
					});
				};
		
				componentLocator.on('click',function(event) {
					$('*').each(function() {
						this.addEventListener('click', findElement, true);
						$(this).addClass('js-component-locator-pointer');
					});
					
					// stop current running animation
					componentLocatorTab.stop(true, true);
					hideHover();
					
					componentLocatorTab.css('backgroundColor', 'darkgrey');
				});
				
				function showHover() {
				    componentLocatorTab.addClass('backgroundColorImportant');
				}
				
				function hideHover() {
				    componentLocatorTab.removeClass('backgroundColorImportant');
				}
				
				function makeLocatorTransparent() {
					componentLocatorTab.animate({ backgroundColor: 'transparent' }, {duration: 3000});
			   	}
           });
		</script>";
	}

	public function getPanel()
	{
		return "";
	}
}
