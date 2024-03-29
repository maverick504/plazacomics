<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Serie;
use App\Chapter;
use App\Page;
use App\Post;
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
            'pages' => 'required|array|min:1|max:' . COMIC_CHAPTER_MAX_PAGES
        ]);

        $serie = Serie::with('authors')->where('id', '=', $request->get('serie_id'))->firstOrFail();

        // Validate that the specified serie is owned by the user.
        if(!$serie->isOwnedBy(auth()->user()->id)) {
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

            // Create a new post to announce the new chapter
            $post = new Post();
            $post->user_id = auth()->user()->id;
            $post->explicit_content = $serie->explicit_content;
            $post->publish_date = $chapter->relase_date;
            $post->type = Post::TYPE_NEW_CHAPTER;
            $post->serie_id = $serie->id;
            $post->chapter_id = $chapter->id;
            $post->save();

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
        $chapter = Chapter::select('chapters.*')
        ->join('series', 'chapters.serie_id', '=', 'series.id')
        ->where('series.state', '!=', SERIE_STATE_DRAFT)
        ->where('chapters.id', '=', $id)
        ->firstOrFail();

        if($chapter->hasBeenRelased()) {
            $chapter->pages = $chapter->pages()->get();
        }

        // Get chapter number
        $chapter->number = Chapter::where('serie_id', '=', $chapter->serie_id)
        ->where(function($query) use($chapter) {
          $query->where('relase_date', '<', $chapter->relase_date)
          ->orwhere(function($query) use($chapter) {
              $query->where('relase_date', '=', $chapter->relase_date)
              ->where('created_at', '<', $chapter->created_at)
              ->orwhere(function($query) use($chapter) {
                  $query->where('relase_date', '=', $chapter->relase_date)
                  ->where('created_at', '=', $chapter->created_at)
                  ->where('id', '<', $chapter->id);
              });
          });
        })
        ->count()+1;

        // Get previous chapter.
        $chapter->previous_chapter = Chapter::select('id', 'slug', 'title', 'thumbnail_url', 'relase_date', 'total_pages')
        ->where('serie_id', '=', $chapter->serie_id)
        ->where(function($query) use($chapter) {
          $query->where('relase_date', '<', $chapter->relase_date)
          ->orwhere(function($query) use($chapter) {
              $query->where('relase_date', '=', $chapter->relase_date)
              ->where('created_at', '<', $chapter->created_at)
              ->orwhere(function($query) use($chapter) {
                  $query->where('relase_date', '=', $chapter->relase_date)
                  ->where('created_at', '=', $chapter->created_at)
                  ->where('id', '<', $chapter->id);
              });
          });
        })
        ->orderBy('relase_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->orderBy('id', 'desc')
        ->first();

        // Get next chapter.
        $chapter->next_chapter = Chapter::select('id', 'slug', 'title', 'thumbnail_url', 'relase_date', 'total_pages')
        ->where('serie_id', '=', $chapter->serie_id)
        ->where(function($query) use($chapter) {
          $query->where('relase_date', '>', $chapter->relase_date)
          ->orwhere(function($query) use($chapter) {
              $query->where('relase_date', '=', $chapter->relase_date)
              ->where('created_at', '>', $chapter->created_at)
              ->orwhere(function($query) use($chapter) {
                  $query->where('relase_date', '=', $chapter->relase_date)
                  ->where('created_at', '=', $chapter->created_at)
                  ->where('id', '>', $chapter->id);
              });
          });
        })
        ->orderBy('relase_date', 'asc')
        ->orderBy('created_at', 'asc')
        ->orderBy('id', 'asc')
        ->first();

        $chapter->visits = visits($chapter)->count();
        visits($chapter)->increment();

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
        $chapter = Chapter::findOrFail($id);

        if(!$chapter->isOwnedBy(auth()->user()->id)) {
            return response()->json([ 'message' => MESSAGE_CHAPTER_NOT_ACCESSIBLE ], 403);
        }

        // Validate the request
        $request->validate([
            'title' => 'required|string|max:45',
            'slug' => [ 'required', 'string', new ValidSlug, 'max:45' ], // Unique isn't required, that's ok for chapters.
            'relase_date' => 'required|string|date_format:d/m/Y',
            'pages' => 'required|array|min:1|max:' . COMIC_CHAPTER_MAX_PAGES
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

                if(!in_array($filename, $currentPagesFiles) && $filename !== 'thumbnail.jpg') {
                    Storage::disk('spaces')->delete('chapters/' . $chapterHashid . '/' . $filename);
                }
            }

            // Recount chapters and pages of the modified serie
            $serie = Serie::find($chapter->serie_id);
            $serie->recountChaptersAndPages();

            // Update the post related to this chapter
            $post = Post::where('chapter_id', $chapter->id)->first();
            if($post) {
                $post->publish_date = $chapter->relase_date;
                $post->save();
            }

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
     * Update the chapter's thubmanil image.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateThumbnail(Request $request, $id)
    {
        $chapter = Chapter::find($id);

        if(!$chapter) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(!$chapter->isOwnedBy(auth()->user()->id)) {
            return response()->json([ 'message' => MESSAGE_CHAPTER_NOT_ACCESSIBLE ], 403);
        }

        $validator = \Validator::make($request->all(), [
            'image' => [ 'required', 'image' ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors('image')], 400);
        }

        $image = Image::make($request->file('image'));

        // Validate image size.
        if($image->width()!==COMIC_CHAPTER_THUMBNAIL_WIDTH || $image->height()!==COMIC_CHAPTER_THUMBNAIL_HEIGHT) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
               'image' => ['The image size is not valid']
            ]);
        }

        // Save the image in the server
        $image->stream();
        $filePath = 'chapters/' . Hashids::encode($chapter->id) . '/thumbnail.jpg';
        Storage::disk('spaces')->put($filePath, $image, 'public');

        // Update the chapter
        return tap($chapter)->update([
            'thumbnail_url' => $filePath
        ]);
    }

    /**
     * Removes the chapter's thumbnail image.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function removeThumbnail(Request $request, $id)
    {
        $chapter = Chapter::find($id);

        if(!$chapter) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(!$chapter->isOwnedBy(auth()->user()->id)) {
            return response()->json([ 'message' => MESSAGE_CHAPTER_NOT_ACCESSIBLE ], 403);
        }

        // Remove the file from storage
        Storage::disk('spaces')->delete($chapter->thumbnail_url);

        // Update the chapter
        return tap($chapter)->update([
            'thumbnail_url' => null
        ]);
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

        if(!$chapter->isOwnedBy(auth()->user()->id)) {
            return response()->json([ 'message' => MESSAGE_CHAPTER_NOT_ACCESSIBLE ], 403);
        }

        DB::beginTransaction();
        try {
            // Remove the pages
            Storage::disk('spaces')->deleteDirectory('chapters/' . Hashids::encode($chapter->id));
            Page::where('chapter_id', $chapter->id)->delete();

            // Remove the post related to this chapter
            Post::where('chapter_id', $chapter->id)->delete();

            // Remove the chapter itself
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
        $serie = Serie::findOrFail($serieId);

        if(!$serie->isOwnedBy(auth()->user()->id)) {
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
        ])->where('id', '=', $id)->firstOrFail();

        if(!$chapter->isOwnedBy(auth()->user()->id)) {
            return response()->json([ 'message' => MESSAGE_CHAPTER_NOT_ACCESSIBLE ], 403);
        }

        return $chapter;
    }
}
