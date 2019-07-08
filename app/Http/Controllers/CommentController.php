<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Laravelista\Comments\CommentControllerInterface;
use Laravelista\Comments\Comment;
use App\Notifications\UserCommentedChapter;
use Illuminate\Support\Facades\Notification;
use App\Chapter;

class CommentController extends Controller implements CommentControllerInterface
{
    use ValidatesRequests, AuthorizesRequests;

    /**
     * Returns all the comments on a chapter.
     *
     * @return \Illuminate\Http\Response
     */
    public function chapterIndex($chapterId)
    {
        $chapter = Chapter::find($chapterId);

        if(!$chapter) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(!$chapter->hasBeenRelased()) {
            return response()->json([ 'message' => MESSAGE_CHAPTER_NOT_ACCESSIBLE ], 403);
        }

        return $chapter->comments()->get();
    }

    /**
     * Creates a new comment for given model.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'chapter_id' => 'required|numeric',
            'message' => 'required|string|max:500'
        ]);

        $chapter = Chapter::find($request->get('chapter_id'));

        if(!$chapter) {
            return response()->json([ 'message' => MESSAGE_NOT_FOUND ], 404);
        }

        if(!$chapter->hasBeenRelased()) {
            return response()->json([ 'message' => MESSAGE_CHAPTER_NOT_ACCESSIBLE ], 403);
        }

        DB::beginTransaction();
        try {
            $comment = new Comment;

            $comment->commenter()->associate(auth()->user());
            $comment->commentable()->associate($chapter);
            $comment->comment = $request->message;
            $comment->approved = true;
            $comment->save();

            $serie = $chapter->serie()->first();
            $authors = $serie->authors()->get();
            Notification::send($authors, new UserCommentedChapter(auth()->user(), $serie, $chapter, $comment));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([ 'message' => MESSAGE_UNKNOWN_ERROR ], 500);
        }

        // Success! Return the comment
        return response()->json(Comment::find($comment->id), 201);
    }

    /**
     * Updates the message of the comment.
     */
    public function update(Request $request, Comment $comment)
    {
        /*
        $this->authorize('edit-comment', $comment);

        $this->validate($request, [
            'message' => 'required|string'
        ]);

        $comment->update([
            'comment' => $request->message
        ]);

        return redirect()->to(url()->previous() . '#comment-' . $comment->id);
        */
    }

    /**
     * Deletes a comment.
     */
    public function destroy(Comment $comment)
    {
        if($comment->commenter_type != 'App\User' || $comment->commenter_id != auth()->user()->id) {
          return response()->json(null, 403);
        }

        if($comment->children()->count() > 0) {
          return response()->json([ 'comment' => 'This comment will not be deleted because it has replies.' ], 400);
        }

        $comment->delete();

        return response()->json(null, 204);
    }

    /**
     * Creates a reply "comment" to a comment.
     */
    public function reply(Request $request, Comment $comment)
    {
        if($comment.child_id !== null) {
          $error = \Illuminate\Validation\ValidationException::withMessages([
             'comment' => [ "It is not allowed to reply to a reply." ]
          ]);
          throw $error;
        }

        $this->validate($request, [
            'message' => 'required|string|max:500'
        ]);

        $reply = new Comment;

        $reply->commenter()->associate(auth()->user());
        $reply->commentable()->associate($comment->commentable);
        $reply->parent()->associate($comment);
        $reply->comment = $request->message;
        $reply->approved = true;
        $reply->save();

        // Success! Return the comment
        return response()->json(Comment::find($reply->id), 201);
    }
}
