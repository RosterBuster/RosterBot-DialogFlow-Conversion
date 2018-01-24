<?php

namespace Rosterbuster\Dialogflow;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MergeCommand extends Command
{
    /**
     * Instance of merger class.
     *
     * @var Merger
     */
    protected $merger;

    /**
     * Instance of zipper class.
     *
     * @var Zipper
     */
    protected $zipper;

    protected function configure()
    {
        $this->setName('merge')
            ->addArgument('pathToTrainingZip', InputArgument::REQUIRED, 'The zip with the training (probably PROD)')
            ->addArgument('pathToDevelopmentZip', InputArgument::REQUIRED, 'The zip with the new intents (probably DEV)')
            ->setDescription('Merge two dialogflow exports zips together.');

        $this->outputDir   = 'dialogflow-merged';
        $this->entitiesDir = 'dialogflow-merged/entities';
        $this->intentsDir  = 'dialogflow-merged/intents';

        $this->merger = new Merger($this->outputDir, $this->entitiesDir, $this->intentsDir);
        $this->zipper = new Zipper();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $trainingZip    = $input->getArgument('pathToTrainingZip');
        $developmentZip = $input->getArgument('pathToDevelopmentZip');

        // 1. Unzip files
        $output->writeln('Unzipping files');
        $trainingPath    = $this->zipper->unzip($trainingZip);
        $developmentPath = $this->zipper->unzip($developmentZip);

        // 2. Start merging process
        $output->writeln("Merging {$trainingZip} with {$developmentZip}");
        $this->start();

        $this->merger->copyDevelopment($developmentPath);

        // 3. Merge entities
        $output->writeln('Merging entities');
        $this->merger->mergeEntities($trainingPath, $developmentPath);

        // 4. Merge intents
        $output->writeln('Merging intents');
        $this->merger->mergeIntents($trainingPath, $developmentPath);

        // 5. Cleanup
        $this->stop($trainingPath, $developmentPath);
    }

    /**
     * Remove the directory and make a zip.
     *
     * @param  string $trainingDir
     * @param  string $developmentDir
     * @return void
     */
    protected function stop(string $trainingDir, $developmentDir)
    {
        $this->zipper->zip($this->outputDir);

        $this->deleteDirectory($this->outputDir);
        $this->deleteDirectory($trainingDir);
        $this->deleteDirectory($developmentDir);
    }

    /**
     * Set up the directory where we are going to export everything into.
     *
     * @return void
     */
    protected function start()
    {
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir);
        }

        if (!is_dir($this->entitiesDir)) {
            mkdir($this->entitiesDir);
        }

        if (!is_dir($this->intentsDir)) {
            mkdir($this->intentsDir);
        }
    }

    /**
     * Delete the directory at the given path.
     *
     * @param  string $path
     * @return void
     */
    protected function deleteDirectory(string $path)
    {
        if (!is_dir($path)) {
            return;
        }

        $objects = scandir($path);

        foreach ($objects as $object) {
            if ($object == '.' || $object == '..') {
                continue;
            }

            $item = "{$path}/{$object}";

            if (is_dir($item)) {
                $this->deleteDirectory($item);

                continue;
            }

            unlink($item);
        }

        rmdir($path);
    }
}
