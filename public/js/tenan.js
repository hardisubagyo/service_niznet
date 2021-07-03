$("document").ready(function(){
  var accessToken =  window.Laravel.api_token;
  $.ajax({
    url: window.Laravel.app_url + "/api/tenan/get-list",
    type: "GET",
    dataType: "json",
    headers: {"Authorization": "Bearer " + accessToken},
    crossDomain: true,
    beforeSend: function( xhr ) { 
      $('.preloader').show();
    },
    success: function(data, textStatus, xhr) {
      $('.preloader').hide();
      successLoadtenan(data);
    },
  });

  $("#btn-submit").click(function(){
    var event = $("#tenan-modal #btn-submit").attr("el-event");
    var data = new FormData($("#tenan-form")[0]);
    data.append("_token", window.Laravel.csrfToken);

    $.ajax({
      url: window.Laravel.app_url + "/api/tenan/" + event + "",
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
        $("table[data-model='tenan']").closest("div[id='tenan']").find("a[el-event='search-data']").click();
        $("#tenan-modal").modal("hide");
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

  $("#tenan-modal").on("show.bs.modal", function(e) {
    var invoker = $(e.relatedTarget);

    if(invoker.attr('el-event') == 'edit') {
      var dataJSON = invoker.attr("data-json");
      var dataJSON = JSON.parse(dataJSON);

      $("#tenan-form").find("input[name=id]").val(dataJSON.id);
      $("#tenan-modal #btn-submit").attr("el-event", "edit");
      $("#tenan-form").find("textarea[name=content]").summernote("code", dataJSON.content);
      
      bindToForm($("#tenan-modal"), dataJSON);
      
    } else {
      $("#tenan-form").find("input[name=id]").val(null);
      $("#tenan-modal #btn-submit").attr("el-event", "add");
      $("#tenan-form").find("textarea[name=content]").summernote("code", "");
      resetForm("#tenan-form");
    }
  });
});

var successLoadtenan = (function(responses, dataModel) {
  var tableRows = "";
  var responses = responses.responses == undefined ? responses : responses.responses;

  for(var i = 0; i < responses.data.length; i++) {
    id = responses.data[i].id;
    nama = responses.data[i].nama;
    no_tenan = responses.data[i].no_tenan;
    data_json = responses.data[i].data_json;

    tableRows += "<tr>" +
                   "<td>"+ (i + 1) +"</td>"+
                   "<td>"+ no_tenan +"</td>"+
                   "<td>"+ nama +"</td>"+
                   "<td align='center'>"+
                     "<div class='btn-group'>"+
                       "<a class='btn btn-success btn-xs' href='#' el-event='edit' data-json='"+ data_json +"' data-toggle='modal' data-target='#tenan-modal'><i class='fas fa-pencil-alt'></i></a>"+
                       "<a class='btn btn-danger btn-xs btn-delete' href='#' el-event='edit' data-id='"+ id +"'><i class='fas fa-trash-alt'></i></a>"+
                     "</div>"+
                   "</td>"+
                 "</tr>";
  }

  if(!tableRows) {
    tableRows += "<tr>" +
                 "</tr>";
  }

  $("#table-tenan tbody").html(tableRows);
  paginate(responses, 'tenan');
  $(".preloader").hide();

  $(".btn-delete").click(function(){
    var id = $(this).attr("data-id");
    var confirms =  confirm("Are You sure want to delete this file!");
    var accessToken =  window.Laravel.api_token;

    if(confirms) {
      $.ajax({
        url: window.Laravel.app_url + "/api/tenan/delete",
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
          $("table[data-model='tenan']").closest("div[id='tenan']").find("a[el-event='search-data']").click();
          $('.preloader').hide();
        },
      });
    }
  })
});
