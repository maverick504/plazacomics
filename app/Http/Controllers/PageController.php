<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use Storage;

class PageController extends Controller
{
    /**
     * Upload a page's image to a temporal folder.
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

        if($image->width() > COMIC_PAGE_MAX_WIDTH) {
            $image->resize(COMIC_PAGE_MAX_WIDTH, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        // Validate image size.
        if($image->height() > COMIC_PAGE_MAX_HEIGHT) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
               'image' => ['La imágen es muy alta con proporción a su ancho.']
            ]);
        }

        // Save the image in the server
        $image->stream();
        $fileName = uniqid() . '.jpg';
        Storage::disk('spaces')->put('tmp/' . $fileName, $image, 'public');

        return response()->json([
            'temporal_image_url' => 'tmp/'.$fileName,
            'temporal_file_name' => $fileName
        ]);
    }
}
