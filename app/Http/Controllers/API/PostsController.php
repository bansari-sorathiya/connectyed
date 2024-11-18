<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Posts;
use App\Models\Likes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Response;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['featured', 'show', 'search']]);
    }        

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $data = DB::table('posts as p')
        ->select(
          'p.id as id',
          'p.title as title',
          'p.slug as slug',
          'p.content as content',
          DB::raw("DATE_FORMAT(p.post_date, '%d-%b-%Y') as post_date"),
          'u.name as name',
          'u.role as role',
          'p.created_at as created_at')
        ->leftJoin('users as u', 'u.id', '=', 'p.author_id')
        ->where('slug','=',$slug)
        ->first();
        return response()->json([
            "success" => true,
            "data" => $data,
            "message" => 'Post Detail'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function featured()
    {      
        $posts = DB::table('posts as p')
        ->select(
          'p.id as id',
          'p.title as title',
          'p.slug as slug',
          'p.content as content',
          DB::raw("DATE_FORMAT(p.post_date, '%b %d, %Y') as post_date"),
          'u.name as name',
          'p.created_at as created_at')
        ->leftJoin('users as u', 'u.id', '=', 'p.author_id')
        ->where('featured','=', 1)
        ->get();       

        $dataarr = [];
      foreach ($posts as $post) {
          $imgsrc = Self::get_img_content($post->content);
          $data = array('id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'slug' => $post->slug,
                    'image' => $imgsrc,                    
                    'name' => $post->name,
                    'created_at' => $post->created_at,
                    'post_date' => $post->post_date);
          array_push($dataarr, $data);
      }      
      return response()->json([
          "success" => true,
          "data" => $dataarr,
          "message" => 'Featured Post'
      ], 200);
    }

    public function get_img_content($content)
    {
      preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $content_image_1);
      $imgsrc = (isset($content_image_1['src'])) ? $content_image_1['src'] : url('/')."/upload/images/no-image.jpg";
      return $imgsrc;
    }

    public static function slugify($text)
    {
      $text = preg_replace('~[^\pL\d]+~u', '-', $text);
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
      $text = preg_replace('~[^-\w]+~', '', $text);
      $text = trim($text, '-');
      $text = preg_replace('~-+~', '-', $text);
      $text = strtolower($text);
      if (empty($text)) {
        return 'n-a';
      }
      return str_slug($text);
    }
}
