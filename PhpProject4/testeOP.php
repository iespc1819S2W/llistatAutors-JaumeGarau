<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <!-- 
        <form action="index.php" method="post">
            <p>Ordre:<input type="radio" name="nacionalitat" value="ID_AUT" checked>Codi
                <input type="radio" name="nacionalitat" value="NOM_AUT">Nom</p>
            <p>Introdueix
            Codi:<input type="text" name="codi" value="<?=$codi?>">
            Nom-Aut:<input type="text" name="nom" value="<?=$nom?>">
            </p>
            <input type="hidden" name="contador" value="<?=$contador?>">
            <input type="submit" value="Cercar" name="cercar">
        </form>
        -->
        <?php
        try {
            $mysqli = new mysqli("localhost", "root", "", "biblioteca");
            $mysqli->set_charset("utf8mb4");
        } catch(Exception $e) {
            error_log($e->getMessage());
            exit('Error conectant a la base de dades'); // error per l’usuari !!!
        }
        if ($result = $mysqli->query("SELECT * FROM llibres LIMIT 10")) {
            printf("Select returned %d rows.<br/>\n", $result->num_rows);
        for ($i=0;$i<$result->num_rows;$i++){
            $row = $result->fetch_array(MYSQLI_ASSOC);
            printf ("%s (%s)<br/>\n", $row["TITOL"], $row["ANYEDICIO"]);
        }
        $result->close();
        }
        $mysqli->close();
        ?>
    </body>
</html>
