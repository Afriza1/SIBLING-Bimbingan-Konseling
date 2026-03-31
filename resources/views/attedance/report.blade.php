@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Laporan Absensi Bulan {{ $month }} - {{ $year }}</h2>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                @for ($i = 1; $i <= $daysInMonth; $i++)
                    <th>{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                <tr>
                    <td>{{ $index+1 }}</td>
                    <td>{{ $student->name }}</td>
                    @for ($i = 1; $i <= $daysInMonth; $i++)
                        <td>-</td> {{-- nanti diisi status hadir/izin/sakit --}}
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
