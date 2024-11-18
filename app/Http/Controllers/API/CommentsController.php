<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Comments;
use App\Models\Likes;

class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'list']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $data = Comments::query()
                ->leftjoin('users as u','u.id', '=', 'comments.user_id')
                ->orderBy('comments.id', 'DESC')
                ->select(
                'comments.id as id',
                'comments.post_id as post_id',
                'comments.body as body',
                DB::raw("DATE_FORMAT(comments.created_at, '%d-%b-%Y') as created_at"))
                ->paginate(20); 
        return response()->json([
            "success" => true,
            "data" => $data,
            "message" => 'List of Comment'
        ], 200);
    }

    public function list($postId)
    {   
        $data = Comments::query()
                ->leftjoin('users as u','u.id', '=', 'comments.user_id')
                ->where('comments.post_id', $postId)
                ->orderBy('comments.id', 'DESC')
                ->select(
                'comments.id as id',
                'comments.post_id as post_id',
                'u.name as name',
                'comments.body as body',
                DB::raw("DATE_FORMAT(comments.created_at, '%d-%b-%Y') as date"))
                ->paginate(20); 
        return response()->json([
            "success" => true,
            "data" => $data,
            "message" => 'List of Comment'
        ], 200);
    }

    public function like(Request $request)
    {
        $commentId = $request->id;
        $userId = Auth::user()->id;

        $like = Likes::upsert([[
            'userable_type' => 'user', 
            'userable_id' => $userId,
            'likeable_type' => 'comment',
            'likeable_id' => $commentId,
            'is_liked' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]], ['userable_type', 'userable_id', 'likeable_type', 'likeable_id', 'is_liked']);

        return response()->json([
            "success" => true,
            "data" => $like,
            "message" => 'Success'
        ], 200);
    }

    public function unlike(Request $request)
    {
        $commentId = $request->id;
        $userId = Auth::user()->id;

        $like = Likes::where('userable_id', $userId)
        ->where('likeable_type', 'comment')
        ->where('likeable_id', $commentId)
        ->delete();

        return response()->json([
            "success" => true,
            "data" => $like,
            "message" => 'Success'
        ], 200);
    }

    public function isLike(Request $request)
    {   
        $postId = $request->id;
        $data = Comments::query()
                ->leftjoin('users as u','u.id', '=', 'comments.user_id')
                ->leftjoin(DB::raw("(
                    SELECT userable_id, likeable_id, is_liked FROM likes
                    WHERE likeable_type = 'comment') as l"),function($join){
                    $join->on("l.likeable_id","=","comments.id");
                })
                ->where('comments.post_id', $postId)
                ->orderBy('comments.id', 'DESC')
                ->select(
                'comments.id as id',
                'comments.post_id as post_id',
                'u.name as name',
                'comments.body as body',
                'l.is_liked as is_liked',
                DB::raw("DATE_FORMAT(comments.created_at, '%d-%b-%Y') as date"))
                ->paginate(20); 
        return response()->json([
            "success" => true,
            "data" => $data,
            "message" => 'List of Comment'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'body'=>'required',
        ]);

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;

        $comment = Comments::create($input);

        return response()->json([
            "success" => true,
            "data" => $comment,
            "message" => 'Success'
        ], 200);
    }
}
