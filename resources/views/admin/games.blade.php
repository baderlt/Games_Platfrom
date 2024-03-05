<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
<link rel="stylesheet" href="{{asset('games.css')}}">
</head>
<body>
<!-- resources/views/admin/games/list.blade.php -->







@extends('admin.home')

@section('content')
<div class="container" style="overflow-y: none;">
    <div>
    <h2 style="text-align: center; margin: 10">Games List</h2>
    <form id="form" action="" method="get">
    <div style="text-align:rightz ; margin: 10px" class="searchBox">

        <input class="searchInput" type="text" id="search" name="search" value="{{$serched}}" placeholder="Search">
        <button class="searchButton"  >

        <svg xmlns="http://www.w3.org/2000/svg" width="29" height="29" viewBox="0 0 29 29" fill="none">
                <g clip-path="url(#clip0_2_17)">
                    <g filter="url(#filter0_d_2_17)">
                        <path d="M23.7953 23.9182L19.0585 19.1814M19.0585 19.1814C19.8188 18.4211 20.4219 17.5185 20.8333 16.5251C21.2448 15.5318 21.4566 14.4671 21.4566 13.3919C21.4566 12.3167 21.2448 11.252 20.8333 10.2587C20.4219 9.2653 19.8188 8.36271 19.0585 7.60242C18.2982 6.84214 17.3956 6.23905 16.4022 5.82759C15.4089 5.41612 14.3442 5.20435 13.269 5.20435C12.1938 5.20435 11.1291 5.41612 10.1358 5.82759C9.1424 6.23905 8.23981 6.84214 7.47953 7.60242C5.94407 9.13789 5.08145 11.2204 5.08145 13.3919C5.08145 15.5634 5.94407 17.6459 7.47953 19.1814C9.01499 20.7168 11.0975 21.5794 13.269 21.5794C15.4405 21.5794 17.523 20.7168 19.0585 19.1814Z" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" shape-rendering="crispEdges"></path>
                    </g>
                </g>
                <defs>
                    <filter id="filter0_d_2_17" x="-0.418549" y="3.70435" width="29.7139" height="29.7139" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood>
                        <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"></feColorMatrix>
                        <feOffset dy="4"></feOffset>
                        <feGaussianBlur stdDeviation="2"></feGaussianBlur>
                        <feComposite in2="hardAlpha" operator="out"></feComposite>
                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"></feColorMatrix>
                        <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_2_17"></feBlend>
                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_2_17" result="shape"></feBlend>
                    </filter>
                    <clipPath id="clip0_2_17">
                        <rect width="28.0702" height="28.0702" fill="white" transform="translate(0.403503 0.526367)"></rect>
                    </clipPath>
                </defs>
            </svg>


        </button>
        </form>

      </div>
    </div>
    <div style="height: calc(100vh - 126px); overflow-y: auto;padding: 10px;">
    @if(count($games) > 0 )
@foreach ($games as $game )
<div class="grid  {{ $game->trashed() ? 'game-geleted':'' }}">
    <div class="miniature">
    <img src="{{ file_exists(public_path('storage/'.$game->vignette))? asset('storage/'.$game->vignette) : asset('game.png') }}" alt="Vignette">


    </div>
    <div class="info">
    <h3><b>Auteur:</b>{{ $game->author->name }}  </h3>
        <h3><b>Title :</b>{{$game->titre}}</h3>
        <h3> <b>Slug :</b>{{$game->slug}}</h3>
        <h3> <b>Description :</b>{{$game->description}}</h3>
        @if ($game->versions->isNotEmpty())
        <h3> <b>Last Version on </b> {{ $game->versions->first()->created_at }}  </h3>
        <h3><b>Number version :</b>{{ $game->versions->first()->version }}  </h3>
                    @else
                  <h3>
                      - No versions available
                      </h3>
                      @endif

                 
             
       
    </div>

    @if($game->trashed())
    <div class="Deleted">
                    Deleted
                       </div>
                @endif
                @if(!$game->trashed())        
    <div>
        <button class="views"><a href="{{route('admin.games.slug',['slug'=>$game->slug])}}">View </a></button>
    </div>
    @endif
</div>
    
@endforeach
@else
<h2 style="text-align: center; margin-top:10%; ">No results found .. !</h2>
@endif
</div>
</div>
@endsection
@push('scripts')
    
<script  >
    document.addEventListener('DOMContentLoaded', function () {
        const searchButton = document.getElementsByClassName('searchButton')[0];
        const form = document.querySelector('#form');
        
        searchButton.addEventListener('click', function (e) {
            e.preventDefault();
            
            const searchInput = document.getElementById('search');
            const searchValue = searchInput.value;
            form.action = "/admin/games?search="+searchValue;
            
            form.submit();
        });
    });
</script>

@endpush

</body>

</html>