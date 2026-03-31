@extends('layouts.dashboard')
@section('content')
<div>
  <h4 class="my-2" style="font-family: NunitoSans-ExtraBold; color: var(--title-color);line-height: 75px;">Pengaturan Akun</h4>
  <div class="card border-0 shadowNavbar" id="panel">
    <div class="card-body">
      <div class="row">
        <div class="col-md-3">
          <div class="nav flex-column nav-pills me-3 settings" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <button class="nav-link text-start active mb-2" id="v-pills-home-tab" data-bs-toggle="pill" data-bs-target="#v-pills-home"
              type="button" role="tab" aria-controls="v-pills-home" aria-selected="true">Profile</button>
            <button class="nav-link text-start mb-2" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile"
              type="button" role="tab" aria-controls="v-pills-profile" aria-selected="false">Keamanan</button>
            <button class="nav-link text-start mb-2" id="v-pills-messages-tab" data-bs-toggle="pill" data-bs-target="#v-pills-messages"
              type="button" role="tab" aria-controls="v-pills-messages" aria-selected="false">Notifikasi</button>
            <button class="nav-link text-start mb-2" id="v-pills-settings-tab" data-bs-toggle="pill" data-bs-target="#v-pills-settings"
              type="button" role="tab" aria-controls="v-pills-settings" aria-selected="false" disabled>Tentang</button>
          </div>
        </div>
        <div class="col-md-9">
          <div class="tab-content" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab"
              tabindex="0">
              <div class="card border-0" style="background-color: var(--container-color)">
                <div class="card-body">
                  <h4 class="card-title" style="font-family: NunitoSans-ExtraBold">Informasi Profile</h4>
                  <div class="card mt-4" style="border-radius: 16px; border-color: #e2e2e4; background-color: var(--container-color)">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                          @if (auth()->user()->photo)
                            <img src="{{ route('user.showImage', auth()->user()->id) }}" alt="profile" class="rounded-circle" width="50"/>
                          @else
                            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=random" alt="profileImg" />
                          @endif
                          <div class="ms-3">
                            <h5 class="card-title m-0" style="font-family: NunitoSans-ExtraBold">{{ auth()->user()->name }}</h5>
                            <p class="m-0 small">{{ auth()->user()->getRoleNames()->first() ?? 'Tidak Ada Role' }}</p>
                            <p class="card-text small" style="color: var(--text-color-light)">{{ auth()->user()->email }}</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card mt-4" style="border-radius: 16px; border-color: #e2e2e4; background-color: var(--container-color)">
                    <div class="card-body">
                      <h5 class="card-title" style="font-family: NunitoSans-Bold ">Data Personal</h5>
                      <form action="{{ route('user.setting_account', auth()->user()->id) }}" method="POST" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                          <div style="display: flex; justify-content: center; align-items: center;">
                            <img id="outputUpdate" src="{{ route('user.showImage', auth()->user()->id) }}" alt="Foto" style="width: 90px; height: 90px; object-fit: cover; border-radius: 50%; margin-bottom: 10px;">
                          </div>
                          <div class="mb-3">
                              @if (auth()->user()->photo)
                                <p class="text-muted">Jika tidak ingin mengganti file, biarkan kosong.</p>
                              @else
                                <p class="text-danger">Belum ada Foto yang diunggah.</p>
                              @endif
                            <input type="file" class="form-control" id="photo" name="photo" accept=".pdf,.jpg,.png" onchange="loadFileUpdate(event)">
                          </div>
                          <div class="col-md-6">
                            <div class="mb-3">
                              @php $isSiswaRole = auth()->user()->hasRole('Siswa'); @endphp
                                <label for="nip" class="col-form-label">
                                {{ $isSiswaRole ? 'NISN' : 'NIP/NUPTK' }}
                                </label>
                                <input type="text" class="form-control" id="nip" name="nomor_induk"
                                value="{{ auth()->user()->nomor_induk }}"
                                placeholder="{{ $isSiswaRole ? 'Masukkan NISN' : 'Masukkan NIP atau NUPTK' }}">
                            </div>
                            <div class="mb-3">
                              <label for="name" class="col-form-label">Nama:</label>
                              <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}">
                            </div>
                            <div class="mb-3">
                              <label for="gender" class="col-form-label">Jenis Kelamin</label>
                              <select class="form-control" id="gender" name="gender">
                                <option value="laki-laki" {{ auth()->user()->gender == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="perempuan" {{ auth()->user()->gender == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                              </select>
                            </div>
                            <div class="mb-3">
                              <label for="place_of_birth" class="col-form-label">Tempat Lahir</label>
                              <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" value="{{ auth()->user()->place_of_birth }}">
                            </div>
                            <div class="mb-3">
                              <label for="address" class="col-form-label">Alamat</label>
                              <textarea class="form-control" id="address" name="address">{{ auth()->user()->address }}</textarea>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="mb-3">
                              <label for="religion" class="col-form-label">Agama</label>
                              <select class="form-control" id="religion" name="religion">
                                <option value="Islam" {{ auth()->user()->religion == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ auth()->user()->religion == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ auth()->user()->religion == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ auth()->user()->religion == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ auth()->user()->religion == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Konghucu" {{ auth()->user()->religion == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                              </select>
                            </div>
                            <div class="mb-3">
                              <label for="phone_number" class="col-form-label">Nomor Telepon</label>
                              <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ auth()->user()->phone_number }}">
                            </div>
                            <div class="mb-3">
                              <label for="date_of_birth" class="col-form-label">Tanggal Lahir</label>
                              <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ auth()->user()->date_of_birth }}">
                            </div>
                            <div class="mb-3">
                              <label for="email" class="col-form-label">Email</label>
                              <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}">
                            </div>
                          </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                      </form>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab"
              tabindex="0">
              <div class="card border-0 shadowNavbar">
                <div class="card-body">
                  <h5 class="card-title">Keamanan</h5>
                  <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab"
              tabindex="0">
              <div class="card border-0 shadowNavbar">
                <div class="card-body">
                  <h5 class="card-title
                  ">Notifikasi</h5>
                  <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="email_notification" name="email_notification" {{ auth()->user()->email_notification ? 'checked' : '' }}>
                      <label class="form-check-label" for="email_notification">Email Notification</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                  </form>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab"
              tabindex="0">
              <div class="card border-0 shadowNavbar">
                <div class="card-body">
                  <h5 class="card-title">Tentang</h5>
                  <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quidem
                    voluptates. Quisquam, quidem voluptates.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

<script>
  var loadFileUpdate = function(event) {
    var outputUpdate = document.getElementById('outputUpdate');
    outputUpdate.src = URL.createObjectURL(event.target.files[0]);

  }
</script>
