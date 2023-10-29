<?php
namespace TWB\Config;

use Slim\App;
use Slim\Routing\RouteCollectorProxy as Proxy;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;
use TWB\Constants\HttpStatusCode;


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
            $group->post('/login', function(Request $request, Response $response, array $params) {
                // @todo login処理をするよ
                $response->withStatus(HttpStatusCode::OK)->getBody()->write('Healthy');
                return $response;
            });

            $group->group('/comments', function (Proxy $group) {
                $group->get('/', function (Request $request, Response $response, array $params) {
                    // @todo comment listを返す
                    $response->withStatus(HttpStatusCode::OK)->getBody()->write('Healthy');
                    return $response;
                });
                $group->get('/{commentId}', function (Request $request, Response $response, array $params) {
                    // @todo comment listを返す
                    $response->withStatus(HttpStatusCode::OK)->getBody()->write('Healthy');
                    return $response;
                });
                $group->post('/{commentId}/update', function (Request $request, Response $response, array $params) {
                    // @todo comment listを返す
                    $response->withStatus(HttpStatusCode::OK)->getBody()->write('Healthy');
                    return $response;
                });
                $group->post('/{commentId}/delete', function (Request $request, Response $response, array $params) {
                    // @todo comment listを返す
                    $response->withStatus(HttpStatusCode::OK)->getBody()->write('Healthy');
                    return $response;
                });
            });

            $group->group('/accounts', function (Proxy $group) {
                $group->get('/me', function (Request $request, Response $response, array $params) {
                    // @todo ログイン済みユーザーのaccountのデータを返す
                    $response->withStatus(HttpStatusCode::OK)->getBody()->write('Healthy');
                    return $response;
                });

                $group->post('/create', function (Request $request, Response $response, array $params) {
                    // @todo accountを作成する
                    $response->withStatus(HttpStatusCode::OK)->getBody()->write('Healthy');
                    return $response;
                });
            });
        });

        return $app;
    }
}