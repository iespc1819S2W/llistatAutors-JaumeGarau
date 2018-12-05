<!DOCTYPE html>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
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
        $nouNom = isset($_POST['nouNom'])?$_POST['nouNom']:"";
        $nacioNou = isset($_POST['nacioNou'])?$_POST['nacioNou']:"";
        $idEditar = 0;
        $mysqli = conectar();
        $result = $mysqli->query("SELECT COUNT(*) FROM autors where NOM_AUT LIKE \"%$nom%\";");
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $fi = $row["COUNT(*)"];
        $diferencia = $fi%20;
        if ($diferencia!=0){
            $fi -= $diferencia;
        } else {
            $fi -= 20;
        }

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
        } else if (isset($_POST['crear'])){
            $result = $mysqli->query("SELECT MAX(ID_AUT) FROM autors;");
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $maxId = $row["MAX(ID_AUT)"] + 1;
            if($nacioNou==""){
            $result = $mysqli->query("INSERT INTO autors (ID_AUT, NOM_AUT) VALUES ($maxId, '$nouNom');");
            } else {
            $result = $mysqli->query("INSERT INTO autors (ID_AUT, NOM_AUT, FK_NACIONALITAT) VALUES ($maxId, '$nouNom', '$nacioNou');");
            }
            $nouNom = "";
        } else if (isset($_POST['borrar'])){
            $idEliminar = isset($_POST['borrar'])?$_POST['borrar']:"0";
            $result = $mysqli->query("DELETE FROM autors WHERE autors.ID_AUT = $idEliminar;");
        } else if (isset($_POST['editar'])){
            $idEditar = isset($_POST['editar'])?$_POST['editar']:"0";
        } else if (isset($_POST['guardar'])){
            $idEdit = isset($_POST['guardar'])?$_POST['guardar']:"0";
            $nomEdit = isset($_POST['nomEdit'])?$_POST['nomEdit']:"";
            //printf($nomEdit . " Hola " . $idEdit);
            $result = $mysqli->query("UPDATE autors SET NOM_AUT = '$nomEdit' WHERE ID_AUT = $idEdit;");
        }
        $paginaActual = $contador/20;
        $paginaFinal = $fi/20;
        $resultNacio = $mysqli->query("SELECT NACIONALITAT FROM nacionalitats;");
        
        printf("<div style='float:left;'>\n<form action='mantenirTaula.php' method='post' id='form1'>\n");
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
        printf("<input  name='contador' value='$contador' type='hidden'>\n");
        printf("<input type='submit' value='Cercar' name='cercar'>\n");
        printf("<input type='submit' value='Primer' name='primer'>\n");
        printf("<input type='submit' value='Anterior' name='anterior'>\n");
        printf("<input type='submit' value='Següent' name='seguent'>\n");
        printf("<input type='submit' value='Darrer' name='darrer'>\n");
        printf("<p>Nom nou element:<input type='text' name='nouNom' value='$nouNom'></p>\n");
        printf("<p>Nacionalitat:<select name='nacioNou' form='form1'>\n");
        $opcionsNacio = "<option value='' selected>SenseValor</option>\n";
        for ($i=0;$i<$resultNacio->num_rows;$i++){
            $row = $resultNacio->fetch_array(MYSQLI_ASSOC);
            $nacionalitatSel = $row["NACIONALITAT"];
            $opcionsNacio .= "<option value='$nacionalitatSel'>$nacionalitatSel</option>\n";
        }
        printf($opcionsNacio);
        printf("</select></p>\n");
        printf("<input type='submit' value='Crear' name='crear'></p>\n");
        printf("<p>Pagina $paginaActual/$paginaFinal</p>\n");
        printf("</form>\n");

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
                printf("\n<tr><td>Nom Autor</td><td>Nacionalitat</td><td>Id</td></tr>\n");
            if($idEditar==0){
                for ($i=0;$i<$result->num_rows;$i++){
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $idTaula = $row["ID_AUT"];
                    $nomTaula = $row["NOM_AUT"];
                    $nacioTaula = $row["FK_NACIONALITAT"];
                    printf("<tr><td>$nomTaula</td><td>$nacioTaula</td><td>$idTaula</td><td><button type='submit' value='$idTaula' name='borrar' form='form1'>Borrar</button></td><td><button type='submit' value='$idTaula' name='editar' form='form1'>Editar</button></td></tr>\n");
                }
            } else {
                for ($i=0;$i<$result->num_rows;$i++){
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $idTaula = $row["ID_AUT"];
                    $nomTaula = $row["NOM_AUT"];
                    $nacioTaula = $row["FK_NACIONALITAT"];
                    if ($idTaula!=$idEditar){
                        printf("<tr><td>$nomTaula</td><td>$nacioTaula</td><td>$idTaula</td><td><button type='submit' value='$idTaula' name='borrar' form='form1'>Borrar</button></td><td><button type='submit' value='$idTaula' name='editar' form='form1'>Editar</button></td></tr>\n");
                    } else if ($nacioTaula!="") {
                        printf("<tr><td><input type='text' name='nomEdit' value='$nomTaula' form='form1'></td>n<td><select name='nacioEdit' form='form1'>\n");
                        $opcionsNacio = "<option value=''>SenseValor</option>\n";
                        for ($i=0;$i<$resultNacio->num_rows;$i++){
                            $row = $resultNacio->fetch_array(MYSQLI_ASSOC);
                            $nacionalitatSel = $row["NACIONALITAT"];
                            $opcionsNacio .= "<option value='$nacionalitatSel'>$nacionalitatSel</option>\n";
                        }
                        printf($opcionsNacio);
                        printf("</select></td>\n<td>$idTaula</td><td><button type='submit' value='$idTaula' name='guardar' form='form1'>Guardar</button></td><td><button type='submit' value='Carcela' name='carcela' form='form1'>Carcela</button></td></tr>\n");
                    } else {
                        printf("<tr><td><input type='text' name='nomEdit' value='$nomTaula' form='form1'></td>n<td><select name='nacioEdit'>\n$opcionsNacio</select></td>\n<td>$idTaula</td><td><button type='submit' value='$idTaula' name='guardar' form='form1'>Guardar</button></td><td><button type='submit' value='Carcela' name='carcela' form='form1'>Carcela</button></td></tr>\n");
                    }
                    
                }
            }
            printf("</table>\n</div>\n");
                $result->close();
        } else {
            printf("<p>Ups! un bug</p>\n");
        }
        $mysqli->close();
        
        function conectar(){
            try {
                $mysqli = new mysqli("localhost", "root", "", "biblioteca");
                $mysqli->set_charset("utf8mb4");
                return $mysqli;
            } catch(Exception $e) {
                error_log($e->getMessage());
                exit('Error conectant a la base de dades');
            }
        }
        
        ?>
    </body>
</html>