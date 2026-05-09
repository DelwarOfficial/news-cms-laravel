<!DOCTYPE html>
<html>
<head>
    <title>NewsCore Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold">Dashboard</h1>
            <div class="flex items-center gap-x-4">
                <span class="text-sm text-gray-500">Welcome back, Admin</span>
                <a href="{{ route('admin.posts.create') }}" class="bg-black text-white px-5 py-2.5 rounded-2xl text-sm font-semibold flex items-center gap-x-2">
                    <i class="fas fa-plus"></i>
                    <span>New Post</span>
                </a>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-3xl shadow flex items-center gap-x-4">
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-newspaper text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Total Posts</div>
                    <div class="text-4xl font-bold">{{ $stats['total_posts'] }}</div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-3xl shadow flex items-center gap-x-4">
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Published</div>
                    <div class="text-4xl font-bold text-green-600">{{ $stats['published_posts'] }}</div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-3xl shadow flex items-center gap-x-4">
                <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Pending</div>
                    <div class="text-4xl font-bold text-yellow-600">{{ $stats['pending_posts'] }}</div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-3xl shadow flex items-center gap-x-4">
                <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Total Users</div>
                    <div class="text-4xl font-bold">{{ $stats['total_users'] }}</div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Posts -->
            <div class="bg-white rounded-3xl shadow border">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h3 class="font-semibold text-lg">Recent Posts</h3>
                    <a href="{{ route('admin.posts.index') }}" class="text-sm text-indigo-600 hover:underline">View all →</a>
                </div>
                <div class="divide-y">
                    @forelse($recentPosts as $post)
                    <div class="px-8 py-4 flex justify-between items-center hover:bg-gray-50">
                        <div class="font-medium">{{ Str::limit($post->title, 50) }}</div>
                        <div class="text-sm text-gray-500">{{ $post->author->name ?? 'N/A' }}</div>
                    </div>
                    @empty
                    <div class="px-8 py-12 text-center text-gray-500">No posts yet.</div>
                    @endforelse
                </div>
            </div>
            
            <!-- Popular Posts -->
            <div class="bg-white rounded-3xl shadow border">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h3 class="font-semibold text-lg">Popular Posts</h3>
                    <span class="text-sm text-gray-500">By views</span>
                </div>
                <div class="divide-y">
                    @forelse($popularPosts as $post)
                    <div class="px-8 py-4 flex justify-between items-center hover:bg-gray-50">
                        <div class="font-medium">{{ Str::limit($post->title, 50) }}</div>
                        <div class="text-sm text-gray-500">{{ number_format($post->view_count) }} views</div>
                    </div>
                    @empty
                    <div class="px-8 py-12 text-center text-gray-500">No popular posts yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>
</html>