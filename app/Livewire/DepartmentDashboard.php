<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;

class DepartmentDashboard extends Component
{
    use WithPagination;

    public Department $department;
    public $activeTab = 'overview'; // overview, members, announcements
    public $newAnnouncementTitle;
    public $newAnnouncementBody;

    public function mount(Department $department)
    {
        $this->department = $department;
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function postAnnouncement()
    {
        $this->validate([
            'newAnnouncementTitle' => 'required|string|max:255',
            'newAnnouncementBody' => 'required|string',
        ]);

        Announcement::create([
            'department_id' => $this->department->id,
            'user_id' => auth()->id(),
            'title' => $this->newAnnouncementTitle,
            'body' => $this->newAnnouncementBody,
        ]);

        $this->newAnnouncementTitle = '';
        $this->newAnnouncementBody = '';
        session()->flash('message', 'Announcement posted successfully.');
    }

    public function render()
    {
        return view('livewire.department-dashboard', [
            'members' => $this->department->members()->paginate(10),
            'announcements' => $this->department->announcements()->paginate(5),
        ]);
    }
}
