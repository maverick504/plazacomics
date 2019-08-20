<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\ValidSlug;
use DateTime;
use Image;
use Storage;
use Hashids;

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
            'username' => [ 'required', 'max:45', new ValidSlug, 'unique:users,username,' . $user->id ],
            'name' => 'nullable|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'gender' => 'nullable|in:M,F',
            'birth_date' => 'nullable|date_format:d/m/Y',
            'location' => 'nullable',
            'about' => 'nullable',
            'links' => 'nullable|array|max:10'
        ]);

        if($request->get('birth_date')) {
            $birthDate = DateTime::createFromFormat('d/m/Y', $request->get('birth_date'))->format('Y-m-d h:i:s');
        } else {
            $birthDate = null;
        }

        $links = [];

        // Validate links.
        if($request->has('links')) {
          foreach($request->get('links') as $link) {
            if(!array_key_exists('url', $link)) {
              throw \Illuminate\Validation\ValidationException::withMessages([
                 'links' => [ "El formato no es válido, falta url en alguno de los enlaces." ]
              ]);
            }
            if(strlen($link['url']) === 0) {
              throw \Illuminate\Validation\ValidationException::withMessages([
                 'links' => [ "El formato no es válido, la url en alguno de los enlaces está vacía." ]
              ]);
            }
            if(strlen($link['url']) > 250) {
              throw \Illuminate\Validation\ValidationException::withMessages([
                 'links' => [ "El formato no es válido, la url de alguno de los enlaces supera los 250 caracteres." ]
              ]);
            }

            if(!array_key_exists('title', $link)) {
              throw \Illuminate\Validation\ValidationException::withMessages([
                 'links' => [ "El formato no es válido, falta título en alguno de los enlaces." ]
              ]);
            }
            if(strlen($link['title']) === 0) {
              throw \Illuminate\Validation\ValidationException::withMessages([
                 'links' => [ "El formato no es válido, el título de alguno de los enlaces está vacío." ]
              ]);
            }
            if(strlen($link['title']) > 45) {
              throw \Illuminate\Validation\ValidationException::withMessages([
                 'links' => [ "El formato no es válido, el título de alguno de los enlaces supera los 45 caracteres." ]
              ]);
            }

            $links[] = array(
              'url' => $link['url'],
              'title' => $link['title']
            );
          }
        }

        return tap($user)->update([
            'username' => $request->get('username'),
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'gender' => $request->get('gender'),
            'birth_date' => $birthDate,
            'location' => $request->get('location'),
            'about' => $request->get('about'),
            'links' => $links
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
            'image'=> [ 'required', 'image' ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors('image')], 400);
        }

        $image = Image::make($request->file('image'));

        // Validate image size.
        if($image->width()!==AVATAR_WIDTH || $image->height()!==AVATAR_HEIGHT) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
               'image' => ['The image size is not valid' ]
            ]);
        }

        // Save the image in the server
        $image->stream();
        $filePath = 'user-avatars/' . Hashids::encode($user->id) . '_' . uniqid() . '.jpg';
        Storage::disk('spaces')->put($filePath, $image, 'public');

        // Update the user
        return tap($user)->update([
          'avatar_url' => $filePath
        ]);
    }
}
