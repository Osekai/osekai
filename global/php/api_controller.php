<?php

class Response {
    private int $statusCode;
    private string $contentType;
    private mixed $content;

    /**
     * @param int $statusCode 
     * @param string $contentType 
     * @param mixed $content 
     */
    public function __construct(mixed $content, string $contentType, int $statusCode = 200) {
    	$this->statusCode = $statusCode;
    	$this->contentType = $contentType;
    	$this->content = $content;
    }

	/**
	 * @return int
	 */
	public function getStatusCode(): int {
		return $this->statusCode;
	}

	/**
	 * @return string
	 */
	public function getContentType(): string {
		return $this->contentType;
	}

	/**
	 * @return mixed
	 */
	public function getContent(): mixed {
		return $this->content;
	}
}

class NotImplementedResponse extends Response {
    /**
     * @param int $statusCode 
     * @param string $contentType 
     * @param mixed $content 
     */
    public function __construct() {
        parent::__construct("", "text/plain", 501);
    }
}

class JsonResponse extends Response {
    public function __construct(mixed $json_content, int $statusCode = 200) {
        parent::__construct(json_encode($json_content), "application/json", $statusCode);
    }
}

class BadRequestJsonResponse extends JsonResponse {
    public function __construct(mixed $error = "Bad request") {
        parent::__construct($error, 400);
    }
}

class UnauthorizedJsonResponse extends JsonResponse {
    public function __construct(mixed $error = "Unauthorized") {
        parent::__construct($error, 401);
    }
}

class ApiController {
    protected function get(): Response { 
        return new NotImplementedResponse; 
    }

    protected function post(): Response { 
        return new NotImplementedResponse; 
    }

    public function execute() {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'POST':
                $response = $this->post();
                break;
            case 'GET':
                $response = $this->get();
                break;
            default:
                $response = new NotImplementedResponse;
                break;
        }

        http_response_code($response->getStatusCode());
        header("Content-Type: " . $response->getContentType());
        echo $response->getContent();
    }
}