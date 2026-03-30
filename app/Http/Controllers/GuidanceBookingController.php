<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuidanceBooking;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class GuidanceBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    private function getRole()
    {
        $user = auth()->user();
        return DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', get_class($user))
            ->value('roles.name');
    }

    public function index()
    {
        $role    = $this->getRole();
        $isSiswa = $role === 'Siswa';

        if ($isSiswa) {
            // Siswa hanya lihat booking miliknya sendiri
            $user    = auth()->user();
            $student = \App\Http\Controllers\HomeController::autoLinkStudent($user);

            $guidanceBookings = $student
                ? GuidanceBooking::where('name', $student->name)->latest()->get()
                : collect([]);

            return view('data_booking_bimbingan', [
                'guidanceBookings' => $guidanceBookings,
                'active'           => 'guidance_booking',
                'isSiswa'          => true,
                'student'          => $student,
            ]);
        }

        // Admin / Guru BK / Wali Kelas — lihat semua
        return view('data_booking_bimbingan', [
            'guidanceBookings' => GuidanceBooking::latest()->get(),
            'active'           => 'guidance_booking',
            'isSiswa'          => false,
            'student'          => null,
        ]);
    }

    // Khusus siswa: form booking mandiri
    public function bookingForm()
    {
        $user    = auth()->user();
        $student = \App\Http\Controllers\HomeController::autoLinkStudent($user);

        $timeSlots        = ['09:00', '09:15', '11:30', '11:45'];
        $maxBookingPerSlot = 3;
        $slotInfo = [];
        foreach ($timeSlots as $time) {
            $booked = GuidanceBooking::where('booking_date', 'like', "%" . $time . "%")->count();
            $slotInfo[] = [
                'time'      => $time,
                'remaining' => max(0, $maxBookingPerSlot - $booked),
                'full'      => ($booked >= $maxBookingPerSlot),
            ];
        }

        return view('siswa_booking', [
            'active'   => 'guidance_booking',
            'student'  => $student,
            'slotInfo' => $slotInfo,
        ]);
    }

    public function handlePost(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'booking_date' => 'required|date',
            'booking_time' => 'required|string|max:255',
            'status'       => 'required|string|max:255',
        ]);

        $bookingDateTime  = $request->booking_date . ' ' . $request->booking_time . ':00';
        $existingBookings = GuidanceBooking::where('booking_date', $bookingDateTime)->count();

        if ($existingBookings >= 3) {
            return redirect()->back()->withInput()->withErrors([
                'booking_time' => 'Waktu ini sudah penuh! Silakan pilih waktu lain.'
            ]);
        }

        $guidanceBooking               = new GuidanceBooking();
        $guidanceBooking->name         = $request->name;
        $guidanceBooking->phone_number = $request->phone_number;
        $guidanceBooking->booking_date = $bookingDateTime;
        $guidanceBooking->status       = $request->status;
        $guidanceBooking->save();

        return redirect()->route('guidanceBooking.index')->with('success', 'Booking bimbingan berhasil diajukan!');
    }

    // Alias untuk siswa submit booking
    public function store(Request $request)
    {
        return $this->handlePost($request);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => 'required|numeric',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'status'       => 'required|in:pending,confirmed,completed',
        ]);

        $bookingDateTime = $request->booking_date . ' ' . $request->booking_time . ':00';

        $guidanceBooking               = GuidanceBooking::findOrFail($id);
        $guidanceBooking->name         = $request->name;
        $guidanceBooking->phone_number = $request->phone_number;
        $guidanceBooking->booking_date = $bookingDateTime;
        $guidanceBooking->status       = $request->status;
        $guidanceBooking->save();

        return redirect()->route('guidanceBooking.index')->with('success', 'Status berhasil diperbarui.');
    }

    public function destroy($id)
    {
        GuidanceBooking::findOrFail($id)->delete();
        return redirect()->route('guidanceBooking.index')->with('success', 'Pemesanan berhasil dihapus.');
    }
}
