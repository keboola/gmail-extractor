<?php

namespace Keboola\GmailExtractor;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class StateFilesTest extends \PHPUnit_Framework_TestCase
{
    private $path = '/tmp/state-files';

    /** @var Filesystem */
    private $fs;

    protected function setUp()
    {
        $this->fs = new Filesystem;
        $this->fs->remove($this->path);
    }

    protected function tearDown()
    {
        $this->fs->remove($this->path);
    }

    public function testWithoutStateFiles()
    {
        $stateFiles = new StateFiles($this->path);

        $this->assertSame(null, $stateFiles->getStateIn());
    }

    public function testWithStateFiles()
    {
        $this->fs->mkdir($this->path . '/in');
        $state = [
            'query-dates' => [
                'subject:gmail-extractor' => '2016-03-10 11:09:54'
            ],
        ];
        $this->fs->dumpFile($this->path . '/in/state.yml', Yaml::dump($state));
        $stateFiles = new StateFiles($this->path);
        $stateFiles->setStateOut($state);
        $stateFiles->saveStateOut();

        $this->assertFileEquals($this->path . '/in/state.yml', $this->path . '/out/state.yml');

    }
}
