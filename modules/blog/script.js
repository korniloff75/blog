$(document).ready(function(){
$('#no').click(function(){
var no = 1;
var id_pg = $('#id_pg').val();

$.ajax({
url: '/modules/blog/send.php',
type: 'post',
dataType: 'json',
data: {
'no': no, 'id_pg': id_pg
},
success: function(data){
$('.no-num').html(data.result);						
}					
});

});
});
$(document).ready(function(){
$('#yes').click(function(){
var yes = 1;
var id_pg = $('#id_pg').val();

$.ajax({
url: '/modules/blog/send.php',
type: 'post',
dataType: 'json',
data: {
'yes': yes, 'id_pg': id_pg
},
success: function(data){
$('.yes-num').html(data.result);						
}					
});

});
});