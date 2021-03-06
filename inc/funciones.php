<?

function porra($id_porrista,$fase=1,$admin=false) {

	global $conexion;

	$query="SELECT j.id,j.nombre,e.nombre pais, e.bandera FROM equipo e,jugador j,porrista p WHERE j.id_equipo=e.id AND j.id=p.id_goleador AND p.id='".$id_porrista."'";
	if ($fase>1) $query="SELECT j.id,j.nombre,e.nombre pais, e.bandera, a.nombre arbitro FROM equipo e,jugador j,porrista p,arbitro a WHERE j.id_equipo=e.id AND j.id=p.id_goleador AND a.id=p.id_arbitro AND p.id='".$id_porrista."'";
	$res=mysql_query($query,$conexion);
	$arra=mysql_fetch_array($res);

        if ($fase>1 && date("Y-m-d H:i:s")<"2012-06-21 20:45:00" && !$admin) {
            if ($arra["arbitro"]!="") "Este jugador ha realizado ya su apuesta para la segunda fase. La ver�s cuando empiece el primer partido de las elimintatorias :)";
            else echo "Este jugador todav�a no ha realizado su apuesta para la segunda fase.";
            return;
        }

	echo "<span class='red'><b>Pichichi:</b></span> <img src='".WEB_ROOT."/images/badges/".$arra["bandera"]."' width=16 height=16>&nbsp;".$arra["nombre"]." (".$arra["pais"].")<br>";
	if ($fase>1) echo "<span class='red'><b>�rbitro de la final:</b></span> ".$arra["arbitro"]."<br>";

	$condicionFase = ($fase>1) ? " AND p.fase>1 ":" AND p.fase=1 ";
	$query="SELECT e1.nombre nombre1, e1.bandera bandera1, e2.nombre nombre2, e2.bandera bandera2, a.resultado1, a.resultado2, p.fecha, p.hora, a.quiniela FROM apuesta a, equipo e1, equipo e2, partido p WHERE a.id_partido=p.id AND a.id_equipo1=e1.id AND a.id_equipo2=e2.id AND a.id_porrista='".$id_porrista."' ".$condicionFase." ORDER BY fecha,hora";
	$res=mysql_query($query,$conexion);

	while ($arra=mysql_fetch_array($res)) {
		if ($arra["resultado1"]==$arra["resultado2"] && $fase>1) {
			$empate="(gana <b>".$arra["nombre".$arra["quiniela"]]."</b> por penaltis)";
		}
		else $empate="";
		echo date("d/m/Y H:i",strtotime($arra["fecha"]." ".$arra["hora"]))." <img src='".WEB_ROOT."/images/badges/".$arra["bandera1"]."' width=16 height=16>&nbsp;".$arra["nombre1"]." <span class='red'><b>".$arra["resultado1"]."</b></span>&nbsp;<img src='".WEB_ROOT."/images/badges/".$arra["bandera2"]."' width=16 height=16>&nbsp;".$arra["nombre2"]." <span class='red'><b>".$arra["resultado2"]."</b></span> ".$empate."<br>";
	}

}

function puntos($id_porrista) {

	global $conexion;

	// Identidad del porrista
	$query="SELECT p.nick,concat(p.nombre,' ',p.apellido) nombreporrista,p.id_arbitro,j.id,j.nombre,e.nombre equipo,e.id idEquipo FROM porrista p,jugador j, equipo e WHERE p.id_goleador=j.id AND p.id='".$id_porrista."' AND j.id_equipo=e.id";
	$res=mysql_query($query,$conexion);
	$arra=mysql_fetch_array($res);
	$nick=$arra["nick"];
	$id_goleador=$arra["id"];
	$id_arbitro=$arra["id_arbitro"];
	$nombrePorrista=normalizaNombre($arra["nombreporrista"]);
	$nombreGoleador=$arra["nombre"];
	$equipoGoleador=$arra["equipo"];
	$idEquipoGoleador=$arra["idEquipo"];

	$query="SELECT
		e1.nombre equiporeal1, e1.bandera banderareal1, e2.nombre equiporeal2, e2.bandera banderareal2,
		e3.nombre equipoapuesta1, e3.bandera banderaapuesta1, e4.nombre equipoapuesta2, e4.bandera banderaapuesta2,
		p.fecha, p.hora, p.fase, p.resultado1 r1, p.resultado2 r2, a.resultado1 r3, a.resultado2 r4,
		p.id idPartido, p.quiniela q1, a.quiniela q2 FROM partido p LEFT JOIN apuesta a ON a.id_partido=p.id,equipo e1,equipo e2,equipo e3,equipo e4 WHERE p.id_equipo1=e1.id AND p.id_equipo2=e2.id AND a.id_equipo1=e3.id AND a.id_equipo2=e4.id AND a.id_porrista='".$id_porrista."' AND p.resultado1>=0 AND (p.id_equipo1='".$idEquipoGoleador."' OR p.id_equipo2='".$idEquipoGoleador."' OR a.quiniela=p.quiniela) ORDER BY p.fecha DESC, p.hora DESC";
	$res=mysql_query($query,$conexion);

	$puntos=0; $stringDevuelve="";

	//$stringDevuelve=$query."<br>";

	$puntosQuiniela[1]=1;
	$puntosQuiniela[2]=2;
	$puntosQuiniela[3]=3;
	$puntosQuiniela[4]=4;
	$puntosQuiniela[5]=5;
	$puntosQuiniela[6]=0; // 3er puesto
	$puntosResultado[1]=1.5;
	$puntosResultado[2]=3;
	$puntosResultado[3]=4.5;
	$puntosResultado[4]=6;
	$puntosResultado[5]=7.5;
	$rotulo[1]="PRIMERA FASE";
	//$rotulo[2]="OCTAVOS DE FINAL";
	$rotulo[2]="CUARTOS DE FINAL";
	$rotulo[3]="SEMIFINAL";
	$rotulo[4]="FINAL";
	//$rotulo[6]="TERCER Y CUARTO PUESTO";

	if ($id_arbitro==208) {

		$stringDevuelve="<span class='red'>�RBITRO DE LA FINAL</span><br>";
		$stringDevuelve.="<img src='".WEB_ROOT."/images/badges/portugal.png' width=16 height=16> <b>PEDRO PROEN�A (PORTUGAL)</b> <span class='green'>5 puntos</span> ";
		$stringDevuelve.="<br><br>";
		$puntos+=5;

	}

	if (in_array($id_goleador,array(242,229,209,234,249,224))) {

		$stringDevuelve.="<span class='red'>PICHICHI DE LA EURO (BALOTELLI, CRISTIANO RONALDO, DZAGOEV, FERNANDO TORRES, MANDZUKIC O MARIO G�MEZ)</span><br>";
		$stringDevuelve.="<b>".$nombreGoleador."</b> (".$equipoGoleador.") <span class='green'>5 puntos</span> ";
		$stringDevuelve.="<br><br>";
		$puntos+=5;

	}

	while ($arra=mysql_fetch_array($res)) {
		$stringPartido=""; $stringValido=false;
		$stringPartido="<span class='red'>".$rotulo[$arra["fase"]]."</span><br>";
		$stringPartido.="<img src='".WEB_ROOT."/images/badges/".$arra["banderareal1"]."' width=16 height=16> <b>".strtoupper($arra["equiporeal1"])."</b> <span class='red'>".$arra["r1"]."</span> ";
		$stringPartido.="<img src='".WEB_ROOT."/images/badges/".$arra["banderareal2"]."' width=16 height=16> <b>".strtoupper($arra["equiporeal2"])."</b> <span class='red'>".$arra["r2"]."</span> ";
		$stringPartido.="(".date("d/m/Y H:i",strtotime($arra["fecha"]." ".$arra["hora"])).")<br>";

		if ($arra["fase"]>1 && ($arra["r3"]==$arra["r4"])) {
			$empate="(gana ".$arra["equipoapuesta".$arra["q2"]]." por penaltis)";
		}
		else $empate="";

		if ($arra["idPartido"]!=63)
			$stringPartido.="Apuesta de <span class='red'>".$nombrePorrista."</span>: ".$arra["equipoapuesta1"]." <span class='red'>".$arra["r3"]."</span> ".$arra["equipoapuesta2"]." <span class='red'>".$arra["r4"]."</span> ".$empate."<br>";

		if ($arra["q1"]==$arra["q2"]) {

			if ($arra["fase"]>1) {
				 if ($arra["equiporeal1"]==$arra["equipoapuesta1"] && $arra["q1"]=="1") $stringValido=true;
				 else if ($arra["equiporeal2"]==$arra["equipoapuesta2"] && $arra["q1"]=="2") $stringValido=true;
			}
			else $stringValido=true;

			if ($stringValido) {
				if ($arra["r1"]>$arra["r2"] || $arra["q1"]=="1") $tipo_victoria="victoria de <span class='red'>".$arra["equiporeal1"]."</span>";
				if ($arra["r2"]>$arra["r1"] || $arra["q1"]=="2") $tipo_victoria="victoria de <span class='red'>".$arra["equiporeal2"]."</span>";
				if ($arra["r1"]==$arra["r2"] && $arra["fase"]==1) $tipo_victoria="<span class='red'>empate</span>";
                                $plural = ($puntosQuiniela[$arra["fase"]]==1) ? "":"s";
				$stringPartido.="Puntos por acertar ".$tipo_victoria.": <span class='green'>".$puntosQuiniela[$arra["fase"]]." punto".$plural."</span><br>";
				$puntos+=$puntosQuiniela[$arra["fase"]];
				if ($arra["r1"]==$arra["r3"] && $arra["r2"]==$arra["r4"]) {
					$stringPartido.="Puntos por acertar <span class='red'>resultado</span>: <span class='green'>".$puntosResultado[$arra["fase"]]." puntos</span><br>";
					$puntos+=$puntosResultado[$arra["fase"]];
				}
			}
		}
		if ($equipoGoleador==$arra["equiporeal1"] || $equipoGoleador==$arra["equiporeal2"]) {
			$query="SELECT count(*) g FROM goles WHERE id_goleador='".$id_goleador."' AND id_partido='".$arra["idPartido"]."'";
			$res2=mysql_query($query,$conexion);
			$arra2=mysql_fetch_array($res2);
			$goles=$arra2["g"];

			if ($goles) {
				$puntosPorGoles=$goles*1.5;
				$stringPartido.="Goles de <span class='red'>".$nombreGoleador."</span> en el partido: ";
				$stringPartido.="<span class='red'>".$goles."</span> <span class='green'>".$puntosPorGoles." puntos</span><br>";
				$stringValido=true;
				$puntos+=$puntosPorGoles;
			}
		}

		$stringPartido.="<br>";
		if ($stringValido) $stringDevuelve.=$stringPartido;
	}
	$stringDevuelve.="<b>Total puntos: <span class='green'>".$puntos."</span></b><br><br>";
	if ($puntos==0) $stringDevuelve="<span class='red'>".$nick."</span> todav�a no se ha estrenado, estamos esperando su momento :)";
	return array($puntos,$stringDevuelve);
}

function optionsGoleadores($selected="",$id_equipo1=0,$id_equipo2=0) {

	global $conexion;

	if ($id_equipo1) $query1=" AND (e.id='".$id_equipo1."'";
	if ($id_equipo1 && $id_equipo2) $query1.=" OR e.id='".$id_equipo2."'";
	if ($id_equipo1) $query1.=")";

	$query="SELECT j.id,j.nombre,e.nombre pais FROM equipo e,jugador j WHERE j.id_equipo=e.id $query1 ORDER BY nombre";
	$res=mysql_query($query,$conexion);

	while ($arra=mysql_fetch_array($res)) {
		$sel = ($arra["id"]==$selected) ? "selected":"";
		$devuelve.= "<option value='".$arra["id"]."' ".$sel.">".$arra["nombre"]." (".$arra["pais"].")</option>";
	}

	return $devuelve;

}

function partido($id_partido) {

	global $conexion,$nickRegistrado;

	$diasSemana=array("Domingo","Lunes","Martes","Mi�rcoles","Jueves","Viernes","S�bado");

	$query="SELECT p.*,e1.nombre equipo1,e2.nombre equipo2,e1.bandera bandera1,e2.bandera bandera2 FROM partido p LEFT JOIN equipo e1 ON e1.id=p.id_equipo1 LEFT JOIN equipo e2 ON e2.id=p.id_equipo2 WHERE p.id='$id_partido'";
	$res=mysql_query($query,$conexion);
	$partido=mysql_fetch_array($res);
	if ($partido["equipo1"]=="") {
		$partido["equipo1"]="???";
		$partido["bandera1"]="../ask.jpg";
	}
	if ($partido["equipo2"]=="") {
		$partido["equipo2"]="???";
		$partido["bandera2"]="../ask.jpg";
	}

	$temp=explode(" ",$partido["equipo1"]);
	if (count($temp)==2) $partido["equipo1"]=$temp[0]."<br>".$temp[1];
	if (count($temp)==3) $partido["equipo1"]=$temp[0]." ".$temp[1]."<br>".$temp[2];

	$temp=explode(" ",$partido["equipo2"]);
	if (count($temp)==2) $partido["equipo2"]=$temp[0]."<br>".$temp[1];
	if (count($temp)==3) $partido["equipo2"]=$temp[0]." ".$temp[1]."<br>".$temp[2];

	if ($partido["fase"]==1) {

		$query="SELECT quiniela,count(*) s FROM apuesta WHERE id_partido='$id_partido' GROUP BY quiniela";
		$res=mysql_query($query,$conexion);
		while ($arra=mysql_fetch_array($res)) {
			$quinielas[$arra["quiniela"]]=$arra["s"];
		}

		$totalapuestas=$quinielas["1"]+$quinielas["X"]+$quinielas["2"];
		$porcQuiniela["1"]=($totalapuestas) ? number_format($quinielas["1"]*100/$totalapuestas,0,".",""):0;
		$porcQuiniela["X"]=($totalapuestas) ? number_format($quinielas["X"]*100/$totalapuestas,0,".",""):0;
		$porcQuiniela["2"]=($totalapuestas) ? number_format($quinielas["2"]*100/$totalapuestas,0,".",""):0;
	}

	else {

		$pronosticos=array(); $totalPronosticos=0; $pronString="";

		$query="SELECT id_equipo1,count(*) s FROM apuesta WHERE quiniela='1' AND id_partido='".$id_partido."' AND id_equipo1!=0 GROUP BY id_equipo1";
		$res=mysql_query($query,$conexion);
		while ($arra=mysql_fetch_array($res)) {
			$pronosticos[$arra["id_equipo1"]]=$arra["s"];
			$totalPronosticos+=$arra["s"];
		}
		$query="SELECT id_equipo2,count(*) s FROM apuesta WHERE quiniela='2' AND id_partido='".$id_partido."' AND id_equipo2!=0 GROUP BY id_equipo2";
		$res=mysql_query($query,$conexion);
		while ($arra=mysql_fetch_array($res)) {
			$pronosticos[$arra["id_equipo2"]]=$arra["s"];
			$totalPronosticos+=$arra["s"];
		}
		foreach($pronosticos as $equipo => $pronostico) {
			$query="SELECT nombre,bandera FROM equipo WHERE id='".$equipo."'";
			$res=mysql_query($query,$conexion);
			$arra=mysql_fetch_array($res);
			$pronString.="<img src='".WEB_ROOT."/images/badges/".$arra["bandera"]."' width=20 height=20> ".$arra["nombre"].": <b>".number_format($pronostico*100/$totalPronosticos,0)."%</b><br>";
		}

	}

	$query="SELECT CONCAT(resultado1,'-',resultado2) r,count(*) s FROM apuesta WHERE id_partido='$id_partido' GROUP BY r ORDER BY s DESC";
	$res=mysql_query($query,$conexion);
	$arra=mysql_fetch_array($res);
	$resultado=$arra["r"];

	$devuelve="
	<table width=260>
		<tr>
			<td align='center' colspan='2'>".$diasSemana[date("w",strtotime($partido["fecha"]))]." <b>".date("d/m",strtotime($partido["fecha"]))."</b> Hora <b>".substr($partido["hora"],0,5)."</b></td>
		</tr>
		<tr>
			<td align='center'><img src='".WEB_ROOT."/images/badges/".$partido["bandera1"]."' width=64 height=64></td>
			<td align='center'><img src='".WEB_ROOT."/images/badges/".$partido["bandera2"]."' width=64 height=64></td>
		</tr>
		<tr>
			<td align='center'><h2>".$partido["equipo1"]."</h2></td>
			<td align='center'><h2>".$partido["equipo2"]."</h2></td>
		</tr>";

	date_default_timezone_set("Europe/Madrid");

	if ($partido["fase"]==1) {
	$devuelve.="
		<tr>
			<td align='center' colspan='2'>Nuestros apostantes dicen:<br><span class='red'><b>1: </b></span>".$porcQuiniela["1"]."%&nbsp;&nbsp;&nbsp;<span class='red'><b>X: </b></span>".$porcQuiniela["X"]."%&nbsp;&nbsp;&nbsp;<span class='red'><b>2: </b></span>".$porcQuiniela["2"]."%</td>
		</tr>
		<tr>
			<td align='center' colspan='2'>Resultado m�s repetido: <span class='red'><b>".$resultado."</b></span></td>
		</tr>";
	}
	else {
		if ($id_partido==63) {
			$devuelve.="
			<tr>
				<td align='center' colspan='2'>El partido por el tercer puesto s�lo cuenta en Enroporra a efectos del pichichi</td>
			</tr>";
		}
		else if (date("Y-m-d H:i:s")>"2012-06-21 20:45:00") {
		$devuelve.="
			<tr>
				<td align='center' colspan='2'>Nuestros apostantes dicen:<br>".$pronString."</td>
			</tr>";
		}
	}

	if ($nickRegistrado) {

		if ($partido["fase"]==1) {
		$query="SELECT a.resultado1,a.resultado2 FROM apuesta a, porrista p WHERE a.id_porrista=p.id AND a.id_partido='".$id_partido."' AND p.nick='".$nickRegistrado."'";
		$res=mysql_query($query,$conexion);
		$arra=mysql_fetch_array($res);
		$resultadoNick=$arra["resultado1"]."-".$arra["resultado2"];

		$devuelve.="
		<tr>
			<td align='center' colspan='2'>Apuesta de <span class='red'>".strtoupper($nickRegistrado)."</span>: <span class='red'><b>".$resultadoNick."</b></span></td>
		</tr>";
		}

	}

	/*else {
		$devuelve.="
		<tr>
			<td align='center' colspan='2'><a href='cuenta.php'>�Y qu� apost� yo?</a></span></td>
		</tr>";
	}*/

	$devuelve.="
	</table>";

	return $devuelve;

}

function normalizaNombre($nombre) {

	include(DOCUMENT_ROOT."/inc/apellidosConTilde.php");

	$nombre=ucwords(strtr(strtolower($nombre), "�������", "�������"));
	$nombre=str_replace(" De "," de ",str_replace(" Del "," del ",str_replace(" De La "," de la ",str_replace(" De Los "," de los ",str_replace(" De Las "," de las ",str_replace(" Y "," y ",str_replace(" E "," e ",$nombre)))))));
	for ($i=0; $i<strlen($nombre); $i++) {
		if ($nombre[$i]=="-"||$nombre[$i]=="'") $nombre[$i+1]=ucfirst(substr($nombre,$i+1,1));
	}
	for ($i=0; $i<count($apellidosConTilde); $i++) {
		$nombre=str_replace($apellidosConTilde[$i][0],$apellidosConTilde[$i][1],$nombre);
	}

	return $nombre;
}

function apuestaPartidos($id_porrista,$proximosPartidos) {

	global $conexion;

	$devuelve.="&nbsp;&nbsp;&nbsp;&nbsp;";

	foreach ($proximosPartidos as $partido) {
		if ($partido==63) continue;
		$query="SELECT e1.bandera bandera1,e2.bandera bandera2,a.resultado1,a.resultado2 FROM equipo e1,equipo e2,apuesta a WHERE a.id_equipo1=e1.id AND a.id_equipo2=e2.id AND a.id_porrista='$id_porrista' AND a.id_partido='$partido'";
		$res=mysql_query($query,$conexion);
		$arra=mysql_fetch_array($res);
		if ($num=mysql_num_rows($res))
			$devuelve.="<img src='".WEB_ROOT."/images/badges/".$arra["bandera1"]."' width=20 height=20> <span class='red'><b>".$arra["resultado1"]."-".$arra["resultado2"]."</b></span> <img src='".WEB_ROOT."/images/badges/".$arra["bandera2"]."' width=20 height=20>&nbsp;&nbsp;&nbsp;&nbsp;";
	}

	return $devuelve;
}

function cmp($a, $b) {
   	if ($a["puntos"] == $b["puntos"]) {
       	return (strtoupper(str_replace("�","A",str_replace("�","O",$a["nombre"]))) < strtoupper(str_replace("�","A",str_replace("�","O",$b["nombre"])))) ? -1 : 1;
   	}
   	return ($a["puntos"] < $b["puntos"]) ? 1 : -1;
}

function convierteAmigos($cadena) {
	if ($cadena=="") return;
	$cadena=substr(substr(str_replace("'","",$cadena),1),0,-1);
	$cadena="'".str_replace(",","','",$cadena)."'";
	return $cadena;
}

function clasificacion($tipo="completa") {

	global $conexion,$_COOKIE,$nickRegistrado,$NOMBRE_TORNEO,$PARTIDOS_SEGUNDA_FASE;

	date_default_timezone_set("Europe/Madrid");

	if ($tipo=="completa") {
		$cabecera="<span class='red'>GENERAL</span>";
		$condicionQuery=" AND id NOT IN (141,115,149)";
		$destacados=5;
	}
	if ($tipo=="amigos") {
		$cabecera="<span class='red'>de tu grupo de amigos</span>";
		$amigosEnro=convierteAmigos($_COOKIE["amigosEnro"]);
		if ($amigosEnro) {
			$condicionQuery=" AND nick IN ($amigosEnro)";
		}
		else return $devuelve;
		$destacados=1;
	}

	$query="SELECT count(*) c FROM partido WHERE fecha<='".date("Y-m-d")."' AND resultado1>=0";
	$res=mysql_query($query,$conexion);
	$arra=mysql_fetch_array($res);
	$partidos=$arra["c"];

	if ($partidos) {

		$nombresExistentes=array();
		$devuelve.= "Clasificaci�n $cabecera a d�a de hoy (<span class='red'><b>$partidos</b></span> partidos disputados y apuntados) :<br><br>";

		$query="SELECT id,fase FROM partido WHERE resultado1<0 ORDER BY fecha,hora LIMIT 4";
		$res=mysql_query($query,$conexion);
		$proximosPartidos=array();
		while ($arra=mysql_fetch_array($res)) {
			if ($arra["fase"]==1 || ($arra["fase"]>1 && date("Y-m-d H:i:s")>"2012-06-21 20:45:00"))
                        $proximosPartidos[]=$arra["id"];
		}

		$arrayPorristas=array();
		$query="SELECT id,nick,nombre,apellido,id_goleador,id_arbitro FROM porrista WHERE pagado='si' ".$condicionQuery;
		$res=mysql_query($query,$conexion);
		$i=0;
		while ($arra=mysql_fetch_array($res)) {
			$arrayPorristas[$i]["nick"]=$arra["nick"];
			$arrayPorristas[$i]["id"]=$arra["id"];
			$arrayPorristas[$i]["nombre"]=normalizaNombre($arra["nombre"]." ".$arra["apellido"]);
			$arrayPorristas[$i]["id_goleador"]=$arra["id_goleador"];
			$arrayPorristas[$i]["id_arbitro"]=$arra["id_arbitro"];
			if (in_array($arrayPorristas[$i]["nombre"],$nombresExistentes)) $arrayPorristas[$i]["nombre"].=" (2)";
			else $nombresExistentes[]=$arrayPorristas[$i]["nombre"];
			list($arrayPorristas[$i]["puntos"],$arrayPorristas[$i]["string"])=puntos($arra["id"]);
			$i++;
		}

		usort($arrayPorristas, "cmp");

		$clasificacion=1; $puntuacionAnterior="";
		$devuelve.= "<table cellpadding=0 cellspacing=0 border=0>";
		$devuelve.= "<tr><td colspan='3'></td><td>Pts.</td><td>Apuesta</td><td>&nbsp;&nbsp;&nbsp;&nbsp;Apuesta en pr�ximos partidos</td></tr>";
		foreach ($arrayPorristas as $porrista) {

			if ($bgColor=="#DDDDDD") $bgColor="#EEEEEE"; else $bgColor="#DDDDDD";

			if ($clasificacion<=$destacados) {
				$head1="<h2>";
				$head2="</h2>";
                                $retorno="";
			}
			else {
                            $head1=$head2="";
                            $retorno="<br>";
                        }


                        if (!$stringGoleador[$porrista["id_goleador"]]) {
                                $query="SELECT j.nombre nombrej,e.nombre nombree,e.bandera,count(g.id) goles FROM equipo e,jugador j LEFT JOIN goles g ON g.id_goleador=j.id WHERE j.id_equipo=e.id AND j.id='".$porrista["id_goleador"]."' GROUP BY j.id";
        			$res=mysql_query($query,$conexion);
                		$arra=mysql_fetch_array($res);
                                for ($i=1; $i<=$arra["goles"]; $i++) $stringGoleador[$porrista["id_goleador"]].="&nbsp;<img src='".WEB_ROOT."/images/balon.gif' width=10 height=10>";
                                $stringGoleador[$porrista["id_goleador"]].="&nbsp;<img src='".WEB_ROOT."/images/badges/".$arra["bandera"]."' width=10 height=10> <span class='little'>".$arra["nombrej"]." (".$arra["nombree"].", ".$arra["goles"].")</span>";
                        }

			$clasificacionString = ($puntuacionAnterior==$porrista["puntos"]) ? "":$clasificacion;
			$puntuacionAnterior=$porrista["puntos"];

			$colorDestacado = (strtolower($nickRegistrado)==strtolower($porrista["nick"])) ? "bgColor='#FFFF00'":"bgColor='$bgColor'";
                        if (strtolower($nickRegistrado)!=strtolower($porrista["nick"]) && $GLOBALS["amigos"]!=1) {
                            if (strpos(strtolower($_COOKIE["amigosEnro"]),",".strtolower($porrista["nick"]).",")!==false)
                                $colorDestacado="bgColor='#DDAA33'";
                        }

			$colorFuturaApuesta = (strtolower($nickRegistrado)==strtolower($porrista["nick"])) ? "bgColor='#FFFF00'":"bgColor='$bgColor'";
                        $stringProximasApuestas = apuestaPartidos($porrista["id"],$proximosPartidos);
                        if (!count($proximosPartidos)) {
                            if (date("Y-m-d H:i:s")<"2012-06-21 20:30:00") $stringProximasApuestas="&nbsp;Cuando comience la siguiente fase publicaremos todas las apuestas&nbsp;";
                        }
                        if ($porrista["id_arbitro"]>0 && date("Y-m-d H:i:s")<"2012-06-21 20:45:00") {
                            $query="SELECT COUNT( * ) FROM partido p, apuesta a WHERE a.id_partido = p.id AND p.fase >1 AND a.id_equipo1 >0 AND a.id_equipo2 >0 AND a.id_porrista =".$porrista["id"];
                            $resComprobacion=mysql_query($query,$conexion);
                            $partidosSegundaFase=mysql_fetch_array($resComprobacion);
                            $partidosSegundaFase=$partidosSegundaFase[0];
                            if ($partidosSegundaFase==$PARTIDOS_SEGUNDA_FASE) $segundaFaseOK="<span class='green'>2� Fase OK</span>";
                            else $segundaFaseOK="<span class='black'>Problema rellenando segunda fase</span>";
                        }
                        else $segundaFaseOK="";

			$devuelve.= "<tr ".$colorDestacado."><td nowrap>";
			$devuelve.= $head1."&nbsp;<span class='red'><b>".$clasificacionString."</b></span>".$head2."</td><td nowrap>".$head1."&nbsp;".$porrista["nombre"]." [<span class='red'><b>".$porrista["puntos"]."</b></span>]&nbsp;".$head2.$segundaFaseOK.$retorno.$stringGoleador[$porrista["id_goleador"]];
			$devuelve.= "</td><td width='20'></td><td align='center' bgColor='#FFFFFF' style='padding: 0px 0px 0px 10px;'>";
			$devuelve.= "<div id='enlace_".str_replace(" ","",strtoupper($porrista["nick"]))."'><a alt='Ver los puntos que lleva ".$porrista["nombre"]."' href='javascript:verDetalle(\"".str_replace(" ","",strtoupper($porrista["nick"]))."\")'><img src='".WEB_ROOT."/images/bombilla.jpg' alt='Ver los puntos que lleva ".$porrista["nombre"]."' width=32 height=32></a></div>";
			$devuelve.= "</td><td align='center' bgColor='#FFFFFF'><a href='".WEB_ROOT."/cuenta.php?accion=ver&nick=".$porrista["nick"]."'><img src='".WEB_ROOT."/images/sobre.jpg' alt='Ver la apuesta completa de ".$porrista["nombre"]."' width=32 height=32></a></td>";
			$devuelve.= "<td ".$colorFuturaApuesta." nowrap>".$stringProximasApuestas."</td>";
			$devuelve.= "</tr>";
			$devuelve.= "<tr><td colspan='5'>";
			$devuelve.= "<div id='detalle_".str_replace(" ","",strtoupper($porrista["nick"]))."' style='display:none'><p>".$porrista["string"]."</p></div>";
			$devuelve.= "</td></tr>";
			$clasificacion++;
			if ($clasificacion==($destacados+1)) $devuelve.= "<tr><td colspan='5' height=20></td></tr>";
		}
		$devuelve.= "</table><br><br>";

	} // END existen partidos reales

	else {
		$devuelve.= "<p>Todav�a no ha comenzado ".$NOMBRE_TORNEO." en Enroporra :)</p>";
	}

	$WEB_ROOT=WEB_ROOT;
	$devuelve.= <<< EOT
		<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js'></script>
		<script type='text/javascript'>
		function verDetalle(nombre) {
			$("#detalle_"+nombre).show();
			$("#enlace_"+nombre).html("<a href='javascript:ocultarDetalle(\""+nombre+"\")'><img src='$WEB_ROOT/images/bombillaoff.jpg' alt='Ver los puntos que lleva "+nombre+"' width=32 height=32></a>");
		}
		function ocultarDetalle(nombre) {
			$("#detalle_"+nombre).hide();
			$("#enlace_"+nombre).html("<a href='javascript:verDetalle(\""+nombre+"\")'><img src='$WEB_ROOT/images/bombilla.jpg' alt='Ver los puntos que lleva "+nombre+"' width=32 height=32></a>");
		}

		</script>
EOT;

	return $devuelve;

} // END funcion clasificacion

?>
