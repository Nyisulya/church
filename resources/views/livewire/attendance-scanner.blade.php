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
                <h3 class="text-lg font-semibold mb-4">📷 {{ __('Scan QR Code') }}</h3>
                <div wire:ignore>
                    <div id="scanner-container" class="relative rounded-lg overflow-hidden border-2 border-gray-200 bg-gray-900" style="height: 300px;">
                        <video id="video" class="w-full h-full object-cover" autoplay muted playsinline></video>
                        <canvas id="canvas" style="display:none;"></canvas>
                        <div id="loading-overlay" class="absolute inset-0 bg-gray-50 flex items-center justify-content-center" style="display:flex;">
                            <div class="text-center w-full">
                                <p class="text-gray-600 text-sm mt-4">⏳ {{ __('Starting camera...') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-3 text-center">{{ __('Position QR code within the camera view') }}</p>
                <div id="scanner-error" style="display:none;" class="mt-2 p-3 bg-red-100 text-red-700 text-sm rounded text-center"></div>
            </div>

            {{-- Result Section --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">✅ {{ __('Scan Result') }}</h3>

                @if($scanMessage)
                    <div class="p-4 rounded-lg mb-4 {{ $scanStatus === 'success' ? 'bg-green-100 text-green-800 border border-green-300' : ($scanStatus === 'warning' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-red-100 text-red-800 border border-red-300') }}">
                        <span class="font-medium">{{ $scanMessage }}</span>
                    </div>
                @else
                    <div class="text-center text-gray-400 py-12">
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
            <h3 class="text-lg font-semibold mb-4">✏️ {{ __('Manual Entry (No QR Code)') }}</h3>
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
                        class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-md transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                        @if(!$eventId || !$selectedMemberId) disabled @endif
                    >
                        ✅ {{ __('Check In') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
(function () {
    var camStream = null;

    function callLivewire(data) {
        var el = document.querySelector('[wire\\:id]');
        if (el && window.Livewire) {
            try { Livewire.find(el.getAttribute('wire:id')).handleScan(data); return; } catch(e) {}
        }
        @this.handleScan(data);
    }

    function boot() {
        var video   = document.getElementById('video');
        var canvas  = document.getElementById('canvas');
        var overlay = document.getElementById('loading-overlay');
        var errBox  = document.getElementById('scanner-error');

        if (!video || !canvas) { setTimeout(boot, 250); return; }
        if (camStream) return; // already running

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            if (overlay) overlay.style.display = 'none';
            if (errBox)  { errBox.textContent = '⚠️ Scanner inahitaji HTTPS. Hakikisha URL inaanza na https://'; errBox.style.display = 'block'; }
            return;
        }

        var lastData = '', lastTime = 0;

        function onCode(raw) {
            var now = Date.now();
            if (raw === lastData && now - lastTime < 3000) return;
            lastData = raw; lastTime = now;
            console.log('QR:', raw);
            callLivewire(raw);
        }

        function tick() {
            if (!camStream) return;
            if (video.readyState === video.HAVE_ENOUGH_DATA && video.videoWidth > 0) {
                canvas.width  = video.videoWidth;
                canvas.height = video.videoHeight;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0);

                if ('BarcodeDetector' in window) {
                    new BarcodeDetector({ formats: ['qr_code'] })
                        .detect(video)
                        .then(function(r) { if (r.length) onCode(r[0].rawValue); })
                        .catch(function(){});
                } else if (typeof jsQR === 'function') {
                    var img  = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    var code = jsQR(img.data, img.width, img.height, { inversionAttempts: 'attemptBoth' });
                    if (code && code.data) onCode(code.data);
                }
            }
            requestAnimationFrame(tick);
        }

        navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 } } })
            .catch(function() { return navigator.mediaDevices.getUserMedia({ video: true }); })
            .then(function(s) {
                camStream = s;
                video.srcObject = s;
                return video.play();
            })
            .then(function() {
                if (overlay) overlay.style.display = 'none';
                requestAnimationFrame(tick);
            })
            .catch(function(e) {
                if (overlay) overlay.style.display = 'none';
                var msg = '⚠️ ';
                if      (e.name === 'NotAllowedError')  msg += 'Ruhusa ya kamera imekataliwa. Bonyeza ikoni ya kufuli kwenye URL na uruhusu kamera, kisha refresh.';
                else if (e.name === 'NotFoundError')    msg += 'Hakuna kamera kwenye kifaa hiki.';
                else if (e.name === 'NotReadableError') msg += 'Kamera inatumiwa na app nyingine. Funga na ujaribu tena.';
                else                                    msg += (e.message || 'Hitilafu ya kamera.');
                if (errBox) { errBox.textContent = msg; errBox.style.display = 'block'; }
                console.error(e);
            });
    }

    // Start
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    window.addEventListener('beforeunload', function() {
        if (camStream) camStream.getTracks().forEach(function(t) { t.stop(); });
    });
})();
</script>
@endpush
