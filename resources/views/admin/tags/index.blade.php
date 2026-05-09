<!DOCTYPE html>
<html>
<head>
    <title>Tags - NewsCore Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold">Tags</h1>
            <a href="{{ route('admin.tags.create') }}" class="bg-black text-white px-6 py-3 rounded-2xl">+ New Tag</a>
        </div>

        <div class="bg-white rounded-3xl shadow overflow-hidden border">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-8 py-5 text-left">Name</th>
                        <th class="px-8 py-5 text-left">Slug</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($tags as $tag)
                    <tr>
                        <td class="px-8 py-5 font-medium">{{ $tag->name }}</td>
                        <td class="px-8 py-5 text-gray-500">{{ $tag->slug }}</td>
                        <td class="px-8 py-5 text-right">
                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline" onclick="return confirm('Delete tag?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-8 py-12 text-center text-gray-500">No tags yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>