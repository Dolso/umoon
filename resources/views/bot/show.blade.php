@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-10">
                <div class="container mt-3">
                    <a href="{{ route('bots.index') }}"><h4>Список ботов</h4></a>
                    <a href="{{ route('bots.edit', $bot) }}"><h4>Изменить параметры бота</h4></a>

                    <h1>{{$bot->name}}</h1>

                    <div>{{$bot->description}}</div>

                    @if ($bot->is_active == 1)
                        <div class = "font-weight-bold text-success status_bot mt-3">Бот включен</div>
                    @else
                        <div class = "font-weight-bold text-danger status_bot mt-3">Бот выключен</div>
                    @endif
                    

                    @if ($bot->is_active == 1)
                        <button type="button" class="btn btn-secondary turn_on_off mt-3">Выключить бот</button>
                    @else
                        <button type="button" class="btn btn-primary turn_on_off mt-3">Включить бот</button>
                    @endif

                </div>
                <div class="container mt-3">
                    <div class = "font-weight-bold hash mt-3">Код: {{$bot->hash}}</div>
                </div>
                <div class="container mt-3">
                    <label for="triggers" class="control-label">Тригеры</label>

                    <textarea class = 'form-control triggers' name="triggers" cols="20" rows="20">{{$triggers_json}}</textarea></p>

                    <input class="btn btn-info button_triggers" type="submit" value="Сохранить тригеры">
                </div>
            </div>
            <div class="col-sm-2">
                <div class = "container">
                    Оченеь много текста
                </div>
            </div>
        </div>
    </div>

@endsection

<script type="text/javascript">

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

window.onload = function () {
    $('.btn.turn_on_off').click(function (event) {
        var status = $('.btn.turn_on_off').html();
        $.ajax({
            url: "{{ route('bots.update', $bot) }}",
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')    
            },
            data: {
                "switch" : status
            },
            success:function(data) {

                var turn_on_off = $('.btn.turn_on_off');
                var status_bot = $('.status_bot');

                if (status == 'Включить бот') {
                    turn_on_off.html('Выключить бот');
                    turn_on_off.removeClass('btn-primary');
                    turn_on_off.addClass('btn-secondary');

                    status_bot.html('Бот включен');
                    status_bot.removeClass('text-danger');
                    status_bot.addClass('text-success');

                } else if (status == 'Выключить бот') {
                    turn_on_off.html('Включить бот');
                    turn_on_off.removeClass('btn-secondary');
                    turn_on_off.addClass('btn-primary');

                    status_bot.html('Бот выключен');
                    status_bot.removeClass('text-success');
                    status_bot.addClass('text-danger');
                }
                
                if ($('.error-switch').length) {
                    $('.error-switch').remove();
                }
                console.log('The data was sent successfully');
            },
            error: function (msg) {
                if ($('.error-switch').length == 0) {
                    $(".btn.turn_on_off").after("<p class='text-danger font-weight-normal error-switch'>Изменение не были приняты</p>");
                }
                console.log('Warning. Data was not sent');
                console.log(msg);
            }
        });
    });

    $('.button_triggers').click(function (event) {
        var textarea = $('.triggers').val();
        console.log('Обновление тригеров');
        if (IsJsonString(textarea)) {
            $.ajax({
                url: "{{ route('triggers.update', $bot) }}",
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')    
                },
                data: {
                    "triggers" : textarea
                },
                success:function(data) {
                    console.log('Тригеры успешно сохранены');
                },
                error: function (msg) {
                    if ($('.error-switch').length == 0) {
                        $(".btn.turn_on_off").after("<p class='text-danger font-weight-normal error-switch'>Изменение не были приняты</p>");
                    }
                    console.log('Warning. Тригеры не сохранены');
                    console.log(msg);
                }
            });
        }
    });
}
</script>