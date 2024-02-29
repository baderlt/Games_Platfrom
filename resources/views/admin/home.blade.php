<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{asset('index.css')}}">
    <style>
        * {
            color: white;

            margin: 0;
            box-sizing: border-box;
        }

        #main {
            display: flex;
            flex-direction: row;
            width: 100%;
            min-width: 100%;
            min-height: 100vh;
            color: #990000;
            text-shadow: 1px 1px 1px red;
        }

        #side {
            width: 20%;
            min-width: 15%;
            position: fixed;
            height: 100%;
            position: fixed;
            background-color: #2B2B2B;
            display: flex;
            flex-direction: column;
            justify-items: center;
            align-items: center;



        }

        #right {
            width: 80%;
            height: 100%;
            min-height: 100vh;
            margin-left: 20%;
            padding: 10px;
            background-color: #131313;
        }

        #nav {
            margin-top: 30px;
            display: flex;
            gap: 20px;
            flex-direction: column;
            width: 100%;
            padding:10px

        }

        #nav>button {
            height: 50px;
            background-color: #131313;
            box-shadow: 0 0 6px black;
            border: 0;
            justify-content: center;
            display: flex;
            text-shadow: 1px 1px 1px #FF9999, 2px 2px 1px blue, 4px 4px 1px red;
            border-radius:10px ;
            text-align: center;
            


        }

#nav>button:hover{
    background-color: #990000;
}

        a {
            display: flex;
            text-decoration: none;
            flex-direction: row;
            align-items: center;
            width:100%;
            height: 100%;
            justify-content: center;
         

        
        }
        .active{
            background-color: #990000 !important;
        }

     
    </style>
</head>

<body>
    <div id="main">
        <header id='side'>

            <img style="padding-top: 10px;" src="{{asset('game.png')}}" alt="game_img" width="100" height="100">
            <nav id="nav">

                <button  @class(["btn-nav","active"=>request()->is('admin/admins','admin/admins/*')]) class="{{url()->current() == route('admin.users') ? 'active':''}}"><a  href="{{ route('admin.admins') }}">
                        <img src="{{asset('admin.png')}}" alt="admin" width="30" height="30">
                        &ensp; <p> Admins
                        </p>
                    </a></button>
                <button  @class(["btn-nav","active"=>request()->is('admin/games' ,'admin/games/*') ]) ><a href="{{ route('admin.games') }}">
                        <img src="{{asset('games.png')}}" alt="admin" width="30" height="30">&ensp; <p> Games</p>
                </a></button>
                <button @class(["btn-nav","active"=>request()->is('admin/users','admin/users/*')])   {{request()->is('/users') ? 'active':''}}" ><a href="{{ route('admin.users') }}">
                <img src="{{asset('users.png')}}" alt="admin" width="30" height="30">&ensp; <p> Users</p>    
                </a></button>
<form action="{{route('admin.logout')}}" method="post">
<button type="submit" style="position: absolute; left: 4px;align-items: center;   bottom: 4px;">
<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#990000"  class="bi bi-box-arrow-left" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z"/>
  <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z"/>
</svg>
</button>
</form>
            </nav>
        </header>

        <main id="right">
            @yield('content')
        </main>
    </div>
</body>
@stack("scripts")

</html>