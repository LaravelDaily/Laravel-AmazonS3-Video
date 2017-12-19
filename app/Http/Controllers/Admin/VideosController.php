<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\App;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVideosRequest;
use App\Http\Requests\Admin\UpdateVideosRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\Media;

class VideosController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of Video.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // App::make('files')->link(storage_path('app/public'), public_path('storage'));
        if (! Gate::allows('video_access')) {
            return abort(401);
        }
        
        if (request('show_deleted') == 1) {
            if (! Gate::allows('video_delete')) {
                return abort(401);
            }
            $videos = Video::onlyTrashed()->get();
        } else {
            $videos = Video::all();
        }
        
        return view('admin.videos.index', compact('videos'));
    }

    /**
     * Show the form for creating new Video.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('video_create')) {
            return abort(401);
        }
        return view('admin.videos.create');
    }

    /**
     * Store a newly created Video in storage.
     *
     * @param \App\Http\Requests\StoreVideosRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVideosRequest $request)
    {
        if (! Gate::allows('video_create')) {
            return abort(401);
        }
        $request = $this->saveFiles($request);
        $video = Video::create($request->all());
        foreach ($request->input('video_id', []) as $index => $id) {
            $model = config('medialibrary.media_model');
            $file = $model::find($id);
            $file->model_id = $video->id;
            $file->save();
        }
        
        return redirect()->route('admin.videos.index');
    }

    /**
     * Show the form for editing Video.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('video_edit')) {
            return abort(401);
        }
        $video = Video::findOrFail($id);
        
        return view('admin.videos.edit', compact('video'));
    }

    /**
     * Update Video in storage.
     *
     * @param \App\Http\Requests\UpdateVideosRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVideosRequest $request, $id)
    {
        if (! Gate::allows('video_edit')) {
            return abort(401);
        }
        $request = $this->saveFiles($request);
        $video = Video::findOrFail($id);
        $video->update($request->all());
        if ($request->video === true) {
            $media = [];
            foreach ($request->input('video_id[]') as $index => $id) {
                $model = config('laravel-medialibrary.media_model');
                $file = $model::find($id);
                $file->model_id = $video->id;
                $file->save();
                $media[] = $file->toArray();
            }
            $video->updateMedia($media, 'video');
        }
        return redirect()->route('admin.videos.index');
    }

    /**
     * Display Video.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        if (! Gate::allows('video_view')) {
            return abort(401);
        }
        $video = Video::findOrFail($id);
        
        return view('admin.videos.show', compact('video'));
    }

    /**
     * Remove Video from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('video_delete')) {
            return abort(401);
        }
        $video = Video::findOrFail($id);
        $video->deletePreservingMedia();
        if (Storage::disk('s3')->exists($video->file_name)) {
            $video->delete();
        }
        // $disk = Storage::disk('s3');
        // $video = $disk->findUrl()->delete();
        
        return redirect()->route('admin.videos.index');
    }

    /**
     * Delete all selected Video at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('video_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Video::whereIn('id', $request->input('ids'))->get();
            
            foreach ($entries as $entry) {
                $entry->deletePreservingMedia();
            }
        }
    }

    /**
     * Restore Video from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('video_delete')) {
            return abort(401);
        }
        $video = Video::onlyTrashed()->findOrFail($id);
        $video->restore();
        
        return redirect()->route('admin.videos.index');
    }

    /**
     * Permanently delete Video from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('video_delete')) {
            return abort(401);
        }
        $video = Video::onlyTrashed()->findOrFail($id);
        $video->forceDelete();
        
        return redirect()->route('admin.videos.index');
    }
}
