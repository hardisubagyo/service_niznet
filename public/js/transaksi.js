$("document").ready(function(){
  var accessToken =  window.Laravel.api_token;
  $.ajax({
    url: window.Laravel.app_url + "/api/transaksi/get-list",
    type: "GET",
    dataType: "json",
    headers: {"Authorization": "Bearer " + accessToken},
    crossDomain: true,
    beforeSend: function( xhr ) { 
      $('.preloader').show();
    },
    success: function(data, textStatus, xhr) {
      $('.preloader').hide();
      successLoadtransaksi(data);
    },
  });

  $("#btn-submit").click(function(){
    var event = $("#transaksi-modal #btn-submit").attr("el-event");
    var data = new FormData($("#transaksi-form")[0]);
    data.append("_token", window.Laravel.csrfToken);

    $.ajax({
      url: window.Laravel.app_url + "/api/transaksi/" + event + "",
      type: "POST",
      dataType: "json",
      data: data,
      processData: false,
      contentType: false,
      headers: {"Authorization": "Bearer " + accessToken},
      crossDomain: true,
      beforeSend: function( xhr ) {
        $('.loader').show();
      },
      success: function(datas, textStatus, xhr) {
        alert('Data berhasil di simpan');
        $("table[data-model='transaksi']").closest("div[id='transaksi']").find("a[el-event='search-data']").click();
        $("#transaksi-modal").modal("hide");
      },
      error: function(datas, textStatus, xhr) {
        msgError = "";
        for(var item in datas.responseJSON.errors) {
          msgError += datas.responseJSON.errors[item][0] + "*";
        }
        alert(msgError);
      }
    });
  })

  $("#transaksi-modal").on("show.bs.modal", function(e) {
    var invoker = $(e.relatedTarget);

    if(invoker.attr('el-event') == 'edit') {
      var dataJSON = invoker.attr("data-json");
      var dataJSON = JSON.parse(dataJSON);

      $("#transaksi-form").find("input[name=id]").val(dataJSON.id);
      $("#transaksi-form #img-name").text(dataJSON.filename);
      $("#transaksi-modal #btn-submit").attr("el-event", "edit");
      $("#transaksi-form").find("textarea[name=content]").summernote("code", dataJSON.content);
      
      bindToForm($("#transaksi-modal"), dataJSON);
      
    } else {
      $("#transaksi-form").find("input[name=id]").val(null);
      $("#transaksi-modal #btn-submit").attr("el-event", "add");
      $("#transaksi-form").find("textarea[name=content]").summernote("code", "");
      resetForm("#transaksi-form");
    }
  });
});

var successLoadtransaksi = (function(responses, dataModel) {
  var tableRows = "";
  var responses = responses.responses == undefined ? responses : responses.responses;

  for(var i = 0; i < responses.data.length; i++) {
    id = responses.data[i].id;
    no_trx = responses.data[i].no_trx;
    tgl_trx = responses.data[i].tgl_trx;
    nama_tenan = responses.data[i].nama_tenan;
    nama_customer = responses.data[i].nama_customer;
    jumlah_trx = responses.data[i].jumlah_trx;
    photo = responses.data[i].photo;
    data_json = responses.data[i].data_json;

    tableRows += "<tr>" +
                   "<td>"+ (i + 1) +"</td>"+
                   "<td>"+ no_trx +"</td>"+
                   "<td>"+ tgl_trx +"</td>"+
                   "<td>"+ nama_tenan +"</td>"+
                   "<td>"+ nama_customer +"</td>"+
                   "<td>"+ jumlah_trx +"</td>"+
                   "<td><img class='img-fluid' width='50px' src='"+ window.Laravel.app_url + "/uploads/photo/" + photo +"'></img></td>"+
                   "<td align='center'>"+
                     "<div class='btn-group'>"+
                       "<a class='btn btn-success btn-xs' href='#' el-event='edit' data-json='"+ data_json +"' data-toggle='modal' data-target='#transaksi-modal'><i class='fas fa-pencil-alt'></i></a>"+
                       "<a class='btn btn-danger btn-xs btn-delete' href='#' el-event='edit' data-id='"+ id +"'><i class='fas fa-trash-alt'></i></a>"+
                     "</div>"+
                   "</td>"+
                 "</tr>";
  }

  if(!tableRows) {
    tableRows += "<tr>" +
                 "</tr>";
  }

  $("#table-transaksi tbody").html(tableRows);
  paginate(responses, 'transaksi');
  $(".preloader").hide();

  $(".btn-delete").click(function(){
    var id = $(this).attr("data-id");
    var confirms =  confirm("Are You sure want to delete this file!");
    var accessToken =  window.Laravel.api_token;

    if(confirms) {
      $.ajax({
        url: window.Laravel.app_url + "/api/transaksi/delete",
        type: "POST",
        dataType: "json",
        data:"id"+"="+id,
        headers: {"Authorization": "Bearer " + accessToken},
        crossDomain: true,
        beforeSend: function( xhr ) { 
          $('.preloader').show();
        },
        success: function(data, textStatus, xhr) {
          alert('Data berhasil di hapus');
          $("table[data-model='transaksi']").closest("div[id='transaksi']").find("a[el-event='search-data']").click();
          $('.preloader').hide();
        },
      });
    }
  })
});
