<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Models\Post;

class PostController extends Controller
{
    //

    public function __construct(){
        // $this->middleware("auth");
    }

    // WEB GET: /posts
    public function index(){
        //page, limit, sort_by, sort_type, search
        //Cache::remember($key, $time, function())
        //Cache::rememberForever($key, function())
        $columns = Cache::rememberForever("posts-columns", function(){
            return Schema::getColumnListing('posts'); // get columns on posts table
        });
        $page = request()->page; // get the page from client
        $sort_by = request()->sort_by; // get the sort by / column name from client
        $sort_type = request()->sort_type; // get the sort type from client
        $search = request()->search; // get the search query from client
        $limit = request()->limit; // get the limit query from client
        if(empty($page) || !is_numeric($page) || $page < 1)
            $page = 1;
        if(empty($sort_by) || !in_array($sort_by, $columns)){
            $sort_by = "id";
            if($sort_type != "asc")
                $sort_type = "desc";
        }
        if(empty($sort_type) || $sort_type != "desc") // $sort_type == "desc"
            $sort_type = "asc";
        if((empty($limit) || !is_numeric($limit)) || $limit < 12)
            $limit = 12;
        $limit = floor($limit);
        $page = floor($page);
        $skip = ($page - 1) * $limit;
        $Posts = Cache::remember("posts-index-$search-$sort_by-$sort_type", now()->addSeconds(30), function () use($search, $sort_by, $sort_type) {
            return Post::where("content", "LIKE", "%$search%")->whereRaw("post_id IS NULL")->orderBy($sort_by, $sort_type)->get();
        });
        $pages = ceil($Posts->count() / $limit);
        $Posts = $Posts->skip($skip)->take($limit);
        if($page != 1 && $Posts->count() == 0)
            abort(404);
        $pagination = [
            "page" => $page,
            "pages" => $pages,
            "sort_by" => $sort_by,
            "sort_type" => $sort_type,
            "search" => $search,
            "limit" => $limit
        ];
        return view("posts.index", compact("Posts", "pagination"));
    }


    // WEB POST: /posts
    public function store(Request $request){
        $data = $this->validate($request, [
            "content" => "required|max:1000",
        ]);
        auth()->user()->Posts()->create($data);
        Cache::flush();
        return redirect()->route("posts")->with("message", [
            "type" => "success",
            "content" => "<b>Post</b> has been created."
        ]);
    }

    // WEB DELETE: /posts/{id}
    public function delete($id){
        $post = Post::find($id);
        if(is_null($post))
            abort(404);
        else if($post->user_id == auth()->user()->id || auth()->user()->can("Delete Post Everyone")){
            $post->delete();
        }
        else{
            abort(403);
        }
        Cache::flush();
        return redirect()->route("posts")->with("message", [
            "type" => "danger",
            "content" => "<b>Post</b> has been deleted."
        ]);
    }

    // WEB PATCH: /posts/{id}
    public function update(Request $request, $id){
        $post = Post::find($id);
        if(is_null($post))
            abort(404);
        else if($post->user_id == auth()->user()->id ){
            $data = $this->validate($request, [
                "content" => "required",
            ]);
            $post->update($data);
        }
        else{
            abort(403);
        }
        Cache::flush();
        return redirect()->route("posts")->with("message", [
            "type" => "warning",
            "content" => "<b>Post</b> has been updated."
        ]);
    }

    // WEB GET: /posts/{id}
    public function show($id){
        $Post = Cache::remember("post-$id", now()->addSeconds(30) ,function() use ($id){
            $post = Post::find($id);
            $post->Comments;
            return $post;
        });

        
        if(is_null($Post))
            abort(404);
        // //cache like counts
        // Cache::remember("post-$id-likes", now()->addSeconds(30), function()use($Post){
        //     $Post->Likes;
        // });
        return view("posts.show", compact("Post"));
    }

    // WEB API POST: /posts/{post}/like
    public function like(Post $Post){
        $Post->Likes()->toggle([auth()->user()->id]);
        return response(["count" => $Post->Likes->count()]);
    }

    //WEB API POST: /posts/{post}/comment
    public function comment(Request $request, Post $Post){
        $data = $this->validate($request,[
            "content" => "required|max:1000"
        ]);
        $post = $Post->Comments()->create([
            "content" => $data['content'],
            "user_id" => auth()->user()->id
        ]);
        $post->User;
        Cache::forget("post-" . $Post->id);
        return response([
            "post" => $post
        ]);
    }
}
