function sortvideo() {
    event.stopPropagation();
    var sortlink = $("#sortlink option:selected").attr('value');
    var sorted = $('.gallery').attr('value');
    var nid = $('.profile').attr('value');
    var url = 'index?id=' + id;
    var id = $('.gallery').attr('id');
    geturl(id, url);

    $.ajax({
      type: 'get',
      url: 'ajax',
      data: {
        phpget: 1,
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

function loadvideo() {
    event.stopPropagation();
    var id = $("#loc option:selected").attr('value');
    var nid = $('.profile').attr('value');
    var url = 'index?id=' + id;
    geturl(id, url);

    $.ajax({
      type: 'get',
      url: 'ajax',
      data: {
        phpget: 1,
        id: id,
        nid: nid
      },

      success: function(data) {
        $(".files").html(data);
      }
    });
}
