function popup(btn) {
  var share = ($(btn).attr("value"));

  $.ajax({
    type: 'post',
    url: '../req/ajax',
    data: {
      phpfunc: 18,
      share: share
    },

    success: function(data) {
      var modal = document.querySelector(".folderPopup");
      modal.classList.toggle("show-modal");
      $(".icont").html(data);
    }
  });

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


//pdf lightbox
function popupdf(pdfmodal) {
  var pdfm = ($(pdfmodal).attr("value"));

  $.ajax({
    type: 'post',
    url: '../req/ajax',
    data: {
      phpfunc: 20,
      pdfm: pdfm
    },

    success: function(data) {
      var modal = document.querySelector(".folderPopup");
      modal.classList.toggle("show-modal");
      $(".icont").html(data);
    }
  });
}


//rename modal
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

//rename files
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
      infoBarShow();
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


//load listview
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


//load gridview
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


//return listview
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


//return gridview
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


//return mobileview
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


//listview hierarchy
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


//gridview hierarchy
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


//display listview
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

//display gridview
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



//delete files
function deletemulti() {
    $('#deletebtn').mousedown(function() {

    var deleted = $(".deletebox input:checkbox:checked").map(function(){
      return $(this).val();
    }).get();

    var nid = $('.profile').attr('value');
    var sorted = $('.listview').attr('value');
    var id = $(this).attr("id");
    var url = 'folders?id=' + id;
    geturl(id, url);
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

//sort links
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

//load Location
function loadlocation() {
    event.stopPropagation();
    var id = $("#loc option:selected").attr('value');
    var nid = $('.profile').attr('value');
    var url = 'photos?id=' + id;
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


//sort photos
function sortphotos() {
    event.stopPropagation();
    var sortlink = $("#sortlink option:selected").attr('value');
    var sorted = $('.gallery').attr('value');
    var nid = $('.profile').attr('value');
    var url = 'photos?id=' + id;
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


//empty trash
function emptytrash() {
    event.stopPropagation();
    var nid = $('.profile').attr('value');
    var id = 'drives';
    var url = 'trash?id=' + id;
    geturl(id, url);

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 22,
        nid: nid
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 21,
        nid: nid
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 23,
        nid: nid
      },

      success: function(data) {
        $(".progress").html(data);
      }
    });
}


//restore files
function restoreFiles() {
    event.stopPropagation();
    var nid = $('.profile').attr('value');
    var trash = $('#restore').attr('value');
    var id = 'drives';
    var url = 'trash?id=' + id;
    geturl(id, url);

    var selected = [];
    $("input[name='selected[]']:checked").each(function () {
      selected.push(this.value);
    });

    $.ajax({
      type: 'post',
      url: '../req/ajax',
      data: {
        phpfunc: 24,
        nid: nid,
        selected: selected,
        trash: trash
      },

      success: function(data) {
        console.log(selected)
        //$(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 22,
        nid: nid
      },

      success: function(data) {
        $(".files").html(data);
      }
    });
}


//delete list
function deleteList() {
    event.stopPropagation();
    var trash = $('#del').attr('value');
    var nid = $('.profile').attr('value');
    var sorted = $('.listview').attr('value');
    var id = $('.listview').attr('id');
    var url = 'folders?id=' + id;
    geturl(id, url);

    var selected = [];
    $("input[name='selected[]']:checked").each(function () {
      selected.push(this.value);
    });

    $.ajax({
      type: 'post',
      url: '../req/ajax',
      data: {
        phpfunc: 24,
        nid: nid,
        selected: selected,
        trash: trash
      },

      success: function(data) {
        console.log(selected)
        //$(".files").html(data);
      }
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
        phpget: 25,
        nid: nid,
        id:id
      },

      success: function(data) {
        $(".latestwrap").html(data);
      }
    });
}


//delete grid
function deleteGrid() {
    event.stopPropagation();
    var trash = $('#del').attr('value');
    var nid = $('.profile').attr('value');
    var sorted = $('.gridview').attr('value');
    var id = $('.gridview').attr('id');
    var url = 'folders?id=' + id;
    geturl(id, url);

    var selected = [];
    $("input[name='selected[]']:checked").each(function () {
      selected.push(this.value);
    });

    $.ajax({
      type: 'post',
      url: '../req/ajax',
      data: {
        phpfunc: 24,
        nid: nid,
        selected: selected,
        trash: trash
      },

      success: function(data) {
        console.log(selected)
        //$(".files").html(data);
      }
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

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 25,
        nid: nid,
        id:id
      },

      success: function(data) {
        $(".latestwrap").html(data);
      }
    });
}


//delete from trash
function trashDelete() {
    event.stopPropagation();
    var nid = $('.profile').attr('value');
    var id = 'drives';
    var url = 'trash?id=' + id;
    geturl(id, url);

    var selected = [];
    $("input[name='selected[]']:checked").each(function () {
      selected.push(this.value);
    });

    $.ajax({
      type: 'post',
      url: '../req/ajax',
      data: {
        phpfunc: 26,
        nid: nid,
        selected: selected
      },

      success: function(data) {
        console.log(selected)
        //$(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 22,
        nid: nid
      },

      success: function(data) {
        $(".files").html(data);
      }
    });

    $.ajax({
      type: 'get',
      url: '../req/ajax',
      data: {
        phpget: 23,
        nid: nid
      },

      success: function(data) {
        $(".progress").html(data);
      }
    });
}

function modal() {
  var modal = document.querySelector("#folderPopup");
  modal.classList.toggle("show-modal");
}
