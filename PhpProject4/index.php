<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        $contador = isset($_POST['contador'])?$_POST['contador']:"0";
        $ordre = isset($_POST['nacionalitat'])?$_POST['nacionalitat']:"ID_AUT";
        $nom = isset($_POST['nom'])?$_POST['nom']:"";
        $codi = isset($_POST['codi'])?$_POST['codi']:"";
        $inici = 5870;
        printf("<form action='index.php' method='post'>\n");
        if ($ordre=="ID_AUT"){
            printf("<p>Ordre:<input type='radio' name='nacionalitat' value='ID_AUT' checked='true'>Codi\n");
            printf("<input type='radio' name='nacionalitat' value='NOM_AUT'>Nom</p>\n");
        } else {
            printf("<p>Ordre:<input type='radio' name='nacionalitat' value='ID_AUT'>Codi\n");
            printf("<input type='radio' name='nacionalitat' value='NOM_AUT' checked>Nom</p>\n");
        }
        printf("<p>Introdueix\n");
        printf("Codi:<input type='text' name='codi' value='$codi'>\n");
        printf("Nom-Aut:<input type='text' name='nom' value='$nom'>\n");
        printf("</p>\n");
        printf("<input type='hidden' name='contador' value='$contador'>\n");
        printf("<input type='submit' value='Cercar' name='cercar'>\n");
        printf("<input type='submit' value='Inici' name='inici'>\n");
        printf("<input type='submit' value='Enrera' name='enrere'>\n");
        printf("<input type='submit' value='Envant' name='endavant'>\n");
        printf("<input type='submit' value='Fi' name='fi'>\n");
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
        } else if ($nom!==""){
            if ($result = $mysqli->query("SELECT * FROM autors where NOM_AUT LIKE \"%$nom%\" ORDER BY $ordre;")) {
                $error = false;
            } else {
                printf("<p>\"$nom\" No es un nom valid</p>\n");
            }
        }else {
            if ($result = $mysqli->query("SELECT * FROM autors ORDER BY $ordre LIMIT ".(int)$contador.", 10;")) { //LIMIT 10
                $error = false;
            }
        }
        
        if(!$error){
            printf("Select returned %d rows.<br/>\n", $result->num_rows);
            printf("<table>\n<tr><td><img src='https://pbs.twimg.com/profile_images/818804541739110400/yh6DKyD9.jpg' alt='Logo Ies Pau Casesnoves' width='100'></img></td><td>Biblio</td></tr>\n");
            printf("\n<tr><td>Nom Autor</td><td>Id</td></tr>\n");
            for ($i=0;$i<$result->num_rows;$i++){
                $row = $result->fetch_array(MYSQLI_ASSOC);
            printf("<tr><td>%s</td><td>%s</td></tr>\n", $row["NOM_AUT"], $row["ID_AUT"]);
            }
            printf("</table> \n");
            $result->close();
        } else {
            printf("<p>Ups! un bug</p> \n");
        }
        $mysqli->close();
        ?>
    </body>
</html>
