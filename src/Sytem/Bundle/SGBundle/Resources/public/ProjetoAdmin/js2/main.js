//function ValidaCpf(e){
           document.getElementById("usuario").onkeypress= function(e) {
             if (e.keyCode == 13) {
                  //Pega o ID
                 
                  var id= $(this).attr('rel');

                  //var url = Routing.generate('SytemSGBundle_Solicitacoes_searchCpfuser');
                  //Pega o valor do usuario atual...
                  var itens = document.getElementById("usuario").value;
                  //Função ajax
                  $.ajax({
                    //Tipo de envio POST ou GET...
                    type: "POST",
                    //Caminho do arquivo PHP...
                    url: "{{ path('searchCpfuser') }}",
                    //Arquvios passados via POST neste caso, segue o mesmo modelo para GET...
                    data: {'usuario':itens,'id':id},
                    //Se der tudo ok no envio...
                    success: function(resposta){
                      if(id==1){
                      //Colocar a resposta do aqruivo na div....
                      $("#itenscpf").html(resposta);
                      /*alert('Item Adicionado');*/}
                      else{
                        $("#itenscpf2").html(resposta);
                      /*alert('Item Adicionado');*/}

                      }
                    }
                    error: function(){
                      alert('Erro ao conectar o servidor!!!!');
                    }
                  });
                  e.preventDefault();
                  alert(itens);
                }
            } 

  