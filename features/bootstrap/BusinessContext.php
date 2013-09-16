<?php

require_once 'vendor/autoload.php';
use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Selector\CssSelector,
    Behat\Mink\Selector\NamedSelector;
use Behat\Mink\Exception\ExpectationException;
use Symfony\Component\Yaml\Parser;

/**
 * Features context.
 */
class BusinessContext extends MinkContext
{
    /** @BeforeScenario */
    function prepare($event) {

    		// parse the config file
        $parser = new \Symfony\Component\Yaml\Parser();
        $string = file_get_contents('selectors.yml');
        $result = $parser->parse($string);

        // now iterate to create selectors
        $css = new CssSelector();
        $named =  $this->getSession()->getSelectorsHandler()->getSelector('named');
        foreach ($result as $name => $selector) {
        	$named->registerNamedXpath($name, $css->translateToXPath($selector));
      	}

    }

    function getElements($element, $selector) {
        try {
            $nodes = $element->findAll('named', $selector);
        } catch (Exception $e) {
            $nodes = array();
        }
        if (count($nodes) == 0) {
            $nodes = $element->findAll('css', $selector);
        }
        return $nodes;
    }

    function getPageElements($selector) {
        return $this->getElements($this->getSession()->getPage(), $selector);
    }

    /**
     * Make this look into named selectors and then CSS selectors
     *
     */
    public function assertNumElements($num, $element)
    {
        // find named then css
        $nodes = $this->getPageElements($element);

        // check
        if (intval($num) !== count($nodes)) {
            $message = sprintf('%d elements matching Named or CSS "%s" found on the page, but should be %d.', count($nodes), $element, $num);
            throw new ExpectationException($message, $this->getSession());
        }
    }
}
