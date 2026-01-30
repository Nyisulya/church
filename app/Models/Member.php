<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Member extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly(['*']);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            $member->member_number = 'MEM-' . strtoupper(uniqid());
        });
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'member_number',
        'full_name',
        'gender',
        'phone',
        'email',
        'date_of_birth',
        'marital_status',
        'marriage_date',
        'wedding_date',
        'address',
        'salvation_date',
        'baptism_date',
        'profile_photo',
        'emergency_contact_name',
        'emergency_contact_phone',
        'status',
        'registration_type',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'marriage_date' => 'date',
        'wedding_date' => 'date',
        'salvation_date' => 'date',
        'baptism_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_member')
                    ->withPivot('role', 'joined_at', 'left_at', 'status')
                    ->withTimestamps();
    }

    public function qrCode()
    {
        return $this->hasOne(MemberQrCode::class);
    }
    public function getQrCodeContentAttribute()
    {
        return json_encode([
            'id' => $this->id,
            'name' => $this->full_name,
            'number' => $this->member_number,
        ]);
    }

    public function smallGroups()
    {
        return $this->belongsToMany(SmallGroup::class, 'small_group_member')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function smallGroupResponses()
    {
        return $this->hasMany(SmallGroupResponse::class);
    }

    /**
     * Celebration-related scopes and accessors
     */
    public function scopeUpcomingBirthdays($query, $days = 30)
    {
        // Get all members with birthdays, then filter in PHP for database compatibility
        return $query->whereNotNull('date_of_birth');
    }

    public function scopeTodaysBirthdays($query)
    {
        $today = now();
        
        // For SQLite compatibility, use proper date format
        if (config('database.default') === 'sqlite') {
            return $query->whereNotNull('date_of_birth')
                ->whereRaw("strftime('%m-%d', date_of_birth) = ?", [$today->format('m-d')]);
        }
        
        // For MySQL
        return $query->whereNotNull('date_of_birth')
            ->whereMonth('date_of_birth', $today->month)
            ->whereDay('date_of_birth', $today->day);
    }

    public function getIsBirthdayTodayAttribute()
    {
        if (!$this->date_of_birth) return false;
        return $this->date_of_birth->isBirthday();
    }

    public function getIsAnniversaryTodayAttribute()
    {
        $date = $this->anniversary_date;
        if (!$date) return false;
        return $date->isBirthday();
    }

    public function getAnniversaryDateAttribute()
    {
        return $this->marriage_date ?? $this->wedding_date;
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getNextBirthdayAttribute()
    {
        if (!$this->date_of_birth) return null;
        
        $birthday = $this->date_of_birth->setYear(now()->year);
        if ($birthday->isPast()) {
            $birthday->addYear();
        }
        return $birthday;
    }

    public function getDaysUntilBirthdayAttribute()
    {
        return $this->next_birthday ? now()->diffInDays($this->next_birthday) : null;
    }

    public function getYearsMarriedAttribute()
    {
        return $this->wedding_date ? $this->wedding_date->diffInYears(now()) : null;
    }

    public function getYearsSinceSalvationAttribute()
    {
        return $this->salvation_date ? $this->salvation_date->diffInYears(now()) : null;
    }

    /**
     * Check if member profile is complete
     * 
     * @return bool
     */
    public function isProfileComplete(): bool
    {
        $requiredFields = [
            'full_name',
            'email',
            'phone',
            'gender',
            'date_of_birth',
            'marital_status',
            'address',
            'salvation_date',
            'baptism_date',
            'emergency_contact_name',
            'emergency_contact_phone',
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        // Check wedding date if married
        if ($this->marital_status === 'married' && empty($this->wedding_date)) {
            return false;
        }

        return true;
    }

    /**
     * Get list of missing required fields
     * 
     * @return array
     */
    public function getMissingFields(): array
    {
        $missing = [];
        $requiredFields = [
            'full_name' => 'Full Name',
            'email' => 'Email',
            'phone' => 'Phone Number',
            'gender' => 'Gender',
            'date_of_birth' => 'Date of Birth',
            'marital_status' => 'Marital Status',
            'address' => 'Address',
            'salvation_date' => 'Salvation Date',
            'baptism_date' => 'Baptism Date',
            'emergency_contact_name' => 'Emergency Contact Name',
            'emergency_contact_phone' => 'Emergency Contact Phone',
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($this->$field)) {
                $missing[] = $label;
            }
        }

        // Check wedding date if married
        if ($this->marital_status === 'married' && empty($this->wedding_date)) {
            $missing[] = 'Wedding Date';
        }

        return $missing;
    }
}
