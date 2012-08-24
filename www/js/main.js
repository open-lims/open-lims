$.ajaxSetup({cache: false});


$.fn.center = function () {
	this.css("position","absolute");
	this.css("top", ( $(window).height() - this.height() ) / 2+$(window).scrollTop() + "px"); 
	this.css("left", ( $(window).width() - this.width() ) / 2 +$(window).scrollLeft() + "px"); 
	
	return this;
}

function getQueryParams(qs)
{	
    qs = qs.split("+").join(" ");
    var params = {},
        tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs))
    {
        params[decodeURIComponent(tokens[1])]
            = decodeURIComponent(tokens[2]);
    }

    return params;
}

var get_array = getQueryParams(document.location.search);