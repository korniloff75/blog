$(document).ready(function(){
$('#no').click(function(){
var no = 1;
var id_pg = $('#id_pg').val();
var module_news = $('#module_news').val();

$.ajax({
url: '/modules/news_categories/send.php',
type: 'post',
dataType: 'json',
data: {
'no': no, 'id_pg': id_pg, 'module_news': module_news
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
var module_news = $('#module_news').val();

$.ajax({
url: '/modules/news_categories/send.php',
type: 'post',
dataType: 'json',
data: {
'yes': yes, 'id_pg': id_pg, 'module_news': module_news
},
success: function(data){
$('.yes-num').html(data.result);						
}					
});

});
});