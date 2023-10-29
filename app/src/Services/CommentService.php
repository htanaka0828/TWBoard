<?php
namespace TWB\Services;

use TWB\Models\CommentModel;
use \Illuminate\Contracts\Pagination\LengthAwarePaginator as Paginator;

class CommentService
{

    const DEFAULT_PER_PAGE = 20;

    public function __construct(
        protected ?CommentModel $commentModel = null)
    {
        $this->commentModel = $this->commentModel ?? new CommentModel();
    }

    public function list(int $page = 1): Paginator
    {
        return $this->commentModel->getBuilder()->paginate(self::DEFAULT_PER_PAGE, ['*'], 'page', $page);
    }

    public function findById($id)
    {
        return $this->commentModel->findByKey(CommentModel::PRIMARY_KEY, $id);
    }

    public function delete($id)
    {
        return $this->commentModel->delete($id);
    }
}
