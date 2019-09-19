require(['jquery','owlcarousel'], function($){ 	
    var base_url = require.toUrl('');
    var urlLeft = base_url + "images/left-arrow.png";
    var urlRight = base_url + "images/right-arrow.png";

	jQuery('.owl-carousel').owlCarousel({
    		loop:true,
            nav:true,
    		responsiveClass:true,
    		responsive:{
        		0:{
            		items:1,
            		nav:true,
			        loop: true,
                    navText: ["<img src='"+urlLeft+"' />","<img src='"+urlRight+"' />"]
        		},
        		600:{
            		items:3,
            		nav:true,
			        loop:true,
                    navText: ["<img src='"+urlLeft+"' />","<img src='"+urlRight+"' />"]
        		},
        		1000:{
            		items:5,
            		nav:true,
            		loop:true,
                    navText: ["<img src='"+urlLeft+"' />","<img src='"+urlRight+"' />"]
        		}
    		}
	});

 	
});
