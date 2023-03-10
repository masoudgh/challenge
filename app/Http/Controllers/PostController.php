<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\GeneralResource;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return (new PostCollection($this->postRepository->getAllPosts()))->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PostStoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(PostStoreRequest $request): JsonResponse
    {
        $post = $this->postRepository->createPost(array_merge($request->validated(), ['user_id' => Auth::id()]));
        $post->load(['user']);

        return (new PostResource($post))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PostUpdateRequest $request
     * @param Post $post
     *
     * @return JsonResponse
     */
    public function update(PostUpdateRequest $request, Post $post): JsonResponse
    {
        $this->postRepository->updatePost($post->id, $request->validated());

        return (new PostResource($post->refresh()->load(['user'])))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     *
     * @return JsonResponse
     */
    public function show(Post $post): JsonResponse
    {
        return (new PostResource($post->load(['user'])))->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     *
     * @return Response
     */
    public function destroy(Post $post): Response
    {
        $post->delete();

        return (new GeneralResource(null))->response()->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
