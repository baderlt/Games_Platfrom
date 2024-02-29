<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{asset('games.css')}}">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


    <style>
        #deleted{
            color: red;

        }

        #delete{
    
            background-color: red;
            font-size: 20px;
            height: 30px;
            border-radius: 4px 4px 4px 4px ;


        }
        a{
            text-decoration: none;
            color: black;
        }
    </style>
</head>
<body>
@extends('admin.home')

@section('content')
@if (isset($error))
<div>
    <h1>{{$error}}</h1>
</div>
    
@endif
    <div class="game">
    @if ($game)
        <div class="left">

            <h1><b>{{ $game->titre }}</b> </h1> 
            <div class="game-miniature">
            <img class="img-game" src="{{ file_exists(public_path('storage/'.$game->vignette))? asset('storage/'.$game->vignette) : asset('game.png')}}" alt="Vignette">
            </div>   
            <h3><b>Slug</b>: {{ $game->slug }}</h3>
            <h3><b>Description:</b> {{ $game->description }}</p>
            <h3><b>Author:</b> {{ $game->auteur }}</p>
            <h3><b>Score Count:</b> {{ $game->scoreCount }}</p>
      
        <h3><b>Last Version </b> {{$game->last_version}}</h3>
            <h3><b>Upload Timestamp:</b> {{ $game->uploadTimestamp }}</p>
            @if($game->trashed())
                   <p id='deleted'> Deleted</p>
                  @else 
                  <div style="text-align:right;width: 100%;">
                  <form action="{{ route('admin.games_delete', ['slug' => $game->slug]) }}" method="POST">
            @csrf
            @method('DELETE')

<button style="text-align: right;"  data-slug="{{$game->slug}}" id="delete" > Delete</button> 
    </from>

</div>
                @endif 

        </div>
        <div class="right">
            <h1>Scores {{$game->slug}}</h1>
            <div style="flex-direction: row; display: flex;">
                <form action="" style=" display: flex; gap:10px; ">
                    <select name="version" id="version" style="height: 30px;  background-color: #2B2B2B;"  id="" required>
                        <option value="">Versions </option>
                        @foreach ($versions as $v)
                        <option value="{{$v->version}}">V{{$v->version}} </option>
                        @endforeach
                    </select>
                    <input type="submit"  style="  height: 30px;
    background-color: rgb(129, 19, 19);
    border: 0;
    border-radius: 4px;
    padding: 4px;width:100px; top:0px ;position: relative;" value="search" >
 
                </form>
                <form id="formDelete" action="" method="POST"  onsubmit="return confirm('Are you sure you want to delete scores ?')">
    @csrf
    @method('DELETE')   
    <button type="submit"  id="Btn-delete" data-game="{{$game->slug}}"  class="Deleted">
           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                    </svg>
       
                </button>
                </form>
            </div>
    
            @foreach ($scores as  $score)
            <div class="score">
                <div>
                    <h2><b>#</b>{{$score->name}}</h4>
                    <h4 ><b>Version:</b>{{$score->version}}</h4>
                </div>

                <h4><b>Score:</b>{{$score->score}}</h4>
                <div>

                    <h4>{{$score->date}}</h4>
                    
                    <button class="Deleted"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                    </svg></button>
                </div>
            </div>
                
            @endforeach
        </div>

    @endif
    </div>
    <script  >

const delete_Btn =document.getElementById('Btn-delete')
console.log(delete_Btn.dataset.game);
// console.log(delete_Btn);  
delete_Btn.addEventListener('click',(e)=>{     
    //  console.log(e); 
        const form3 = document.getElementById('formDelete');
        const version = document.getElementById('version').value;
        console.log(version);
        console.log(e.target.dataset.game);
        // console.log(form3)
        form3.setAttribute("action","/admin/scores/"+e.target.dataset.game+"?version="+version);
        form3.setAttribute("methode","delete");
    


});
</script>
@endsection


</body>
</html>