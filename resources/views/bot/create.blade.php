@extends('layouts.app')


@section('content')
    <div class="container">
		@if (Session::has('flash_message'))
			<font color="Red">{{ Session::get('flash_message') }}</font>
		@endif
		<a href="{{ route('bots.index') }}"><h4>Список ботов</h4></a>
		<div style="max-width: 40rem;">
			{{ Form::model($bot, ['url' => route('bots.store'), 'files' => 'true']) }}
				{{ Form::label('name', 'Название бота', [ 'class' => 'control-label']) }}
				{{ Form::text('name', '', [ 'class' => 'form-control' ]) }}<br>
				{{ Form::label('description', 'Описание', [ 'class' => 'control-label' ]) }}
				{{ Form::textarea('description', '', [ 'class' => 'form-control' ]) }}<br>
                {{ Form::label('token', 'Token', [ 'class' => 'control-label' ]) }}
				{{ Form::password('token', [ 'class' => 'form-control fa-eye-slash', 'placeholder'=>'Token']) }}<br>
                {{ Form::label('confirmation_token', 'Confirmation Token', [ 'class' => 'control-label']) }}
				{{ Form::password('confirmation_token', [ 'class' => 'form-control fa-eye-slash', 'placeholder'=>'Confirmation token']) }}<br>
				{{ Form::submit('Добавить бота', [ 'class' => 'btn btn-info' ]) }}
			{{ Form::close() }}
		</div>
	</div>
@endsection