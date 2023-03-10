<?php

namespace App\Interfaces;

interface PostRepositoryInterface
{
    public function getAllPosts();

    public function getPostById($postID);

    public function deletePost($postID);

    public function createPost(array $postDetails);

    public function updatePost($postID, array $newDetails);
}
