$("document").ready(function(){
  var accessToken =  window.Laravel.api_token;
  $.ajax({
    url: window.Laravel.app_url + "/api/user/get-list",
    type: "GET",
    dataType: "json",
    headers: {"Authorization": "Bearer " + accessToken},
    crossDomain: true,
    beforeSend: function( xhr ) { 
      $('.preloader').show();
    },
    success: function(data, textStatus, xhr) {
      $('.preloader').hide();
      successLoadUser(data);
    },
  });

  $("#btn-submit").click(function(){
    var event = $("#user-modal #btn-submit").attr("el-event");
    var data = new FormData($("#user-form")[0]);
    data.append("_token", window.Laravel.csrfToken);

    $.ajax({
      url: window.Laravel.app_url + "/api/user/" + event + "",
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
        $("table[data-model='user']").closest("div[id='user']").find("a[el-event='search-data']").click();
        $("#user-modal").modal("hide");
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

  $("#user-modal").on("show.bs.modal", function(e) {
    var invoker = $(e.relatedTarget);

    if(invoker.attr('el-event') == 'edit') {
      var dataJSON = invoker.attr("data-json");
      var dataJSON = JSON.parse(dataJSON);

      $("#user-form").find("input[name=id]").val(dataJSON.id);
      $("#user-modal #btn-submit").attr("el-event", "edit");
      $("#user-form").find("textarea[name=content]").summernote("code", dataJSON.content);
      
      bindToForm($("#user-modal"), dataJSON);
      
    } else {
      $("#user-form").find("input[name=id]").val(null);
      $("#user-modal #btn-submit").attr("el-event", "add");
      $("#user-form").find("textarea[name=content]").summernote("code", "");
      resetForm("#user-form");
    }
  });
});

var successLoadUser = (function(responses, dataModel) {
  var tableRows = "";
  var responses = responses.responses == undefined ? responses : responses.responses;

  for(var i = 0; i < responses.data.length; i++) {
    id = responses.data[i].id;
    nama = responses.data[i].nama;
    no_customer = responses.data[i].no_customer;
    no_tlp = responses.data[i].no_tlp;
    tanggal_lahir = responses.data[i].tanggal_lahir;
    alamat = responses.data[i].alamat;
    data_json = responses.data[i].data_json;

    tableRows += "<tr>" +
                   "<td>"+ (i + 1) +"</td>"+
                   "<td>"+ no_customer +"</td>"+
                   "<td>"+ nama +"</td>"+
                   "<td>"+ tanggal_lahir +"</td>"+
                   "<td>"+ no_tlp +"</td>"+
                   "<td>"+ alamat +"</td>"+
                   "<td align='center'>"+
                     "<div class='btn-group'>"+
                       "<a class='btn btn-success btn-xs' href='#' el-event='edit' data-json='"+ data_json +"' data-toggle='modal' data-target='#user-modal'><i class='fas fa-pencil-alt'></i></a>"+
                       "<a class='btn btn-danger btn-xs btn-delete' href='#' el-event='edit' data-id='"+ id +"'><i class='fas fa-trash-alt'></i></a>"+
                     "</div>"+
                   "</td>"+
                 "</tr>";
  }

  if(!tableRows) {
    tableRows += "<tr>" +
                 "</tr>";
  }

  $("#table-user tbody").html(tableRows);
  paginate(responses, 'user');
  $(".preloader").hide();

  $(".btn-delete").click(function(){
    var id = $(this).attr("data-id");
    var confirms =  confirm("Are You sure want to delete this file!");
    var accessToken =  window.Laravel.api_token;

    if(confirms) {
      $.ajax({
        url: window.Laravel.app_url + "/api/user/delete",
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
          $("table[data-model='user']").closest("div[id='user']").find("a[el-event='search-data']").click();
          $('.preloader').hide();
        },
      });
    }
  })
});
