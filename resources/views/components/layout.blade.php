<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <div class="collapse navbar-collapse d-flex w-100 justify-content-between">
                <div class="navbar-nav">
                    <a class="nav-link active" aria-current="page" href="/">Home</a>
                    <a class="nav-link" href="{{ route('profile.show', ['user' => auth()->id()]) }}">Profile</a>
                    <a class="nav-link" href="/activities">Activities</a>
                    <a class="nav-link" href="{{ route('dashboard')}}">Dashboard</a>
                    <a class="nav-link" href="/about">About</a>
                    <a class="nav-link" href="/contact">Contact</a>
                </div>
                <div class="d-flex gap-2">
                    @guest
                        <a class="btn btn-primary" href="/login">Login</a>
                        <a class="btn btn-secondary" href="/register">Register</a>
                    @endguest
                    @auth
                        <form method="POST" action="/logout">
                            @csrf
                            <button class="btn btn-primary">logout</button>
                        </form>
                        {{-- <a class="btn btn-primary" href="/logout">Logout</a> --}}
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    {{$slot}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
