<?php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    session_start();
    
    // recupera os dados da session
    if(isset($_SESSION['info'])){
        $dados = $_SESSION['info'];
    }
    
    // adiciona o novo valor na session
    if(isset($_POST['adicionar'])){
        if($_POST['novoValor'] != "" || $_POST['novoValor'] != null){
            $dados[] = $_POST['novoValor'];
            $_SESSION['info'] = $dados;
        }
    }
    
    // remove os valoes selecionado
    if(isset($_GET['remover'])){
        unset($dados[$_GET['remover']]);
        $_SESSION['info'] = $dados;
        header("Location: estatistica.php");
    }
    
    //destroi a session
    if(isset($_POST['limpar'])){
        session_destroy();
        header("Location: estatistica.php");
    }
    
    //lista os dados salvos
    function listar($dados){
        if(isset($dados)){
            foreach ($dados as $indice => $valor){
                echo"<tr>
                <th scope='row'>$indice</th>
                <td>$valor</td>
                <td><a href='estatistica.php?remover=$indice'><img src='estatistica-del.png' class='carousel-control-prev-icon d-block'></a></td>
                </tr>";
            }
        }
    }
    
    //lista a tabela e faz o calculo de x - xb
    function tabCalculo($dados){
        if(isset($_SESSION['info'])){
            
            $i=1;
            $xb = $_SESSION['xb'];
            
            foreach ($dados as $x){
                $x2 = pow($x,2);
                $cal = $x - $xb;
                echo
                "<tr>
                <th scope='row'>$i</th>
                    <td>$x</td>
                    <td>$x - $xb = ", $cal ,"</td>
                    <td>$cal<sup>2</sup> = ", pow($cal, 2) ,"</td>
                </tr>";
                $i++;
                $xtotal = $xtotal + $x;
                $x2total = $x2total + pow($cal, 2);
            }
            echo "<tr class='table-dark'>
        <td colspan='3' class='table-dark font-weight-bold text-dark'>Total: </td>
        <td class='table-dark font-weight-bold text-primary'>$x2total</td>
        </tr>";
        }
    }
    
    //faz o calculo de amplitude
    function calculoAmplitude($dados){
        if(isset($dados) && sizeof($dados) > 0){
            $maior = max($dados);
            $menor = min($dados);
            echo "<p> A<sub>t</sub> = $maior - $menor", "</p>";
            echo "<p> A<sub>t</sub> =", $maior - $menor, "</p>";
        }
        
    }
    
    //faz o calculo de X, X2, Xb, e quantidade de elementos;
    function media($dados){
        if(isset($dados) && sizeof($dados) > 0){
            $i=0;
            $x = 0;
                foreach ($dados as $valor){
                    $x = $x + $valor;
                    $x2 = $x2 + pow($valor,2);
                    $i++;
                }
                echo "<p> x&#772 = $x / $i </p>";
                echo "<p> x&#772 = ", $x / $i, " </p>";
                
                $xb = $x / $i;
                
                
                foreach ($dados as $val){
                    $x2 = pow($val ,2);
                    $cal = $val - $xb;
                    //$xtotal = $xtotal + $val;
                    $x2total = $x2total + pow($cal, 2);
                }
                
                
                $_SESSION['x'] = $x;
                $_SESSION['x2'] = $x2;
                $_SESSION['xb'] = $xb;
                $_SESSION['xx2'] = $x2total;
                $_SESSION['totalelementos'] = $i;
        }    
    }
    
    //faz o calculo de variancia
    function variancia(){
        if(isset($_SESSION['totalelementos']) && isset($_SESSION['totalelementos'])){
            $xx2 = $_SESSION['xx2'];
            $i = $_SESSION['totalelementos'];
            //algum bug ele esta pegando metade do valor
            
                echo "<div class='alert alert-warning' role='alert'>
                    verificar tabela para ver os Valores de X no calculo
                </div>";
                echo "<p>S<sup>2</sup> = ", $xx2," / ( ", $i ," - 1)</p>";
                $variancia = $xx2/ ($i-1);
                echo "<p>S<sup>2</sup> = $variancia </p>";
                $_SESSION['variancia'] = $variancia;
            
        }
    }
    
    // faz o calculo de variancia
    function desvio(){
        if(isset($_SESSION['variancia']) && $_SESSION['variancia'] >  0){
            
            $variancia = $_SESSION['variancia'];
            
            echo "<p>DP = &#8730 $variancia</p>";
            echo "<p>DP = ", sqrt($variancia) ,"</p>";

        }
    }
    
?>

<!DOCTYPE html>
<html lang="pt-br">
    
    <head>
        <!-- Meta tags Obrigatórias -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <title>Estatistica</title>
    </head>
    
    <body onload=goFocus('valor')>
        <?php //include("../navbar.php"); ?>
        <div class="container">
            <div class="row">
                <!--esquerdo-->
                <div class="col">
                    <!--formulario para adicionar os valores-->
                    <form action="" method="post">
                        <!--campo de input do formulario-->
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Dados" aria-label="Dados" aria-describedby="basic-addon2" name="novoValor" id="valor">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submite" name="adicionar">Adicionar</button>
                            </div>
                        </div>
                    </form>
                    <!--tabela de valores-->
                    <table class='table table-striped table-hover'>
                        <thead>
                            <tr>
                                <th scope='col'>#</th>
                                <th scope='col'>Valor</th>
                                <th scope='col'>Remover</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php listar($dados);?>
                        </tbody>
                    </table>
                    <!--formulario para limpar e remover dados-->
                    <form action="" method="post">
                        <button type="submite" class="btn btn-primary" name="limpar">Limpar</button>
                    </form>
                </div>
                <!--direito-->
                <div class="col">
                    <!--menu de navegação -->
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Calculos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Tabela</a>
                        </li>
                        <!--<li class="nav-item">
                            <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Contact</a>
                        </li> -->
                    </ul>
                    
                    
                    <div class="tab-content" id="pills-tabContent">
                        <!--pagina do menu 1-->
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                            
                            <h5 class="text-primary">Amplitude total para dados não agrupados: </h5>
                            <p> A<sub>t</sub> = nºmaior - nºmenor <br/></p>
                            <?php calculoAmplitude($dados); ?> 
                            
                            <h5 class="text-primary">Media x&#772: </h5>
                            <p> x&#772 = Total / Numero de elementos </p>
                            <?php media($dados); ?>
                            
                            <h5 class="text-primary">Variancia amostral: </h5>
                            <p>S<sup>2</sup> = ∑ ( X - x&#772 )<sup>2 </sup>/ (n - 1)</p>
                            <?php variancia(); ?> 
                            
                            <h5 class="text-primary">Desvio Padrao: </h5>
                            <p>DP = &#8730 variancia amostral</p>
                            <?php desvio(); ?>
                        </div>
                        <!--pagina do menu 2-->
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <table class='table table-striped'>
                                <thead>
                                    <tr>
                                        <th scope='col'>#</th>
                                        <th scope='col'>X</th>
                                        <th scope='col'>X - x&#772</th>
                                        <th scope='col'>(X - x&#772)<sup>2</sup></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php tabCalculo($dados);?>
                                </tbody>
                            </table>
                        </div>
                        <!--pagina do menu 3
                        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                            
                        </div>-->
                    </div>
                    
                </div>
                
            </div>
        </div>
        <!-- JavaScript (Opcional) -->
        <!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
        <script type="text/javascript">
            function goFocus(elementID){
                document.getElementById(elementID).focus();
            }
        </script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    </body>
</html>