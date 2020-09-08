@extends('layouts.app')


@section('content')
    <div class="container">
        <h1>Список ботов</h1>
        @if (Session::has('flash_message'))
            <font color="Red">{{ Session::get('flash_message') }}</font>
        @endif
        <a href=" {{ route('bots.create') }}"><h3>Создать бота</h3></a>
        @foreach ($user->bots as $bot)   
            <div class="card mb-3" style="max-width: 50rem;">
                <div class="card-header">
                    @if ($bot->is_active == 1)
                        <div>Бот включен</div>
                    @else
                        <div>Бот выключен</div>
                    @endif
                </div>  
                <div class="card-body text-dark">  
                    <a href= "{{route('bots.show', $bot)}}" >
                        <h3 class="card-title">{{$bot->name}}</h3>
                    </a>
                    <p class="card-text">{{$bot->description}}</p>
                </div>  
            </div>          
        @endforeach
    </div>
@endsection