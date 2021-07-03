@extends('layouts.app')
@section('content')
<div class="content-header">
</div>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card" id="transaksi">
          <div class="card-header">
            <h3 class="card-title">Transaksi Table</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-block bg-gradient-primary btn-sm" data-toggle="modal" data-target="#transaksi-modal">add</button>
            </div>
          </div>
          <div class="card-body">
            <div class="input-group mb-3">
              <input type="text" class="form-control has-primary" name="search_value" data-model="transaksi" placeholder="Search Key">
              <div class="input-group-append">
                <a type="button" class="input-group-text btn btn-primary btn-border btn-flat" el-event="search-data" data-model="transaksi"><i class="fa fa-search"></i></a>
              </div>
            </div>
            <table class="table table-bordered" id="table-transaksi" data-model="transaksi" request-url="{{ route('api-transaksi') }}" on-success-load="successLoadtransaksi">
              <thead>                  
                <tr>
                  <th style="width: 10px">#</th>
                  <th>No Transaksi</th>
                  <th>Tgl Transaksi</th>
                  <th>Tenan</th>
                  <th>Customer</th>
                  <th>Jumlah Trx</th>
                  <th>Foto</th>
                  <th style="width:150px;text-align: center;" >Action</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <div class="card-footer clearfix">
            <div class="row col-sm-6 pull-left">
              <span class="page-into" style="white-space: nowrap;"></span>
            </div>
            <ul class="pagination pagination-sm m-0 float-right"></ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<div class="modal fade" id="transaksi-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add transaksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form role="form" id="transaksi-form">
          <input type="hidden" name="id" id="id">
          <div class="card-body">
            <div class="form-group">
              <label for="no_trx">No Transaksi</label>
              <input type="text" class="form-control" name="no_trx" id="no_trx" placeholder="no_trx">
            </div>
            <div class="form-group">
              <label for="tgl_trx">Tanggal Transaksi</label>
              <input type="date" class="form-control" name="tgl_trx" id="tgl_trx" placeholder="Tanggal Transaksi">
            </div>
            <div class="form-group">
              <label for="id_customer">Customer</label>
              <select name="id_customer" id="id_customer" class="form-control select2" style="width: 100%;">
                <option value="">Pilih Customer</option>
                @foreach($customerList as $row)
                <option value="{{$row->id}}">{{$row->nama}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="id_tenan">Tenan</label>
              <select name="id_tenan" id="id_tenan" class="form-control select2" style="width: 100%;">
                <option value="">Pilih Tenan</option>
                @foreach($tenanList as $row)
                <option value="{{$row->id}}">{{$row->nama}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="jumlah_trx">Jumlah Transaksi</label>
              <input type="number" class="form-control" name="jumlah_trx" id="jumlah_trx" placeholder="jumlah transaksi">
            </div>
            <div class="form-group">
              <label for="photo">Upload Foto</label>
              <input type="file" class="form-control" name="photo" id="photo">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" el-event="add" id="btn-submit">Save changes</button>
      </div>
    </div>
  </div>
</div>
<script src="{{asset('js/event.js')}}"></script>
<script src="{{asset('js/transaksi.js')}}"></script>
@endsection

