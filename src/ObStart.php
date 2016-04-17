<?php

namespace OrdBild;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use ZipArchive;
use GuzzleHttp\Client;

class ObStart extends Command
{
    protected function configure()
    {
        $this->setName('download')
            ->addArgument('name', InputArgument::REQUIRED, 'Vad ska temat heta?')
            ->setDescription('Ladda ned starttemat ob_start');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $themeName = $input->getArgument('name');
        
        $output->writeln('Påbörjar nedladdning...');
        
        $this->download($themeName);
        
        $output->writeln('Nedladdning färdig.');

        $this->extract($themeName);
        
        $output->writeln('ZIP extraherad.');

        $output->writeln('Temat ' . $themeName . ' är färdigt!');
    }

    protected function download($themeName)
    {
        $client = new Client;
        $response = $client->get('https://github.com/ordbild/ob_start/archive/master.zip');
        file_put_contents($themeName.'.zip', $response->getBody());

        return $this;
    }

    protected function extract($themeName)
    {
        $fileName = $themeName . '.zip';
        
        $zipArchive = new ZipArchive;
        $zipArchive->open($fileName);
        $zipArchive->extractTo('wp-content/themes');
        rename('wp-content/themes/ob_start-master', 'wp-content/themes/'.$themeName);
        $zipArchive->close();

        @chmod($fileName, 0777);
        @unlink($fileName);

        return $this;
    }
}