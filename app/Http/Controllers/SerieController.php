<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Serie;
use App\User;
use App\Banner;
use App\SerieAuthor;
use App\Genre;
use App\Licence;
use App\Chapter;
use App\Page;
use App\Rules\ValidSlug;
use App\Rules\ValidSerieState;
use Auth;
use Image;
use Storage;
use Hashids;

/**
 * Notice: 'Serie' is not a valid word in english language, it should be 'Series'.
 * Now I know it but I didn't know it back when I chosed table and model names.
 * When I noticed that mistake, it was too late to rename; So when you see something
 * called 'Serie', it means one of 'Series'.
 *
 * Emiliano
 */

class SerieController extends Controller
{
    /**
     * Display a listing of the series.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Serie::with([
            'genre1',
            'genre2'
        ])
        ->public()
        ->paginate(RESULTS_PER_PAGE);
    }

    /**
     * Display a listing of the series sorted by newest.
     *
     * @return \Illuminate\Http\Response
     */
    public function new()
    {
        return Serie::with([
            'genre1',
            'genre2'
        ])
        ->public()
        ->latest()
        ->limit(4)
        ->get();
    }

    /**
     * Display a listing of the series sorted by popularity.
     *
     * @return \Illuminate\Http\Response
     */
    public function popular()
    {
        $topIds = visits('App\Serie')->top(4)->pluck('id')->toArray();

        $query = Serie::with([
            'genre1',
            'genre2'
        ])
        ->public()
        ->whereIn('id', $topIds);

        foreach($topIds as $id) {
          $query->orderByRaw('id=' . $id . ' DESC');
        }

        return $query->get();
    }

    /**
     * Store a newly created serie in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => [ 'required', 'string', 'max:45', Rule::unique('series', 'name') ],
            'slug' => [ 'required', 'string', 'max:45', new ValidSlug, Rule::unique('series', 'slug') ],
            'genre1' => [ 'required', 'integer', 'exists:genres,id' ],
            'genre2' => [ 'nullable', 'integer', 'exists:genres,id', 'different:genre1' ],
            'licence' => [ 'required', 'integer', 'exists:licences,id' ],
            'synopsis' => [ 'required', 'string', 'max:1000' ]
        ]);

        DB::beginTransaction();
        try {
            // Store the serie
            $serie = new Serie();
            $serie->name = $request->get('name');
            $serie->slug = $request->get('slug');
            $serie->state = SERIE_STATE_DRAFT;
            $serie->genre1 = $request->get('genre1');
            $serie->genre2 = $request->get('genre2');
            $serie->licence = $request->get('licence');
            $serie->synopsis = $request->get('synopsis');
            $serie->explicit_content = $request->get('explicit_content');
            $serie->save();

            // Store the author
            $serieAuthor = new SerieAuthor();
            $serieAuthor->serie_id = $serie->id;
            $serieAuthor->user_id = Auth::user()->id;
            $serieAuthor->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([ 'message' => MESSAGE_UNKNOWN_ERROR ], 500);
        }

        // Success! Return the serie
        return response()->json($serie, 201);
    }

    /**
     * Display the specified serie.
     *
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $serie = Serie::with([
            'genre1',
            'genre2',
            'licence',
            'authors' => function($query) {
                $query->select('users.id', 'users.username', 'users.avatar_url');
            }
        ])->public()->where('id', '=', $id)->first();

        if(!$serie) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(Auth::user()) {
            $serie->user_is_subscriber = $serie->isSubscribedBy(Auth::user());
            $serie->user_liked = $serie->isLikedBy(Auth::user());
        }
        $serie->likes_count = $serie->likers()->count();
        $serie->subscribers_count = $serie->subscribers()->count();

        $serie->visits = visits($serie)->count();
        visits($serie)->increment();

        return $serie;
    }

    /**
     * Update the specified serie in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $serie = Serie::find($id);

        if(!$serie) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(!$serie->isOwnedBy(Auth::user()->id)) {
            return response()->json([ 'message' => MESSAGE_SERIE_NOT_ACCESSIBLE ], 403);
        }

        // Validate the request
        $request->validate([
            'name' => [ 'required', 'string', 'max:45', Rule::unique('series', 'name')->ignore($serie->id) ],
            'slug' => [ 'required', 'string', 'max:45', new ValidSlug, Rule::unique('series', 'slug')->ignore($serie->id) ],
            'state' => [ 'required', 'string', new ValidSerieState ],
            'genre1' => [ 'required', 'integer', 'exists:genres,id' ],
            'genre2' => [ 'nullable', 'integer', 'exists:genres,id', 'different:genre1' ],
            'licence' => [ 'required', 'integer', 'exists:licences,id' ],
            'synopsis' => [ 'required', 'string', 'max:1000' ]
        ]);

        // It is required that the serie have more than one chapter published to change his state from draft to any other.
        if($request->get('state') != SERIE_STATE_DRAFT) {
            $availableChaptersCount = Chapter::where('serie_id', $serie->id)->where('relase_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
            if($availableChaptersCount == 0) {
                $error = \Illuminate\Validation\ValidationException::withMessages([
                   'state' => [ "No puedes cambiar el estado de esta serie porque no tiene capítulos publicados aún." ]
                ]);
                throw $error;
            }
        }

        DB::beginTransaction();
        try {
            // Update the serie
            $serie->name = $request->get('name');
            $serie->slug = $request->get('slug');
            $serie->state = $request->get('state');
            $serie->genre1 = $request->get('genre1');
            $serie->genre2 = $request->get('genre2');
            $serie->licence = $request->get('licence');
            $serie->synopsis = $request->get('synopsis');
            $serie->explicit_content = $request->get('explicit_content');
            $serie->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => MESSAGE_UNKNOWN_ERROR
            ], 500);
        }

        // Success! Return the serie
        return response()->json($serie, 200);
    }

    /**
     * Update the serie's cover image.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateCover(Request $request, $id)
    {
        $serie = Serie::find($id);

        if(!$serie) {
            return response()->json([
                'message' => MESSAGE_NOT_FOUND
            ], 404);
        }

        if(!$serie->isOwnedBy(Auth::user()->id)) {
            return response()->json([
                'message' => MESSAGE_SERIE_NOT_ACCESSIBLE
            ], 403);
        }

        $validator = \Validator::make($request->all(), [
            'image'=> [ 'required', 'image' ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors('image')], 400);
        }

        $image = Image::make($request->file('image'));

        // Validate image size.
        if($image->width()!==COMIC_COVER_WIDTH || $image->height()!==COMIC_COVER_HEIGHT) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
               'image' => ['The image size is not valid']
            ]);
        }

        // Save the image in the server
        $image->stream();
        $filePath = 'series/' . Hashids::encode($serie->id) . '/cover.jpg';
        Storage::disk('spaces')->put($filePath, $image, 'public');

        // Update the serie
        return tap($serie)->update([
            'cover_url' => $filePath
        ]);
    }

    /**
     * Update the serie's banner image.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateBanner(Request $request, $id)
    {
        $serie = Serie::find($id);

        if(!$serie) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(!$serie->isOwnedBy(Auth::user()->id)) {
            return response()->json([ 'message' => MESSAGE_SERIE_NOT_ACCESSIBLE ], 403);
        }

        $validator = \Validator::make($request->all(), [
            'image'=> [ 'required', 'image' ]
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors('image')], 400);
        }

        $image = Image::make($request->file('image'));

        // Validate image size.
        if($image->width()!==COMIC_BANNER_WIDTH || $image->height()!==COMIC_BANNER_HEIGHT) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
               'image' => ['The image size is not valid']
            ]);
        }

        // Save the image in the server.
        $image->stream();
        $filePath = 'series/' . Hashids::encode($serie->id) . '/banner.jpg';
        Storage::disk('spaces')->put($filePath, $image, 'public');

        // Update the serie,
        return tap($serie)->update([
            'banner_url' => $filePath
        ]);
    }

    /**
     * Removes the serie's banner image.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function removeBanner(Request $request, $id)
    {
        $serie = Serie::find($id);

        if(!$serie) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(!$serie->isOwnedBy(Auth::user()->id)) {
            return response()->json([ 'message' => MESSAGE_SERIE_NOT_ACCESSIBLE ], 403);
        }

        // Remove the file from storage
        Storage::disk('spaces')->delete($serie->banner_url);

        // Update the serie
        return tap($serie)->update([
            'banner_url' => null
        ]);
    }

    /**
     * Remove the specified serie from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // ...
    }

    /**
     * Returns all the published series by user(author).
     *
     * @return \Illuminate\Http\Response
     */
    public function authorIndex($userId)
    {
        return Serie::with([
            'genre1',
            'genre2'
        ])->ownedBy($userId)->public()->get();
    }

    /**
     * Returns all the series owned by the logged-in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function userIndex()
    {
        return Serie::with([
            'genre1',
            'genre2'
        ])->ownedBy(Auth::user()->id)->get();
    }

    /**
     * Returns a serie from the series owned by the logged-in user.
     *
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function userShow($id)
    {
        $serie = Serie::with([
            'genre1',
            'genre2',
            'licence'
        ])->where('id', '=', $id)->first();

        if(!$serie) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(!$serie->isOwnedBy(Auth::user()->id)) {
            return response()->json([ 'message' => MESSAGE_SERIE_NOT_ACCESSIBLE ], 403);
        }

        return $serie;
    }
}
