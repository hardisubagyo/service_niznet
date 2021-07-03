@extends('layouts.app')
@section('content')
<div class="content-header">
</div>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card" id="tenan">
          <div class="card-header">
            <h3 class="card-title">Tenan Table</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-block bg-gradient-primary btn-sm" data-toggle="modal" data-target="#tenan-modal">add</button>
            </div>
          </div>
          <div class="card-body">
            <div class="input-group mb-3">
              <input type="text" class="form-control has-primary" name="search_value" data-model="tenan" placeholder="Search Key">
              <div class="input-group-append">
                <a type="button" class="input-group-text btn btn-primary btn-border btn-flat" el-event="search-data" data-model="tenan"><i class="fa fa-search"></i></a>
              </div>
            </div>
            <table class="table table-bordered" id="table-tenan" data-model="tenan" request-url="{{ route('api-tenan') }}" on-success-load="successLoadtenan">
              <thead>                  
                <tr>
                  <th style="width: 10px">#</th>
                  <th>No Tenan</th>
                  <th>Nama</th>
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
<div class="modal fade" id="tenan-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add tenan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form role="form" id="tenan-form">
          <input type="hidden" name="id" id="id">
          <div class="card-body">
            <div class="form-group">
              <label for="no_tenan">No Tenan</label>
              <input type="text" class="form-control" name="no_tenan" id="no_tenan" placeholder="no_tenan">
            </div>
            <div class="form-group">
              <label for="nama">Nama</label>
              <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama">
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
<script src="{{asset('js/tenan.js')}}"></script>
@endsection

