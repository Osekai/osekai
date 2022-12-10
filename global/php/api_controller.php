<?php

class ApiResult {
    private int $statusCode;
    private mixed $value;

    /**
     * @param int $statusCode 
     * @param string $contentType 
     * @param mixed $content 
     */
    public function __construct(mixed $value, int $statusCode) {
    	$this->statusCode = $statusCode;
    	$this->value = $value;
    }

	/**
	 * @return int
	 */
	public function getStatusCode(): int {
		return $this->statusCode;
	}

	/**
	 * @return mixed
	 */
	public function getValue(): mixed {
		return $this->value;
	}
}

class EmptyContent {}

class OkApiResult extends ApiResult {
    public function __construct(mixed $value = "Ok") {
        parent::__construct($value, 200);
    }
}

class NotImplementedApiResult extends ApiResult {
    /**
     * @param int $statusCode 
     * @param string $contentType 
     * @param mixed $content 
     */
    public function __construct() {
        parent::__construct(new EmptyContent, 501);
    }
}

class BadArgumentsApiResult extends ApiResult {
    public function __construct(mixed $error = "Bad request") {
        parent::__construct($error, 400);
    }
}

class ResourceNotFoundApiResult extends ApiResult {
    public function __construct(mixed $error = "Resource not found") {
        parent::__construct($error, 404);
    }
}

class UnknownErrorResult extends ApiResult {
    public function __construct(mixed $error = "Unknown Error") {
        parent::__construct($error, 500);
    }
}

class UnauthorizedResult extends ApiResult {
    public function __construct(mixed $error = "Unauthorized") {
        parent::__construct($error, 401);
    }
}

class ApiController {
    public function get(): ApiResult { 
        return new NotImplementedApiResult; 
    }

    public function post(): ApiResult { 
        return new NotImplementedApiResult; 
    }
}

interface ApiResultSerializer {
    function serialize(ApiResult $result): string;
    function getContentType(): string;
}

class JsonApiResultSerializer implements ApiResultSerializer {
    public function serialize(ApiResult $result): string {
        return json_encode($result->getValue());
    }
    public function getContentType(): string {
        return "application/json";
    }
}

class ApiControllerExecutor {
    public static function execute(ApiController $controller, ApiResultSerializer $serializer) {
        try {
            $method = $_SERVER['REQUEST_METHOD'];

            switch ($method) {
                case 'POST':
                    $result = $controller->post();
                    break;
                case 'GET':
                    $result = $controller->get();
                    break;
                default:
                    $result = new NotImplementedApiResult;
                    break;
            }
        } catch (InvalidParameterException $exception) {
            $result = new BadArgumentsApiResult($exception->getMessage());
        } catch (InvalidOperationException $exception) {
            $result = new BadArgumentsApiResult($exception->getMessage());
        } catch (ResourceNotFoundException $exception) {
            $result = new ResourceNotFoundApiResult($exception->getMessage());
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            Logging::PutLog("Got exception in " . $controller::class . ":" . $exception->getMessage());
            $result = new UnknownErrorResult();
        }

        http_response_code($result->getStatusCode());
        header("Content-Type: " . $serializer->getContentType());
        echo $serializer->serialize($result);
    }
}
