@extends('layouts.admin', ['title' => __('backend.packages_types_add')])

@section('content')

    <div class="page-title">
        <h3>{{ __('backend.packages_types_add') }}</h3>
        <div class="page-breadcrumb">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">{{ __('backend.home') }}</a></li>
                <li>{{ __('backend.package') }}</li>
                <li class="active">{{ __('backend.packages_types_add') }}</li>
            </ol>
        </div>
    </div>

    <div id="main-wrapper">
    {{ $typeTest ? $typeTest : '-' }}
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-white">
                    <div class="panel-heading clearfix">
                        <div class="col-md-12">
                            <h4 class="panel-title">{{ __('backend.packages_types_add') }}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <form method="post" action="{{route('packages-types.store')}}" enctype="multipart/form-data">

                            {{csrf_field()}}

                            <div class="row" style="line-height: 2.5;">

                            <div class="col-md-1 form-group"> {{ __('backend.title') }}  </div>
                            <div class="col-md-5 form-group{{$errors->has('title') ? ' has-error' : ''}}">
                                <input type="text" class="form-control" name="title">
                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>


                            <div class="col-md-1 form-group"> {{ __('backend.package') }} </div>
                            <div class="col-md-5 form-group{{$errors->has('package_id') ? ' has-error' : ''}}">
                                <select class="form-control" name="package_id">
                                    <option value="">Seleccione</option>
                                    @foreach($packages as $element)
                                        <option value="{{ $element->id }}" >{{ $element->title }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('package_id'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('package_id') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-12 form-group">
                                
                                <div class="col-md-1 form-group" style="margin-left: -15px;"> {{ __('backend.package_time') }}  </div>
                                <div class="col-md-1 form-group{{$errors->has('length') ? ' has-error' : ''}}">
                                <input type="number" class="form-control" name="length">
                                    @if ($errors->has('length'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('length') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-3 form-group" >{{ __('backend.minutes') }}</div>
                            
                            </div>

                            <div class="col-md-1 form-group"> {{ __('backend.status') }}  </div>
                            <div class="col-md-11 form-group{{$errors->has('status') ? ' has-error' : ''}}">
                                <select class="form-control" name="status">
                                    <option value="1">{{ __('backend.active') }}</option>
                                    <option value="0">{{ __('backend.disabled') }}</option>
                                </select>
                                @if ($errors->has('status'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('status') }}</strong>
                                    </span>
								@endif
                            </div>

                            <div class="col-md-1 form-group"> {{ __('backend.booking') }} </div>
                            <div class="col-md-11 form-group">
                                <div class="row">
                                    <div class="col-md-1 form-group">Min</div>
                                    <div class="col-md-5 form-group{{$errors->has('booking_min') ? ' has-error' : ''}}">
                                        <input type="number" class="form-control" name="booking_min">
                                        @if ($errors->has('booking_min'))
                                            <span class="help-block">
                                                <strong class="text-danger">{{ $errors->first('booking_min') }}</strong>
                                            </span>
								        @endif
                                    </div>
                                    <div class="col-md-1 form-group">Max</div>
                                    <div class="col-md-5 form-group{{$errors->has('booking_max') ? ' has-error' : ''}}">
                                        <input type="number" class="form-control" name="booking_max">
                                        @if ($errors->has('booking_max'))
                                            <span class="help-block">
                                                <strong class="text-danger">{{ $errors->first('booking_max') }}</strong>
                                            </span>
								        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1 form-group"> {{ __('backend.user') }}  </div>
                            <div class="col-md-11 form-group">
                                <div class="row">
                                    <div class="col-md-1 form-group">Min</div>
                                    <div class="col-md-5 form-group{{$errors->has('player_min') ? ' has-error' : ''}}">
                                        <input type="number" class="form-control" name="player_min">
                                        @if ($errors->has('player_min'))
                                            <span class="help-block">
                                                <strong class="text-danger">{{ $errors->first('player_min') }}</strong>
                                            </span>
								        @endif
                                    </div>
                                    <div class="col-md-1 form-group">Max</div>
                                    <div class="col-md-5 form-group{{$errors->has('player_max') ? ' has-error' : ''}}">
                                        <input type="number" class="form-control" name="player_max">
                                        @if ($errors->has('player_max'))
                                            <span class="help-block">
                                                <strong class="text-danger">{{ $errors->first('player_max') }}</strong>
                                            </span>
								        @endif
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-1 form-group"> {{ __('backend.guest') }} </div>
                            <div class="col-md-11 form-group">
                                <div class="row">
                                    <div class="col-md-1 form-group">Min</div>
                                    <div class="col-md-5 form-group form-group{{$errors->has('guest_min') ? ' has-error' : ''}}">
                                        <input type="number" class="form-control" name="guest_min">
                                        @if ($errors->has('guest_min'))
                                            <span class="help-block">
                                                <strong class="text-danger">{{ $errors->first('guest_min') }}</strong>
                                            </span>
								        @endif
                                    </div>
                                    <div class="col-md-1 form-group">Max</div>
                                    <div class="col-md-5 form-group form-group{{$errors->has('guest_max') ? ' has-error' : ''}}">
                                        <input type="number" class="form-control" name="guest_max">
                                        @if ($errors->has('guest_max'))
                                            <span class="help-block">
                                                <strong class="text-danger">{{ $errors->first('guest_max') }}</strong>
                                            </span>
								        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12 form-group text-right">
                                <button type="submit" class="btn btn-primary btn-lg">{{ __('backend.create') }}</button>
                            </div>         
                            
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
