<!DOCTYPE html>
<html>
<head>
    <title>Posts - NewsCore Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between mb-8">
            <h1 class="text-4xl font-bold">Posts</h1>
            <a href="{{ route('admin.posts.create') }}" class="bg-black text-white px-6 py-3 rounded-2xl">+ New Post</a>
        </div>

        <div class="bg-white rounded-3xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-8 py-5 text-left">Title</th>
                        <th class="px-8 py-5 text-left">Author</th>
                        <th class="px-8 py-5 text-left">Status</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($posts as $post)
                    <tr>
                        <td class="px-8 py-5 font-medium">{{ $post->title }}</td>
                        <td class="px-8 py-5 text-gray-600">{{ $post->author->name ?? 'N/A' }}</td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 text-xs rounded-full 
                                {{ $post->status == 'published' ? 'bg-green-100 text-green-700' : 
                                   ($post->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ ucfirst($post->status) }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right space-x-4">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-indigo-600">Edit</a>
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-600" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>