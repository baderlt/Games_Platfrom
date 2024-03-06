<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>users</title>



    <style>
        .modal {
            width: 100vw;
            height: 100vh;
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(4px);
            display: flex;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .from-modal {
            margin-left: 30%;
            margin-top: 5%;
            height: 200px;
            background-color: #131313;
            border: 0;
            border-radius: 9px;
            box-shadow: 2px 2px 12px red;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .from-modal>* {
            margin-top: 20px;
        }

        .hidden {
            display: none;
        }

        .blockUser {
            border: 0;
            border-radius: 4px;
            height: 50px;

        }
    </style>
</head>

<body>

    @extends('admin.home')

    @section('content')
    <div class="container">
        <h2 style="text-align: center; margin: 10px">Users List</h2>
        <form action="" method="get" id="formsearch">
            <div style="text-align:rightz ; margin: 10px" class="searchBox">

                <input class="searchInput" type="text" id="search" name="search" placeholder="Search">
                <button class="searchButton">



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
            </div>

        </form>


        <table class="table">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Name</th>
                    <th>Last Conexion </th>
                    <th>Join at </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="{{$user->blocked ==1 ? 'blocked':''}} Profile">
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->lastConextion }}</td>
                    <td>{{ $user->created_at }}</td>

                    <td id="actions">
                        @if ($user->blocked== 0 )


                        <button class="actions-btn blockBtn" data-user="{{$user->id}}" style="background-color: #990000 ;   box-shadow: 4px 4px 8px red;" id='Unblock'>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="red" class="bi bi-ban" viewBox="0 0 16 16">
                                <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0" />
                            </svg> Block</button>

                        @else
                        <form method="POST" id="form" action="{{route('admin.BlockUser',['user_id'=>$user->id])}}">
                            @csrf
                            <button type="submit" id="Unblock">Unblock</button>
                        </form>
                        @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td style="text-align: center;" colspan="5">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $users->links('pagination::bootstrap-5') }} <!-- Pagination links -->
    </div>

    <div class="modal hidden ">

        <div class="from-modal">
            <h1>Block user </h1>
            <form style="display: block;" method="POST" class="bodyModal" id="form1">
                @csrf
                <select style="height: 50px;  background-color: #2B2B2B;" required name="reason">
                    <option value=""> Chose raison </option>
                    <option value="0">You have been blocked by an administrator</option>
                    <option value="1">You have been blocked for spamming</option>
                    <option value="2">You have been blocked for cheating</option>
                </select>
                <button type="submit" class="blockUser" style="background-color: #990000;">Block</button>
            </form>
        </div>
    </div>
    @push("scripts")
    <script type="module">
        const modal = document.querySelector('.modal')


        document.querySelectorAll('.Profile').forEach((item) => {
            item.addEventListener('click', test);
        })

        function test() {
            console.log('ddd');
        }
        document.getElementsByClassName('from-modal')[0].addEventListener('click', (e) => {
            e.stopPropagation();
        })
        /////for close modal
        modal.addEventListener('click', (e) => {
            modal.classList.add('hidden');
        })
        ////// for block or deblock user 
        const blocs = document.querySelectorAll('.blockBtn')
        blocs.forEach(element => {
            element.addEventListener('click', (e) => {
                e.stopPropagation();
                const form1 = document.getElementById('form1')
                modal.classList.remove('hidden')
                form1.setAttribute("action", "/admin/users/block/" + e.target.dataset.user)

            })
        });
        ///////////////////////////// for search
        const searchButton = document.getElementsByClassName('searchButton')[0];
        const formSerach = document.querySelector('#formsearch');

        searchButton.addEventListener('click', function(e) {
            e.preventDefault();
            const searchInput = document.getElementById('search');
            const searchValue = searchInput.value;
            // if (searchValue=="") {
            //         return false;
            //     }
            formSerach.action = "/admin/users?search=" + searchValue;

            formSerach.submit();
        });
        // });
    </script>
    @endpush

    @endsection
</body>

</html>