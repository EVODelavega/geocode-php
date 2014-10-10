<?php
namespace Geocode\Model;


class Response extends Data
{
    const STATUS_OK = 'OK';
    const STATUS_EMPTY = 'ZERO_RESULTS';
    const STATUS_LIMIT = 'OVER_QUERY_LIMIT';
    const STATUS_DENIED = 'REQUEST_DENIED';
    const STATUS_INVALID = 'INVALID_REQUEST';
    const STATUS_ERROR = 'UNKNOWN_ERROR';

    const ERRMODE_STRICT = 1;
    const ERRMODE_NOTICE = 2;
    const ERRMODE_CRITIC = 3;
    const ERRMODE_SILENT = 4;
    /**
     * @var string
     */
    protected $status = null;

    /**
     * @var array
     */
    protected $results = null;

    /**
     * @var int
     */
    protected $errMode = self::ERRMODE_NOTICE;

    private static $StatusCodes = array(
        self::STATUS_OK         => 0,
        self::STATUS_EMPTY      => 1,
        self::STATUS_LIMIT      => 2,
        self::STATUS_DENIED     => 3,
        self::STATUS_INVALID    => 4,
        self::STATUS_ERROR      => 5
    );

    /**
     * @var array
     */
    private static $StatusErrors = array(
        self::STATUS_EMPTY      => 'No results found',
        self::STATUS_LIMIT      => 'You have reached the hourly/daily limit of API calls',
        self::STATUS_DENIED     => 'Your request was denied by google',
        self::STATUS_INVALID    => 'Your request was invalid',
        self::STATUS_ERROR      => 'An unknown error occured'
    );

    public function __construct($mixed = null, $format = null)
    {
        if (is_string($mixed)) {
            if ($format === null) {
                $mixed = trim($mixed);
                if ($mixed{0} === '{') {
                    $format = Config::OUTPUT_JSON;
                } elseif ($mixed{0} === '<') {
                    $format = Config::OUTPUT_XML;
                } else {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Invalid data format for response, no format specified, unable to deduce'
                        )
                    );
                }
                if ($format === Config::OUTPUT_JSON) {
                    //json response
                    $mixed = json_decode($mixed);
                    $error = json_last_error();
                    if ($error !== \JSON_ERROR_NONE) {
                        throw new \RuntimeException(
                            sprintf(
                                'JSON error %d - %s',
                                $error,
                                json_last_error_msg()
                            )
                        );
                    }
                } elseif ($format === CONFIG::OUTPUT_XML) {
                    $mixed = $this->xmlToArray(
                        simplexml_load_string($mixed)
                    );
                }
            }
        }
        if ($mixed instanceof \SimpleXMLElement) {
            $mixed = $this->xmlToArray($mixed);
        }
        parent::__construct($mixed);
    }

    /**
     * @TODO => results must be poured into objects (lat-lang, addresses etc... then, they must be categorized
     * @param array $results
     * @return $this
     */
    public function setResults(array $results)
    {
        $this->results = $results;
        return $this;
    }

    /**
     * @param bool $throwOnError = false
     * @return array
     * @throws \RuntimeException
     */
    public function getResults($throwOnError = false)
    {
        if (!$this->results && $throwOnError === true) {
            throw new \RuntimeException(
                sprintf(
                    'Status %s: %s',
                    $this->status,
                    $this->status === self::STATUS_OK ? 'OK' : static::$StatusErrors[$this->status]
                ),
                static::$StatusCodes[$this->status]
            );
        }
        return $this->results;
    }

    /**
     * @param $stat
     * @return $this
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function setStatus($stat)
    {
        if (!isset(static::$StatusCodes[$stat])) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s is an unknown status code, refer to %s::STATUS_* constants',
                    $stat,
                    __CLASS__
                )
            );
        }
        $level = self::$StatusCodes[$stat];
        if ($level >= $this->errMode) {
            throw new \RuntimeException(
                sprintf(
                    'Error %s: %s',
                    $stat,
                    self::$StatusErrors[$stat]
                ),
                $level
            );
        }
        $this->status = $stat;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param \SimpleXMLElement $dom
     * @return array
     */
    protected function xmlToArray(\SimpleXMLElement $dom)
    {
        $result = array();
        foreach ($dom as $key => $value) {
            /** @var \SimpleXMLElement $value */
            if (!isset($result[$key]))
                $result[$key] = array();
            if ($value->count() > 1)
                $result[$key][] = $this->xmlToArray($value);
            else
                $result[$key] = (string) $value;
        }
        return $result;
    }
}