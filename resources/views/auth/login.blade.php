<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <link rel="icon" type="image/x-icon" href="https://www.google.com/url?sa=i&url=https%3A%2F%2Fpixabay.com%2Fimages%2Fsearch%2Flogo%2F&psig=AOvVaw1YUjYe-iRjfurbcTS_INJQ&ust=1718302260823000&source=images&cd=vfe&opi=89978449&ved=0CBIQjRxqFwoTCIjAx_DU1oYDFQAAAAAdAAAAABAE">
</head>
<body>
    <h1>Login</h1>
    @if (session('error'))
        <div>
            {{ session('error') }}
            @if (session('attempts_left') !== null)
                <br>
                Attempts left: {{ session('attempts_left') }}
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <label for="identifier">Email or Name:</label>
            <input type="text" name="identifier" id="identifier" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
</body>
</html>
