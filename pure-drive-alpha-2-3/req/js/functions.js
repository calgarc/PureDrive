$("html").disableSelection();

//Selected files
function sel() {
  $(".deletebox").change(function() {
    if ($(this).prop('checked') == true) {
      $(this).parent().parent().addClass("highlight");

    } else if ($(this).prop('checked') == false) {
      $(this).parent().parent().removeClass("highlight");
    };
  });
}

function selectall() {
  $(".selectall").change(function() {
    if ($(this).prop('checked') == true) {
      $(".selector").find(".deletebox").prop("checked", true);
      $(".deletebox").parent().parent().addClass("highlight");

    } else if ($(this).prop('checked') == false) {
      $(".selector").find(".deletebox").prop("checked", false);
      $(".deletebox").parent().parent().removeClass("highlight");
    };
  });
}

//auto scale file names
function resizeInput() {
  $(this).attr('size', $(this).val().length);
}

$('input[type="text"]')
  // event handler
  .keyup(resizeInput)
  // resize on page load
  .each(resizeInput);

//submit
function submit() {
  $("#delete").submit();
}

// // protect
// $( document ).ajaxStart(function() {
//   var id = $(this).attr('value');
//   var nid = $('.profile').attr('value');
//   var url = 'folders?id=' + id;
//   var sorted = $('.listview').attr('value');
//   var src = $('.loggedin').attr('src');
//   geturl(id, url);
// 
//   $.ajax({
//     type: 'get',
//     url: '../req/ajax',
//     data: {
//       phpget: 17,
//       id: id,
//       nid:nid,
//       src: src
//     },
// 
//     success: function(data) {
//       $(".profiledown").html(data);
//     }
//   });
//   $( "#aniout" ).hide();
//   
// });

//infobar
function infoBar() {
  //var x = document.getElementById("infocont");
  var y = document.getElementById("right");
  $('#infocont').animate({
    bottom: '0px'
  });
  
  y.style.padding = "25px 25px 325px 25px";
  $('.hup').css("display", "block");
  $('.hdown').css("display", "none");
}

function infoBar2() {
  $('#infocont').animate({
    bottom: '-280px'
  });
  
  var y = document.getElementById("right");
  y.style.padding = "25px";
  $('.hup').css("display", "none");
  $('.hdown').css("display", "block");
}

//share
$(".share").click(function() {
  alert($(this).attr("value"));
});

function popup(btn) {
  var share = ($(btn).attr("value"));

  $.ajax({
    type: 'post',
    url: '../req/ajax',
    data: {
      phpfunc: 0,
      share: share
    },

    success: function(data) {
      var modal = document.querySelector(".folderPopup");
      modal.classList.toggle("show-modal");
      $(".icont").html(data);
    }
  });

}

//close modal
function closemodal() {
  var modal = document.querySelector("#folderPopup");
  modal.classList.toggle("show-modal");
  wavesurfer.stop();
}

//lightbox
function popupc(imgmodal) {
  var imgm = ($(imgmodal).attr("value"));

  $.ajax({
    type: 'post',
    url: '../req/ajax',
    data: {
      phpfunc: 1,
      imgm: imgm
    },

    success: function(data) {
      var modal = document.querySelector(".folderPopup");
      modal.classList.toggle("show-modal");
      $(".icont").html(data);
    }
  });
}

//video lightbox
function popupv(videomodal) {
  var videom = ($(videomodal).attr("value"));

  $.ajax({
    type: 'post',
    url: '../req/ajax',
    data: {
      phpfunc: 2,
      videom: videom
    },

    success: function(data) {
      var modal = document.querySelector(".folderPopup");
      modal.classList.toggle("show-modal");
      $(".icont").html(data);
    }
  });
}

//audio lightbox
function popupa(audiomodal) {
  var audiom = ($(audiomodal).attr("value"));

  $.ajax({
    type: 'post',
    url: '../req/ajax',
    data: {
      phpfunc: 4,
      audiom: audiom
    },

    success: function(data) {
      var modal = document.querySelector(".folderPopup");
      modal.classList.toggle("show-modal");
      $(".icont").html(data);
    }
  });
}

function renamepopup(btn) {
  var name = $(btn).attr('value');
  var nid = $('.profile').attr('value');

  $.ajax({
    type: 'get',
    url: '../req/ajax',
    data: {
      phpget: 14,
      name: name,
      nid: nid
    },

    success: function(data) {
      var modal = document.querySelector(".folderPopup");
      modal.classList.toggle("show-modal");
      $(".icont").html(data);
    }
  });
}

function rnamefiles() {
  $('.renamebtn').click(function() {
    var salted = $('.modaltop').attr('id');
    var rename = $('#copied').val() + '-' + salted;
    var name = $(this).attr('value');
    var nid = $('.profile').attr('value');
    var id = $('.listview').attr('id');
    var url = 'folders?id=' + id;
    var sorted = $('.listview').attr('value');
    var sortlink = sorted;
    geturl(id, url);

    $.ajax({
      type: 'post',
      url: '../req/ajax',
      data: {
        phpfunc: 14,
        rename: rename,
        name: name,
        nid: nid
      },

      success: function(data) {
        var modal = document.querySelector(".folderPopup");
        modal.classList.toggle("show-modal");
        console.log(rename);
        console.log(name);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 7,
        id: id,
        nid: nid,
        sortlink: sortlink,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });
  });

}

//details
function infodet(info) {
  var detfile = ($(info).attr("value"));

  $.ajax({
    type: 'post',
    url: '../req/ajax',
    data: {
      phpfunc: 3,
      detfile: detfile
    },

    success: function(data) {
      $(".infodet").html(data);
      infoBar();
    }
  });
}

//search list
function searchl(search) {
  var query = $('input[name="srch"]').val();
  var nid = $('.profile').attr('value');
  var id = 'drives';
  var sorted = $('.listview').attr('value');

  $.ajax({
    type: 'post',
    url: '../req/ajax',
    data: {
      phpfunc: 5,
      query: query,
      nid: nid,
      sorted: sorted
    },

    success: function(data) {
      $(".files").html(data);
    }
  });

  $.ajax({
    type: 'get',
    url: '../req/ajax',
    data: {
      phpget: 9,
      id: id,
      nid: nid
    },

    success: function(data) {
      $(".hier").html(data);
    }
  });
}

//search grid
function searchg(search) {
  var query = $('input[name="srch"]').val();
  var nid = $('.profile').attr('value');
  var id = 'drives';
  var sorted = $('.listview').attr('value');

  $.ajax({
    type: 'post',
    url: '../req/ajax',
    data: {
      phpfunc: 6,
      query: query,
      nid: nid,
      sorted: sorted
    },

    success: function(data) {
      //$('#search').submit();
      console.log(query);
      $(".files").html(data);
    }
  });

  $.ajax({
    type: 'get',
    url: '../req/ajax',
    data: {
      phpget: 9,
      id: id,
      nid: nid
    },

    success: function(data) {
      $(".hier").html(data);
    }
  });
}


function geturl(page, url) {
  if (typeof(history.pushState) != "undefined") {
    var obj = {
      Page: page,
      Url: url
    };
    history.pushState(obj, obj.Page, obj.Url);
  }
}

//load directory
function loaddir() {
  $('.columnd').dblclick(function() {
    event.stopPropagation();
    var id = $(this).attr('value');
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    var sorted = $('.listview').attr('value');
    geturl(id, url);

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 7,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 9,
        id: id,
        nid: nid
      },

      success: function(data) {
        $(".hier").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 13,
        nid: nid,
        id: id
      },

      success: function(data) {
        $(".pag").html(data);
      }
    });

  });
}

function loadgrid() {
  $('.columng').dblclick(function() {
    var id = $(this).attr('value');
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    var sorted = $('.listview').attr('value');
    geturl(id, url);

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 8,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 9,
        id: id,
        nid: nid
      },

      success: function(data) {
        $(".hier").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 13,
        nid: nid,
        id: id
      },

      success: function(data) {
        $(".pag").html(data);
      }
    });
  });

  $('.columnd').dblclick(function() {
    var id = $(this).attr('value');
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    geturl(id, url);
    var sorted = $('.listview').attr('value');

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 8,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 9,
        id: id,
        nid: nid
      },

      success: function(data) {
        $(".hier").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 13,
        nid: nid,
        id: id
      },

      success: function(data) {
        $(".pag").html(data);
      }
    });
  });
}

//mobile grid
function loadmobilegrid() {
  $('.columng').click(function() {
    var id = $(this).attr('value');
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    geturl(id, url);
    var sorted = $('.listview').attr('value');

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 8,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 13,
        nid: nid,
        id: id
      },

      success: function(data) {
        $(".pag").html(data);
      }
    });
  });

  $('.columnd').click(function() {
    var id = $(this).attr('value');
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    geturl(id, url);

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 8,
        id: id,
        nid: nid
      },

      success: function(data) {
        $(".files").html(data);
      }
    });
  });
}

//return
function returnlist() {
  $('.columnd').dblclick(function() {
    event.stopPropagation();
    var id = 'drives';
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    geturl(id, url);
    var sorted = 'file_name';

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 7,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 9,
        id: id,
        nid: nid
      },

      success: function(data) {
        $(".hier").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 13,
        nid: nid,
        id: id
      },

      success: function(data) {
        $(".pag").html(data);
      }
    });

  });
}

function returngrid() {
  $('.columnd').dblclick(function() {
    event.stopPropagation();
    var id = 'drives';
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    geturl(id, url);
    var sorted = 'file_name';

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 8,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 9,
        id: id,
        nid: nid
      },

      success: function(data) {
        $(".hier").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 13,
        nid: nid,
        id: id
      },

      success: function(data) {
        $(".pag").html(data);
      }
    });

  });
}

function returnmobilegrid() {
  $('.columnd').click(function() {
    event.stopPropagation();
    var id = 'drives';
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    geturl(id, url);
    var sorted = 'file_name';

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 8,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 9,
        id: id,
        nid: nid
      },

      success: function(data) {
        $(".hier").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 13,
        nid: nid,
        id: id
      },

      success: function(data) {
        $(".pag").html(data);
      }
    });

  });
}

function listhier() {
  $('.returnbtn').click(function() {
    event.stopPropagation();
    var id = 'drives';
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    var sorted = $('.listview').attr('value');
    geturl(id, url);

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 7,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 9,
        id: id,
        nid: nid
      },

      success: function(data) {
        $(".hier").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 13,
        nid: nid,
        id: id
      },

      success: function(data) {
        $(".pag").html(data);
      }
    });

  });
}

function gridhier() {
  $('.returnbtn').click(function() {
    event.stopPropagation();
    var id = 'drives';
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    var sorted = $('.listview').attr('value');
    geturl(id, url);

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 8,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 9,
        id: id,
        nid: nid
      },

      success: function(data) {
        $(".hier").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 13,
        nid: nid,
        id: id
      },

      success: function(data) {
        $(".pag").html(data);
      }
    });

  });
}

//display files
function dispfiles() {
  $('.listlinks').click(function() {
    event.stopPropagation();
    var id = $(this).attr('value');
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    var sorted = $('.listview').attr('value');
    geturl(id, url);

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 7,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 9,
        id: id,
        nid: nid
      },

      success: function(data) {
        $(".hier").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 13,
        nid: nid,
        id: id
      },

      success: function(data) {
        $(".pag").html(data);
      }
    });

  });
}

function dispgridfiles() {
  $('.listlinks').click(function() {
    event.stopPropagation();
    var id = $(this).attr('value');
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    var sorted = $('.listview').attr('value');
    geturl(id, url);

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 8,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 9,
        id: id,
        nid: nid
      },

      success: function(data) {
        $(".hier").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 13,
        nid: nid,
        id: id
      },

      success: function(data) {
        $(".pag").html(data);
      }
    });

  });
}

//add to favorites
function addfav(btn) {
  var fav = $(btn).attr("value");
  var nid = $('.profile').attr('value');
  var id = $(btn).attr("id");
  var url = 'folders?id=' + id;
  var sorted = $('.listview').attr('value');
  geturl(id, url);

  $.ajax({
    type: 'post',
    url: '../req/ajax',
    data: {
      phpfunc: 10,
      fav: fav,
      sorted: sorted
    },

    success: function(data) {}
  });

  $.ajax({
    type: 'get',
    url: '../req/ajax',
    data: {
      phpget: 7,
      id: id,
      nid: nid,
      sorted: sorted
    },

    success: function(data) {
      $(".files").html(data);
    }
  });

  $.ajax({
    type: 'get',
    url: '../req/ajax',
    data: {
      phpget: 15,
      nid: nid
    },

    success: function(data) {
      $(".favs").html(data);
    }
  });
}

//delete files
function deletefiles() {
  $('.deletebtn').mousedown(function() {
    var deleted = $(this).attr("value");
    var nid = $('.profile').attr('value');
    var sorted = $('.listview').attr('value');
    var id = $(this).attr("id");
    var url = 'folders?id=' + id;
    geturl(id, url);

    $.ajax({
      type: 'post',
      url: '../req/ajax',
      data: {
        phpfunc: 11,
        nid: nid,
        deleted: deleted
      },

      success: function(data) {}
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 7,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 13,
        nid: nid,
        id: id
      },

      success: function(data) {
        $(".pag").html(data);
      }
    });
  });
}

//sort by
function sortby() {
  $('.deletebtn').mousedown(function() {
    var sortby = $(this).attr('value');
    var sorted = $('.listview').attr('value');
    var id = 'drives';
    var url = 'folders?id=' + id;
    geturl(id, url);

    $.ajax({
      type: 'post',
      url: '../req/ajax',
      data: {
        phpget: 7,
        id: id,
        sortby: sortby,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });
  });
}

function sortlink() {
  $('.sort').mousedown(function() {
    var sortlink = $(this).attr('value');
    var sortses = $(this).serialize();
    var sorted = $('.listview').attr('value');
    var nid = $('.profile').attr('value');
    var id = $('.listview').attr('id');
    var url = 'folders?id=' + id;
    var asc = $('.list').attr('id');
    geturl(id, url);

    $.ajax({
      type: 'post',
      url: '../req/ajax',
      data: {
        phpget: 7,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 7,
        id: id,
        nid: nid,
        sortlink: sortlink,
        sorted: sorted,
        asc: asc
      },

      success: function(data) {
        $(".files").html(data);
      }
    });
  });
}

//listview
function listview() {
  $('.btnlist').mousedown(function() {
    var disp = $(this).attr("value");
    var nid = $('.profile').attr('value');
    var id = 'drives';
    var url = 'folders?id=' + id;
    var sorted = $('.listview').attr('value');
    geturl(id, url);

    $.ajax({
      type: 'post',
      url: '../req/ajax',
      data: {
        phpfunc: 12,
        disp: disp,
        sorted: sorted
      },

      success: function(data) {}
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 7,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });
  });
}

//gridview
function gridview() {
  $('.btngrid').mousedown(function() {
    var disp = $(this).attr("value");
    var nid = $('.profile').attr('value');
    var id = 'drives';
    var url = 'folders?id=' + id;
    var sorted = $('.listview').attr('value');
    geturl(id, url);

    $.ajax({
      type: 'post',
      url: '../req/ajax',
      data: {
        phpfunc: 12,
        disp: disp,
        sorted: sorted,
        nid: nid
      },

      success: function(data) {}
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 8,
        id: id,
        nid: nid,
        sorted: sorted
      },

      success: function(data) {
        $(".files").html(data);
      }
    });
  });
}

// mobile menu
function mobilebar() {
  var x = document.getElementById("mobile");

  if (x.style.display === "block") {
    x.style.display = "none";
  } else {
    x.style.display = "block";
  }
}

//app dashboard
function opendash() {
  $('#dash').animate({
    top: '0%'
  });

  $('#dsh').css("display", "none");
  $('#undsh').css("display", "block");
  $('li').removeClass('active');
  $('#undsh').addClass('active');
}

function closedash() {
  $('#dash').animate({
    top: '100%'
  });

  $('#dsh').css("display", "block");
  $('#undsh').css("display", "none");
}

function loadlocation() {
    event.stopPropagation();
    var id = $("#loc option:selected").attr('value');
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    geturl(id, url);

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 16,
        id: id,
        nid: nid
      },

      success: function(data) {
        $(".files").html(data);
      }
    });
}

function sortphotos() {
    event.stopPropagation();
    var sortlink = $("#sortlink option:selected").attr('value');
    var sorted = $('.gallery').attr('value');
    var nid = $('.profile').attr('value');
    var url = 'folders?id=' + id;
    var id = $('.gallery').attr('id');
    geturl(id, url);

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 16,
        sortlink: sortlink,
        nid: nid,
        id: id,
        sorted: sorted
        
      },

      success: function(data) {
        $(".files").html(data);
      }
    });
}
