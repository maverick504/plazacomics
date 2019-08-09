<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;
use Image;
use Storage;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function feed(Request $request)
    {
        $followingsIds = auth()->user()->followings()->pluck('id')->toArray();

        return Post::distinct()
        ->select([
            'posts.*',
            DB::raw("SUM(CASE WHEN (followable_type = 'App\Post' &&
                                    followables.relation = 'like' &&
                                    followables.deleted_at IS NULL)
                                    THEN 1 ELSE 0 END) AS likes_count"),
            DB::raw("SUM(CASE WHEN (followable_type = 'App\Post' &&
                                    followables.relation = 'like' &&
                                    followables.deleted_at IS NULL &&
                                    followables.user_id = " . auth()->user()->id . ")
                                    THEN 1 ELSE 0 END) > 0 AS user_liked")
        ])
        ->with([ 'author' ])
        ->leftJoin('followables', 'posts.id', '=', 'followables.followable_id')
        ->whereIn('posts.user_id', $followingsIds)
        ->where('posts.explicit_content', '=', false) // This should be temporal.
        ->latest()
        ->groupBy('posts.id')
        ->paginate(RESULTS_PER_PAGE);
    }

    /**
     * Display a listing of posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Post::with([
            'author'
        ]);

        if (in_array('type', $_GET)) {
            switch($_GET['type']) {
                case 'illustration':
                    $query->where('type', Post::TYPE_ILLUSTRATION);
                    break;
            }
        }

        return $query->get();
    }

    /**
     * Display a listing of random posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function random(Request $request)
    {
        return Post::inRandomOrder()
        ->limit(3)
        ->get();
    }

    /**
     * Store a newly created post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'image_temporal_file_name' => 'required|string',
            'title' => 'required|string',
            'description' => 'nullable|string|max:1024',
            'explicit_content' => 'boolean'
        ]);

        $uniqid = uniqid();

        $tmpPath = 'tmp/' . $request->get('image_temporal_file_name');
        $hiResPath = 'posts/' . $uniqid . '_hi_res.jpg';
        Storage::disk('spaces')->move($tmpPath, $hiResPath);

        $imageHiRes = Image::make(Storage::disk('spaces')->get($hiResPath));

        $thumbnail = $this->makeThumbnail($imageHiRes, $uniqid);
        $lowRes = $this->makeLowRes($imageHiRes, $uniqid);
        $hiRes = array(
            'url' => $hiResPath,
            'width' => $imageHiRes->width(),
            'height' => $imageHiRes->height()
        );

        $images = array(
            array(
                'thumbnail' => $thumbnail,
                'low_res' => $lowRes,
                'hi_res' => $hiRes
            )
        );

        DB::beginTransaction();
        try {
            // Store the post
            $post = new Post();
            $post->user_id = auth()->user()->id;
            $post->title = $request->get('title');
            $post->description = $request->get('description');
            $post->images = $images;
            $post->explicit_content = $request->get('explicit_content');
            $post->publish_date = date('Y-m-d H:i:s');
            $post->type = Post::TYPE_ILLUSTRATION;
            $post->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([ 'message' => MESSAGE_UNKNOWN_ERROR ], 500);
        }

        // Success! Return the post
        return response()->json($post, 201);
    }

    /**
     * Display the specified post.
     *
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::with([
            'author'
        ])->where('id', '=', $id)->first();

        if(!$post) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(auth()->user()) {
            $post->user_liked = $post->isLikedBy(auth()->user());
            $post->author->user_is_follower = $post->author->isFollowedBy(auth()->user());
        }
        $post->likes_count = $post->likers()->count();
        $post->author->followers_count = $post->author->followers()->count();

        $post->visits = visits($post)->count();
        visits($post)->increment();

        return $post;
    }

    /**
     * Update the specified post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if(!$post) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if($post->user_id !== auth()->user()->id) {
            return response()->json([ 'message' => MESSAGE_POST_NOT_ACCESSIBLE ], 403);
        }

        // Validate the request
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string|max:1024',
            'explicit_content' => 'boolean'
        ]);

        // Update the post
        return tap($post)->update([
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'explicit_content' => $request->get('explicit_content')
        ]);
    }

    /**
     * Remove the specified post from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if(!$post) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if($post->user_id !== auth()->user()->id) {
            return response()->json([ 'message' => MESSAGE_POST_NOT_ACCESSIBLE ], 403);
        }

        $thumbnailPath = $post->images[0]['thumbnail']['url'];
        $lowResPath = $post->images[0]['low_res']['url'];
        $hiResPath = $post->images[0]['hi_res']['url'];

        Storage::disk('spaces')->delete($thumbnailPath);
        Storage::disk('spaces')->delete($lowResPath);
        Storage::disk('spaces')->delete($hiResPath);

        $post->delete();

        return response()->json(null, 204);
    }

    /**
     * Upload a post's image to a temporal folder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response Returns a public url to access the temporally uploaded image.
     */
    public function uploadImage(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'image'=> [ 'required' ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors('image')], 400);
        }

        $image = Image::make($request->image);

        // Validate image size.
        if($image->width() > POST_IMAGE_MAX_WIDTH || $image->height() > POST_IMAGE_MAX_HEIGHT) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
               'image' => ['El tamaño de la imágen no es válido.']
            ]);
        }

        // Save the image in the server
        $image->stream();
        $fileName = uniqid() . '.jpg';
        Storage::disk('spaces')->put('tmp/' . $fileName, $image, 'public');

        return response()->json([
            'temporal_url' => 'tmp/'.$fileName,
            'temporal_file_name' => $fileName
        ]);
    }

    private function makeThumbnail($image, $uniqid) {
        // backup image status
        $image->backup();

        // Crop the image to make it square.
        if($image->width() > $image->height()) {
          $image->crop($image->height(), $image->height(), intval(($image->width() / 2) - ($image->height() / 2)), 0);
        } else {
          $image->crop($image->width(), $image->width(), 0, intval(($image->height() / 2) - ($image->width() / 2)));
        }

        // Resize the image.
        $image->resize(160, 160, function ($constraint) {
            $constraint->aspectRatio();
        });

        // Save the image
        $image->stream();

        $fileName = $uniqid . '_thumbnail.jpg';
        Storage::disk('spaces')->put('posts/' . $fileName, $image, 'public');

        // reset image (return to backup state)
        $image->reset();

        return array(
            'url' => 'posts/' . $fileName,
            'width' => $image->width(),
            'height' => $image->height()
        );
    }

    private function makeLowRes($image, $uniqid) {
        // backup image status
        $image->backup();

        // resize the image
        $image->resize(320, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        // Save the image
        $image->stream();

        $fileName = $uniqid . '_low_res.jpg';
        Storage::disk('spaces')->put('posts/' . $fileName, $image, 'public');

        // reset image (return to backup state)
        $image->reset();

        return array(
            'url' => 'posts/' . $fileName,
            'width' => $image->width(),
            'height' => $image->height()
        );
    }

    /**
     * Returns all the public posts by user (author).
     *
     * @return \Illuminate\Http\Response
     */
    public function authorIndex($userId)
    {
      $query = Post::with([
        'author'
      ])->where('user_id', $userId);

      if (in_array('type', $_GET)) {
        switch($_GET['type']) {
          case 'illustration':
            $query->where('type', Post::TYPE_ILLUSTRATION);
            break;
        }
      }

      return $query->get();
    }

    /**
     * Returns all the posts owned by the logged-in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function userIndex()
    {
      return Post::where('user_id', auth()->user()->id)->latest()->get();
    }

    /**
     * Returns a post from the posts owned by the logged-in user.
     *
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function userShow($id)
    {
      $post = Post::where('id', '=', $id)->first();

      if(!$post) {
          return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
      }

      if($post->user_id !== auth()->user()->id) {
          return response()->json([ 'message' => MESSAGE_POST_NOT_ACCESSIBLE ], 403);
      }

      return $post;
    }
}
