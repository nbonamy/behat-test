<?php

require_once 'vendor/autoload.php';
use Behat\Behat\Context\ClosuredContextInterface,
Behat\Behat\Context\TranslatedContextInterface,
Behat\Behat\Context\BehatContext,
Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Event\SuiteEvent;
use Behat\Mink\Selector\CssSelector,
    Behat\Mink\Selector\NamedSelector;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class WebFeatureContext extends MinkContext
{
    /** @BeforeScenario */
    function prepare($event) {
        $css = new CssSelector();
        $named =  $this->getSession()->getSelectorsHandler()->getSelector('named');
        $named->registerNamedXpath('transaction', $css->translateToXPath('tr.transaction'));
        $named->registerNamedXpath('negative transaction', $css->translateToXPath('tr.transaction td.amount.negative'));
        $named->registerNamedXpath('description', $css->translateToXPath('td.description'));
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
     * @Given /^I wait (\d+) second$/
     */
    public function iWaitSecond($duration)
    {
        sleep($duration);
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



    /**
     * @Then /^all "([^"]*)" "([^"]*)" should contain "([^"]*)"$/
     */
    public function allShouldContain($selector, $subselector, $containee)
    {
        // find named then css
        $nodes = $this->getPageElements($selector);
        foreach ($nodes as $node) {
            $subnodes = $this->getElements($node, $subselector);
            if (count($subnodes) == 0) {
                $message = sprintf('Could not find a "%s" element for "%s"', $subselector, $selector);
                throw new ExpectationException($message, $this->getSession());
            }
            $subnode = current($subnodes);
            if (strpos($subnode->getText(), $containee) === FALSE) {
                $message = sprintf('Should have found an element containing "%s", found "%s"', $containee, $subnode->getText());
                throw new ExpectationException($message, $this->getSession());
            }
        }
    }

   /**
    * @Then /^all "([^"]*)" "([^"]*)" should be$/
    */
   public function allShouldBe($selector, $subselector, PyStringNode $string)
   {
        // find named then css
        $lines = $string->getLines();
        $nodes = $this->getPageElements($selector);
        foreach ($nodes as $node) {
            $subnodes = $this->getElements($node, $subselector);
            if (count($subnodes) == 0) {
                $message = sprintf('Could not find a "%s" element for "%s"', $subselector, $selector);
                throw new ExpectationException($message, $this->getSession());
            }
            $subnode = current($subnodes);
            if ($subnode->getText() !== current($lines)) {
                $message = sprintf('Should have found an element equal to "%s", found "%s"', current($lines), $subnode->getText());
                throw new ExpectationException($message, $this->getSession());
            }
            next($lines);
        }
   }

}
