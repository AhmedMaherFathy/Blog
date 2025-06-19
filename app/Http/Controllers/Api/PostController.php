<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Support\Str;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostController extends Controller
{
    use HttpResponse;
    public function index()
    {
        $posts = Post::with(['user', 'media'])->get();
        return response()->json($posts);
    }

    public function store(PostRequest $request)
    {
        $post = Post::create([
            'title' => $request->validated()['title'],
            'content' => $request->validated()['content'],
            'user_id' => auth()->id()
        ]);

        if ($request->hasFile('images')) {
            $post->addMultipleMediaFromRequest($request->file('images'));
        }

        return $this->successResponse(message: 'Post created successfully', code: 201);
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->with(['user:name,id', 'media'])
            ->firstOrFail();

        return $this->successResponse(new PostResource($post));
    }

    public function update(PostRequest $request, string $slug)
    {
        try {
            DB::beginTransaction();

            $post = Post::with('media')
                ->where('slug', $slug)
                ->firstOrFail();
            Gate::authorize('update', $post);


            $updateData = [
                'title' => $request->validated('title'),
                'content' => $request->validated('content'),
            ];
            $slug = $post->slug;

            if ($request->has('title.en')) {
                $updateData['slug'] = Str::slug($request->input('title.en'));
            }

            $post->update([
                'title' => $updateData['title'],
                'content' => $updateData['content'],
                'slug' => $slug ?? $post->slug,
            ]);


            if ($request->hasFile('images')) {
                $post->updatePostMedia($request->file('images'));
            }

            info("arrive here");

            DB::commit();

            return $this->successResponse(
                data: new PostResource($post->refresh()),
                message: 'Post updated successfully'
            );
        } catch (AuthorizationException $e) {
            DB::rollBack();
            return $this->errorResponse(
                message: 'You are not authorized to update this post',
                status: 403
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(
                message: 'Failed to update post',
                status: 500
            );
        }
    }

    public function destroy($slug)
    {
        try {
            $post = Post::with(['media', 'comments'])
                ->where('slug', $slug)
                ->firstOrFail();

            Gate::authorize('delete', $post);

            DB::transaction(function () use ($post) {
                $post->media()->delete();

                $post->comments()->delete();

                $post->delete();
            });

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
