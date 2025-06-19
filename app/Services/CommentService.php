<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class CommentService
{
    public function createComment(array $data, string $postSlug, int $userId): Comment
    {
        $post = Post::where('slug', $postSlug)->firstOrFail();

        $comment = new Comment([
            'content' => $data['content'],
            'user_id' => $userId,
            'parent_id' => $data['parent_id'] ?? null
        ]);

        return $post->comments()->save($comment);
    }

    public function getPostComments(string $postSlug): Collection
    {
        return Comment::with([
            'user:id,name',
            'replies.user:id,name',
            'replies.replies.user:id,name'
        ])
        ->whereHas('post', function($q) use ($postSlug) {
            $q->where('slug', $postSlug);
        })
        ->whereNull('parent_id')
        ->get();
    }
}
