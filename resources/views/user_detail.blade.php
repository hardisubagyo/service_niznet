@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"> <a href="{{route('home')}}">Dashboard</a> / User Detail</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <center><h3>DATA PRIBADI PELAMAR</h3></center>
                    <hr></hr>
                    <form method="POST" action="{{ route('update-user-detail') }}" aria-label="{{ __('update-user-detail') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="id_posisi_lamaran" class="col-md-4 col-form-label text-md-right">{{ __('Posisi yang di lamar') }}</label>
                            <div class="col-md-6">
                                <select id="id_posisi_lamaran" class="form-control{{ $errors->has('id_posisi_lamaran') ? ' is-invalid' : '' }}" readonly required>
                                    @foreach($posisiLamaranList as $row)
                                    <option value="{{$row->id}}" @if(isset($userDetail) && $userDetail->id_posisi_lamaran == $row->id) selected @endif>{{$row->nama_posisi}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama" class="col-md-4 col-form-label text-md-right">{{ __('Nama') }}</label>
                            <div class="col-md-6">
                                <input id="nama" type="text" class="form-control{{ $errors->has('nama') ? ' is-invalid' : '' }}" readonly value="@if(isset($userDetail)) {{$userDetail->nama}} @endif" required autofocus>
                                @if ($errors->has('nama'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('nama') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="no_ktp" class="col-md-4 col-form-label text-md-right">{{ __('No KTP') }}</label>
                            <div class="col-md-6">
                                <input id="no_ktp" type="text" class="form-control{{ $errors->has('no_ktp') ? ' is-invalid' : '' }}" readonly value="@if(isset($userDetail)) {{$userDetail->no_ktp}} @endif" required autofocus>
                                @if ($errors->has('no_ktp'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('no_ktp') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tempat_lahir" class="col-md-4 col-form-label text-md-right">{{ __('Tempat lahir') }}</label>
                            <div class="col-md-6">
                                <input id="tempat_lahir" type="text" class="form-control{{ $errors->has('tempat_lahir') ? ' is-invalid' : '' }}" readonly value="@if(isset($userDetail)) {{$userDetail->tempat_lahir}} @endif" required autofocus>
                                @if ($errors->has('tempat_lahir'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('tempat_lahir') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tanggal_lahir" class="col-md-4 col-form-label text-md-right">{{ __('Tanggal lahir') }}</label>
                            <div class="col-md-6">
                                <input id="tanggal_lahir" type="date" class="form-control{{ $errors->has('tanggal_lahir') ? ' is-invalid' : '' }}" readonly value="@if(isset($userDetail)) {{$userDetail->tanggal_lahir}} @endif" required autofocus>
                                @if ($errors->has('tanggal_lahir'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('tanggal_lahir') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="id_jk" class="col-md-4 col-form-label text-md-right">{{ __('Jenis Kelamin') }}</label>
                            <div class="col-md-6">
                                <select id="id_jk" class="form-control{{ $errors->has('id_jk') ? ' is-invalid' : '' }}" readonly required>
                                    @foreach($jenisKelaminList as $row)
                                    <option value="{{$row->id}}" @if(isset($userDetail) && $userDetail->id_jk == $row->id) selected @endif>{{$row->nama_jk}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="id_agama" class="col-md-4 col-form-label text-md-right">{{ __('Agama') }}</label>
                            <div class="col-md-6">
                                <select id="id_agama" class="form-control{{ $errors->has('id_agama') ? ' is-invalid' : '' }}" readonly required>
                                    @foreach($agamaList as $row)
                                    <option value="{{$row->id}}">{{$row->nama_agama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="id_golongan_darah" class="col-md-4 col-form-label text-md-right">{{ __('Golongan Darah') }}</label>
                            <div class="col-md-6">
                                <select id="id_golongan_darah" class="form-control{{ $errors->has('id_golongan_darah') ? ' is-invalid' : '' }}" readonly required>
                                    @foreach($golongandarahList as $row)
                                    <option value="{{$row->id}}"  @if(isset($userDetail) && $userDetail->id_golongan_darah == $row->id) selected @endif>{{$row->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="id_status_menikah" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
                            <div class="col-md-6">
                                <select id="id_status_menikah" class="form-control{{ $errors->has('id_status_menikah') ? ' is-invalid' : '' }}" readonly required>
                                    @foreach($statusMenikahList as $row)
                                    <option value="{{$row->id}}"  @if(isset($userDetail) && $userDetail->id_status_menikah == $row->id) selected @endif>{{$row->nama_status}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_ktp" class="col-md-4 col-form-label text-md-right">{{ __('Alamat KTP') }}</label>
                            <div class="col-md-6">
                                <Textarea id="alamat_ktp" type="text" class="form-control{{ $errors->has('alamat_ktp') ? ' is-invalid' : '' }}" readonly value="{{ old('alamat_ktp') }}" required autofocus>@if(isset($userDetail)) {{$userDetail->alamat_ktp}} @endif</textarea>
                                @if ($errors->has('alamat_ktp'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('alamat_ktp') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_tinggal" class="col-md-4 col-form-label text-md-right">{{ __('Alamat Tinggal') }}</label>
                            <div class="col-md-6">
                                <Textarea id="alamat_tinggal" type="text" class="form-control{{ $errors->has('alamat_tinggal') ? ' is-invalid' : '' }}" readonly value="{{ old('alamat_tinggal') }}" required autofocus>@if(isset($userDetail)) {{$userDetail->alamat_tinggal}} @endif</textarea>
                                @if ($errors->has('alamat_tinggal'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('alamat_tinggal') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" readonly value="@if(isset($userDetail)) {{$userDetail->email}} @endif" required>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="no_tlp" class="col-md-4 col-form-label text-md-right">{{ __('No Tlp') }}</label>
                            <div class="col-md-6">
                                <input id="no_tlp" type="text" class="form-control{{ $errors->has('no_tlp') ? ' is-invalid' : '' }}" readonly value="@if(isset($userDetail)) {{$userDetail->no_tlp}} @endif" required autofocus>
                                @if ($errors->has('no_tlp'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('no_tlp') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="orang_terdekat" class="col-md-4 col-form-label text-md-right">{{ __('Orang Terdekat') }}</label>
                            <div class="col-md-6">
                                <input id="orang_terdekat" type="text" class="form-control{{ $errors->has('orang_terdekat') ? ' is-invalid' : '' }}" readonly value="@if(isset($userDetail)) {{$userDetail->orang_terdekat}} @endif" required autofocus>
                                @if ($errors->has('orang_terdekat'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('orang_terdekat') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="skill" class="col-md-4 col-form-label text-md-right">{{ __('Skill') }}</label>
                            <div class="col-md-6">
                                <Textarea id="skill" type="text" class="form-control{{ $errors->has('skill') ? ' is-invalid' : '' }}" readonly value="{{ old('skill') }}" required autofocus>@if(isset($userDetail)) {{$userDetail->skill}} @endif</textarea>
                                @if ($errors->has('skill'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('skill') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bersedia_penempatan" class="col-md-4 col-form-label text-md-right">{{ __('Bersedia Penempatan') }}</label>
                            <div class="col-md-6">
                                <input type="radio" readonly value="1" @if(isset($userDetail) && $userDetail->bersedia_penempatan == 1) checked  @endif>Ya
                                <input type="radio" readonly value="0" @if(isset($userDetail) && $userDetail->bersedia_penempatan == 0) checked  @endif>Tidak
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="penghasilan_harapan" class="col-md-4 col-form-label text-md-right">{{ __('Penghasilan Harapan') }}</label>
                            <div class="col-md-6">
                                <input id="penghasilan_harapan" type="text" class="form-control{{ $errors->has('penghasilan_harapan') ? ' is-invalid' : '' }}" readonly value="@if(isset($userDetail)) {{$userDetail->penghasilan_harapan}} @endif" required autofocus>
                                @if ($errors->has('penghasilan_harapan'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('penghasilan_harapan') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
