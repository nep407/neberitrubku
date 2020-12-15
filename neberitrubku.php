<?php

namespace Neberitrubku;

class Checker
{
    public $useragent = 'Mozilla BoberBox';
    private $_url = 'https://www.neberitrubku.ru/nomer-telefona/';
    private $_timeout = 1;
    private $_maxRedirects = 3;
    private $_followLocation = true;
    private $_cookieFileLocation = './';
    private $_includeHeader = false;
    private $_noBody = false;
    private $_referer = '';
    protected $phone;
    protected $ratingValue;
    protected $ratingCount;
    protected $reviewCount;
    protected $bestRating;
    protected $worstRating;

    /**
     * @param $phone
     * @return bool
     */
    public function findByPhone($phone): bool
    {
        $data = $this->request($this->_url . $phone);
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/is', $data, $match);
        $json = json_decode($match[1]);
        if (is_object($json)) {
            $this->phone = $json->name;
            $this->ratingValue = (int)$json->aggregateRating->ratingValue;
            $this->ratingCount = (int)$json->aggregateRating->ratingCount;
            $this->reviewCount = (int)$json->aggregateRating->reviewCount;
            $this->bestRating  = (int)$json->aggregateRating->bestRating;
            $this->worstRating = (int)$json->aggregateRating->worstRating;
            return true;
        }

        return false;
    }

    /**
     * @param $url
     * @param array $postFields
     * @return string
     */
    private function request($url, $postFields = []) : string
    {
        $s = curl_init();

        curl_setopt($s, CURLOPT_URL, $url);
        curl_setopt($s, CURLOPT_HTTPHEADER, ['Expect:']);
        curl_setopt($s, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($s, CURLOPT_MAXREDIRS, $this->_maxRedirects);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_FOLLOWLOCATION, $this->_followLocation);
        curl_setopt($s, CURLOPT_COOKIEJAR, $this->_cookieFileLocation);
        curl_setopt($s, CURLOPT_COOKIEFILE, $this->_cookieFileLocation);

        if ($postFields) {
            curl_setopt($s, CURLOPT_POST, true);
            curl_setopt($s, CURLOPT_POSTFIELDS, $postFields);
        }

        if ($this->_includeHeader) {
            curl_setopt($s, CURLOPT_HEADER, true);
        }

        if ($this->_noBody) {
            curl_setopt($s, CURLOPT_NOBODY, true);
        }

        curl_setopt($s, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($s, CURLOPT_REFERER, $this->_referer);

        $responseBody = curl_exec($s);

        curl_close($s);

        return $responseBody;
    }

    /**
     * @return int
     */
    public function getRatingValue() : int
    {
        return $this->ratingValue;
    }

    /**
     * @return int
     */
    public function getRatingCount() : int
    {
        return $this->ratingCount;
    }

    /**
     * @return int
     */
    public function getReviewCount() : int
    {
        return $this->reviewCount;
    }

    /**
     * @return int
     */
    public function getBestRating() : int
    {
        return $this->bestRating;
    }

    /**
     * @return int
     */
    public function getWorstRating() : int
    {
        return $this->worstRating;
    }

    /**
     * @return string
     */
    public function getPhone() : string
    {
        return $this->phone;
    }

}