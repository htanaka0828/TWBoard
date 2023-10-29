<?php
namespace TWB\Config;

use Slim\App;
use Slim\Routing\RouteCollectorProxy as Proxy;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;
use TWB\Constants\HttpStatusCode;

use TWB\Controllers\CommentController;


class Routes {
    public function __invoke(App $app) {
        $app->options('/{routes:.+}', function ($request, $response, $args) {
            return $response;
        });

        $app->add(function ($request, $handler) {
            $response = $handler->handle($request);
            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, Access-Api-Token, Api-Token, sentry-trace, baggage')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        });

        $app->get('/', function ($request, $response, $args) {
            $response->getBody()->write("Hello world!");
            return $response;
        });
 
        $app->get('/health', function (Request $request, Response $response, array $params) {
            $response->withStatus(HttpStatusCode::OK)->getBody()->write('Healthy');
            return $response;
        });

        $app->group('/api', function (Proxy $group) {
            // $group->post('/login', LoginController::class . ':login');
            // $group->post('/logout', LoginController::class . ':logout');

            $group->group('/comments', function (Proxy $group) {
                $group->get('/', CommentController::class . ':list');
                $group->get('/{commentId}', CommentController::class . ':get');

                // @todo 中身の実装をまだしてない
                // $group->post('/create', CommentController::class . ':create');
                // $group->post('/{commentId}/update', CommentController::class . ':update');

                $group->post('/{commentId}/delete', CommentController::class . ':delete');
            });

            $group->group('/accounts', function (Proxy $group) {
                // $group->get('/me', AccountController::class . ':me');
                // $group->post('/create', AccountController::class . ':create');
            });
        });

        return $app;
    }
}