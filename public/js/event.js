function doAjax(url, data, dataType, method, beforeSend, success, error) {
  var accessToken =  window.Laravel.api_token;
  $.ajax({
    cache: false,
    url: url,
    data: data,
    headers: {"Authorization": "Bearer " + accessToken},
    dataType: dataType,
    method: method,
    processData: false,
    contentType: false,
    beforeSend: function( xhr ) {
      $('.preloader').show();
    },
    success: success,
    error: error
  });
}

function beforeSendLoadTable(el) {
  return function() {
    $(".preloader").show();
  }
}

function setDdMenuOpen(el) {
  if(el) {
    el.on("show.bs.dropdown", function() {
      var leftPos = $(this).closest(".table-responsive").scrollLeft();

      $(this).closest(".table-responsive").animate({scrollLeft: leftPos + 400}, 800);
      $(this).closest(".table-responsive").find(".dropdown-menu").attr("style", "position: sticky !important; float: none;");
      $(this).closest(".table-responsive").find(".btn-group > .btn").attr("style", "float: none;");
    });
  }
}

$(document).on("click", "a[el-event='search-data']", function(event) {
  event.preventDefault();
  
  var invoker = $(this);
  var dataModel = invoker.attr("data-model");

  $("#" + dataModel + "-form .alert").hide();
  $("div#" + dataModel + " .alert").hide();

  var requestURL = invoker.attr("request-url");

  if(requestURL == undefined) {
    requestURL = $("table[data-model='" + dataModel + "']").attr("request-url");
  }

  var url = requestURL + "/get-list";
  var apiRoute = invoker.attr("api-route");

  if(apiRoute == undefined) {
    apiRoute = $("table[data-model='" + dataModel + "']").attr("api-route");
  }

  if(apiRoute !== undefined) {
    url = requestURL + "/" + apiRoute;
  }

  var onSuccessLoad = invoker.attr("on-success-load");

  if(onSuccessLoad == undefined) {
    onSuccessLoad = $("table[data-model='" + dataModel + "']").attr("on-success-load");
  }

  var onError = invoker.attr("on-error");

  if(onError == undefined) {
    onError = $("table[data-model='" + dataModel + "']").attr("on-error");
  }

  var data = new FormData();
  var searchCols = invoker.attr("search-cols");

  if(searchCols == undefined) {
    searchCols = $("table[data-model='" + dataModel + "']").attr("search-cols");
  }

  var searchValue = $("input[name='search_value'][data-model='" + dataModel + "']").val();

  if(searchValue == undefined) {
    searchValue = "";
  }

  var searchDateCols = invoker.attr("date-cols");

  if(searchDateCols == undefined) {
    searchDateCols = $("table[data-model='" + dataModel + "']").attr("date-cols");
  }

  var searchDateValue = $("input[name='search_date_value'][data-model='" + dataModel + "']").val();
  var page = invoker.attr("page");

  if(page == undefined) {
    page = $("table[data-model='" + dataModel + "']").attr("page");
  }

  var filterData = invoker.attr("filter-data");

  if(filterData == undefined) {
    filterData = $("table[data-model='" + dataModel + "']").attr("filter-data");
  }

  if(filterData) {
    var filterData = filterData.split(";");

    for(var i = 0; i < filterData.length; i++) {
      var filter = filterData[i].split(":");

      data.append("filter_data[" + filter[0] + "][]", filter[1]);
    }
  }

  var filterId = $("table[data-model='" + dataModel + "']").attr("filter-id");

  if(filterId) {
    var field = dataModel.replace(/-/g, "_").replace("_approval", "") + ".id";

    data.append("filter_data[" + field + "][]", filterId);
  }

  var filterDataId = $("table[data-model='" + dataModel + "']").attr("filter-data-id");

  if(filterDataId) {
    var filterDataId = filterDataId.split(",");

    for(var i = 0; i < filterDataId.length; i++) {
      var filter = filterDataId[i].split(":");

      data.append("filter_data_id[" + filter[0] + "]", filter[1]);
    }
  }

  var sortColumn = invoker.attr("sort-col");

  if(sortColumn == undefined) {
    sortColumn = $("table[data-model='" + dataModel + "']").attr("sort-col");
  }

  var sortOrder = invoker.attr("sort-order");

  if(sortOrder == undefined) {
    sortOrder = $("table[data-model='" + dataModel + "']").attr("sort-order");
  }

  data.append("_method", "GET");
  data.append("api_token", window.Laravel.api_token);
  data.append("page", page);
  data.append("where_field", searchCols);
  data.append("where_value", searchValue);

  if(searchDateCols !== undefined) {
    data.append("where_date_field", searchDateCols);
    data.append("where_date_value", searchDateValue);
  }

  data.append("order_by", sortColumn);
  data.append("order_ascdesc", sortOrder);

  var success = (function(response) {
    if(response.status !== false) {
      if(onSuccessLoad !== undefined) {
        window[onSuccessLoad](response.responses, dataModel);

        setDdMenuOpen($("table[data-model='" + dataModel + "'] .btn-group"));
      }

      if($("table[data-model='" + dataModel + "']").html() !== undefined) {
        $("table[data-model='" + dataModel + "']").attr("search-val", searchValue);

      } else {
        invoker.attr("search-val", searchValue);

        $("div[data-model=" + dataModel + "]").attr("search-val", searchValue);
      }

      $("#" + dataModel + "-download-form input[name='where_value']").val(searchValue);
      $("#" + dataModel + "-download-form input[name='where_date_value']").val(searchDateValue);

      $(".preloader").hide();

      if($("table[data-model='" + dataModel + "']").attr("filter-id")) {
        $("a[data-model='" + dataModel + "'][modal-event='edit']").click();
      }

    } else {
      $("#" + dataModel + "-form .alert").show().addClass("alert-danger");
      $("#" + dataModel + "-form .alert").html(response.message);
    }

    $("table[data-model='" + dataModel + "']").removeAttr("filter-id");
  });

  var error = (function(response) {
    $("div#" + dataModel + " .alert").show().addClass("alert-danger");
    $("div#" + dataModel + " .alert").html(response.responseJSON.message);
    $(".preloader").hide();

    if(onError !== undefined) {
      window[onError](response.responses, dataModel);
    }

    $("table[data-model='" + dataModel + "']").removeAttr("filter-id");
  });

  doAjax(url, data, "json", "post", beforeSendLoadTable($(this)), success, error);

  return false;
});

function bindToForm(formModal, formData, homeCheckIn, homeCheckOut) {
  $(formModal).find("form").find(":input").each(function() {
    if($(this).attr("name") == "api_token" || this.nodeName == "BUTTON") {}
    else {
      if(formData[$(this).attr("name")] == null && $(this).attr("name") !== "receipt_photo[]") {}
      else if(this.nodeName === "SELECT") {
        if(homeCheckIn != null && $(this).attr("name") == "id_office_location_in") {
          var elValue = 'home-' + formData[$(this).attr("name")];
        } else if(homeCheckOut != null && $(this).attr("name") == "id_office_location_out") {
          var elValue = 'home-' + formData[$(this).attr("name")];
        } else {
          var elValue = formData[$(this).attr("name")];

          if(this.hasAttribute("multiple") && elValue) {
            elValue = elValue.split(",");
          }
        }

        $(this).val(elValue).trigger("change");
        $(this).val(elValue).trigger("select2:select");
      } else {
        if($(this).hasClass("select2-search__field")) {}
        else if($(this).hasClass("wysihtml5")) {
          $(formModal).find("form").find(".wysihtml5").summernote("code", formData[$(this).attr("name")]);

        } else if($(this).attr("type") == "radio") {
          $(formModal).find("form").find("input[type=radio][name=" + $(this).attr("name") + "][value='" + formData[$(this).attr("name")] + "']").iCheck("check");

        } else if($(this).attr("type") == "file") {
          if($(this).attr("dlink") !== undefined && $(this).attr("dlink") !== false) {
            if(formData[$(this).attr("name")]) {
              var splitFile = formData[$(this).attr("name")].split(", ");
              var tmpFile = "";
              var dlink = $(this).attr("dlink");

              if(splitFile.length > 1) {
                splitFile.forEach(function(element) {
                  tmpFile += "<a href='" + dlink + formData.id + '/' + element + "'><label><i class='fa fa-file-o'></i> <span>" + element + "</span></label></a>";
                });

                $(".current_file-" + $(this).attr("name")).html(tmpFile);

              } else {
                if(formData[$(this).attr("name")]) {
                  $(".current_file-" + $(this).attr("name"))
                    .html("<a href='" + $(this).attr("dlink") + formData.id + "'><label><i class='fa fa-file-o'></i> <span>" + formData[$(this).attr("name")] + "</span></label></a>");
                }
              }
            }
          }

        } else {
          if($(this).hasClass("auto-numeric")) {
            var value = formData[$(this).attr("name")];

            this.value = parseInt(value);

          } else if($(this).hasClass("display-multiple-path-files")) {
            if(formData[$(this).attr("name")] !== null) {
              $(".current_file-" + $(this).attr("name")).html(decodeURIComponent(formData[$(this).attr("name")]).replace(/\+/g, " "));
            }

          } else if($(this).attr("data-date-format")) {
            this.value = moment(formData[$(this).attr("name")], "YYYY-MM-DD").format($(this).attr("data-date-format"));

            $(this).data("daterangepicker").setStartDate(this.value);

          } else {
            this.value = formData[$(this).attr("name")];
          }
        }
      }

      if($(this)[0].hasAttribute("data-disable-on-edit") || $(this)[0].hasAttribute("disabled")) {
        $(this).attr("disabled", "disabled");
      }
    }
  });

  $(formModal).find("form").find("img").each(function() {
    $(this).attr("src", formData[$(this).attr("name")]);
  });
}

function resetForm(form) {
  $(form)[0].reset();
  $(form).find(".select2").val("").trigger("change");
  $(form).find(".wysihtml5").summernote("code", "");
  $(form).find(".current_file").html("");
}

function paginate(responses, dataModel) {
  var paginate = "";
  var infoTable = "";

  if(responses.total) {
    paginate += responses.current_page === 1 ? "<li class='page-item disabled'><span class='page-link'>&laquo;</span></li>"
                : "<li class='page-item'><a class='page-link' href='#' rel='prev' page='1' el-event='show-page'>&laquo;</a></li>";

    var lastPage = responses.last_page > 10 ? 10 : responses.last_page;

    for(var i = 1; i <= lastPage; i++) {
      if(i == 3 && responses.current_page > 6 && responses.last_page > 10) {
        paginate += "<li class='page-item disabled'><span class='page-link'>...</span></li>";
      }

      if(responses.last_page > 10 && ((i >= 3 && (responses.last_page - responses.current_page) <= 5) || i >= 9)) {
        paginate += responses.current_page === (responses.last_page - (10 - i)) ? "<li class='page-item active'><span class='page-link'>" + (responses.last_page - (10 - i)) + "</span></li>"
                    : "<li class='page-item'><a class='page-link' href='#' page='" + (responses.last_page - (10 - i)) + "'' el-event='show-page'>" + (responses.last_page - (10 - i)) + "</a></li>";

      } else if(responses.last_page > 10 && responses.current_page > 6 && i > 2 && i < 9) {
        if(i >= 3 && i <= 5) {
          paginate += "<li class='page-item'><a class='page-link' href='#' page='" + (responses.current_page - (6 - i)) + "' el-event='show-page'>" + (responses.current_page - (6 - i)) + "</a></li>";

        } if(i == 6) {
          paginate += "<li class='page-item active'><span class='page-link'>" + responses.current_page + "</span></li>";

        } if(i >= 6 && i <= 8) {
          paginate += "<li class='page-item'><a class='page-link' href='#' page='" + (responses.current_page + (i - 5)) + "' el-event='show-page'>" + (responses.current_page + (i - 5)) + "</a></li>";
        }

      } else {
        paginate += (responses.current_page === i) ? "<li class='page-item active'><span class='page-link'>" + i + "</span></li>"
                    : "<li class='page-item'><a class='page-link' href='#' page='" + i + "' el-event='show-page'>" + i + "</a></li>";
      }

      if(i == 8 && responses.last_page > 10 && (responses.last_page - responses.current_page) > 5) {
        paginate += "<li class='page-item disabled'><span class='page-link'>...</span></li>";
      }
    }

    paginate += responses.next_page_url ? "<li class='page-item' ><a class='page-link' href='#' rel='next' page='" + responses.last_page + "' el-event='show-page'>&raquo;</a></li>"
                : "<li class='disabled'><span class='page-link'>&raquo;</span></li>";

    infoTable += "showing" + " " + "row" + " " + responses.from + " ";
    infoTable += "to" + " " + responses.to + " ";
    infoTable += "from total" + " " + responses.total + " ";
    infoTable += "rows" + " (" + responses.last_page + " " + "pages" + ")";

  } else {
    infoTable = "no data available";
  }

  $("div[id='" + dataModel + "']").find(".pagination").html(paginate);
  $("div[id='" + dataModel + "']").find(".page-into").html(infoTable);
}

$(document).on("click", "a[el-event='show-page']", function(event) {
  event.preventDefault();

  var invoker = $(this);
  var dataModel = invoker.closest(".card").attr("id");
  var requestURL = $("table[data-model='" + dataModel + "']").attr("request-url");
  var url = requestURL + "/get-list";
  var data = new FormData();
  var page = invoker.attr("page");
  var onSuccessLoad = $("table[data-model='" + dataModel + "']").attr("on-success-load");

  data.append("_method", "GET");
  data.append("api_token", window.Laravel.api_token);
  data.append("page", page);

  var success = (function(response) {
    if(response.status !== false) {
      if(onSuccessLoad !== undefined) {
        console.log(response.responses)
        window[onSuccessLoad](response, dataModel);

        setDdMenuOpen($("table[data-model='" + dataModel + "'] .btn-group"));
      }

      $.getScript(requestURL + "/../../js/cellFormat.js");

      $("table[data-model='" + dataModel + "']").attr("page", page);
      $("div[data-model='" + dataModel + "']").attr("page", page);
      $(".preloader").hide();

    } else {
      $("#" + dataModel + "-form .alert").show().addClass("alert-danger");
      $("#" + dataModel + "-form .alert").html(response.message);
    }
  });

  doAjax(url, data, "json", "post", beforeSendLoadTable(invoker), success, null);

  return false;
});
