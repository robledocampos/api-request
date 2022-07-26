<?php


class RequestService
{
    private string $basePath;
    private bool $ssl;
    private $curl;
    private string $endpoint;
    private string $queryString;

    const REQUEST_METHODS = ["POST", "GET", "PUT", "DELETE", "PATCH"];

    public function __construct(string $basePath, bool $ssl = false)
    {
        $this->basePath = $basePath;
        $this->ssl = $ssl;
        $this->endpoint = "";
        $this->queryString = "";
        $this->curl = curl_init();
        if ($this->ssl) $this->setSsL();
    }

    public function call() : Array
    {
        curl_setopt($this->curl, CURLOPT_URL, $this->mountUrl());
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HEADER, 1);
        $curlResult = curl_exec($this->curl);
        $httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
        $header = substr($curlResult, 0, $headerSize);
        $body = substr($curlResult, $headerSize);
        $result['httpCode'] = $httpCode;
        $result['header'] = $this::getCurlHeaders($header);
        $result['body'] = $body;
        curl_close($this->curl);

        return $result;
    }


    public function setHeaders(Array $headers) : void
    {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
    }

    public function setMethod(string $method) : void
    {
        $method = strtoupper($method);
        if (!in_array($method, self::REQUEST_METHODS)) {

            throw new UnknownMethodException();
        }
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
    }

    public function setEndpoint(string $endpoint) : void
    {
        $this->endpoint = $endpoint;
    }

    public function setQueryString(Array $parameters) : void
    {
        $this->queryString = "?".http_build_query($parameters, '', '&');
    }

    public function setPayload(Array $payload) : void
    {
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $payload);
    }

    private function mountUrl() : string
    {
        $url = $this->basePath . $this->endpoint;
        if ($this->queryString) $url .= $this->queryString;

        return $url;
    }

    private function setSsL() : void
    {
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, $this->ssl);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
    }

    private function getCurlHeaders($header) : Array
    {
        $headers = [];
        $lines = explode("\n",$header);
        foreach ($lines as $line) {
            $dotsPosition = strpos($line,":");
            if ($dotsPosition !== false) {
                $headerName = substr($line,0, $dotsPosition);
                $headerValue = substr($line, $dotsPosition+2, strlen($line));
                $headers[$headerName] = $headerValue;
            }
        }

        return $headers;
    }
}
