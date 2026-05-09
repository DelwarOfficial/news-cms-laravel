<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto bg-white p-10 rounded-3xl">
        <h1 class="text-3xl font-bold mb-8">Create New Post</h1>
        
        <form action="{{ route('admin.posts.store') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block font-semibold mb-2">Title</label>
                <input type="text" name="title" class="w-full border px-5 py-3 rounded-2xl" required>
            </div>
            
            <div class="mb-6">
                <label class="block font-semibold mb-2">Content</label>
                <textarea name="content" rows="12" class="w-full border px-5 py-3 rounded-2xl" required></textarea>
            </div>
            
            <div class="mb-8">
                <label class="block font-semibold mb-2">Status</label>
                <select name="status" class="w-full border px-5 py-3 rounded-2xl">
                    <option value="draft">Draft</option>
                    <option value="pending">Pending Review</option>
                    <option value="published">Published</option>
                </select>
            </div>
            
            <button type="submit" class="bg-black text-white px-10 py-4 rounded-2xl font-bold">Publish Post</button>
        </form>
    </div>
</body>
</html>