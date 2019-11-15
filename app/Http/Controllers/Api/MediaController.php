<?php

namespace Momentum\Http\Controllers\Api;

use Auth;
use Storage;
use Validator;
use Illuminate\Http\Request;
use Momentum\Media;
use Momentum\Events\FileUploaded;
use Momentum\Events\Media\MediaCreated;
use Momentum\Http\Controllers\Controller;

/**
 * Handles any AJAX-API requests related to media.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class MediaController extends Controller
{
    /**
     * Returns all media.
     * @since 0.2.5
     *
     * @return \Momentum\Media
     */
    public function index()
    {
        return Media::whereCurrentUserOrOrganization()
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Returns a media.
     * @since 0.2.5
     * 
     * @param int $id Media ID.
     *
     * @return \Momentum\Media
     */
    public function show($id)
    {
        return Media::whereCurrentUserOrOrganization()
            ->where('id', $id)
            ->first();
    }

    /**
     * Attempts to upload a file and save its information as
     * a new media record.
     * Returns a json response with the results.
     * @since 0.2.5
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if (!$request->hasFile('file'))
            return response()->json([
                'errors' => ['file' => [trans('media.validation.file.required')]],
            ], 422);

        $files = $request->file('file');
        if (!is_array($files))
            $files = [$files];
        $medias = [];
        // Loop files and validate
        foreach ($files as $file) {
            $media = new Media;
            // Since only 1 drive exists, upload code will be placed here
            if (config('media.driver') === 'laravel' && $file->isValid()) {

                // Validator
                $validator = Validator::make(
                    [
                        'size' => $file->getSize(),
                        'mime' => $file->getMimeType(),
                    ],
                    [
                        'size' => 'required|numeric|between:'.config('media.min_size').','.config('media.max_size'),
                        'mime' => 'required|in:'.implode(',', config('media.mimes')),
                    ]
                );

                if ($validator->fails())
                    return response()->json(['errors' => $validator->messages()], 422);

                // Prepare media model
                $media->user_id = Auth::user()->id;
                $media->path = config('media.drivers.laravel.storage');
                $media->filename = (config('media.drivers.laravel.unique_name') ? uniqid().'-' : '')
                    .$file->getClientOriginalName();
                $media->relative = config('media.relative_path').$media->filename;
                $media->mime = $file->getMimeType();
                $media->size = $file->getSize();

                if ($request->input('is_organization', false) === 1
                    || $request->input('is_organization', false) === true
                )
                    $media->organization_id = Auth::user()->organization_id;

                $media->file = $file;
                $medias[] = $media;
            }
        }
        // Loop and store media
        foreach ($medias as $media) {
            $media->file->storeAs(config('media.relative_path'), $media->filename, $media->storage_disk);
            event(new FileUploaded($media->file, $media));

            unset($media->file);

            $media->save();
            event(new MediaCreated($media));
        }

        return response()->json([
            'message'  => trans('media.uploaded'),
            'success'  => true,
        ]);
    }

    /**
     * Renders a spefific media.
     * @since 0.2.5
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($id)
    {
        $media = Media::whereCurrentUserOrOrganization()
            ->where('id', $id)
            ->first();

        if ($media === null)
            abort(404);

        return response(Storage::disk($media->storage_disk)->get($media->relative))
                ->header('Content-Type', $media->mime)
                ->header('Content-disposition', ($media->canForceDownload ? 'attachment; ' : '').'filename="'.$media->filename.'"')
                ->header('Content-Description', $media->description);
    }
}
