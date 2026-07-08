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
                        <div id="reader" style="width: 100%; height: 100%;"></div>
                        <div id="loading-overlay" class="absolute inset-0 bg-gray-50 flex items-center justify-content-center" style="display:flex; z-index: 10; pointer-events: none;">
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
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
(function () {
    var html5QrCode = null;
    var started = false;

    function playBeep() {
        try {
            var ctx = new (window.AudioContext || window.webkitAudioContext)();
            var osc = ctx.createOscillator();
            var gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.value = 900;
            gain.gain.setValueAtTime(0, ctx.currentTime);
            gain.gain.linearRampToValueAtTime(0.15, ctx.currentTime + 0.05);
            gain.gain.linearRampToValueAtTime(0, ctx.currentTime + 0.15);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.15);
        } catch (e) {}
    }

    function flashGreen() {
        var container = document.getElementById('scanner-container');
        if (container) {
            var originalBorder = container.style.borderColor;
            container.style.borderColor = '#10b981';
            container.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.6)';
            setTimeout(function() {
                container.style.borderColor = originalBorder;
                container.style.boxShadow = 'none';
            }, 500);
        }
    }

    function callLivewire(data) {
        if (window.Livewire) {
            Livewire.dispatch('qr-scanned', { qrContent: data });
        }
    }

    function boot() {
        var readerEl = document.getElementById('reader');
        var overlay  = document.getElementById('loading-overlay');
        var errBox   = document.getElementById('scanner-error');

        if (!readerEl) { setTimeout(boot, 250); return; }
        if (started) return;

        started = true;
        var lastData = '', lastTime = 0;

        function onCode(raw) {
            var now = Date.now();
            if (raw === lastData && now - lastTime < 3000) return;
            lastData = raw; lastTime = now;
            console.log('QR scanned:', raw);
            playBeep();
            flashGreen();
            callLivewire(raw);
        }

        // Initialize html5-qrcode engine
        html5QrCode = new Html5Qrcode("reader");

        var config = {
            fps: 20,
            qrbox: function(width, height) {
                var size = Math.min(width, height) * 0.75;
                return { width: size, height: size };
            }
        };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            function(decodedText, decodedResult) {
                onCode(decodedText);
            },
            function(errorMessage) {
                // Verbose parse errors, safely ignore
            }
        )
        .then(function() {
            if (overlay) overlay.style.display = 'none';
            var videoElement = readerEl.querySelector('video');
            if (videoElement) {
                videoElement.style.width = '100%';
                videoElement.style.height = '100%';
                videoElement.style.objectFit = 'cover';
            }
        })
        .catch(function(err) {
            if (overlay) overlay.style.display = 'none';
            var msg = '⚠️ ';
            if (err.indexOf && err.indexOf('NotAllowedError') !== -1) {
                msg += 'Ruhusa ya kamera imekataliwa. Ruhusu kamera kwenye mipangilio ya kivinjari chako.';
            } else {
                msg += 'Hitilafu ya kamera: ' + err;
            }
            if (errBox) {
                errBox.textContent = msg;
                errBox.style.display = 'block';
            }
            console.error(err);
        });
    }

    // Start
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    // Livewire cleanup and restart on navigate
    document.addEventListener('livewire:navigated', function() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().catch(function(e){});
        }
        started = false;
        setTimeout(boot, 300);
    });

    window.addEventListener('beforeunload', function() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().catch(function(e){});
        }
    });
})();
</script>
@endpush
