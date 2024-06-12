<!DOCTYPE html>
<html>
<head>
    <title>Home Page 1</title>
</head>
<body>
    <h1 style="border-bottom: 10px solid; text-align:center">Welcome {{auth()->user()->name}} to Home Page 1</h1>

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
