<?php

namespace App\Repositories;

use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements PostRepositoryInterface
{
    public function getAllPosts(): Collection
    {
        return Post::all();
    }

    public function getPostById($postID): Post
    {
        return Post::findOrFail($postID);
    }

    public function deletePost($postID)
    {
        Post::destroy($postID);
    }

    public function createPost(array $postDetails): Post
    {
        return Post::create($postDetails);
    }

    public function updatePost($postID, array $newDetails): int
    {
        return Post::query()->where('id', '=', $postID)->update($newDetails);
    }
}
