<?php

namespace Rosterbuster\Dialogflow;

use ZipArchive;

class Zipper
{
    /**
     * Instance of php's zip archive.
     *
     * @var ZipArchive
     */
    protected $zipArchive;

    public function __construct()
    {
        $this->zipArchive = new ZipArchive();
    }

    /**
     * Unzip the given file.
     *
     * @param  string $path
     * @return string
     */
    public function unzip(string $path)
    {
        if (!$this->zipArchive->open($path)) {
            throw nex \Exception('Unable to unzip archive.');
        }

        $destination = str_replace('.zip', '', $path);
        $this->zipArchive->extractTo($destination);
        $this->zipArchive->close();

        return $destination;
    }

    /**
     * Put everything in a zip.
     *
     * @param  string $path
     * @return void
     */
    public function zip(string $path)
    {
        $path = realpath($path);

        if (!$this->zipArchive->open($path . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            throw new \Exception('Could not create zip archive.');
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                continue;
            }

            $filePath     = $file->getRealPath();
            $relativePath = substr($filePath, strlen($path) + 1);

            // Add current file to archive
            $this->zipArchive->addFile($filePath, $relativePath);
        }

        $this->zipArchive->close();
    }
}
