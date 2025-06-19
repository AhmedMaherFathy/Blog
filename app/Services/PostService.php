<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\UploadedFile;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostService
{
    public function createPost(array $data, ?array $images = null): Post
    {
        $post = Post::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'user_id' => auth()->id()
        ]);

        if ($images) {
            $this->addPostMedia($post, $images);
        }

        return $post;
    }

    public function getPostBySlug(string $slug): Post
    {
        return Post::where('slug', $slug)
            ->with(['user:name,id', 'media'])
            ->firstOrFail();
    }

    public function updatePost(string $slug, array $data, ?array $images = null): Post
    {
        return DB::transaction(function () use ($slug, $data, $images) {
            $post = Post::with('media')
                ->where('slug', $slug)
                ->firstOrFail();

            Gate::authorize('update', $post);

            $updateData = [
                'title' => $data['title'],
                'content' => $data['content'],
            ];

            if (isset($data['title']['en'])) {
                $updateData['slug'] = Str::slug($data['title']['en']);
            }

            $post->update($updateData);

            if ($images) {
                $this->updatePostMedia($post, $images);
            }

            return $post->refresh();
        });
    }

    public function deletePost(string $slug): void
    {
        DB::transaction(function () use ($slug) {
            $post = Post::with(['media', 'comments'])
                ->where('slug', $slug)
                ->firstOrFail();

            Gate::authorize('delete', $post);

            $post->media()->delete();
            $post->comments()->delete();
            $post->delete();
        });
    }

    protected function addPostMedia(Post $post, array $images): void
    {
        foreach ($images as $image) {
            $post->addMedia($image)->toMediaCollection('post_images');
        }
    }

    protected function updatePostMedia(Post $post, array $images): void
    {
        $post->clearMediaCollection('post_images');
        $this->addPostMedia($post, $images);
    }
}
