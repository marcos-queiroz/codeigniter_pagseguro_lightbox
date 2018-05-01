<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">

        <title>Exemplo PagSeguro</title>
    </head>
    
    <body>
        
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>Exemplo PagSeguro</h1>
                </div>

                <div class="card-deck">
                    <?php foreach ($produtos as $p): ?>
                        <div class="col-md-4">
                            <div class="card" style="width: 18rem;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $p->descricao ?></h5>
                                    <p class="card-text"><strong>R$ </strong><?= $p->preco ?></p>
                                    <a href="#" class="btn btn-primary btn-pagar" data-id="<?= $p->id ?>" data-descricao="<?= $p->descricao ?>" data-preco="<?= $p->preco ?>">Comprar</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>


        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script
        src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    
    
        <!-- PagSeguro -->
        <script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>
        <!-- <script type="text/javascript" src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script> -->
        
        <script>
            $(document).ready(function() {
                
                // button para comprar 1 item
                $('.btn-pagar').on('click', function(){
                    
                    // captura os valores do data atributo
                    var data = {
                        id: $(this).data('id'),
                        descricao: $(this).data('descricao'),
                        preco: $(this).data('preco')
                    };
                    
                    // url do metodo
                    var url = '<?= base_url('home/comprar') ?>';
                    
                    // envia um post para o metodo
                    $.post(url, data, function(result){
                        console.log('retorno do post: '+ result);

                        // passa o codigo para o Lightbox do PagSeguro
                        PagSeguroLightbox({
                            code: result
                            }, {
                            success : function(transactionCode) {
                                alert("success - " + transactionCode);
                            },
                            abort : function() {
                                alert("abort");
                            }
                        });
                    });
                });
                
            });

            
        </script>
        <!--/ PagSeguro -->
    
    </body>
</html>