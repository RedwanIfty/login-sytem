<!DOCTYPE html>
<html>
<head>
    <title>Home Page 2</title>
</head>
<body>
    <h1>Welcome to Home Page 2</h1>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <a href="{{ route('logout') }}"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        Logout
    </a>
</body>
</html>
