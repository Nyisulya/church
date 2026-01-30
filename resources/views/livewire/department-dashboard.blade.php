<div>
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $department->name }} {{ __('Dashboard') }}</h1>
                <p class="text-sm text-gray-500">{{ $department->description }}</p>
            </div>
            <div class="text-sm text-gray-500">
                {{ __('Total Members') }}: {{ $department->members()->count() }}
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-gray-50 border-b border-gray-200">
            <nav class="-mb-px flex">
                <button wire:click="setTab('overview')" class="px-6 py-3 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ __('Overview') }}
                </button>
                <button wire:click="setTab('members')" class="px-6 py-3 border-b-2 font-medium text-sm {{ $activeTab === 'members' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ __('Members') }}
                </button>
                <button wire:click="setTab('announcements')" class="px-6 py-3 border-b-2 font-medium text-sm {{ $activeTab === 'announcements' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ __('Announcements') }}
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Overview Tab -->
            @if($activeTab === 'overview')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800">{{ __('Recent Announcements') }}</h3>
                        <ul class="mt-2 space-y-2">
                            @forelse($announcements->take(3) as $announcement)
                                <li class="text-sm text-blue-600">
                                    <span class="font-bold">{{ $announcement->title }}</span> - {{ $announcement->created_at->diffForHumans() }}
                                </li>
                            @empty
                                <li class="text-sm text-gray-500">{{ __('No recent announcements.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                    <!-- Add more stats widgets here -->
                </div>
            @endif

            <!-- Members Tab -->
            @if($activeTab === 'members')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Role') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Joined') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($members as $member)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">{{ $member->full_name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $member->pivot->role ?? __('Member') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $member->pivot->created_at ? $member->pivot->created_at->format('M d, Y') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $members->links() }}
                    </div>
                </div>
            @endif

            <!-- Announcements Tab -->
            @if($activeTab === 'announcements')
                <div>
                    <div class="mb-6 bg-gray-50 p-4 rounded">
                        <h3 class="text-lg font-semibold mb-2">{{ __('Post New Announcement') }}</h3>
                        @if (session()->has('message'))
                            <div class="bg-green-100 text-green-800 p-2 rounded mb-2">
                                {{ session('message') }}
                            </div>
                        @endif
                        <form wire:submit.prevent="postAnnouncement">
                            <div class="mb-2">
                                <input type="text" wire:model="newAnnouncementTitle" placeholder="{{ __('Title') }}" class="w-full rounded border-gray-300 shadow-sm">
                                @error('newAnnouncementTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-2">
                                <textarea wire:model="newAnnouncementBody" placeholder="{{ __('Message body...') }}" class="w-full rounded border-gray-300 shadow-sm" rows="3"></textarea>
                                @error('newAnnouncementBody') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">{{ __('Post Announcement') }}</button>
                        </form>
                    </div>

                    <div class="space-y-4">
                        @foreach($announcements as $announcement)
                            <div class="border rounded p-4">
                                <div class="flex justify-between items-start">
                                    <h4 class="text-lg font-bold">{{ $announcement->title }}</h4>
                                    <span class="text-xs text-gray-500">{{ $announcement->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <p class="text-gray-700 mt-2">{{ $announcement->body }}</p>
                                <div class="mt-2 text-xs text-gray-500">{{ __('Posted by') }}: {{ $announcement->author->name }}</div>
                            </div>
                        @endforeach
                        <div class="mt-4">
                            {{ $announcements->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
