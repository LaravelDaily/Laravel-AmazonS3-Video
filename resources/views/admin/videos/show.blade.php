@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.videos.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.videos.fields.name')</th>
                            <td field-key='name'>{{ $video->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.videos.fields.video')</th>
                            <td field-key='video'> @foreach($video->getMedia('video') as $media)
                                <p class="form-group">
                                    <a href="{{ $media->getUrl() }}" target="_blank" download="{{ $media->getUrl() }}">{{ $media->name }} ({{ round($media->size / 1000000, 2) }} MB)</a>
                                </p>
                            <video width="920" height="640" controls><source src="{{$media->getUrl()}}"> {{$media->name}} </video>
                            @endforeach</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.videos.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop
