@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.videos.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.videos.store'], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('name', trans('quickadmin.videos.fields.name').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control file-upload', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('video', trans('quickadmin.videos.fields.video').'*', ['class' => 'control-label']) !!}
                    {!! Form::file('video[]', [
                        'class' => 'form-control file-upload',
                        'data-url' => route('admin.media.upload'),
                        'accept' => 'video/mp4',
                        'data-bucket' => 'video',
                        'data-filekey' => 'video',
                        ]) !!}
                    <p class="help-block"></p>
                    <div class="photo-block">
                        <div class="progress-bar form-group">&nbsp;</div>
                        <div class="files-list"></div>
                    </div>
                    @if($errors->has('video'))
                        <p class="help-block">
                            {{ $errors->first('video') }}
                        </p>
                    @endif
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop
@section('javascript')
    @parent

    <script src="{{ asset('quickadmin/plugins/fileUpload/js/jquery.iframe-transport.js') }}"></script>
    <script src="{{ asset('quickadmin/plugins/fileUpload/js/jquery.fileupload.js') }}"></script>
    <script src="{{ asset('quickadmin/plugins/fileUpload/js/jquery.fileupload-video.js') }}"></script>
<script>
$(function () {
    $('.file-upload').each(function () {
        var $this = $(this);
        var $parent = $(this).parent();
        $(this).fileupload({
            maxChunkSize: 10000000, // 10 mb
            dataType: 'json',
            formData: {
                model_name: 'Video',
                bucket: $this.data('bucket'),
                file_key: $this.data('filekey'),
                _token: '{{ csrf_token() }}'
            },
            
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    var $line = $($('<p/>', {class: "form-group"}).html(file.name + ' (' + ((file.size / 1000000).toFixed(2)) + ' MB)')).appendTo($parent.find('.files-list'));
                 	  $line.append('<a href="#" class="btn btn-xs btn-danger remove-file">Remove</a>');
                      $line.append('<input type="hidden" name="' + $this.data('bucket') + '_id[]" value="' + file.id + '"/>');
                });
                $parent.find('.progress-bar').hide().css(
                    'width',
                    '0%'
                );
            }
        }).on('fileuploadprogressall', function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $parent.find('.progress-bar').show().css(
                'width',
                progress + '%'
            );
        });
    });
    $(document).on('click', '.remove-file', function () {
        var $parent = $(this).parent();
        $parent.remove();
        return false;
    });
});
    </script>
@stop