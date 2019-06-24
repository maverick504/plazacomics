<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use Image;
use Storage;
use Hashids;
use App\Rules\ValidSlug;
use App\Rules\ValidBase64Image;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'username' => [ 'required', new ValidSlug, 'unique:users,username,'.$user->id ],
            'name' => 'nullable',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'gender' => 'nullable|in:M,F',
            'birth_date' => 'nullable|date_format:d/m/Y',
            'location' => 'nullable',
            'about' => 'nullable'
        ]);

        if($request->get('birth_date')) {
            $birthDate = DateTime::createFromFormat('d/m/Y', $request->get('birth_date'))->format('Y-m-d h:i:s');
        } else {
            $birthDate = null;
        }

        return tap($user)->update([
            'username' => $request->get('username'),
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'gender' => $request->get('gender'),
            'birth_date' => $birthDate,
            'location' => $request->get('location'),
            'about' => $request->get('about')
        ]);
    }

    /**
     * Update the user's profile picture.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $validator = \Validator::make($request->all(), [
            'image'=> [ 'required', new ValidBase64Image ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors('image')], 400);
        }

        $image = Image::make($request->image);

        // Validate image size.
        if($image->width()!==AVATAR_WIDTH || $image->height()!==AVATAR_HEIGHT) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
               'image' => ['The image size is not valid' ]
            ]);
        }

        // Save the image in the server
        $image->stream();
        $fileName = Hashids::encode($user->id) . '.jpg';
        Storage::disk('spaces')->put('user-avatars/' . $fileName, $image, 'public');

        // Update the user
        return tap($user)->update([
          'avatar_url' => 'user-avatars/' . $fileName
        ]);
    }
}
