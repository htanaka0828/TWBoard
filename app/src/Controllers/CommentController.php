<?php
namespace TWB\Controllers;

use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;
use TWB\Constants\HttpStatusCode;
use TWB\Services\LogService as Logger;
use TWB\Controllers\AbstractController;
use TWB\Services\CommentService;


class CommentController extends AbstractController
{

    public function __construct(
        protected ?Logger $logger = null,
        protected ?CommentService $commentService = null
        )
    {
        $this->commentService = $this->commentService ?? new CommentService();
        parent::__construct($logger);
    }

    public function list(Request $request, Response $response, array $params): Response
    {
        // page番号を取得する(getパラメーター)
        $page = $request->getQueryParam('page', 1);

        // そのページで表示すべきデータを取得する
        $pager = $this->commentService->list($page);

        // レスポンスを返す
        return $this->renderJsonPager($response, $pager);
    }

    public function get(Request $request, Response $response, array $params): Response
    {
        // comment idを取得する(URLのパスから)
        $commentId = $params['commentId'];

        // そのidのcommentを取得する
        $comment = $this->commentService->findById($commentId);
        if($comment) {
            // レスポンスを返す
            return $this->renderJson($response, HttpStatusCode::OK, json_decode(json_encode($comment), true));
        }

        return $this->renderJson($response, HttpStatusCode::NOT_FOUND);
    }

    public function delete(Request $request, Response $response, array $params): Response
    {
        // comment idを取得する(URLのパスから)
        $commentId = $params['commentId'];

        // そのidのcommentを取得する
        $comment = $this->commentService->findById($commentId);
        if(!$comment) {
            // データが無ければ404を返す
            return $this->renderJson($response, HttpStatusCode::NOT_FOUND);
        }

        // そのidのcommentを取得する
        $this->commentService->delete($commentId);

        // レスポンスを返す
        return $this->renderJson($response);
    }
}