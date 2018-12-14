<?php

namespace Cdf\PannelloAmministrazioneBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fi\OsBundle\DependencyInjection\OsFunctions;
use Cdf\PannelloAmministrazioneBundle\DependencyInjection\ProjectPath;
use Cdf\PannelloAmministrazioneBundle\DependencyInjection\GeneratorHelper;
use Cdf\PannelloAmministrazioneBundle\DependencyInjection\PannelloAmministrazioneUtils;

class GenerateOrmEntitiesCommand extends Command
{

    protected $apppaths;
    protected $genhelper;
    protected $pammutils;

    protected function configure()
    {
        $this
                ->setName('pannelloamministrazione:generateormentities')
                ->setDescription('Genera le entities partendo da un modello workbeanch mwb')
                ->setHelp('Genera i file orm per le entities partendo da un modello workbeanch mwb, <br/>bi.mwb Fi/BiCoreBundle default<br/>')
                ->addArgument('mwbfile', InputArgument::REQUIRED, 'Nome file mwb, bi.mwb')
        ;
    }

    public function __construct(ProjectPath $projectpath, GeneratorHelper $genhelper, PannelloAmministrazioneUtils $pammutils)
    {
        $this->apppaths = $projectpath;
        $this->genhelper = $genhelper;
        $this->pammutils = $pammutils;

        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(0);

        $mwbfile = $input->getArgument('mwbfile');

        $wbFile = $this->apppaths->getDocPath() . DIRECTORY_SEPARATOR . $mwbfile;
        $checkprerequisiti = $this->genhelper->checkprerequisiti($mwbfile, $output);

        if ($checkprerequisiti < 0) {
            return -1;
        }

        $destinationPath = $this->genhelper->getDestinationEntityOrmPath();

        $exportJson = $this->genhelper->getExportJsonFile();
        $scriptGenerator = $this->genhelper->getScriptGenerator();
        $destinationPathEscaped = $this->genhelper->getDestinationEntityOrmPath();
        $exportjsonfile = $this->genhelper->getJsonMwbGenerator();

        $exportjsonreplaced = str_replace('[dir]', $destinationPathEscaped, $exportjsonfile);

        file_put_contents($exportJson, $exportjsonreplaced);
        $sepchr = OsFunctions::getSeparator();
        if (OsFunctions::isWindows()) {
            $workingdir = $this->apppaths->getRootPath();
            $command = $scriptGenerator . '.bat';
            $arguments[] = '--config=' . $exportJson;
            $arguments[] = $wbFile;
            $arguments[] = $destinationPathEscaped;
        } else {
            $phpPath = OsFunctions::getPHPExecutableFromPath();
            $workingdir = $this->apppaths->getRootPath();
            $command = $scriptGenerator;
            $arguments[] = '--config=' . $exportJson;
            $arguments[] = $wbFile;
            $arguments[] = $destinationPathEscaped;
        }

        $schemaupdateresult = $this->pammutils->runCommand($command, $arguments, $workingdir);
        if ($schemaupdateresult["errcode"] < 0) {
            $output->writeln($schemaupdateresult["message"]);
            return 1;
        } else {
            $output->writeln($schemaupdateresult["message"]);
        }

        $this->genhelper->removeExportJsonFile();
        $tablecheck = $this->genhelper->checktables($destinationPath, $wbFile, $output);

        if ($tablecheck < 0) {
            return 1;
        }

        $output->writeln('<info>Entities yml create</info>');
        return 0;
    }

}
