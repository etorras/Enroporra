<?
$query="SELECT * FROM noticias WHERE activa='si' ORDER BY fecha DESC";
$res=mysql_query($query,$conexion);
while ($arra=mysql_fetch_array($res)) {
	echo "<h1 class='red'>".$arra["titular"]."</h1>";
	echo "<p>".$arra["cuerpo"]."</p>";
}
?>
<h1 class='red'>BIENVENIDO</h1>
<p>
Bienvenido a la ENROPORRA de la Eurocopa de Polonia y Ucrania 2012. El mejor site para seguir online los resultados y pron�sticos de la <b>Porra de la Eurocopa de F�tbol</b>. <br><br>

Llevamos 18 a�os (desde EE.UU. 1994) organizando Porras sin �nimo de lucro (entre amigos) en todas las Eurocopas y Mundiales de F�tbol.<br><br>

La Porra consta de dos fases, que se detallan m�s ampliamente en las <a href='<?= $ENLACE_BASES ?>' target='_blank'>BASES</a>:<br><br>

<b>a)</b> Primera fase > Se elaboran pron�sticos de todos los partidos de la primera fase, otorg�ndose puntos tanto por acertar el ganador (o empate) como por el resultado. Igualmente en la primera fase hay que apostar por un Pichichi para el torneo que ir� dando puntos por cada gol que anote en el campeonato. Si al final del campeonato tu Pichichi se corona como el m�ximo goleador del torneo, tendr�s puntos adicionales.<br><br>

<b>b)</b> Segunda fase > Se presenta una vez conocido el cuadro final del torneo. Igualmente hay que apostar por ganadores y resultados, elaborando el cuadro final. Los puntos se reparten igual que en la primera fase. Tambi�n te puedes llevar puntos adicionales si aciertas el �rbitro de la final. <b>IMPORTANTE</b>: Entre la 1� y 2� fase habr� muy poco tiempo, por lo que habr� que hacerlo r�pidamente en ese margen de tiempo.<br><br>

<b>c)</b> Habr� premios (10% de la recaudaci�n) para el ganador de la Primera Fase y para los cinco primeros clasificados al finalizar la Segunda Fase. (40% recaudaci�n para el primero, 20% para el segundo, 15% para el tercero, 10% para el cuarto y 5% para el quinto).<br><br>

<b>d)</b> Existe una comisi�n que resuelve todas las dudas e incidencias durante el desarrollo de la Porra. <br><br>

<b>e)</b> El participante en la Porra acepta las condiciones y t�rminos de la misma. <b>Importante:</b> Si no se ha formalizado el pago antes del inicio del primer partido, se quedar� excluido de la misma, tal y como dicen las bases.<br><br>


Gracias por visitarnos y <a href='apuesta.php'>�Rellena tu apuesta!</a><br><br>&nbsp;
</p>

<? include "mapa.php" ?>
