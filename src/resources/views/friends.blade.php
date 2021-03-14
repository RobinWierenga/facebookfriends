<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Friends</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

    <link href="/css/app.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
    <!-- Styles -->
</head>
<body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0"
        crossorigin="anonymous"></script>

<div class="container">
    <h1>Friends</h1>

    <form method="get" id="shortest_path_form">
        <div class="row">
            <div class="col">
                <input type="text" name="from" value="{{ $from }}" class="form-control" placeholder="Find from user"
                       aria-label="From user">
            </div>
            <div class="col">
                <input type="text" name="to" value="{{ $to }}" class="form-control" placeholder="To friend"
                       aria-label="To friend">
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Go</button>
            </div>
        </div>
    </form>
    @if ($error != '')
        <div class="alert alert-danger" role="alert">
            {{ $error }}
        </div>
    @endif
    @if ($from != '' && count($path) == 0)
        <div class="alert alert-danger" role="alert">
            Could not find a path from {{ $from }} to {{ $to }}
        </div>
    @endif
    @if ($from != '' && count($path) > 0)

        <b>Found path</b>
        <div class="alert alert-success" role="alert">

            @foreach($path as $user)
                {{ $user }}



                @if(!$loop->last)
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right" viewBox="0 0 16 16">
                            <path d="M6 12.796V3.204L11.481 8 6 12.796zm.659.753l5.48-4.796a1 1 0 0 0 0-1.506L6.66 2.451C6.011 1.885 5 2.345 5 3.204v9.592a1 1 0 0 0 1.659.753z"></path>
                        </svg>
                @endif

            @endforeach

        </div>
        <div>
            search took: {{ $duration }} seconds
        </div>
       @endif


    <form method="get" id="show_friends_form">
        <div class="row">
            <div class="col">
                <input name="user_id" type="text" class="form-control" placeholder="Show friends of user" id="user_id"
                       aria-describedby="user_id"
                       value="{{ $user_id }}"></div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Go</button>
            </div>
        </div>

    </form>


    <table class="table">
        <thead>
        <tr>
            <th scope="col">user_id</th>
            <th scope="col">friend_id</th>
            <th scope="col">friend_name</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($friends as $friend)
            <tr>
                <td> {{ $friend->user_id }}</td>
                <td> {{ $friend->friend_id }}</td>
                <td> {{ $friend->friend_name }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="row">
        {{ $friends->links() }}
    </div>
</div>

</div>

</body>
</html>
