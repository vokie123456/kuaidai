@extends('admin.layout')

@section('title', $pageName)

@section('page-wrapper')

    <br>

    <div class="row">
        <div class="col-xs-12">
            @if(isset($error))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    {{$error}}
                </div>
            @endif

            @if(isset($success))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    {{$success}}
                </div>
            @endif


        </div>
    </div>

@endsection