<!-- resources/views/activities/index.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activities</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .activity-card {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .activity-card-item {
            width: 45%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .activity-card-item:hover {
            transform: scale(1.05);
        }

        .activity-card-item h3 {
            margin: 0;
            font-size: 1.5em;
            color: #333;
        }

        .activity-card-item p {
            color: #666;
            line-height: 1.5;
        }

        .activity-card-item .category {
            color: #007bff;
            font-weight: bold;
        }

        .activity-card-item .image {
            margin-top: 15px;
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .empty-state {
            text-align: center;
            color: #777;
        }

        /* Button styling */
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Activities</h1>
     
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

        @if ($activities->isEmpty())
        <div class="empty-state">
            <p>No activities found.</p>
        </div>
    @else
        <div class="activity-card">
            @foreach ($activities as $activity)
                <div class="activity-card-item">
                    <h3><a href="{{ route('activities.show', $activity->id) }}">{{ $activity->name }}</a></h3>
                    <p class="category">{{ $activity->category }}</p>
                    <p>{{ Str::limit($activity->description, 100) }}</p>
                    <p>{{ Str::limit($activity->content, 50) }}</p>

                    @if ($activity->photo)
                        <img src="{{ asset('storage/' . $activity->photo) }}" alt="{{ $activity->name }}" class="image">
                    @endif
                </div>
            @endforeach
        </div>
    @endif
    @auth
    @if(Auth::user()->role == 1)
        <a href="{{ route('activities.create') }}" class="btn btn-success mb-3">+ Add Activity</a>
    @endif
    @endauth
    
    </div>

</body>
</html>
