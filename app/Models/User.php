<?php

namespace App\Models;

use App\Repositories\StudentSubject\StudentSubjectInterface;
use App\Services\CachingService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'image',
        'password',
        'current_address',
        'permanent_address',
        'school_id',
        'gender',
        'dob',
        'occupation',
        'reset_request',
        'status',
        'deleted_at'
    ];

    protected static function boot() {
        parent::boot();
        static::deleting(static function ($user) { // before delete() method call this
            if ($user->isForceDeleting() && $user->getRawOriginal('image') && Storage::disk('public')->exists($user->getRawOriginal('image'))) {
                Storage::disk('public')->delete($user->getRawOriginal('image'));
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'full_name',
    ];

    public function student() {
        return $this->hasOne(Students::class, 'user_id', 'id')->withTrashed();
    }

    // public function parent() {
    //     return $this->hasOne(Parents::class, 'user_id', 'id');
    // }

    public function staff() {
        return $this->hasOne(Staff::class, 'user_id', 'id');
    }

    public function class_section_teacher() {
        return $this->hasOne(ClassSection::class, 'class_teacher_id')->withTrashed();
    }

    /**
     * This method will only work with Teacher Role
     * @return HasManyThrough
     */
    public function timetable() {

        return $this->hasManyThrough(Timetable::class, SubjectTeacher::class, 'teacher_id', 'subject_teacher_id');
    }

    public function subjectTeachers() {
        return $this->hasMany(SubjectTeacher::class, 'teacher_id');
    }

    public function school() {
        return $this->belongsTo(School::class)->withTrashed();
    }

//    public function guardianRelationChild() {
//        return $this->hasMany(Students::class, 'guardian_id');
//    }

    public function scopeOwner($query) {
        if (Auth::user()) {
            if (Auth::user()->school_id) {

                if (Auth::user()->hasRole('School Admin')) {
                    return $query->where('school_id', Auth::user()->school_id);
                }
                if (Auth::user()->hasRole('Teacher')) {
                    return $query->where('school_id', Auth::user()->school_id);
                }
                if (Auth::user()->hasRole('Student')) {
                    return $query->where('school_id', Auth::user()->school_id);
                }
                return $query->where('school_id', Auth::user()->school_id);
            }

            if (!Auth::user()->school_id) {
                if (Auth::user()->hasRole('Super Admin')) {
                    return $query->where('school_id', null);
                }
                if (Auth::user()->hasRole('Guardian')) {
                    $childId = request('child_id');
                    $studentAuth = Students::where('id', $childId)->first();
                    return $query->where('school_id', $studentAuth->school_id);
                }
                return $query->where('school_id', null);
            }
        }


        return $query;
    }

    /**
     * Get all of the support_school for the User
     *
     * @return HasMany
     */
    public function support_school() {
        return $this->hasMany(StaffSupportSchool::class, 'user_id', 'id');
    }


    //Getter Attributes
    public function getImageAttribute($value) {
        if ($value) {
            return url(Storage::url($value));
        }
        return '';
        // return url(Storage::url($value));
    }

    public function getFullNameAttribute() {
        return $this->first_name . ' ' . $this->last_name;
    }


    public function guardianRelationChild() {
        return $this->hasMany(Students::class, 'guardian_id');
    }

    public function child() {
        return $this->hasMany(Students::class, 'guardian_id')->withTrashed();
    }


    public function teacher() {
        return $this->hasOne(Staff::class);
    }


    public function fees_paids() {
        return $this->hasMany(FeesPaid::class, 'student_id')->withTrashed();
    }

    public function fees_paid() {
        return $this->hasOne(FeesPaid::class, 'student_id')->withTrashed();
    }

    public function optional_fees() {
        return $this->hasMany(OptionalFee::class, 'student_id')->withTrashed();
    }

    public function compulsory_fees() {
        return $this->hasMany(CompulsoryFee::class, 'student_id')->withTrashed();
    }

    public function features() {
        // Current active features set to sidebar menu
        $addons = Auth::user()->school->current_addon->pluck('name')->toArray();
        $subscription = Auth::user()->school->current_subscription->features->pluck('name')->toArray();

        return array_merge($addons, $subscription);
    }

    public function exam_result() {
        return $this->hasMany(ExamResult::class, 'student_id', 'id');
    }

    public function exam_marks() {
        return $this->hasMany(ExamMarks::class, 'student_id', 'id');
    }

    public function online_exam_attempts() {
        return $this->hasMany(StudentOnlineExamStatus::class, 'student_id', 'id');
    }

    protected function setDobAttribute($value) {
        $this->attributes['dob'] = date('Y-m-d', strtotime($value));
    }

    public function extra_student_details() {
        return $this->hasMany(ExtraStudentData::class, 'student_id', 'id')->withTrashed();
    }


    public function selectedStudentSubjects() {
        $studentSubject = app(StudentSubjectInterface::class);
        $cache = app(CachingService::class);
        $currentSemester = $cache->getDefaultSemesterData($this->school_id);

        $core_subjects = $this->class_section->class->core_subjects()->where(function ($query) use ($currentSemester) {
            (isset($currentSemester) && !empty($currentSemester)) ? $query->where('semester_id', $currentSemester->id)->orWhereNull('semester_id') : $query->orWhereNull('semester_id');
        })->get();

        $subjects = $core_subjects->toArray();

        $elective_subject_count = $this->class_section->class->elective_subject_groups()->where(function ($query) use ($currentSemester) {
            (isset($currentSemester) && !empty($currentSemester)) ? $query->where('semester_id', $currentSemester->id)->orWhereNull('semester_id') : $query->orWhereNull('semester_id');
        })->count();

        if ($elective_subject_count > 0) {
            $elective_subjects = $studentSubject->builder()->where('student_id', $this->id)->with('class_subject.subject')->get();
            $subjects = array_merge($subjects, $elective_subjects->toArray());
        }
        return collect($subjects);
    }


    public function currentSemesterClassSubjects() {
        $cache = app(CachingService::class);
        $currentSemester = $cache->getDefaultSemesterData();
        $core_subjects = $this->class_section->class->core_subjects()->where(function ($query) use ($currentSemester) {
            (isset($currentSemester) && !empty($currentSemester)) ? $query->where('semester_id', $currentSemester->id)->orWhereNull('semester_id') : $query->orWhereNull('semester_id');
        })->get();
        $elective_subjects = $this->class_section->class->elective_subject_groups()->where(function ($query) use ($currentSemester) {
            (isset($currentSemester) && !empty($currentSemester)) ? $query->where('semester_id', $currentSemester->id)->orWhereNull('semester_id') : $query->orWhereNull('semester_id');
        })->with('subjects')->get();
        return ['core_subject' => $core_subjects, 'elective_subject_group' => $elective_subjects];
    }

    /**
     * Get the user_status associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user_status()
    {
        return $this->hasOne(UserStatusForNextCycle::class);
    }
    public function getSchoolIdAttribute($value) {
        if (!empty($value)) {
            return $value;
        }

        if (Auth::check() && Auth::user()->hasRole('Guardian')) {
            $child_id = request('child_id');
            $user = self::select('school_id')->whereHas('student', function ($q) use ($child_id) {
                $q->where('id', $child_id);
            })->first();

            return $user->school_id ?? null;
        }

        return null;
    }
}
