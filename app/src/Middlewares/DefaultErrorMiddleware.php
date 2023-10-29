<?php
namespace TWB\Middlewares;

use Throwable;

use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Http\ServerRequest as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Monolog\Logger;
use TWB\Constants\HttpStatusCode;

class DefaultErrorMiddleware
{

    /**
     * DefaultErrorMiddleware constructor.
     * @param App $app
     * @param Logger $logger
     */
    public function __construct(private App $app, private Logger $logger)
    {}

    /**
     * @param Throwable $exception
     * @param bool $displayErrorDetails
     * @param bool $logErrors
     * @param bool $logErrorDetails
     * @return Response
     */
    public function __invoke(
        Request $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails) : Response
    {
        $response = $this->app->getResponseFactory()->createResponse();
        if ($exception instanceof HttpNotFoundException) {
            $response = $response->withStatus(HttpStatusCode::NOT_FOUND);
            $response->getBody()->write($exception->getMessage());
        } else {
            $this->logger->critical($exception->getMessage());
            if (method_exists($exception, 'getErrorCodes')) {
                $errors = $exception->getErrorCodes();
                $messages = [];
                foreach ($errors as $key => $error) {
                    $messages[$key] = is_array($error) ? array_values(array_unique($error)): [$error];
                }
                $result = ['errors' => $messages];
                $response = $response->withStatus($exception->getCode());
            } else {
                $result = ['errors' => $exception->getMessage()];
                $response = $response->withStatus(HttpStatusCode::INTERNAL_SERVER_ERROR);
            }
            $response = $response->withHeader('Content-type', 'application/json');
            $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));
        }
        return $response;
    }
}