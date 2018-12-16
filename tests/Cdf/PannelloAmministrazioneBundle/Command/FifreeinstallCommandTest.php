<?php

namespace Cdf\PannelloAmministrazioneBundle\Tests\Commands;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class FifreeinstallCommandTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }
    public function testFifreeinstall()
    {
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $commanddroptables = $application->find('bicorebundle:droptables');
        $commandTesterdroptables = new CommandTester($commanddroptables);
        $commandTesterdroptables->execute(array('--force' => true));
        $outputdroptables = $commandTesterdroptables->getDisplay();

        $this->assertRegExp('/.../', $outputdroptables);
        //$this->assertContains('Cosa cercare', $outputimport);

        $commanddropdatabase = $application->find('bicorebundle:dropdatabase');
        $commandTesterdropdatabase = new CommandTester($commanddropdatabase);
        $commandTesterdropdatabase->execute(array('--force' => true));
        $outputdropdatabase = $commandTesterdropdatabase->getDisplay();

        $this->assertRegExp('/.../', $outputdropdatabase);
        //$this->assertContains('Cosa cercare', $outputdropdatabase);

        $commandinstall = $application->find('bicorebundle:install');
        $commandTesterinstall = new CommandTester($commandinstall);
        $commandTesterinstall->execute(array('admin' => 'admin', 'adminpass' => 'admin', 'adminemail' => 'admin@admin.it'));
        $outputinstall = $commandTesterinstall->getDisplay();

        $this->assertRegExp('/.../', $outputinstall);

        //$this->assertContains('Cosa cercare', $outputimport);
        $commandloaddata = $application->find('bicoredemo:loaddefauldata');
        $commandTesterLoaddata = new CommandTester($commandloaddata);
        $commandTesterLoaddata->execute(array());
        $outputloaddata = $commandTesterLoaddata->getDisplay();

        $this->assertRegExp('/.../', $outputloaddata);
        $this->assertContains('Done', $outputloaddata);

        $commandcc = $application->find('cache:clear');
        $commandTestercc = new CommandTester($commandcc);
        $commandTestercc->execute(array('--env' => 'test'));
        $outputcc = $commandTestercc->getDisplay();

        $this->assertRegExp('/.../', $outputcc);
        dump($outputcc);
    }
}
