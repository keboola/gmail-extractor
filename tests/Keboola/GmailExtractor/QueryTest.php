<?php

namespace Keboola\GmailExtractor;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildQuery()
    {
        $query = new Query('subject:gmail-extractor');
        $this->assertEquals('subject:gmail-extractor', $query->buildQuery());

        $datetime = new \DateTime('2016-03-10');
        $query = new Query('subject:gmail-extractor', [], $datetime);
        $this->assertEquals('(subject:gmail-extractor) after:2016/03/10', $query->buildQuery());
    }
}
