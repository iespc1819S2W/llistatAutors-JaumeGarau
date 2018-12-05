<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        $contador = isset($_POST['contador'])?$_POST['contador']:"0";
        $ordre = isset($_POST['ordre'])?$_POST['ordre']:"ID_AUT";
        $ordenat = isset($_POST['ordenat'])?$_POST['ordenat']:"ASC";
        $nom = isset($_POST['nom'])?$_POST['nom']:"";
        $codi = isset($_POST['codi'])?$_POST['codi']:"";
       try {
            $mysqli = new mysqli("localhost", "root", "", "biblioteca");
            $mysqli->set_charset("utf8mb4");
        } catch(Exception $e) {
            error_log($e->getMessage());
            exit('Error conectant a la base de dades');
        }
        $result = $mysqli->query("SELECT COUNT(*) FROM autors where NOM_AUT LIKE \"%$nom%\";");
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $fi = $row["COUNT(*)"];
        $diferencia = $fi%20;
        if ($diferencia!=0){
            $fi -= $diferencia;
        } else {
            $fi -= 20;
        }
        $mysqli->close();

        if (isset($_POST['primer'])) {
            $contador = 0;
        } else if (isset($_POST['anterior'])) {
            if ($contador>=20){
                $contador -= 20;
            }
        } else if (isset($_POST['seguent'])){
            if ($contador<$fi){
                $contador =  $contador+20;
            } elseif ($contador>=$fi) {
                $contador = $fi;
        }
        } else if (isset($_POST['darrer'])){
            $contador = $fi;
        }
        $paginaActual = $contador/20;
        $paginaFinal = $fi/20;
        
        printf("<div style='float:left;'>\n<form action='LlistaAutors.php' method='post'>\n");
        if ($ordre=="ID_AUT"){
            printf("<p>Ordre:<input type='radio' name='ordre' value='ID_AUT' checked='true'>Codi\n");
            printf("<input type='radio' name='ordre' value='NOM_AUT'>Nom</p>\n");
        } else {
            printf("<p>Ordre:<input type='radio' name='ordre' value='ID_AUT'>Codi\n");
            printf("<input type='radio' name='ordre' value='NOM_AUT' checked>Nom</p>\n");
        }
        
        if ($ordenat=="ASC"){
            printf("<p>Ordenat:<input type='radio' name='ordenat' value='ASC' checked='true'>Ascendent\n");
            printf("<input type='radio' name='ordenat' value='DESC'>Descendent</p>\n");
        } else {
            printf("<p>Ordenat:<input type='radio' name='ordenat' value='ASC'>Ascendent\n");
            printf("<input type='radio' name='ordenat' value='DESC' checked>Descendent</p>\n");
        }
        
        printf("<p>Introdueix\n");
        printf("Codi:<input type='text' name='codi' value='$codi'>\n");
        printf("</p>\n");
        printf("<p>Nom-Aut:<input type='text' name='nom' value='$nom'>\n");
        printf("</p>\n");
        printf("<input  name='contador' value='$contador' type='hidden'>\n");// type='hidden'
        printf("<input type='submit' value='Cercar' name='cercar'>\n");
        printf("<input type='submit' value='Primer' name='primer'>\n");
        printf("<input type='submit' value='Anterior' name='anterior'>\n");
        printf("<input type='submit' value='Següent' name='seguent'>\n");
        printf("<input type='submit' value='Darrer' name='darrer'>\n");
        printf("<p>Pagina $paginaActual/$paginaFinal</p>\n");
        printf("</form>\n");

        try {
            $mysqli = new mysqli("localhost", "root", "", "biblioteca");
            $mysqli->set_charset("utf8mb4");
        } catch(Exception $e) {
            error_log($e->getMessage());
            exit('Error conectant a la base de dades'); // error per l’usuari !!!
        }
        
        $result;
        $error = true;
        if($codi!==""){
            if ($result = $mysqli->query("SELECT * FROM autors where ID_AUT = $codi;")) {
                $error = false;
            } else {
                printf("<p>\"$codi\" No es un ID valid</p>\n");
            }
        } else {
            if ($result = $mysqli->query("SELECT * FROM autors where NOM_AUT LIKE \"%$nom%\" ORDER BY $ordre $ordenat LIMIT ".(int)$contador.", 20;")) {
                $error = false;
            } else {
                printf("<p>\"$nom\" No es un nom valid</p>\n");
            }
        }
        
        if(!$error){
            printf("Select returned %d rows.<br/></div>\n", $result->num_rows);
            printf("<div style='float:left;'>\n<table>\n<tr><td><img src='https://pbs.twimg.com/profile_images/818804541739110400/yh6DKyD9.jpg' alt='Logo Ies Pau Casesnoves' width='100'></img></td><td>Biblio</td></tr>\n");
            printf("\n<tr><td>Nom Autor</td><td>Id</td></tr>\n");
            for ($i=0;$i<$result->num_rows;$i++){
                $row = $result->fetch_array(MYSQLI_ASSOC);
            printf("<tr><td>%s</td><td>%s</td></tr>\n", $row["NOM_AUT"], $row["ID_AUT"]);
            }
            printf("</table>\n</div>\n");
            $result->close();
        } else {
            printf("<p>Ups! un bug</p> \n");
        }
        $mysqli->close();
        ?>
    </body>
</html>