<?php

namespace Keboola\GmailExtractor;

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
        $this->stateInFile = $this->path . '/in/state.json';
        $this->stateOutFile = $this->path . '/out/state.json';
        $this->fileSystem = new Filesystem;

        if ($this->fileSystem->exists($this->stateInFile)) {
            $this->stateIn = json_decode(file_get_contents($this->stateInFile), true);
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
        $this->fileSystem->dumpFile($this->stateOutFile, json_encode($this->stateOut));
    }
}
