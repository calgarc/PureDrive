/*

Core UI functions
----------------------------------------------

1. GeneralUI
  1.1 disableSelection()
  1.2 resizeInput()
  1.3 sel()
  1.4 selectAll()
  1.5 navbar ajax linls
  1.6 infoBarShow()
  1.7 infoBarHide()
  1.8 closeModal()
  1.9 opendash()
  1.10 closedash()
  1.11 accordian()

2 Form UI
  2.1 uploadFiles()
  2.2 submit()
  2.3 copy()

*/

//disable html selection
$("html").disableSelection();


//auto scale file names
function resizeInput() {
  $(this).attr('size', $(this).val().length);
}

$('input[type="text"]')
  // event handler
  .keyup(resizeInput)
  // resize on page load
  .each(resizeInput);


//checkbox select files
function sel() {
  $(".deletebox").change(function() {
    if ($(this).prop('checked') == true) {
      $(this).parent().parent().addClass("highlight");

    } else if ($(this).prop('checked') == false) {
      $(this).parent().parent().removeClass("highlight");
    };
  });
}


//checkbox select all files
function selectAll() {
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


// navbar ajax links
$('.framework').on('click', '.nav', function(e) {
  $('.framework').unbind('click');
  e.preventDefault();
  var page = $(this).attr('href');

  $('body').load(page)

  if (page != location.href) {
		window.history.pushState({page:page},"", page);
  }

  console.log(page);
});

$('.framework').on('click', '.slink', function(e) {
  $('.framework').unbind('click');
  e.preventDefault();
  var page = $(this).attr('href');

  $('body').load(page)

  if (page != location.href) {
		window.history.pushState({page:page},"", page);
  }

  console.log(page);
});


//infobar show
function infoBarShow() {
  var y = document.getElementById("right");
  $('#infocont').animate({
    bottom: '0px'
  });

  y.style.padding = "25px 25px 325px 25px";
  $('.hup').css("display", "block");
  $('.hdown').css("display", "none");
}

//infobar hide
function infoBarHide() {
  $('#infocont').animate({
    bottom: '-280px'
  });

  var y = document.getElementById("right");
  y.style.padding = "25px";
  $('.hup').css("display", "none");
  $('.hdown').css("display", "block");
}


//close modal
function closeModal() {
  var modal = document.querySelector("#folderPopup");
  modal.classList.toggle("show-modal");
  wavesurfer.stop();
}


//open dashboard
function opendash() {
  $('#dash').animate({
    top: '0%'
  });

  $('#dsh').css("display", "none");
  $('#undsh').css("display", "block");
  $('li').removeClass('active');
  $('#undsh').addClass('active');
}


//close dashboard
function closedash() {
  $('#dash').animate({
    top: '100%'
  });

  $('#dsh').css("display", "block");
  $('#undsh').css("display", "none");
}


//accordion
function accordion(accord) {
  $(accord).next().toggle();
  $(accord).toggleClass("active");
}

/*

2. Form UI

*/

//upload onsubmit
function uploadFiles() {
  $( "#formed" ).submit();
  $( "#aniout" ).show();
};


//submit
function submit() {
  $("#delete").submit();
}


// copy input text
function copy() {
    var copyText = document.getElementById("copied");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
}
