<x-layout>
    <div class="container">
        <h1>{{ $category->name }}</h1>

        <p><strong>Description:</strong> {{ $category->description }}</p>
        <p><strong>Content:</strong> {{ $category->content }}</p>

        @if ($category->photo)
            <img src="{{ asset('storage/' . $category->photo) }}" alt="{{ $category->name }}"
                 class="img-fluid rounded mb-4" style="max-height: 300px; width: auto;">
        @endif

        <!-- Activities Section -->
        <div class="mt-5">
            <h3>Related Activities</h3>

            @if($activities->count() > 0)
                <div class="row">
                    @foreach($activities as $activity)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                @if($activity->image)
                                    <img src="{{ asset('storage/' . $activity->image) }}"
                                         class="card-img-top img-cover"
                                         alt="{{ $activity->name }}"
                                         style="height: 200px; object-fit: cover;">
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $activity->name }}</h5>
                                    <p class="card-text flex-grow-1 text-muted">
                                        {{ Str::limit($activity->description, 100) }}
                                    </p>
                                    <div class="mt-auto">
                                        <a href="{{ route('activities.show', $activity->id) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                
            @else
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-info-circle me-2"></i>
                    No activities available in this category
                </div>
            @endif
        </div>

        <div class="mt-4 d-flex gap-2 flex-wrap">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Categories
            </a>
        </div>
    </div>

    @push('styles')
        <style>
            .img-cover {
                object-fit: cover;
                width: 100%;
            }
            .card {
                transition: transform 0.3s ease;
            }
            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            }
        </style>
    @endpush
</x-layout>
