<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Serie;
use App\Chapter;
use App\Page;
use App\Rules\ValidSlug;
use Auth;
use Image;
use Storage;
use Hashids;
use DateTime;

class ChapterController extends Controller
{
    /**
     * Returns all the published chapters from a serie.
     *
     * @return \Illuminate\Http\Response
     */
    public function serieIndex($serieId)
    {
        return Chapter::select('chapters.*')
        ->join('series', 'chapters.serie_id', '=', 'series.id')
        ->where('series.id', $serieId)
        ->where('series.state', '!=', SERIE_STATE_DRAFT)
        ->relased()
        ->orderBy('relase_date', 'asc')
        ->orderBy('created_at', 'asc')
        ->get();
    }

    /**
     * Store a newly created chapter in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'serie_id' => 'required|integer|exists:series,id',
            'title' => 'required|string|max:45',
            'slug' => [ 'required', 'string', new ValidSlug, 'max:45' ], // Unique isn't required, that's ok for chapters.
            'relase_date' => 'required|string|date_format:d/m/Y',
            'pages' => 'required|array|min:1|max:30'
        ]);

        // Validate that the specified serie exists and is owned by the user.
        $serie = Serie::find($request->get('serie_id'));

        if(!$serie) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
               'serie' => [ MESSAGE_NOT_FOUND ]
            ]);
            throw $error;
        }

        if(!$serie->isOwnedBy(Auth::user()->id)) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
               'serie' => [ MESSAGE_SERIE_NOT_ACCESSIBLE ]
            ]);
            throw $error;
        }

        $pages = $request->get('pages');

        // Format relase date
        if($request->get('relase_date')) {
            $relaseDate = DateTime::createFromFormat('d/m/Y', $request->get('relase_date'))->format('Y-m-d') . ' 00:00:00';
        } else {
            $relaseDate = null;
        }

        DB::beginTransaction();
        try {
            // Store the chapter
            $chapter = new Chapter();
            $chapter->serie_id = $serie->id;
            $chapter->title = $request->get('title');
            $chapter->slug = $request->get('slug');
            $chapter->relase_date = $relaseDate;
            $chapter->total_pages = sizeof($pages);
            $chapter->save();

            $chapterHashid = Hashids::encode($chapter->id);

            // Store the pages
            for($i=0; $i<sizeof($pages); $i++) {
                $page = $pages[$i];

                $oldPath = 'tmp/' . $page['temporal_file_name'];
                $newPath = 'chapters/' . $chapterHashid . '/' . uniqid() . '.jpg';
                Storage::disk('spaces')->move($oldPath, $newPath);

                $pageRecord = new Page();
                $pageRecord->chapter_id = $chapter->id;
                $pageRecord->order = $i;
                $pageRecord->image_url = $newPath;
                $pageRecord->save();
            }

            // Recount chapters and pages of the modified serie
            $serie->recountChaptersAndPages();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            throw $e;

            return response()->json([ 'message' => MESSAGE_UNKNOWN_ERROR ], 500);
        }

        // Success! Return the chapter
        return response()->json($chapter, 201);
    }

    /**
     * Display the specified chapter.
     *
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $chapter = Chapter::with([
            'pages'
        ])->where('id', '=', $id)->first();

        if(!$chapter) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        $serie = Serie::find($chapter->serie_id);

        if(!$serie->isPublic()) {
            return response()->json([ 'message' => MESSAGE_SERIE_NOT_ACCESSIBLE ], 403);
        }

        if(!$chapter->hasBeenRelased()) {
            return response()->json([ 'message' => MESSAGE_CHAPTER_NOT_ACCESSIBLE ], 403);
        }

        // Get chapter number
        $chapter->number = Chapter::where('relase_date', '<', $chapter->relase_date)
        ->orwhere(function($query) use($chapter) {
            $query->where('relase_date', '=', $chapter->relase_date)
            ->where('created_at', '<', $chapter->created_at);
        })
        ->where('serie_id', '=', $serie->id)
        ->count()+1;

        // Get previous chapter.
        // Please don't touch this query, is an evil query xD
        $chapter->previous_chapter = Chapter::select('id', 'slug', 'title', 'relase_date', 'total_pages')
        ->where('relase_date', '<', $chapter->relase_date)
        ->orwhere(function($query) use($chapter) {
            $query->where('relase_date', '=', $chapter->relase_date)
            ->where('created_at', '<', $chapter->created_at);
        })
        ->where('serie_id', '=', $serie->id)
        ->orderBy('relase_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->first();

        // Get next chapter.
        // This query is evil as well, don't touch it
        $chapter->next_chapter = Chapter::select('id', 'slug', 'title', 'relase_date', 'total_pages')
        ->where('relase_date', '>', $chapter->relase_date)
        ->orwhere(function($query) use($chapter) {
            $query->where('relase_date', '=', $chapter->relase_date)
            ->where('created_at', '>', $chapter->created_at);
        })
        ->where('serie_id', '=', $serie->id)
        ->orderBy('relase_date', 'asc')
        ->orderBy('created_at', 'asc')
        ->first();

        return $chapter;
    }

    /**
     * Update the specified chapter in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $chapter = Chapter::find($id);

        if(!$chapter) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(!$chapter->isOwnedBy(Auth::user()->id)) {
            return response()->json([ 'message' => MESSAGE_CHAPTER_NOT_ACCESSIBLE ], 403);
        }

        // Validate the request
        $request->validate([
            'title' => 'required|string|max:45',
            'slug' => [ 'required', 'string', new ValidSlug, 'max:45' ], // Unique isn't required, that's ok for chapters.
            'relase_date' => 'required|string|date_format:d/m/Y',
            'pages' => 'required|array|min:1|max:30'
        ]);

        $pages = $request->get('pages');

        // Format relase date
        if($request->get('relase_date')) {
            $relaseDate = DateTime::createFromFormat('d/m/Y', $request->get('relase_date'))->format('Y-m-d') . ' 00:00:00';;
        } else {
            $relaseDate = null;
        }

        DB::beginTransaction();
        try {
            // Update the chapter
            $chapter->title = $request->get('title');
            $chapter->slug = $request->get('slug');
            $chapter->relase_date = $relaseDate;
            $chapter->total_pages = sizeof($pages);
            $chapter->save();

            // Delete the old pages from DB.
            Page::where('chapter_id', $chapter->id)->delete();

            $chapterHashid = Hashids::encode($chapter->id);

            // Upload the new pages
            $currentPagesFiles = [];
            for($i=0; $i<sizeof($pages); $i++) {
                $page = $pages[$i];

                if(array_key_exists('temporal_file_name', $page)) {
                    $oldPath = 'tmp/' . $page['temporal_file_name'];
                    $newPath = 'chapters/' . $chapterHashid . '/' . uniqid() . '.jpg';
                    Storage::disk('spaces')->move($oldPath, $newPath);

                    $imageUrl = $newPath;
                } else {
                    $imageUrl = $page['image_url'];
                }

                $pageRecord = new Page([
                    'chapter_id' => $chapter->id,
                    'order' => $i,
                    'image_url' => $imageUrl
                ]);

                $parts = explode('/', $imageUrl);
                $fileName = end($parts);
                $currentPagesFiles[] = $fileName;

                $pageRecord->save();
            }

            // Now we must delete all the unused images of this chapter.
            $files = Storage::disk('spaces')->files('chapters/' . $chapterHashid);
            foreach($files as $file) {
                $parts = explode('/', $file);
                $filename = end($parts);

                if(!in_array($filename, $currentPagesFiles)) {
                    Storage::disk('spaces')->delete('chapters/' . $chapterHashid . '/' . $filename);
                }
            }

            // Recount chapters and pages of the modified serie
            $serie = Serie::find($chapter->serie_id);
            $serie->recountChaptersAndPages();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 500,
                'message' => MESSAGE_UNKNOWN_ERROR
            ], 500);
        }

        // Success! Return the chapter
        return response()->json($chapter, 201);
    }

    /**
     * Remove the specified chapter from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $chapter = Chapter::find($id);

        if(!$chapter) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(!$chapter->isOwnedBy(Auth::user()->id)) {
            return response()->json([ 'message' => MESSAGE_CHAPTER_NOT_ACCESSIBLE ], 403);
        }

        DB::beginTransaction();
        try {
            // Remove images from disk
            Storage::disk('spaces')->deleteDirectory('chapters/' . Hashids::encode($chapter->id));
            Page::where('chapter_id', $chapter->id)->delete();
            $chapter->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 500,
                'message' => MESSAGE_UNKNOWN_ERROR
            ], 500);
        }

        return response()->json(null, 204);
    }

    /**
     * Display all the chapters from a serie owned by the logged-in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function userSerieIndex($serieId)
    {
        $serie = Serie::find($serieId);

        if(!$serie) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(!$serie->isOwnedBy(Auth::user()->id)) {
            return response()->json([ 'message' => MESSAGE_SERIE_NOT_ACCESSIBLE ], 403);
        }

        return Chapter::where('serie_id', $serie->id)
        ->orderBy('relase_date', 'asc')
        ->orderBy('created_at', 'asc')
        ->get();
    }

    /**
     * Returns a chapter from the chapters owned by the logged-in user.
     *
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function userShow($id)
    {
        $chapter = Chapter::with([
            'pages'
        ])->where('id', '=', $id)->first();

        if(!$chapter) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(!$chapter->isOwnedBy(Auth::user()->id)) {
            return response()->json([ 'message' => MESSAGE_CHAPTER_NOT_ACCESSIBLE ], 403);
        }

        return $chapter;
    }
}
