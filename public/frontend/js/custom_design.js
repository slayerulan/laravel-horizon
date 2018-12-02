$(document).ready(function () {
	// Close Mobile Sidemenu on Click
	$('.closeicon').on('click', function () {
		$('#myNavmenu').offcanvas('hide');
	});
});
// tabbed content
// http://www.entheosweb.com/tutorials/css/tabs.asp
$(".tab_content").hide();
$(".tab_content:first").show();

/* if in tab mode */
$("ul.tabs li").click(function () {

	$(".tab_content").hide();
	var activeTab = $(this).attr("rel");
	$("#" + activeTab).fadeIn();

	$("ul.tabs li").removeClass("active");
	$(this).addClass("active");

	$(".tab_drawer_heading").removeClass("d_active");
	$(".tab_drawer_heading[rel^='" + activeTab + "']").addClass("d_active");

});
/* if in drawer mode */
$(".tab_drawer_heading").click(function () {

	$(".tab_content").hide();
	var d_activeTab = $(this).attr("rel");
	$("#" + d_activeTab).fadeIn();

	$(".tab_drawer_heading").removeClass("d_active");
	$(this).addClass("d_active");

	$("ul.tabs li").removeClass("active");
	$("ul.tabs li[rel^='" + d_activeTab + "']").addClass("active");
});


/* Extra class "tab_last"
   to add border to right side
   of last tab */
$('ul.tabs li').last().addClass("tab_last");
$(".league_list_arrow").click(function (e) {
	sessionStorage.countryname = $(this).parents().parents().attr('id');
	if ($("#" + sessionStorage.countryname).attr('class')=='active') {
		$("#" + sessionStorage.countryname).removeClass("active");
	}else {
		$("#" + sessionStorage.countryname).addClass("active");
	}
})

$(".country_list_arrow").click(function (e) {
	e.preventDefault();
	sessionStorage.gamename = $(this).parents().parents().attr('class');
	if ($(this).parents().parents().hasClass("active")) {
		$(this).parents().parents().removeClass("active");

	}else {
		$(this).parents().parents().parents().children().removeClass("active");
		$(this).parents().parents().addClass("active");
	}
})

$(document.body).on('click',".country_list_arrow_live",function(event)
{
		//e.preventDefault();
		sessionStorage.gamename = $(this).parents().parents().attr('class');
		if ($(this).parents().parents().hasClass("active"))
		{
			$(this).parents().parents().removeClass("active");

		}else {
			$(this).parents().parents().parents().children().removeClass("active");
			$("." + sessionStorage.gamename).addClass("active");
		}
});


var wheight = $(window).height();
fheight = $(".footer").height();
$("body").css("min-height", wheight).css("padding-bottom", fheight+15);

$(document.body).on('click', ".box_show_hide", function () {
	$(this).parents().children(".show_hide_div").animate({ height: "toggle" }, 500);
	$(this).children('.icon_class').toggleClass('arrow_up arrow_down');
})
$(document.body).on('click', ".box_show_hide_odds", function () {
	$(this).parents().children(".show_hide_div_odds").animate({ height: "toggle" }, 500);
	$(this).children('.icon_class').toggleClass('arrow_up arrow_down');
})

$(".betslip_heading").click(function () {
	$(".clapsible_bet").animate({ height: "toggle" }, 500);
	$(this).toggleClass("open");



    $(".tooltips span").each(function(){
    	var tooltipheight = $(this).outerHeight();
    	$(this).css("height",tooltipheight).css("margin-top","-" + (tooltipheight / 2) + "px");
    })
});

function showAlert(title=null,content=null,type='blue') {
	$.alert({
		icon: 'fa fa-warning',
		title: title,
		content : content,
		type  : type,
		theme:'material',
		closeIcon: true,
		closeIconClass: 'fa fa-close',
		draggable: false,
	});
}
function showError(content) {
	title = $('#error_title').attr('data-value');
	$.alert({
		icon: 'fa fa-warning',
		title: title,
		content : content,
		type  : 'red',
		theme:'material',
		closeIcon: true,
		draggable: false,
		closeIconClass: 'fa fa-close'
	});
}
function showSuccess(content) {
	title = $('#success_title').attr('data-value');
	$.alert({
		icon: 'fa fa-check-circle',
		title: title,
		content : content,
		type  : 'green',
		theme:'material',
		draggable: false,
		closeIcon: true,
		closeIconClass: 'fa fa-close'
	});
}
function showInfo(content) {
	title = $('#info_title').attr('data-value');
	$.alert({
		icon: 'fa fa-info-circle',
		title: title,
		content : content,
		type  : 'blue',
		theme:'material',
		draggable: false,
		closeIcon: true,
		closeIconClass: 'fa fa-close'
	});
}


$(document.body).find('[data-toggle="tooltip"]').tooltip();
