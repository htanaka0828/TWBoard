<?php
namespace TWB\Controllers;

use \Illuminate\Contracts\Pagination\LengthAwarePaginator as Paginator;
use Slim\Http\Response;
use TWB\Services\LogService as Logger;
use TWB\Constants\HttpStatusCode;

abstract class AbstractController
{
    /**
     * AbstractController constructor.
     */
    public function __construct(
        protected ?Logger $logger = null)
    {
        $this->logger = $logger ?? new Logger();
    }

    /**
     * @param Response $response
     * @param int|null $statusCode
     * @param array $result
     * @return Response
     */
    protected function renderJson(
        Response $response,
        int $statusCode = HttpStatusCode::OK,
        array $result = []): Response
    {
        return $response->withJson($result, $statusCode);
    }

    /**
     * @param Response $response
     * @param Paginator $pager
     * @param string $listKey
     * @param int|null $statusCode
     * @return Response
     */
    protected function renderJsonPager(
        Response $response,
        Paginator $pager,
        string $listKey = 'list',
        int $statusCode = HttpStatusCode::OK,
        ): Response
    {
        $result = [
            $listKey => $pager->items(),
            'pager' => [
                'totalItems' => $pager->total(),
                'totalPages' => $pager->lastPage(),
                'currentPage' => $pager->currentPage(),
            ]
        ];
        return $response->withJson($result, $statusCode);
    }
}