<?php

namespace Keboola\GmailExtractor;

class Query
{
    /** @var string */
    private $query;

    /** @var array headers to save */
    private $headers;

    /** @var null|\DateTime */
    private $date;

    public function __construct($query, $headers = [], $date = null)
    {
        $this->query = $query;
        $this->headers = $headers;
        $this->date = $date;
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
     * @return bool
     */
    public function hasDate()
    {
        return ($this->date instanceof \DateTime);
    }

    /**
     * Gets headers
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Gets built query
     * @return string
     */
    public function buildQuery()
    {
        return $this->hasDate() ? $this->getQueryWithDate() : $this->getQuery();
    }

    /**
     * @return string
     */
    private function getQueryWithDate()
    {
        return '(' . $this->query . ') after:' . $this->date->format('Y/m/d');
    }
}
