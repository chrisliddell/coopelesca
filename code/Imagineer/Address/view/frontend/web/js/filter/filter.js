require(["jquery"], function($){

    if( $("#metodosEnvioShipping").length == 0 ){
    function obtenerIdioma(path){
        var index = path.indexOf("_en");

        if(index != -1){
            return "en";
        }else{
            return "es";
        }
    }
        $(document).ready(function() {
			
                var value = $(this).val();
                var countyDropdown = $("select[name='county']");
                var districtDropdown = $("select[name='district']");
            
            $(document).on("change","select[name='region_id']",function(){
                

                $.ajax({
                    url: '/imagineer/filtering/RegionCounty',
                    type: 'GET',
                    data: {'region': value},                    
                    dataType: 'json',                    
                    success: function(res) {

                        // @var Array
                        var counties = res;                        

                        // clear all current #county OPTIONS
                        countyDropdown.find('option').remove();

                        // clear all current #district OPTIONS
                        districtDropdown.find('option').remove();                    	

                        /*
                        counties.forEach(function(county){

                            // @var option
                            var option = document.createElement("option");                    		

                            // SET value and text to var option
                            option.value = county.value;
                            option.text = county.label;

                            countyDropdown.appendChild(option);

                        });*/

                        // SET value and text to var option
                        var defaultCountyOption = document.createElement("option");
                        
                        defaultCountyOption.value = "";

                        if(obtenerIdioma(window.location.pathname) == "es"){                            
                            defaultCountyOption.text = "Por favor seleccione un cantón.";
                        }else{
                            defaultCountyOption.text = "Please select a canton.";
                        } 
                        $("select[name='county']").append(defaultCountyOption); 
                        counties.forEach(function(county){

                            // @var option
                            var option = document.createElement("option");                          

                            // SET value and text to var option
                            option.value = county.value;
                            option.text = county.label;

                            $("select[name='county']").append(option); 

                        }); 
                        //countyDropdown.appendChild(defaultCountyOption);
                    },                                
                    error: function (err) {}                                    
                });
            });


            $(document).on("change","select[name='county']",function(){
                
                var value = $(this).val();
                var districtDropdown = $("select[name='district']");
                        
                // clear all current #town OPTIONS
                districtDropdown.find('option').remove();                       
                // SET value and text to var option
                var defaultDistrictOption = document.createElement("option");
                
                defaultDistrictOption.value = "";

                if(obtenerIdioma(window.location.pathname) == "es"){                            
                    defaultDistrictOption.text = "Por favor seleccione un distrito.";
                }else{
                    defaultDistrictOption.text = "Please select a district.";
                } 

                $.ajax({
                    url: '/imagineer/filtering/CountyDistrict',
                    type: 'GET',
                    data: {'county': value},                    
                    dataType: 'json',                    
                    success: function(res) {

                        // @var Array
                        var districts = res;

                        // clear all current #district OPTIONS
                        districtDropdown.find('option').remove();                    	
                        // SET value and text to var option
                        var defaultDistrictOption = document.createElement("option");
                        
                        defaultDistrictOption.value = "";

                        if(obtenerIdioma(window.location.pathname) == "es"){                            
                            defaultDistrictOption.text = "Por favor seleccione un distrito.";
                        }else{
                            defaultDistrictOption.text = "Please select a district.";
                        }    
                        $("select[name='district']").append(defaultDistrictOption);
                        districts.forEach(function(district){

                            // @var option
                            var option = document.createElement("option");                    		

                            // SET value and text to var option
                            option.value = district.value;
                            option.text = district.label;



                            $("select[name='district']").append(option);
                            
                        });
                    },                                
                    error: function (err) {}                                    
                });
            });


            $(document).on("change","select[name='district']",function(){
                
                if($('select[name="county"]').find('option:selected').val() == "" ){
                    var value = "undefined";
                }else{
                    var value = $(this).val();    
                }

                if(value != "undefined"){        		                  
                    $.ajax({
                        url: '/imagineer/filtering/DistrictTown',
                        type: 'GET',
                        data: {'district': value},                    
                        dataType: 'json',                    
                        success: function(res) {

                            // @var Array
                            var towns = res;   
                    
                            // clear all current #town OPTIONS
                            $("select[name='town']").find('option').remove();                     

                            towns.forEach(function(town){

                                // @var option
                                var option = document.createElement("option");                          

                                // SET value and text to var option
                                option.value = town.value;
                                option.text = town.label;
                            
                                $("select[name='town']").append(option);                                
                            });
                        },                                
                        error: function (err) {}                                    
                    });
                }else{

                    // clear all current #county OPTIONS
                    $('select[name="town"]').find('option').remove(); 

                    if(obtenerIdioma(window.location.pathname) == "es"){                            
                        $('select[name="town"]').append(
                        $('<option />')
                            .text("Por favor seleccione un pueblo.")
                            .val("")
                        );
                    }else{
                        $('select[name="town"]').append(
                        $('<option />')
                            .text("Please select a town.")
                            .val("")
                        );
                    } 

                }   

            });
        });

    }  
	});
