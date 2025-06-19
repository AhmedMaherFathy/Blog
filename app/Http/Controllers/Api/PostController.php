<?php

namespace App\Http\Controllers\Api;

use App\Services\PostService;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostController extends Controller
{
    use HttpResponse;

    public function __construct(
        private PostService $postService
    ) {}

    public function store(PostRequest $request)
    {
        try {
            $post = $this->postService->createPost(
                $request->validated(),
                $request->hasFile('images') ? $request->file('images') : null
            );

            return $this->successResponse(
                data: new PostResource($post),
                message: 'Post created successfully',
                code: 201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Failed to create post',
                status: 500
            );
        }
    }

    public function show($slug)
    {
        try {
            $post = $this->postService->getPostBySlug($slug);
            return $this->successResponse(new PostResource($post));
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                message: 'Post not found',
                status: 404
            );
        }
    }

    public function update(PostRequest $request, string $slug)
    {
        try {
            $post = $this->postService->updatePost(
                $slug,
                $request->validated(),
                $request->hasFile('images') ? $request->file('images') : null
            );

            return $this->successResponse(
                data: new PostResource($post),
                message: 'Post updated successfully'
            );
        } catch (AuthorizationException $e) {
            return $this->errorResponse(
                message: 'You are not authorized to update this post',
                status: 403
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Failed to update post',
                status: 500
            );
        }
    }

    public function destroy($slug)
    {
        try {
            $this->postService->deletePost($slug);

            return $this->successResponse(
                message: 'Post and all associated content deleted successfully',
                code: 204
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                message: 'Post not found',
                status: 404
            );
        } catch (AuthorizationException $e) {
            return $this->errorResponse(
                message: 'You are not authorized to delete this post',
                status: 403
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Failed to delete post',
                status: 500
            );
        }
    }
}
