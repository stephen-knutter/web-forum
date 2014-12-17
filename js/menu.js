$.fn.clicktoggle = function(a, b) {
    return this.each(function() {
        var clicked = false;
        $(this).click(function() {
            if (clicked) {
                clicked = false;
                return b.apply(this, arguments);
            }
            clicked = true;
            return a.apply(this, arguments);
        });
    });
};

$(function(){

	$("#menuPic").clicktoggle(open, close);
	
	function open(){
		$("#sidebar").css('display', 'block');
	}
	
	function close(){
		$("#sidebar").css('display', 'none');
	}
});

window.onresize = function(){
	var width = document.documentElement.clientWidth;
	if(width > 1100){
		$("#sidebar").css('display','block');
	} else if(width <= 1100){
		$("#sidebar").css('display','none');
	}
}