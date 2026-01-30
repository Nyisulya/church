<div>
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">📱 {{ __('QR Code Attendance Scanner') }}</h1>

        {{-- Event Selection --}}
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Select Event') }} *</label>
            <select wire:model.live="eventId" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="">-- {{ __('Select an Event') }} --</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }} ({{ $event->date->format('M d, Y h:i A') }})</option>
                @endforeach
            </select>
            @if(!$eventId)
                <p class="text-sm text-gray-500 mt-2">⚠️ {{ __('Please select an event to start scanning') }}</p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Scanner Section --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    </svg>
                    {{ __('Scan QR Code') }}
                </h3>
                <div wire:ignore>
                    <div id="scanner-container" class="relative rounded-lg overflow-hidden border-2 border-gray-200 bg-gray-900" style="height: 300px;">
                        <video id="video" class="w-full h-full object-cover" playsinline></video>
                        <canvas id="canvas" class="hidden"></canvas>
                        <div id="loading-overlay" class="absolute inset-0 bg-gray-50 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                </svg>
                                <p class="text-gray-600 text-sm">{{ __('Starting camera...') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-3 text-center">{{ __('Position QR code within the camera view') }}</p>
                <div id="scanner-error" class="hidden mt-2 p-2 bg-red-100 text-red-700 text-sm rounded text-center"></div>
            </div>

            {{-- Result Section --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    </svg>
                    {{ __('Scan Result') }}
                </h3>
                
                @if($scanMessage)
                    <div class="p-4 rounded-lg mb-4 animate-pulse {{ $scanStatus === 'success' ? 'bg-green-100 text-green-800 border border-green-300' : ($scanStatus === 'warning' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-red-100 text-red-800 border border-red-300') }}">
                        <div class="flex items-center">
                            @if($scanStatus === 'success')
                                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($scanStatus === 'warning')
                                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                            <span class="font-medium">{{ $scanMessage }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center text-gray-400 py-12">
                        <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        <p class="text-sm">{{ __('Waiting for QR code scan...') }}</p>
                    </div>
                @endif

                @if($lastScannedMember)
                    <div class="text-center border-t pt-4">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 mx-auto mb-3 flex items-center justify-center">
                            <span class="text-4xl text-white font-bold">{{ substr($lastScannedMember->full_name, 0, 1) }}</span>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900">{{ $lastScannedMember->full_name }}</h4>
                        <p class="text-gray-600 text-sm">{{ $lastScannedMember->member_number ?? __('No member number') }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ $lastScannedMember->email ?? '' }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Manual Entry Section --}}
        <div class="bg-white shadow rounded-lg p-6 mt-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                </svg>
                {{ __('Manual Entry (No QR Code)') }}
            </h3>
            <p class="text-sm text-gray-600 mb-4">{{ __('Use this form to manually check in members who don\'t have a QR code.') }}</p>
            
            <div class="flex gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Select Member') }}</label>
                    <select wire:model="selectedMemberId" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- {{ __('Select a Member') }} --</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->member_number ?? __('No number') }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button 
                        wire:click="manualEntry" 
                        class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-md transition duration-150 ease-in-out flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                        @if(!$eventId || !$selectedMemberId) disabled @endif
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('Check In') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const loadingOverlay = document.getElementById('loading-overlay');
        const errorEl = document.getElementById('scanner-error');
        const canvasContext = canvas.getContext('2d');
        let scanning = false;
        let lastScan = '';
        let lastScanTime = 0;

        async function startCamera() {
            try {
                console.log('Requesting camera access...');
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment' } 
                });
                
                video.srcObject = stream;
                video.setAttribute('playsinline', true);
                await video.play();
                
                console.log('Camera started successfully');
                loadingOverlay.classList.add('hidden');
                scanning = true;
                requestAnimationFrame(scan);
                
            } catch (err) {
                console.error('Camera error:', err);
                loadingOverlay.classList.add('hidden');
                
                let msg = 'Failed to access camera. ';
                if (err.name === 'NotAllowedError') {
                    msg += 'Please grant camera permission and refresh the page.';
                } else if (err.name === 'NotFoundError') {
                    msg += 'No camera found on this device.';
                } else if (err.name === 'NotReadableError') {
                    msg += 'Camera is in use by another application.';
                } else {
                    msg += err.message || 'Unknown error';
                }
                
                errorEl.textContent = msg;
                errorEl.classList.remove('hidden');
            }
        }

        function scan() {
            if (!scanning || video.readyState !== video.HAVE_ENOUGH_DATA) {
                requestAnimationFrame(scan);
                return;
            }

            canvas.height = video.videoHeight;
            canvas.width = video.videoWidth;
            canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "dontInvert",
            });

            if (code && code.data) {
                const now = Date.now();
                // Prevent duplicate scans within 2 seconds
                if (code.data !== lastScan || (now - lastScanTime) > 2000) {
                    console.log('QR Code detected:', code.data);
                    lastScan = code.data;
                    lastScanTime = now;
                    @this.handleScan(code.data);
                }
            }

            requestAnimationFrame(scan);
        }

        // Start camera
        startCamera();

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            scanning = false;
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
        });
    });
</script>
@endpush
