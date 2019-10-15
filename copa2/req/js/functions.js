//Selected files 
function selected() {
$( "input:checked").parent('.column').css( "background-color", "#dddddd" );
$( "input:not(:checked)").parent('.column').removeAttr( "style" );
}

function renamefile(rname) {
var input = rname.substring( 0, rname.indexOf( "-" ) );
$('.filename2').prop('readonly', true);
$('.filename2').attr("name", "");
$('.filename2').removeClass('filename2');
$('.unhide').removeClass('unhide');
document.getElementById(input + '-iname').classList.add('filename2');
document.getElementById(input + '-iname').removeAttribute('readonly');
document.getElementById(input + '-rebtn').classList.add('unhide');
document.getElementById(input + '-iname').setAttribute("name", "rename"); 
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
    
function submit() {
$("#delete").submit();
}


// infobar
function infoBar() {
var x = document.getElementById("infocont");
var y = document.getElementById("right");
x.style.display = "block";
y.style.padding = "25px 25px 325px 25px";
}

function infoBar2() {
var x = document.getElementById("infocont");
var y = document.getElementById("right");
if (x.style.display === "block") {
x.style.display = "none";
y.style.padding = "25px";
} else {
x.style.display = "block";
y.style.padding = "25px 25px 325px 25px";
}
}

//share
$(".share").click(function(){
alert($(this).attr("value"));
});

function popup(btn) {
var share = ($(btn).attr("value"));
$.ajax({
type: 'post',
url: '../req/ajax',
data:{phpfunc: 0, share: share },

success: function(data){
var modal = document.querySelector(".folderPopup");
modal.classList.toggle("show-modal");
console.log(share);
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
data:{phpfunc: 1, imgm: imgm },

success: function(data){
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
data:{phpfunc: 2, videom: videom },

success: function(data){
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
data:{phpfunc: 4, audiom: audiom },

success: function(data){
var modal = document.querySelector(".folderPopup");
modal.classList.toggle("show-modal");
$(".icont").html(data);
}
});
}

//details
function infodet(info) {
var detfile = ($(info).attr("value"));
$.ajax({
type: 'post',
url: '../req/ajax',
data:{phpfunc: 3, detfile: detfile },

success: function(data){
console.log(detfile);
$(".infodet").html(data);
infoBar();
}
});
}

//search list
function searchl(search) {
var query = $('input[name="srch"]').val();
$.ajax({
type: 'post',
url: '../req/ajax',
data:{phpfunc: 5, query: query},

success: function(data){
//$('#search').submit();
console.log(query);
$(".files").html(data);
}
});
}

//search grid
function searchg(search) {
var query = $('input[name="srch"]').val();
$.ajax({
type: 'post',
url: '../req/ajax',
data:{phpfunc: 6, query: query},

success: function(data){
//$('#search').submit();
console.log(query);
$(".files").html(data);
}
});
}


function mkdir(dir) {
var nid = 'drives';
$.ajax({
type: 'post',
url: '../req/ajax',
data:{phpfunc: 8, mdir:mdir},

success: function(data){
console.log(nid);
$(".files").html(data);
}
});
}
