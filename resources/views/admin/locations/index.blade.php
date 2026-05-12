@extends('admin.layouts.app')

@section('title', 'Locations')
@section('page-title', 'Locations')

@section('content')
    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
            <div class="font-semibold">Please fix the highlighted fields.</div>
            <ul class="mt-2 list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <form method="POST" action="{{ route('admin.locations.divisions.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf
            <h2 class="font-bold text-gray-900">New Division</h2>
            <input name="name" placeholder="Name" class="w-full rounded-xl border-gray-200 text-sm" required>
            <input name="slug" placeholder="Slug (optional)" class="w-full rounded-xl border-gray-200 text-sm">
            <input name="name_bangla" placeholder="Bangla name" class="w-full rounded-xl border-gray-200 text-sm">
            <input name="code" placeholder="Code" class="w-full rounded-xl border-gray-200 text-sm">
            <button class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                <i class="fas fa-plus"></i> Add Division
            </button>
        </form>

        <form method="POST" action="{{ route('admin.locations.districts.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf
            <h2 class="font-bold text-gray-900">New District</h2>
            <select name="division_id" class="w-full rounded-xl border-gray-200 text-sm" required>
                <option value="">Select division</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                @endforeach
            </select>
            <input name="name" placeholder="Name" class="w-full rounded-xl border-gray-200 text-sm" required>
            <input name="slug" placeholder="Slug (optional)" class="w-full rounded-xl border-gray-200 text-sm">
            <input name="name_bangla" placeholder="Bangla name" class="w-full rounded-xl border-gray-200 text-sm">
            <input name="code" placeholder="Code" class="w-full rounded-xl border-gray-200 text-sm">
            <button class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                <i class="fas fa-plus"></i> Add District
            </button>
        </form>

        <form method="POST" action="{{ route('admin.locations.upazilas.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf
            <h2 class="font-bold text-gray-900">New Upazila</h2>
            <select name="division_id" class="w-full rounded-xl border-gray-200 text-sm" required>
                <option value="">Select division</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                @endforeach
            </select>
            <select name="district_id" class="w-full rounded-xl border-gray-200 text-sm" required>
                <option value="">Select district</option>
                @foreach($districts as $district)
                    <option value="{{ $district->id }}">{{ $district->name }} - {{ $district->division?->name }}</option>
                @endforeach
            </select>
            <input name="name" placeholder="Name" class="w-full rounded-xl border-gray-200 text-sm" required>
            <input name="slug" placeholder="Slug (optional)" class="w-full rounded-xl border-gray-200 text-sm">
            <input name="name_bangla" placeholder="Bangla name" class="w-full rounded-xl border-gray-200 text-sm">
            <input name="code" placeholder="Code" class="w-full rounded-xl border-gray-200 text-sm">
            <button class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                <i class="fas fa-plus"></i> Add Upazila
            </button>
        </form>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="font-bold text-gray-900">Divisions</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($divisions as $division)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $division->name }}</div>
                            <div class="text-xs text-gray-500">{{ $division->name_bangla }} {{ $division->slug }}</div>
                        </div>
                        <form method="POST" action="{{ route('admin.locations.divisions.destroy', $division) }}" onsubmit="return confirm('Delete this division?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50"><i class="fas fa-trash text-xs"></i></button>
                        </form>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-gray-400">No divisions yet.</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="font-bold text-gray-900">Districts</h2>
            </div>
            <div class="divide-y divide-gray-50 max-h-[620px] overflow-y-auto">
                @forelse($districts as $district)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $district->name }}</div>
                            <div class="text-xs text-gray-500">{{ $district->division?->name }} · {{ $district->slug }}</div>
                        </div>
                        <form method="POST" action="{{ route('admin.locations.districts.destroy', $district) }}" onsubmit="return confirm('Delete this district?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50"><i class="fas fa-trash text-xs"></i></button>
                        </form>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-gray-400">No districts yet.</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="font-bold text-gray-900">Upazilas</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Name</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">District</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Division</th>
                        <th class="text-right px-6 py-3.5 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($upazilas as $upazila)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $upazila->name }}</div>
                                <div class="text-xs text-gray-500">{{ $upazila->name_bangla }} {{ $upazila->slug }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $upazila->district?->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $upazila->division?->name }}</td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.locations.upazilas.destroy', $upazila) }}" class="flex justify-end" onsubmit="return confirm('Delete this upazila?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50"><i class="fas fa-trash text-xs"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">No upazilas yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="border-t border-gray-100 px-6 py-4">
                {{ $upazilas->links() }}
            </div>
        </div>
    </div>
@endsection
