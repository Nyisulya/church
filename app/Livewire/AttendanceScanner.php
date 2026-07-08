<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Member;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.admin')]
class AttendanceScanner extends Component
{
    public $eventId;
    public $lastScannedMember;
    public $scanStatus; // success, error, warning
    public $scanMessage;
    public $selectedMemberId; // for manual entry

    public function mount()
    {
        // Default to the next upcoming event or today's event
        $this->eventId = Event::whereDate('date', '>=', Carbon::today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->value('id');
    }

    public function handleScan($qrContent)
    {
        if (!$this->eventId) {
            $this->scanStatus = 'error';
            $this->scanMessage = 'Please select an event first.';
            return;
        }

        $member = null;

        // Try to parse as JSON first (legacy QR support)
        $data = json_decode($qrContent, true);
        if ($data && isset($data['id'])) {
            $member = Member::find($data['id']);
        } else {
            // Fallback: Find by member number directly (Digital ID Card QR code)
            $member = Member::where('member_number', trim($qrContent))->first();
        }

        if (!$member) {
            $this->scanStatus = 'error';
            $this->scanMessage = 'Mwanachama hakupatikana. QR code content: ' . $qrContent;
            return;
        }

        $this->recordAttendance($member);
    }

    public function manualEntry()
    {
        if (!$this->eventId) {
            $this->scanStatus = 'error';
            $this->scanMessage = 'Please select an event first.';
            return;
        }

        if (!$this->selectedMemberId) {
            $this->scanStatus = 'error';
            $this->scanMessage = 'Please select a member.';
            return;
        }

        $member = Member::find($this->selectedMemberId);
        if (!$member) {
            $this->scanStatus = 'error';
            $this->scanMessage = 'Member not found.';
            return;
        }

        $this->recordAttendance($member);
        $this->selectedMemberId = null; // Reset selection
    }

    private function recordAttendance($member)
    {
        // Check for existing attendance
        $attendance = Attendance::where('event_id', $this->eventId)
            ->where('member_id', $member->id)
            ->first();

        if ($attendance) {
            if ($attendance->status === 'registered') {
                // Update registered member to present
                $attendance->update([
                    'status' => 'present',
                    'scanned_by' => auth()->id(),
                    'scanned_at' => now(),
                ]);
                
                $this->scanStatus = 'success';
                $this->scanMessage = "Checked in registered member: {$member->full_name}";
                $this->lastScannedMember = $member;
                return;
            } elseif ($attendance->status === 'present' || $attendance->status === 'late') {
                $this->scanStatus = 'warning';
                $this->scanMessage = "{$member->full_name} is already checked in.";
                $this->lastScannedMember = $member;
                return;
            }
        }

        // Create new attendance record
        Attendance::create([
            'event_id' => $this->eventId,
            'member_id' => $member->id,
            'scanned_by' => auth()->id(),
            'scanned_at' => now(),
            'status' => 'present',
        ]);

        $this->scanStatus = 'success';
        $this->scanMessage = "Checked in: {$member->full_name}";
        $this->lastScannedMember = $member;
    }

    public function render()
    {
        return view('livewire.attendance-scanner', [
            'events' => Event::whereDate('date', '>=', Carbon::today()->subDays(7))
                ->orderBy('date', 'desc')
                ->get(),
            'members' => Member::where('status', 'active')
                ->orderBy('full_name')
                ->get(),
        ]);
    }
}
