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
use App\Rules\ValidSerieState;
use App\Rules\ValidBase64Image;
use Auth;
use Image;
use Storage;
use Hashids;

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
        ->simplePaginate(RESULTS_PER_PAGE);
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
        ->orderBy('created_at', 'asc')
        ->limit(8)
        ->get();
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
            'slug' => [ 'required', 'string', 'max:45', Rule::unique('series', 'slug') ],
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
            'name' => [ 'required', 'string', 'max:45', Rule::unique('series', 'name') ],
            'slug' => [ 'required', 'string', 'max:45', Rule::unique('series', 'slug') ],
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
                   'state' => [ "Cannot change this serie's state because it doesn't have available chapters yet" ]
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
        return response()->json($serie, 201);
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
            'image'=> [ 'required', new ValidBase64Image ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors('image')], 400);
        }

        $image = Image::make($request->image);

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
            'image'=> [ 'required', new ValidBase64Image ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors('image')], 400);
        }

        $image = Image::make($request->image);

        // Validate image size.
        if($image->width()!==COMIC_BANNER_WIDTH || $image->height()!==COMIC_BANNER_HEIGHT) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
               'image' => ['The image size is not valid']
            ]);
        }

        // Save the image in the server,
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
            'genre2'
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
