<?php

require_once 'vendor/autoload.php';
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\MinkExtension\Context\MinkContext;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BusinessContext
{
    /**
     * @Given /^I wait (\d+) second$/
     */
    public function iWaitSecond($arg1)
    {
        sleep($arg1);
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
