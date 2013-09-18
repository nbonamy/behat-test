<?php

require_once 'vendor/autoload.php';
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Event\StepEvent;

class MantisContext extends RawMinkContext
{

    /** @AfterStep */
    public function failScreenshots(StepEvent $event)
    {
        if ($event->getResult() == StepEvent::FAILED) {

            $feature = $event->getStep()->getParent()->getFeature()->getTitle();
            $scenario = $event->getStep()->getParent()->getTitle();
            $step = $event->getStep()->getText();
            $error = $event->getException()->getMessage();

            // please do not mess up with this !!!!!
            $c = new SoapClient('http://www.nabocorp.com/mantis/api/soap/mantisconnect.php?wsdl');
            $username = 'behat';
            $password = 'behat';

            // get project id
            $project_name = 'Behat Test';
            $project_id = $c->mc_project_get_id_from_name($username, $password, $project_name);

            // create issue
            $issue = array (
                'summary' => '['.$feature.'] '.$scenario. ' failed',
                'description' => 'Step: '.$step."\n".'Error: '.$error,
                'project' => array('id'=>$project_id),
                'category' => 'General'
                );
            $issue_id = $c->mc_issue_add($username, $password, $issue);

            // attach screen shot
            $c->mc_issue_attachment_add(
                $username, $password, $issue_id,
                'screenshot.png', 'image/png', $this->getSession()->getScreenshot());

        }
    }

}
