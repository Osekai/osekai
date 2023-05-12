<?php

class OsekaiHttpRequestResult
{
    private int $statusCode;
    private mixed $result;

	/**
	 * @return mixed
	 */
	public function getResult(): mixed {
		return $this->result;
	}

	/**
	 * @return int
	 */
	public function getStatusCode(): int {
		return $this->statusCode;
	}

    /**
     * @param int $statusCode 
     * @param mixed $result 
     */
    public function __construct(int $statusCode, mixed $result) {
    	$this->statusCode = $statusCode;
    	$this->result = $result;
    }

    public function toJson(): array {
        return json_decode($this->result, flags: JSON_OBJECT_AS_ARRAY);
    }
}

class OsekaiHttpRequest
{
    private CurlHandle $handle;

    public function __construct($url)
    {
        $this->handle = curl_init($url);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->handle, CURLOPT_USERAGENT, "Osekai Website");
    }

    public function setHeaders($headers): self
    {
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        return $this;
    }

    public function get(): OsekaiHttpRequestResult {
        $result = curl_exec($this->handle);
        $statusCode = curl_getinfo($this->handle, CURLINFO_RESPONSE_CODE);

        return new OsekaiHttpRequestResult($statusCode, $result);
    }
}