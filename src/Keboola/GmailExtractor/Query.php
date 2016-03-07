<?php

namespace Keboola\GmailExtractor;

class Query
{
    /** @var  string */
    private $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * Gets query
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }
}
