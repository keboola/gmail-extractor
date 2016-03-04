<?php

namespace Keboola\GmailExtractor;

class Base64UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testDecode()
    {
        $encodedData = 'PGEgaHJlZj0iaHR0cHM6Ly93d3cua2Vib29sYS5jb20iPktlYm9vbGE8L2E-';
        $decodedData = '<a href="https://www.keboola.com">Keboola</a>';

        $this->assertEquals($decodedData, Base64Url::decode($encodedData));
    }
}
