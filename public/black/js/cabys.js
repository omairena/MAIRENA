$('#buscar_cabys').click(function(e) {
            e.preventDefault();
            var codigo = $('#codigo').val();
            var categoria = $('#categoria').val();
            var descripcion = $('#descripcion').val();
            if (codigo.length > 0) {
                if (descripcion.length > 0) {
                    var URL = 'http://ldcg.feisaac.com/public/api/productos/catcoddesc?categoria=' + categoria + '&codigo=' + codigo + '&descripcion='+ descripcion;
                    $.ajax({
                        type:'GET',
                        url: URL,
                        dataType: 'json',
                        success:function(response){
                            var content = '';
                            for (var i = 0; i < response.length; i++) {
                                content += '<tr>';
                                content += "<td><input type='checkbox' class='select-checkbox' name='seleccion[]' id="+ i +" value="+ response[i]['codigo_cabys'] +"></td>";
                                content += '<td>Caegoria # '+response[i]['categoria_0']+'</td>';
                                content += '<td>'+response[i]['codigo_cabys']+'</td>';
                                content += '<td>'+response[i]['descripcion_cabys']+'</td>';
                                content += '<td>'+response[i]['impuesto_cabys']+'</td>';
                                content += '<td>'+response[i]['tarifa_cabys']+'</td>';
                                content += '</tr>';
                            }
                         $('#codigos_cabys tbody').html(content); 
                        },
                        error:function(response){
                            alert('Error en consulta contra API ');
                            console.log(response);
                        }
                    });
                }else{
                    var URL = 'http://ldcg.feisaac.com/public/api/productos/catcod?categoria=' + categoria + '&codigo=' + codigo;
                    $.ajax({
                        type:'GET',
                        url: URL,
                        dataType: 'json',
                        success:function(response){
                            var content = '';
                            for (var i = 0; i < response.length; i++) {
                                content += '<tr>';
                                content += "<td><input type='checkbox' class='select-checkbox' name='seleccion[]' id="+ i +" value="+ response[i]['codigo_cabys'] +"></td>";
                                content += '<td>Caegoria # '+response[i]['categoria_0']+'</td>';
                                content += '<td>'+response[i]['codigo_cabys']+'</td>';
                                content += '<td>'+response[i]['descripcion_cabys']+'</td>';
                                content += '<td>'+response[i]['impuesto_cabys']+'</td>';
                                content += '<td>'+response[i]['tarifa_cabys']+'</td>';
                                content += '</tr>';
                            }
                         $('#codigos_cabys tbody').html(content); 
                        },
                        error:function(response){
                            alert('Error en consulta contra API ');
                            console.log(response);
                        }
                    });
                }
            }else{
                if (descripcion.length > 0) {
                    var URL = 'http://ldcg.feisaac.com/public/api/productos/catdesc?categoria='+ categoria +'&descripcion=' + descripcion;
                    $.ajax({
                        type:'GET',
                        url: URL,
                        dataType: 'json',
                        success:function(response){
                            var content = '';
                            for (var i = 0; i < response.length; i++) {
                                content += '<tr>';
                                content += "<td><input type='checkbox' class='select-checkbox' name='seleccion[]' id="+ i +" value="+ response[i]['codigo_cabys'] +"></td>";
                                content += '<td>Caegoria # '+response[i]['categoria_0']+'</td>';
                                content += '<td>'+response[i]['codigo_cabys']+'</td>';
                                content += '<td>'+response[i]['descripcion_cabys']+'</td>';
                                content += '<td>'+response[i]['impuesto_cabys']+'</td>';
                                content += '<td>'+response[i]['tarifa_cabys']+'</td>';
                                content += '</tr>';
                            }
                         $('#codigos_cabys tbody').html(content); 
                        },
                        error:function(response){
                            alert('Error en consulta contra API ');
                            console.log(response);
                        }
                    });
                }else{
                    var URL = 'http://ldcg.feisaac.com/public/api/productos/categoria?categoria=' + categoria;
                    $.ajax({
                        type:'GET',
                        url: URL,
                        dataType: 'json',
                        success:function(response){
                            var content = '';
                            for (var i = 0; i < response.length; i++) {
                                content += '<tr>';
                                content += "<td><input type='checkbox' class='select-checkbox' name='seleccion[ ]' id="+ i +" value="+ response[i]['codigo_cabys'] +"></td>";
                                content += '<td>Caegoria # '+response[i]['categoria_0']+'</td>';
                                content += '<td>'+response[i]['codigo_cabys']+'</td>';
                                content += '<td>'+response[i]['descripcion_cabys']+'</td>';
                                content += '<td>'+response[i]['impuesto_cabys']+'</td>';
                                content += '<td>'+response[i]['tarifa_cabys']+'</td>';
                                content += '</tr>';
                            }
                         $('#codigos_cabys tbody').html(content); 
                        },
                        error:function(response){
                            alert('Error en consulta contra API ');
                            console.log(response);
                        }
                    });
                }
            }
        });
        var maxChecks = 1;
        $(".select-checkbox").change(function () {
            alert(this.value);
            if($( "input:checked" ).length >= maxChecks)
                $(".select-checkbox:not(:checked)").prop( "disabled", true );
            else
                $(".select-checkboxk:not(:checked)").prop( "disabled", false );
        });