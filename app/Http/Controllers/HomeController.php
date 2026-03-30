<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\JobVacancy;
use App\Models\Student;
use App\Models\Cases;
use App\Models\Guidance;
use App\Models\Attendance;
use App\Models\Achievement;
use App\Models\GuidanceBooking;
use App\Models\LoginLog;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $user = auth()->user();

        $role = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', get_class($user))
            ->value('roles.name');

        if ($role === 'Admin') {
            return $this->adminDashboard();
        } elseif ($role === 'Guru BK') {
            return $this->guruBKDashboard();
        } elseif ($role === 'Wali Kelas') {
            return $this->waliKelasDashboard();
        } elseif ($role === 'Siswa') {
            return $this->siswaDashboard();
        }

        return $this->adminDashboard();
    }

    private function adminDashboard()
    {
        $today           = Carbon::today();
        $firstDayOfMonth = $today->copy()->startOfMonth();
        $days_in_month   = $today->daysInMonth;

        $guidances_per_day = $cases_per_day = $attendances_per_day = $logins_per_day = [];
        $careers_per_month = $achievements_per_month = [];

        for ($i = 1; $i <= $days_in_month; $i++) {
            $date = $firstDayOfMonth->copy()->addDays($i - 1);
            $guidances_per_day[]   = Guidance::whereDate('date', $date)->count();
            $cases_per_day[]       = Cases::whereDate('date', $date)->count();
            $attendances_per_day[] = Attendance::whereDate('date', $date)->count();
            $logins_per_day[]      = LoginLog::whereDate('created_at', $date)->count();
        }

        for ($i = 1; $i <= 12; $i++) {
            $start = $today->copy()->month($i)->startOfMonth();
            $end   = $today->copy()->month($i)->endOfMonth();
            $careers_per_month[]      = JobVacancy::whereBetween('dateline_date', [$start, $end])->count();
            $achievements_per_month[] = Achievement::whereBetween('date', [$start, $end])->count();
        }

        // Riwayat login terbaru (50 data)
        $recent_logins = LoginLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('home.admin', [
            'active'                 => 'home',
            'total_users'            => \App\Models\User::count(),
            'total_students'         => Student::count(),
            'total_guidances'        => Guidance::count(),
            'total_cases'            => Cases::count(),
            'total_job_vacancies'    => JobVacancy::count(),
            'total_attendances'      => Attendance::count(),
            'total_achievements'     => Achievement::count(),
            'days_in_month'          => $days_in_month,
            'guidances_per_day'      => $guidances_per_day,
            'cases_per_day'          => $cases_per_day,
            'attendances_per_day'    => $attendances_per_day,
            'careers_per_month'      => $careers_per_month,
            'achievements_per_month' => $achievements_per_month,
            'logins_per_day'         => $logins_per_day,
            'recent_logins'          => $recent_logins,
        ]);
    }

    private function guruBKDashboard()
    {
        $today           = Carbon::today();
        $firstDayOfMonth = $today->copy()->startOfMonth();
        $days_in_month   = $today->daysInMonth;

        $guidances_per_day = $cases_per_day = $attendances_per_day = [];

        for ($i = 1; $i <= $days_in_month; $i++) {
            $date = $firstDayOfMonth->copy()->addDays($i - 1);
            $guidances_per_day[]   = Guidance::whereDate('date', $date)->count();
            $cases_per_day[]       = Cases::whereDate('date', $date)->count();
            $attendances_per_day[] = Attendance::whereDate('date', $date)->count();
        }

        $pending_bookings = collect([]);
        try {
            $pending_bookings = GuidanceBooking::where('date', '>=', $today)
                ->orderBy('date')->take(5)->get();
        } catch (\Exception $e) {
            $pending_bookings = collect([]);
        }

        return view('home.gurubk', [
            'active'              => 'home',
            'total_guidances'     => Guidance::count(),
            'total_cases'         => Cases::count(),
            'total_attendances'   => Attendance::whereDate('date', $today)->count(),
            'total_bookings'      => $pending_bookings->count(),
            'days_in_month'       => $days_in_month,
            'guidances_per_day'   => $guidances_per_day,
            'cases_per_day'       => $cases_per_day,
            'attendances_per_day' => $attendances_per_day,
            'pending_bookings'    => $pending_bookings,
            'recent_guidances'    => Guidance::orderBy('date', 'desc')->take(5)->get(),
            'recent_cases'        => Cases::orderBy('date', 'desc')->take(5)->get(),
        ]);
    }

    public static function getWaliKelasClass($user)
    {
        return \App\Models\Classes::with('major')
            ->where('wali_kelas_id', $user->id)
            ->first();
    }

    private function waliKelasDashboard()
    {
        $user  = auth()->user();
        $today = Carbon::today();

        $myClass                 = null;
        $students_in_class       = collect([]);
        $total_students_in_class = 0;

        try {
            $myClass = self::getWaliKelasClass($user);
            if ($myClass) {
                $students_in_class       = Student::where('class_id', $myClass->id)->get();
                $total_students_in_class = $students_in_class->count();
            }
        } catch (\Exception $e) {
            $students_in_class = collect([]);
        }

        $attendance_today      = 0;
        $cases_in_class        = 0;
        $achievements_in_class = 0;
        $guidances_in_class    = 0;
        $recent_guidances      = collect([]);
        $recent_achievements   = collect([]);
        $students_with_cases   = collect([]);

        if ($myClass) {
            $studentIds = $students_in_class->pluck('id');

            $attendance_today      = Attendance::whereDate('date', $today)->whereIn('student_id', $studentIds)->count();
            $cases_in_class        = Cases::whereIn('student_id', $studentIds)->count();
            $achievements_in_class = Achievement::whereIn('student_id', $studentIds)->count();
            $guidances_in_class    = Guidance::whereIn('student_id', $studentIds)->count();

            $recent_guidances    = Guidance::whereIn('student_id', $studentIds)->orderBy('date', 'desc')->take(5)->get();
            $recent_achievements = Achievement::whereIn('student_id', $studentIds)->orderBy('date', 'desc')->take(5)->get();

            $students_with_cases = $students_in_class->map(function ($s) {
                $s->cases_count     = Cases::where('student_id', $s->id)->count();
                $s->guidances_count = Guidance::where('student_id', $s->id)->count();
                return $s;
            })->sortByDesc('cases_count')->filter(fn($s) => $s->cases_count > 0)->take(5);
        }

        $firstDayOfMonth     = $today->copy()->startOfMonth();
        $days_in_month       = $today->daysInMonth;
        $attendances_per_day = [];
        for ($i = 1; $i <= $days_in_month; $i++) {
            $date = $firstDayOfMonth->copy()->addDays($i - 1);
            $q    = Attendance::whereDate('date', $date);
            if ($myClass) $q->whereIn('student_id', $students_in_class->pluck('id'));
            $attendances_per_day[] = $q->count();
        }

        return view('home.walikelas', [
            'active'                  => 'home',
            'myClass'                 => $myClass,
            'total_students_in_class' => $total_students_in_class,
            'attendance_today'        => $attendance_today,
            'cases_in_class'          => $cases_in_class,
            'achievements_in_class'   => $achievements_in_class,
            'guidances_in_class'      => $guidances_in_class,
            'students_in_class'       => $students_in_class,
            'students_with_cases'     => $students_with_cases,
            'recent_guidances'        => $recent_guidances,
            'recent_achievements'     => $recent_achievements,
            'days_in_month'           => $days_in_month,
            'attendances_per_day'     => $attendances_per_day,
        ]);
    }

    public static function autoLinkStudent($user)
    {
        if (Student::where('user_id', $user->id)->exists()) {
            return Student::with(['class.major', 'status'])->where('user_id', $user->id)->first();
        }

        $student = Student::with(['class.major', 'status'])
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($user->name))])
            ->whereNull('user_id')
            ->first();

        if ($student) {
            $student->user_id = $user->id;
            $student->save();
        }

        return $student;
    }

    private function siswaDashboard()
    {
        $user  = auth()->user();
        $today = Carbon::today();

        $student = self::autoLinkStudent($user);

        $my_guidances = $my_cases = $my_achievements = $my_attendances = collect([]);
        $total_tidak_hadir = 0;

        if ($student) {
            $my_guidances      = Guidance::where('student_id', $student->id)->orderBy('date', 'desc')->take(5)->get();
            $my_cases          = Cases::where('student_id', $student->id)->orderBy('date', 'desc')->get();
            $my_achievements   = Achievement::where('student_id', $student->id)->orderBy('date', 'desc')->take(5)->get();
            $my_attendances    = Attendance::where('student_id', $student->id)->orderBy('date', 'desc')->take(10)->get();
            $total_tidak_hadir = Attendance::where('student_id', $student->id)
                ->whereMonth('date', $today->month)
                ->whereYear('date', $today->year)
                ->whereIn('presence_status', ['Sakit', 'Ijin', 'Alpa'])
                ->count();
        }

        return view('home.siswa', [
            'active'             => 'home',
            'student'            => $student,
            'total_guidances'    => $my_guidances->count(),
            'total_cases'        => $my_cases->count(),
            'total_achievements' => $my_achievements->count(),
            'total_tidak_hadir'  => $total_tidak_hadir,
            'my_guidances'       => $my_guidances,
            'my_cases'           => $my_cases,
            'my_achievements'    => $my_achievements,
            'my_attendances'     => $my_attendances,
        ]);
    }
}
