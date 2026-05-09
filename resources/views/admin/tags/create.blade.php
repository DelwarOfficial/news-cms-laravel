<!DOCTYPE html>
<html>
<head>
    <title>New Tag - NewsCore</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-lg mx-auto bg-white p-10 rounded-3xl shadow">
        <h1 class="text-3xl font-bold mb-8">Create New Tag</h1>
        
        <form action="{{ route('admin.tags.store') }}" method="POST">
            @csrf
            <div class="mb-8">
                <label class="block text-sm font-semibold mb-2">Tag Name</label>
                <input type="text" name="name" class="w-full border px-5 py-3.5 rounded-2xl" required placeholder="e.g. Breaking News">
            </div>
            
            <button type="submit" class="w-full bg-black text-white py-4 rounded-2xl font-bold">CREATE TAG</button>
        </form>
    </div>
</body>
</html>