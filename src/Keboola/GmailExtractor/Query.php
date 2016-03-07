<?php

namespace Keboola\GmailExtractor;

class Query
{
    /** @var  string */
    private $query;

    /** @var  [] headers to save */
    private $headers;

    public function __construct($query, $headers = [])
    {
        $this->query = $query;
        $this->headers = $headers;
    }

    /**
     * Gets query
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Gets headers
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
