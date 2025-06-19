<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\Comment;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    use HttpResponse;

    public function store(CommentRequest $request, string $postSlug)
    {
        $post = Post::where('slug', $postSlug)->firstOrFail();

        $comment = new Comment([
            'content' => $request->validated('content'),
            'user_id' => auth()->id(),
            'parent_id' => $request->validated('parent_id')
        ]);

        $post->comments()->save($comment);

        return $this->successResponse(
            message: $comment->parent_id ? 'Reply added successfully' : 'Comment added successfully',
        );
    }

    public function getPostComments(string $postSlug)
    {
        $comments = Comment::with([
        'user:id,name',
        'replies.user:id,name', // Eager load user for replies
        'replies.replies.user:id,name' // For nested replies (if needed)
            ])
            ->whereHas('post', function($q) use ($postSlug) {
                $q->where('slug', $postSlug);
            })
            ->whereNull('parent_id')
            ->get();

        return $this->successResponse(CommentResource::collection($comments),message: 'Comments retrieved successfully');
    }
}
