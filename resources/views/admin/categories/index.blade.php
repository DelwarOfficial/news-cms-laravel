<!DOCTYPE html>
<html>
<head>
    <title>Categories - NewsCore Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold">Categories</h1>
            <a href="{{ route('admin.categories.create') }}" class="bg-black text-white px-6 py-3 rounded-2xl">+ New Category</a>
        </div>

        <div class="bg-white rounded-3xl shadow overflow-hidden border">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-8 py-5 text-left">Name</th>
                        <th class="px-8 py-5 text-left">Slug</th>
                        <th class="px-8 py-5 text-left">Parent</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($categories as $category)
                    <tr>
                        <td class="px-8 py-5 font-medium">{{ $category->name }}</td>
                        <td class="px-8 py-5 text-gray-500">{{ $category->slug }}</td>
                        <td class="px-8 py-5 text-gray-500">{{ $category->parent ? $category->parent->name : '—' }}</td>
                        <td class="px-8 py-5 text-right space-x-3">
                            <a href="#" class="text-indigo-600 hover:underline">Edit</a>
                            <a href="#" class="text-red-600 hover:underline">Delete</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-8 py-12 text-center text-gray-500">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>