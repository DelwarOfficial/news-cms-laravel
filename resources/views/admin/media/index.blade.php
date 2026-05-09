<!DOCTYPE html>
<html>
<head>
    <title>Media Library - NewsCore</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold">Media Library</h1>
            
            <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-x-3">
                @csrf
                <input type="file" name="file" required class="text-sm">
                <select name="folder_id" class="border px-4 py-2 rounded-xl text-sm">
                    <option value="">Root Folder</option>
                </select>
                <button type="submit" class="bg-black text-white px-6 py-2.5 rounded-2xl text-sm font-semibold">Upload</button>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-6">
            @forelse($media as $item)
                <div class="bg-white border rounded-2xl overflow-hidden group">
                    <div class="h-40 bg-gray-100 flex items-center justify-center relative">
                        @if(str_starts_with($item->file_type, 'image'))
                            <img src="{{ $item->file_url }}" class="max-h-full max-w-full object-contain" alt="">
                        @else
                            <div class="text-center">
                                <i class="fas fa-file text-4xl text-gray-400"></i>
                            </div>
                        @endif
                        
                        <form action="{{ route('admin.media.destroy', $item) }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-xs" onclick="return confirm('Delete?')">×</button>
                        </form>
                    </div>
                    
                    <div class="p-4 text-sm">
                        <div class="font-medium truncate">{{ $item->name }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ number_format($item->file_size / 1024, 1) }} KB</div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 text-gray-500">No media files yet. Upload your first file.</div>
            @endforelse
        </div>
    </div>
</body>
</html>