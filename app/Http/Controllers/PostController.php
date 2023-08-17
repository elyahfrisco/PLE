<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\PostCategory;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::search()->filter()->order()->with('category')->paginate(10);
        $post_category = PostCategory::orderBy('name')->get();
        return Inertia::render('Post/index', compact('posts', 'post_category'));
    }

    public function _posts()
    {
        $posts = Post::orderBy('id', 'desc')->with('category');

        if (is_numeric(request()->category_id)) {
            $posts->where('post_category_id', request()->category_id);
        }
        if (is_numeric(request()->per_page)) {
            $posts = $posts->paginate(request()->get("per_page", page_limit()));
        } else {
            $posts = $posts->paginate(4);
        }
        return $posts;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $post_category = PostCategory::orderBy('name')->get();
        return Inertia::render('Post/create', compact('post_category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $post = auth()->user()->posts()->create($request->all());

        if ($request->cover_photo) {
            $fileName =  $post->id . "_" . time() . '.' . $request->cover_photo->extension();
            $request->cover_photo->move(public_path(config('app.path_post_cover_photo')), $fileName);
            $post->cover_photo_path = $fileName;
            $post->save();
        }

        session()->flash('success', "Article créé");
        return redirect()->route('posts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        return Inertia::render('Post/view', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        $post_category = PostCategory::orderBy('name')->get();
        return Inertia::render('Post/edit', compact('post', 'post_category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, $id)
    {
        $post = Post::find($id);
        $post->update($request->only('title', 'content', 'post_category_id'));
        if ($request->cover_photo) {
            $fileName =  $post->id . "_" . time() . '.' . $request->cover_photo->extension();
            $request->cover_photo->move(public_path(config('app.path_post_cover')), $fileName);
            $post->cover_photo_path = $fileName;
            $post->save();
        }
        session()->flash('success', "Article modifié");
        return redirect()->route('posts.show', $post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Article supprimé');
    }

    /**
     * Display Post Page
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $post = Post::findOrFail($id);
        return Inertia::render('Post/view', compact('post'));
    }
}
