<?php

namespace Keboola\GmailExtractor;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;

class StateFiles
{
    /** @var string */
    private $path;

    /** @var string */
    private $stateInFile;

    /** @var array */
    private $stateIn;

    /** @var string */
    private $stateOutFile;

    /** @var array */
    private $stateOut;

    /** @var Filesystem */
    private $fileSystem;

    public function __construct($path)
    {
        $this->path = $path;
        $this->stateInFile = $this->path . '/in/state.yml';
        $this->stateOutFile = $this->path . '/out/state.yml';
        $this->fileSystem = new Filesystem;

        if ($this->fileSystem->exists($this->stateInFile)) {
            $this->stateIn = Yaml::parse(file_get_contents($this->stateInFile));
        }
    }

    /**
     * Gets input state
     * @return array
     */
    public function getStateIn()
    {
        return $this->stateIn;
    }

    /**
     * Set output state
     * @param $state array
     */
    public function setStateOut($state)
    {
        $this->stateOut = $state;
    }

    /**
     * Saves output state
     */
    public function saveStateOut()
    {
        $this->fileSystem->mkdir($this->path . '/out');
        $this->fileSystem->dumpFile($this->stateOutFile, Yaml::dump($this->stateOut));
    }
}
