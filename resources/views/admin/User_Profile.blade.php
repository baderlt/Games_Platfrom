@extends('admin.home')
@section('content')
<link rel="stylesheet" href="{{asset('games.css')}}">
<h1 style="margin: 10px; display: flex; gap:10px ; align-items: center;justify-content: center;">
    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-person-vcard" viewBox="0 0 16 16">
        <path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5" />
        <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z" />
    </svg>
    {{$username}}
</h1>

<div class="game">
    <div class="left" style="     height: calc(100vh - 100px);">

        <div class="game-miniature" style="height: 200px;background-color: transparent; border:0 ;margin-top: 0;">
            <img class="img-game" style="border-radius:50%;  height: 190px; width: 190px;" src="{{asset('profile.gif')}}" alt="Vignette">
        </div>
        <h3><b>number:</b>{{$user->id}} </h3>
        <h3><b>name:</b>{{$user->name}} </h3>
        <h3><b>Last connexion:</b>{{$user->lastConextion}}</h3>
        <h3><b>Join at :</b> {{$user->created_at}}</h3>
        <h2 style="text-align: center; padding-top: 10px;">Author games </h2>
        <div style="height: calc(100% - 0px ); overflow-y: auto; border-radius: 5px;padding: 5px; ">
            @foreach ($user->games as $game )
            <div class="game_user {{$game->trashed() ? 'trashed' :''}}" data-slug="{{$game->slug}}" style="margin-top: 10px;position: relative; ">
            <div style="width: 10%; display: flex; justify-content: center; align-items: center;">
                <img src="{{asset('game_.png')}}" width="50px" height="50px" alt="game thumbel">
            </div>
                <div style="display: flex; flex-direction: column; width: 80%; gap:10px">
                    <div style="display: flex; flex-direction: row;justify-content: space-between;">
                        <h4><b># </b>{{$game->id}}</h4>

                        <h4>{{$game->created_at}}</h4>
                    </div>

                    <div>
                        <h4><b>Slug:</b>{{$game->slug}}</h4>
                    </div>
                    @if($game->trashed())
    <div class="" style="position: absolute; right:10px; bottom: 10px; color:red">
 Deleted
                       </div>
                @endif
                </div>
                
            </div>
            @endforeach
        </div>
    </div>




    <div class="right" style="height: calc(100vh - 100px);">

    </div>
</div>

<script>
    document.querySelectorAll('.game_user').forEach((item)=>{
item.addEventListener('click',function(e){
   if( item.classList.contains('trashed')){
    alert('this game was deleted .. !')
    return ;
   };
   let a=document.createElement('a');
   let game = e.target.closest('.game_user').dataset.slug;
   let href=`${window.location.origin}/admin/games/${game}`;
   a.href=href;
   a.click();
 

})
    })
</script>

@endsection
