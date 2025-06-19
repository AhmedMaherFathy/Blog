<?php

namespace App\Http\Controllers\Api;

use App\Services\CommentService;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentController extends Controller
{
    use HttpResponse;

    public function __construct(
        private CommentService $commentService
    ) {}

    public function store(CommentRequest $request, string $postSlug)
    {
        try {
            $comment = $this->commentService->createComment(
                $request->validated(),
                $postSlug,
                auth()->id()
            );

            return $this->successResponse(
                data: new CommentResource($comment),
                message: $comment->parent_id ? 'Reply added successfully' : 'Comment added successfully',
                code: 201
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                message: 'Post not found',
                status: 404
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Failed to add comment',
                status: 500
            );
        }
    }

    public function getPostComments(string $postSlug)
    {
        try {
            $comments = $this->commentService->getPostComments($postSlug);
            return $this->successResponse(
                data: CommentResource::collection($comments),
                message: 'Comments retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Failed to retrieve comments',
                status: 500
            );
        }
    }
}
