<!DOCTYPE html>
<html>
<head>
    <title>Settings - NewsCore Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold mb-8">Site Settings</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow p-10 border">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Site Name</label>
                        <input type="text" name="site_name" value="{{ old('site_name', 'NewsCore') }}" class="w-full border px-5 py-3.5 rounded-2xl">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold mb-2">Contact Email</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', 'contact@newscore.com') }}" class="w-full border px-5 py-3.5 rounded-2xl">
                    </div>
                </div>

                <div class="mt-8">
                    <label class="block text-sm font-semibold mb-2">Site Description</label>
                    <textarea name="site_description" rows="3" class="w-full border px-5 py-3.5 rounded-2xl">Professional News Content Management System</textarea>
                </div>

                <div class="mt-10">
                    <button type="submit" class="bg-black text-white px-10 py-4 rounded-2xl font-bold">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>