<!DOCTYPE html>
<html>
<head>
    <title>Comments - NewsCore</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-4xl font-bold mb-8">Comments Moderation</h1>
        
        <div class="bg-white rounded-3xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-8 py-5 text-left">Comment</th>
                        <th class="px-8 py-5 text-left">Post</th>
                        <th class="px-8 py-5 text-left">Status</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($comments as $comment)
                    <tr>
                        <td class="px-8 py-5">{{ Str::limit($comment->content, 80) }}</td>
                        <td class="px-8 py-5 text-gray-600">{{ $comment->post->title ?? 'N/A' }}</td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 text-xs rounded-full 
                                {{ $comment->status == 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($comment->status) }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            @if($comment->status != 'approved')
                                <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-green-600 hover:underline">Approve</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-8 py-12 text-center text-gray-500">No comments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>