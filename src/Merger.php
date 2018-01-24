<?php

namespace Rosterbuster\Dialogflow;

class Merger
{
    /**
     * The output dir used for generating the merged zip.
     *
     * @var string
     */
    protected $outputDir;

    /**
     * The merged entities directory.
     *
     * @var string
     */
    protected $entitiesDir;

    /**
     * The merged intents directory.
     *
     * @var string
     */
    protected $intentsDir;

    public function __construct($outputDir)
    {
        $this->outputDir   = $outputDir;
        $this->entitiesDir = $outputDir . '/entities';
        $this->intentsDir  = $outputDir . '/intents';
    }

    /**
     * Copy everything from the development environment.
     *
     * @param  string $developmentDir
     * @return void
     */
    public function copyDevelopment(string $developmentDir)
    {
        $files = glob($developmentDir . '/*');

        $this->copyFiles($files, $this->outputDir);
    }

    /**
     * Merge entities from production into development.
     *
     * @param  string $trainingDir
     * @param  string $developmentDir
     * @return void
     */
    public function mergeEntities(string $trainingDir, string $developmentDir)
    {
        $this->merge('entities', $trainingDir, $developmentDir, 'entries', $this->entitiesDir);
    }

    /**
     * Merge the intents from production into development.
     *
     * @param  string $trainingDir
     * @param  string $developmentDir
     * @return void
     */
    public function mergeIntents(string $trainingDir, string $developmentDir)
    {
        $this->merge('intents', $trainingDir, $developmentDir, 'usersays', $this->intentsDir);
    }

    /**
     * Copy the given files to the given destination.
     *
     * @param  array  $files
     * @param  string $destination
     * @return void
     */
    protected function copyFiles(array $files, string $destination)
    {
        foreach ($files as $file) {
            if (is_dir($file)) {
                continue;
            }

            $filenameArray = explode('/', $file);
            $filename      = end($filenameArray);

            copy($file, $destination . '/' . $filename);
        }
    }

    /**
     * Merge two directories with eachother.
     *
     * @param  string $directory
     * @param  string $trainingDir
     * @param  string $developmentDir
     * @param  string $keyword
     * @param  string $destination
     * @return void
     */
    protected function merge(
        string $directory,
        string $trainingDir,
        string $developmentDir,
        string $keyword,
        string $destination
    ) {
        $developmentEntities = glob("{$developmentDir}/{$directory}/*");

        $this->copyFiles($developmentEntities, $destination);

        // Overwrite all files with the given keyword
        $trainingEntities = glob("{$trainingDir}/{$directory}/*{$keyword}*");

        $this->copyFiles($trainingEntities, $destination);
    }
}
