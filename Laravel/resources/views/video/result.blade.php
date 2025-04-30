<x-layout>
    <h1>Prediction Result</h1>

    <p>Video uploaded successfully. Here is the result of the AI prediction:</p>
    <p><strong>Result:</strong> {{ $result_prediction ?? 'N/A' }}</p>
    <p><strong>Class:</strong> {{ $class_prediction ?? 'N/A' }}</p>
    <p><strong>Severity:</strong> {{ $severity ?? 'None' }}</p>
    <p><strong>Face Analysis:</strong>
        @if ($face_analysis)
            Eye Score: {{ $face_analysis['eye_score'] ?? 'N/A' }},
            Emotion Score: {{ $face_analysis['emotion_score'] ?? 'N/A' }}
        @else
            N/A
        @endif
    </p>
    <p><strong>Movement Analysis:</strong>
        @if ($movement_analysis)
            Repetitive Score: {{ $movement_analysis['repetitive_score'] ?? 'N/A' }}
        @else
            N/A
        @endif
    </p>
    <p><strong>Combined Score:</strong> {{ $combined_score ?? 'N/A' }}</p>
</x-layout>