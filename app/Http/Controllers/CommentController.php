<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Laravelista\Comments\CommentControllerInterface;
use Laravelista\Comments\Comment;
use Illuminate\Support\Facades\Notification;
use Response;

class CommentController extends Controller implements CommentControllerInterface
{
    use ValidatesRequests, AuthorizesRequests;

    protected $commentable;

    /**
     * Checks that the GET parameters are received and valid.
     */
    private function getCommentable()
    {
        if(!array_key_exists('commentable_type', $_GET)) {
            return Response::json([ 'message' => 'commentable_type is required.' ], 400);
        }

        if(!array_key_exists('commentable_id', $_GET)) {
            return Response::json([ 'message' => 'commentable_id is required.' ], 400);
        }

        switch($_GET['commentable_type']) {
            case 'chapter':
                $commentable = \App\Chapter::findOrFail($_GET['commentable_id']);
                break;
            case 'post':
                $commentable = \App\Post::findOrFail($_GET['commentable_id']);
                break;
            default:
                return Response::json([ 'message' => 'commentable_type is not valid.' ], 400);
        }

        if($commentable instanceof \App\Chapter && !$commentable->hasBeenRelased()) {
            return response()->json([ 'message' => 'No puedes comentar este capitulo porque no fué publicado aún' ], 403);
        }

        $this->commentable = $commentable;
    }

    /**
     * Returns all the comments on a chapter.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = $this->getCommentable();
        if($response) { return $response; }

        return $this->commentable->comments()->get();
    }

    /**
     * Creates a new comment for given model.
     */
    public function store(Request $request)
    {
        $response = $this->getCommentable();
        if($response) { return $response; }

        $this->validate($request, [
            'message' => 'required|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $comment = new Comment;

            $comment->commenter()->associate(auth()->user());
            $comment->commentable()->associate($this->commentable);
            $comment->comment = $request->message;
            $comment->approved = true;
            $comment->save();

            if($this->commentable instanceof \App\Chapter) {
                $chapter = $this->commentable;
                $serie = $this->commentable->serie()->first();
                $authors = $serie->authors()->get();

                $usersToNotify = array();

                // We want to notify the authors of the series, but if the comment was created by
                // an author, we don't want to notify the author who created the comment.
                foreach($authors as $author) {
                    if($author->id !== auth()->user()->id) {
                        $usersToNotify[] = $author;
                    }
                }

                $info = array(
                    'icon_url' => auth()->user()->avatar_url,
                    'message' => '**' . auth()->user()->username . '** comentó en **' . $chapter->title . '** de tu cómic, **' . $serie->name . '**: "' . $comment->comment . '".',
                    'additional_data' => array(
                        'id' => $comment->id,
                        'commenter_id' => auth()->user()->id,
                        'commenter_username' => auth()->user()->username,
                        'commentable_type' => 'App\Chapter',
                        'serie_id' => $serie->id,
                        'serie_slug' => $serie->slug,
                        'chapter_id' => $chapter->id,
                        'chapter_slug' => $chapter->slug
                    )
                );

                Notification::send($usersToNotify, new \App\Notifications\NewComment($info));
            }
            elseif($this->commentable instanceof \App\Post) {
                $post = $this->commentable()->first();
                $author = $post->author();

                $usersToNotify = array();

                // We don't want to notify the user who created the comment, if it was the author.
                if($author->id !== auth()->user()->id) {
                    $usersToNotify[] = $author;
                }

                $info = array(
                    'icon_url' => auth()->user()->avatar_url,
                    'message' => '**' . auth()->user()->username . '** comentó en tu post **' . $post->title . '**: "' . $comment->comment . '".',
                    'additional_data' => array(
                        'id' => $comment->id,
                        'commenter_id' => auth()->user()->id,
                        'commenter_username' => auth()->user()->username,
                        'commentable_type' => 'App\Post',
                        'post_id' => $post->id
                    )
                );

                Notification::send($usersToNotify, new \App\Notifications\NewComment($info));
            }

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
            return response()->json([ 'comment' => 'Este comentario no se puede eliminar porque tiene respuestas.' ], 400);
        }

        $comment->delete();

        return response()->json(null, 204);
    }

    /**
     * Creates a reply "comment" to a comment.
     */
    public function reply(Request $request, Comment $comment)
    {
        if($comment->child_id !== null) {
          $error = \Illuminate\Validation\ValidationException::withMessages([
             'comment' => [ "No puedes responder a una respuesta directamente, en lugar de eso, responde el comentario original." ]
          ]);
          throw $error;
        }

        $this->validate($request, [
            'message' => 'required|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $reply = new Comment;

            $reply->commenter()->associate(auth()->user());
            $reply->commentable()->associate($comment->commentable);
            $reply->parent()->associate($comment);
            $reply->comment = $request->message;
            $reply->approved = true;
            $reply->save();

            $commentCreator = $comment->commenter()->first();
            $commentChapter = \App\Chapter::find($comment->commentable_id);
            $commentSerie = $commentChapter->serie()->first();

            $usersToNotify = array();

            // We want to notify the creator of the original comment but not if he is the user who created the reply.
            if($commentCreator->id !== auth()->user()->id) {
                $usersToNotify[] = $commentCreator;
            }

            $info = array(
                'icon_url' => auth()->user()->avatar_url,
                'message' => '**' . auth()->user()->username . '** respondió a tu comentario: **' . $reply->comment . '**.',
                'additional_data' => array(
                    'id' => $reply->id,
                    'commenter_id' => auth()->user()->id,
                    'commenter_username' => auth()->user()->username,
                    'commentable_type' => 'App\Comment',
                    'comment_id' => $comment->id,
                    'serie_id' => $commentSerie->id,
                    'serie_slug' => $commentSerie->slug,
                    'chapter_id' => $commentChapter->id,
                    'chapter_slug' => $commentChapter->slug
                )
            );

            Notification::send($usersToNotify, new \App\Notifications\NewComment($info));

            // Notify the other users who replied to the comment.

            $usersToNotify = array();

            foreach($comment->children()->get() as $commentReply) { // We name it $commentReply to avoid override the $reply defined above.
                $replier = $commentReply->commenter()->first();

                if($replier->id !== auth()->user()->id && $replier->id !== $commentCreator->id) {
                    $usersToNotify[] = $replier;
                }
            }

            $info = array(
                'icon_url' => auth()->user()->avatar_url,
                'message' => '**' . auth()->user()->username . '** también respondió a: **' . $reply->comment . '**.',
                'additional_data' => array(
                    'id' => $reply->id,
                    'commenter_id' => auth()->user()->id,
                    'commenter_username' => auth()->user()->username,
                    'commentable_type' => 'App\Comment',
                    'comment_id' => $comment->id,
                    'serie_id' => $commentSerie->id,
                    'serie_slug' => $commentSerie->slug,
                    'chapter_id' => $commentChapter->id,
                    'chapter_slug' => $commentChapter->slug
                )
            );

            Notification::send($usersToNotify, new \App\Notifications\NewComment($info));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([ 'message' => MESSAGE_UNKNOWN_ERROR ], 500);
        }

        // Success! Return the comment
        return response()->json(Comment::find($reply->id), 201);
    }
}
