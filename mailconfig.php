<?php
ini_set('display_errors', 'On');
$ip = $_SERVER['REMOTE_ADDR'];
?>
<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">

    <!-- CSS -->
    <link rel="stylesheet" href="css.css">
    <title>Facturación</title>

    <!-- DataTable -->
    <link href="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.css" rel="stylesheet" type="text/css">
</head>

<body>

    <!-- NAV -->
    <nav class="navbar navbar-light bg-light menuPrincipal">
        <h1 class="navbar-brand pt-4" id="tituloPagina">Facturacion</h1>
    </nav>

    <!--Sticky-->
    <div class="sticky-top">
        <ul class="nav justify-content-center  menuPrincipal pb-4 " id="listadoPaginas">
            <li class="nav-item">
                <a id="textoLogoff" class="nav-link pt-4"> Fecha actual:
                    <?php
                    $hoy = date('d-m-Y');
                    print_r($hoy);
                    ?>
                </a>
            </li>
        </ul>
    </div>

    <!-- Contenedor -->
    <div class="container-fluid p-4">
        <div class="row">

            <div class="col-3 pt-4">
                <!-- Form fecha -->
                <form method="post" action="">
                    <label class="form-label" for="inicio"> Inicio </label>
                    <input type="date" name="incio" id="inicio" required class="form-control">
                    <br>
                    <label class="form-label" for="fin"> Fin </label>
                    <input type="date" name="fin" id="fin" required class="form-control">
                    <br>
                    <input type="submit" value="Buscar" class="btn btn-primary">
                </form>
                <?php
                if (isset($_POST['incio']) && isset($_POST['fin'])) {

                    echo '<br><br>Fechas seleccionada de inicio: <p>' . $_POST['incio'] . '</p>' . 'Fecha seleccionada de fin: <p>' . $_POST['fin'] . '</p>';
                }
                ?>

            </div>

            <div class="col-9 pt-4 pb-4">
                <div class="table-responsive">
                    <table class=" table table-hover " id="MainContent_GridView">
                        <tr>
                            <th>Nº</th>
                            <th>HOST</th>
                            <th>CLIENTES</th>
                            <th>BD</th>
                            <th>Leads</th>
                            <th>Email Send</th>
                            <th>Información</th>
                            <th>Precio lead</th>
                            <th>Prueba2</th>
                        </tr>

                        <?php
                        $arrContextOptions = array(
                            "ssl" => array(
                                "verify_peer" => false,
                                "verify_peer_name" => false,
                            ),
                        );

                        #Recoger datos de dir-iterator.php de cada maquina
                        //*********************************************************
                        $c3p0 = file_get_contents("http://ip/dir-iterator.php");
                        $json['c3p0'] = json_decode($c3p0, true);
                        //*********************************************************
                        $r2d2 = file_get_contents("https://ip/dir-iterator.php", false, stream_context_create($arrContextOptions));
                        $json['r2d2'] = json_decode($r2d2, true);
                        //*********************************************************
                        $obi1 = file_get_contents("https://ip/dir-iterator.php", false, stream_context_create($arrContextOptions));
                        $json['obi1'] = json_decode($obi1, true);
                        //*********************************************************
                        $naboo = file_get_contents("https://ip/dir-iterator.php", false, stream_context_create($arrContextOptions));
                        $json['naboo'] = json_decode($naboo, true);
                        //*********************************************************

                        #Parametros BD
                        $host = "localhost";
                        $dbname = "";
                        $dbusername = "root";
                        $dbpassword = "?";

                        #Crear conexion
                        $cnn = mysqli_connect($host, $dbusername, $dbpassword);

                        #Comproba conexion
                        if (mysqli_connect_errno()) {
                            die("Connection failed: " . mysqli_connect_error());
                        }

                        #Recorrer BD
                        $i = 0;
                        foreach ($json as $machine) {
                            foreach ($machine as $data) {
                                $i++;
                                echo "<tr>";
                                $host = (isset($data["host"]) ? $data["host"] : '');
                                $path_ = str_replace("/var/www/html/", "", $data["path"]);
                                $path_ = str_replace("/app/../var/cache", "", $path_);
                                $path_ = str_replace("/app/cache", "", $path_);
                                $path_ = "http://" . $path_;

                                #Datos
                                if (isset($data["alias"])) {
                                    echo "<td class='host'>" . $i . "</td>";
                                    echo "<td class='host'>" . $host . "</td>";
                                    echo "<td>";
                                    echo "<a class='clients' title='Name: " . $data["alias"] . "' target='_blank' href='" . $path_ . "'>";
                                    echo substr($data["alias"], 0, 15) . "</a></td>";

                                    mysqli_select_db($cnn, $data["db_name"]) or die('no se conecto a la  BD');

                                    #Buscamos las tablas que tengan "leads"
                                    $queryCheckLeadsTable = "SHOW TABLES LIKE '%leads'";
                                    $checkLeadsTable = mysqli_query($cnn, $queryCheckLeadsTable) or die('no se conecto a la  tabla');

                                    #Obtener las tablas que nos interesan
                                    foreach ($checkLeadsTable as $resutBD) {

                                        if (
                                            $resutBD["Tables_in_" . $data["db_name"] . " (%leads)"] == 'mrkt_leads'
                                            or $resutBD["Tables_in_" . $data["db_name"] . " (%leads)"] == 'leads'
                                            or $resutBD["Tables_in_" . $data["db_name"] . " (%leads)"] == 'maufc_leads'
                                            or $resutBD["Tables_in_" . $data["db_name"] . " (%leads)"] == 'mau_leads'
                                            or $resutBD["Tables_in_" . $data["db_name"] . " (%leads)"] == 'frbx_leads'
                                            or $resutBD["Tables_in_" . $data["db_name"] . " (%leads)"] == 'mrktic_leads'
                                        ) {

                                            #Consultar los leads de cada BD
                                            $queryEmailsCount = 'select count(*) from ' . $resutBD["Tables_in_" . $data["db_name"] . " (%leads)"] . ' WHERE email IS NOT NULL';
                                            $resultado = $cnn->query($queryEmailsCount);
                                            $followingdata = $resultado->fetch_array();

                                            #Buscar las tablas de mails
                                            $queryCheckSendTable = "SHOW TABLES LIKE '%email_stats'";
                                            $resultadoSend = $cnn->query($queryCheckSendTable);
                                            $resultadoSendTable = $resultadoSend->fetch_array();

                                            if (isset($_POST['incio']) && isset($_POST['fin'])) {

                                                #Fechas para las querys
                                                $fechaInicio = new DateTime($_POST['incio']);
                                                $fechaFin = new DateTime($_POST['fin']);

                                                #Consultar los emails de cada BD
                                                $queryEmailSend = "select count(*) from " . $resultadoSendTable[0] . " where date_sent  BETWEEN '" .  $fechaInicio->format('Y-m-d') . "' and  '" . $fechaFin->format('Y-m-d') . "'";
                                                $resultadoSendCount = $cnn->query($queryEmailSend);
                                                $resultadoSendTableCount = $resultadoSendCount->fetch_array();
                                            } else {
                                                $resultadoSendTableCount[0] = "No hay un
                                                    \n rango de fecha";
                                            }

                                            #Conectarse a la BD donde guarda los parametros de la instancias precio/fecha de renovacion, etc.
                                            mysqli_select_db($cnn, "marketic-facturacion-bryan") or die('no se conecto a la  BBDD');

                                            #Recuperar los datos de las intancias
                                            $queryParametres = "select * from clientesMautic where name_bd='" . $data["db_name"] . "'";
                                            $resultadoQueryParametres = $cnn->query($queryParametres);
                                            $resultadoParametres = $resultadoQueryParametres->fetch_array();

                                            #Mostrar los resultados
                                            echo ('<td>' .
                                                $data["db_name"] . '</td><td>' .
                                                $followingdata[0] . '</td><td>' .
                                                $resultadoSendTableCount[0] . '</td>');

                                            #Si no existe la intancia como registro en la tabla de facturacion la crea
                                            if (empty($resultadoParametres)) {
                                                echo ('<td>No existe</td>');
                                                $queryInsertBDName = "INSERT INTO clientesMautic (name_bd, precio_lead, precio_email, fecha_caducidad) VALUES ('" . $data["db_name"] . "', '50', '40', '-')";
                                                $cnn->query($queryInsertBDName) or die('No se puedo insertar');
                                            } else {
                                                echo ('<td>')
                        ?>

                                                <!-- Acordeon con los parametros de la instancia -->
                                                <div class="accordion accordion-flush" id="accordionFlushExample<?php echo $i ?>">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="flush-headingOne<?php echo $i ?>">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne<?php echo $i ?>" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                Información
                                                            </button>
                                                        </h2>
                                                        <div id="flush-collapseOne<?php echo $i ?>" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample<?php echo $i ?>">
                                                            <div class="accordion-body">

                                                                <di>
                                                                    <form method="post" action="">
                                                                        Precio lead:
                                                                        <input id="leadPrice<?php echo $i ?>" name="leadPrice<?php echo $i ?>" type="number" value="<?php echo $resultadoParametres[2] ?>" />
                                                                        Precio Email:
                                                                        <input id="leadEmail<?php echo $i ?>" name="leadEmail<?php echo $i ?>" type="number" value="<?php echo $resultadoParametres[3] ?>" />
                                                                        Fecha de caducidad:
                                                                        <input id="leadCaduci<?php echo $i ?>" name="leadCaduci<?php echo $i ?>" type="date" value="<?php echo $resultadoParametres[4] ?>" />
                                                                        <p style="font-size:10px; color:grey">Datos de:
                                                                            <?php echo $resultadoParametres[1] ?> </p>
                                                                        <input hidden id="leadBD<?php echo $i ?>" name="leadBD<?php echo $i ?>" type="text" value="<?php echo $resultadoParametres[1] ?>" />
                                                                        <input type="submit" value="Actualizar" class="btn btn-primary">
                                                                    </form>
                                                            </div>

                                                            <?php
                                                            $postLeadPrice = 'leadPrice' . $i;
                                                            $postLeadEmail = 'leadEmail' . $i;
                                                            $postLeadCadu = 'leadCaduci' . $i;
                                                            $postLeadBD = 'leadBD' . $i;

                                                            if (isset($_POST[$postLeadEmail]) && isset($_POST[$postLeadPrice])) {

                                                                #Conectarse a la BD donde guarda los parametros de la instancias precio/fecha de renovacion, etc.
                                                                mysqli_select_db($cnn, "marketic-facturacion-bryan") or die('no se conecto a la  BBDD');

                                                                $queryInsertBDName = "UPDATE  clientesMautic
                                                                SET precio_lead=" . $_POST[$postLeadPrice] . ",
                                                                precio_email=" . $_POST[$postLeadEmail] . ",
                                                                fecha_caducidad='" . $_POST[$postLeadCadu] . "'
                                                                where name_bd='" . $_POST[$postLeadBD] . "'";

                                                                $cnn->query($queryInsertBDName);
                                                            ?>
                                                                <script>
                                                                    var URLactual = window.location;
                                                                    window.location.replace(URLactual);
                                                                </script>
                                                        <?php
                                                            }
                                                        }
                                                        ?>

                                                        </div>
                                                    </div>
                                                </div>
                </div>

<?php
                                            echo ('</td>');

                                            #Precios Lead
                                            if (($followingdata[0] <= 5000)) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 0) . ' €</td>');
                                            } elseif ($followingdata[0] > 5001  and $followingdata[0] <= 10000) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 1) . ' € </td>');
                                            } elseif ($followingdata[0] > 10001 and $followingdata[0] <= 20000) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 2) . ' €</td>');
                                            } elseif ($followingdata[0] > 20001 and $followingdata[0] <= 30000) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 3) . ' €</td>');
                                            } elseif ($followingdata[0] > 30001 and $followingdata[0] <= 40000) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 4) . ' €</td>');
                                            } elseif ($followingdata[0] > 40001 and $followingdata[0] <= 50000) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 5) . ' €</td>');
                                            } elseif ($followingdata[0] > 50001 and $followingdata[0] <= 60000) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 6) . ' €</td>');
                                            } elseif ($followingdata[0] > 60001 and $followingdata[0] <= 70000) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 7) . ' €</td>');
                                            } elseif ($followingdata[0] > 70001 and $followingdata[0] <= 80000) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 8) . ' €</td>');
                                            } elseif ($followingdata[0] > 80001 and $followingdata[0] <= 90000) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 9) . ' € Revisar precio :)</td>');
                                            } elseif ($followingdata[0] > 90001 and $followingdata[0] <= 100000) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 9) . ' €</td>');
                                            } elseif ($followingdata[0] > 100001 and $followingdata[0] <= 110000) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 9) . ' €</td>');
                                            } elseif ($followingdata[0] > 110001 and $followingdata[0] <= 120000) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 9) . ' €</td>');
                                            } elseif ($followingdata[0] > 120001 and $followingdata[0] <= 130000) {
                                                echo ('<td>' .  ($resultadoParametres[2] * 9) . ' € </td>');
                                            }

                                            #Precio email
                                            if (($resultadoSendTableCount[0] <= 10000)) {
                                                echo ('<td>' . ($resultadoParametres[3] * 0) . '€</td>');
                                            } elseif ($resultadoSendTableCount[0] > 10001 and $resultadoSendTableCount[0] <= 40000) {
                                                echo ('<td>' . ($resultadoParametres[3] * 1) . '€-</td>');
                                            } elseif ($resultadoSendTableCount[0] > 40001 and $resultadoSendTableCount[0] <= 100000) {
                                                echo ('<td>' . ($resultadoParametres[3] * 2) . '€</td>');
                                            } elseif ($resultadoSendTableCount[0] > 100001 and $resultadoSendTableCount[0] <= 200000) {
                                                echo ('<td>' . ($resultadoParametres[3] * 3) . '€</td>');
                                            } elseif ($resultadoSendTableCount[0] > 200001 and $resultadoSendTableCount[0] <= 300000) {
                                                echo ('<td>' . ($resultadoParametres[3] * 4) . '€</td>');
                                            }
                                        }
                                    }
                                }
                                echo "</tr>";
                            }
                        }
                        $cnn->close();
                        $i++;

                        #Instancias adicionales
                        echo '<tr>
                 <th  colspan="8">OTROS</th>
                 </tr>

                 <tr>
                 <td>' . $i++ . '</td>
                 <td>Firabarcelona</td>
                 <td colspan="6"><a class="clients"  href="https://staging.automation.barcelonawineweek.com/s/login" target="_target">Staging Barcelona Wineweek</a></td>
                 </tr>

                 <tr>
                 <td>' . $i++ . '</td>
                 <td>Firabarcelona</td>
                 <td colspan="6"><a class="clients" href="https://automation.barcelonawineweek.com/s/login" target="_blank">Barcelona Wineweek</a></td>
                 </tr>

                 <tr>
                 <td>' . $i++ . '</td>
                 <td>Ibsa</td>
                 <td colspan="6"><a  class="clients" href="https://app.ibsaibericaadvances.es/s/login" target="_blank">Ibsa Iberica Advances</a></td>
                 </tr>


                 <tr>
                 <td>' . $i++ . '</td>
                 <td>Ibsa</td>
                 <td colspan="6"><a class="clients" href="https://app.ibsagroup.es/s/login" target="_blank">Ibsagroup</a></td>
                 </tr>

                    ';
?>
</table>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.js" type="text/javascript"></script>
    <script>
        var tabla = document.querySelector("#MainContent_GridView");
        var datatable = new DataTable(tabla);
    </script>

</body>

</html>