require(['jquery'], function($){

    $(document).on("submit","#formEstadoPedido",function(event){
        event.preventDefault();
    });
    $(document).on("click","#rastreo",function(){
        $.ajax({
            url: BASE_URL+'inicio/index/Ajax',
            beforeSend: function(){
                $("#timeline").html("<div class='cd-timeline__block js-cd-block'><div class='cd-timeline__img cd-timeline__img--picture js-cd-img'><img src='/pub/static/frontend/Coopelesca/almacen/es_CR/images/inactive.png' alt='inactive'></div><div class='cd-timeline__content js-cd-content'><h2></h2><p>Buscando en almacenes ... <i class='fas fa-cog fa-spin'></i></p><span class='cd-timeline__date'></span></div></div>");
            },
            data: $("#formEstadoPedido").serialize(),
            type: "POST",
            dataType: "json"
        }).done(function (data) {
            var opcionesEstadoPedido = data.EstadoPedido;
            var listaEstadoPedidos = JSON.parse(opcionesEstadoPedido);

            if( (listaEstadoPedidos.ListaEstadoPedido === null)||(listaEstadoPedidos.ListaEstadoPedido.length == 0) ){

                $("#timeline").html("");
                var path = window.location.pathname;
                var index = path.indexOf("_en");

                if(index != -1){
                  $("#timeline").html("<div class='cd-timeline__block js-cd-block'><div class='cd-timeline__img cd-timeline__img--picture js-cd-img'><img src='/pub/static/frontend/Coopelesca/almacen/es_CR/images/inactive.png' alt='inactive'></div><div class='cd-timeline__content js-cd-content'><h2></h2><p>No results</p><span class='cd-timeline__date'></span></div></div>");
                }else{
                  $("#timeline").html("<div class='cd-timeline__block js-cd-block'><div class='cd-timeline__img cd-timeline__img--picture js-cd-img'><img src='/pub/static/frontend/Coopelesca/almacen/es_CR/images/inactive.png' alt='inactive'></div><div class='cd-timeline__content js-cd-content'><h2></h2><p>Sin resultados</p><span class='cd-timeline__date'></span></div></div>");                    
                }
                
            }else{
                $("#timeline").html("");
                $.each(listaEstadoPedidos.ListaEstadoPedido, function(item,value) {
                    $("#timeline").append("<div class='cd-timeline__block js-cd-block'><div class='cd-timeline__img cd-timeline__img--picture js-cd-img'><img src='/pub/static/frontend/Coopelesca/almacen/es_CR/images/inactive.png' alt='inactive'></div><div class='cd-timeline__content js-cd-content'><h2>"+value.Estado+"</h2><p>&nbsp;</p><span class='cd-timeline__date'>"+value.Fecha+"</span></div></div>");

                    ultimoEstado = value.Estado;
                });
                $("#codigoEstadoPedido").html(ultimoEstado);
                $("#codigoEstadoPedido").fadeIn("fast");                
            }    


            $("#timelinePedido").fadeIn("fast");


        });
    });

    function VerticalTimeline( element ) {
        this.element = element;
        this.blocks = this.element.getElementsByClassName("js-cd-block");
        this.images = this.element.getElementsByClassName("js-cd-img");
        this.contents = this.element.getElementsByClassName("js-cd-content");

    };

    VerticalTimeline.prototype.showBlocks = function() {
        var self = this;
        for( var i = 0; i < this.blocks.length; i++) {
            (function(i){
                if( self.contents[i].classList.contains("cd-is-hidden") && self.blocks[i].getBoundingClientRect().top <= window.innerHeight*self.offset ) {
                    self.images[i].classList.add("cd-timeline__img--bounce-in");
                    self.contents[i].classList.add("cd-timeline__content--bounce-in");
                    self.images[i].classList.remove("cd-is-hidden");
                    self.contents[i].classList.remove("cd-is-hidden");
                }
            })(i);
        }
    };
 	
});
