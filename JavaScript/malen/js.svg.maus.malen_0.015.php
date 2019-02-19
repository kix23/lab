<?php
echo $_GET['aktion']."<hr>";

#speichern - datei schreiben 
if(isset($_POST['aktion']) && $_POST['aktion']=="speichern"){
echo "Speichern geladen <hr>";
	$bild		= $_POST['bild'];
	$datei 		= fopen("bild.txt","w+");
	#$bild2 	= fgets($datei, 235);
	rewind($datei);
	fwrite($datei, $bild);
	fclose($datei);
}
#laden - datei lesen 
if(isset($_GET['aktion']) && $_GET['aktion']=="laden"){
echo "laden gespeichert <hr>";
	$datei 		= fopen("bild.txt","r+");
	$bild="";
	while (!feof($datei)) {
		$bild.= fgets($datei);
	}
	#$bild		= fgets($datei, 9999);
	#$bild2 	= fgets($datei, 235);
	rewind($datei);
	fclose($datei);
	echo $bild;
}
?>
<html>
<head>
</head>
<body>
<!-- Boxen -->
<style type="text/css">
	div { border:1px solid #888; color:#FFFFFF;font-family:Arial }
	#farben 			{ cursor: default; width:100px; height:200px; position:absolute; top:023px; left:023px; z-index:0;}
	#pinsel 			{ cursor: default; width:100px; height:100px; position:absolute; top:223px; left:023px; z-index:0;}
	#funktionen			{ cursor: default; width:100px; height:050px; position:absolute; top:323px; left:023px; z-index:0;}
	#leinwand			{ cursor: none;    width:800px; height:600px; position:absolute; top:023px; left:123px; z-index:0;}
	#dateifunktionen	{ cursor: default; width:100px; height:050px; position:absolute; top:375px; left:023px; z-index:0;}
</style>

<script type="text/javascript">
	//Start Variablen
	var MausPosition 	={x:0, y:0}; // Mausposition
	var Mausklick 		=false; // Maus gedrueckt?
	var Punkte 			=""; // Linienpfad
	
	farbe		="#000000"; // Startfarbe
	pinsel		="3"; // Pinselgroesse
	Linie		={}; // Linienobjekt
	LinieNr		=0; // Liniennummer

	function farbwahl(farbklick) { 
		farbe=farbklick;
		aktuelle_farbe=document.getElementById("aktuelle_farbe"); 
		aktuelle_farbe.setAttribute('style',"fill:"+farbe+";stroke-width:1;stroke:#000000");
	}

	function pinselwahl(pinselklick) { 
		pinsel=pinselklick;
		aktueller_pinsel=document.getElementById("aktueller_pinsel");
		aktueller_pinsel.setAttribute('style',"fill:#000000;stroke-width:"+(15-pinsel)+";stroke:#ffffff");
	}

	function undo() {
	if(LinieNr>0){
		var undo=document.getElementById("linie"+LinieNr);
		undo.parentNode.removeChild(undo);
		LinieNr--;
		}
		else { alert("Nix da..."); }
	}

	function laden(){
		alert("Lade.");
		lade=new XMLHttpRequest();
		window.onunload = lade.abort ();
		lade.open("GET","?aktion=laden",true); //php_self einbauen...
		lade.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
		
		while(lade.readyState<4){
			alert(lade.readyState);
			if (lade.readyState==4 && lade.status==200){
				alert(lade.responseText);
	//			document.liveText.focus();
	//			document.selection.createRange().duplicate().text = what;
			}
		}
		lade.send();
		bild='<?php if(isset($bild)){ echo $bild; } else{ echo "";}?>';
		if(bild!=""){
			svg=document.getElementById("svg");
			svg.appendChild(bild);
		}
	}

	function speichern(){
		alert("Speicherere .re.re. :)");
		speicher=new XMLHttpRequest();
		window.onunload = speicher.abort ();
		speicher.open("POST","",true); //php_self einbauen...
		speicher.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
		bild="";
		for(var i=1; i<=LinieNr; i++){
			bild=bild.concat("<polyline "
				+"id='"+document.getElementById('linie'+i+"").getAttribute('id')
				+"' points='"+document.getElementById('linie'+i+"").getAttribute('points')
				+"' style='"+document.getElementById('linie'+i+"").getAttribute('style')
				+"'></polyline>\n");
			//alert(document.getElementById('linie'+i+"").getAttribute('points'));
		}
		speicher.send("aktion=speichern&bild="+encodeURI(bild));
	}

	window.addEventListener('mousemove', function(MausStalker){
		MausPosition.x=MausStalker.clientX-123;
		MausPosition.y=MausStalker.clientY-23;
		
		//Mauszeiger
		var PinselX = document.getElementById("PinselX");
		PinselX.setAttribute("x1",MausPosition.x-23);
		PinselX.setAttribute("x2",MausPosition.x+23);
		PinselX.setAttribute("y1",MausPosition.y);
		PinselX.setAttribute("y2",MausPosition.y);
		
		var PinselY = document.getElementById("PinselY");
		PinselY.setAttribute("x1",MausPosition.x);
		PinselY.setAttribute("x2",MausPosition.x);
		PinselY.setAttribute("y1",MausPosition.y-23);
		PinselY.setAttribute("y2",MausPosition.y+23);
		
		//Malen
		if(Mausklick==true) { 
			Punkte=Punkte.concat(MausPosition.x+","+MausPosition.y+" "); 
			Linie.LinieNr=document.getElementById("linie"+LinieNr);
			Linie.LinieNr.setAttribute("points", Punkte);
		}
	}, false);

	//Neue Linie
	addEventListener("mousedown",function(MausDruck){
		if(MausPosition.x>=0 && MausPosition.y>=0) {
				LinieNr++;
				Punkte = "";
				Linie.LinieNr=document.getElementById("linie"+LinieNr);
				Linie.LinieNr=document.createElementNS("http://www.w3.org/2000/svg", "polyline");
				Linie.LinieNr.setAttribute('id','linie'+LinieNr);
				Linie.LinieNr.setAttribute('points',MausPosition.x+","+MausPosition.y+" ");
				Linie.LinieNr.setAttribute("style","fill:none;stroke:"+farbe+";stroke-width:"+pinsel+"");
				svg=document.getElementById("svg");
				svg.appendChild(Linie.LinieNr);
				Linie=document.getElementById("linie"+LinieNr);
				Mausklick=true;
			}
	}, false);
	//Linie Fertig
	addEventListener("mouseup", function(MausGeklickt){
		Mausklick=false;
	}, false);
</script>

<!-- FarbpaletteSVG -->
<div id="farben">
	<span style="font-size:23px; font-family:Arial; color:#AAAAAA">Farbe:</span>
	<svg id="farbe" width=80 height=150>
  		<rect x="00" y="00" width="15" height="15" style="fill:#000000;stroke-width:1;stroke:#000000" onclick="farbwahl('#000000')"></rect>
  		<rect x="15" y="00" width="15" height="15" style="fill:#ff0000;stroke-width:1;stroke:#000000" onclick="farbwahl('#ff0000')"></rect>
  		<rect x="30" y="00" width="15" height="15" style="fill:#00ff00;stroke-width:1;stroke:#000000" onclick="farbwahl('#00ff00')"></rect>
  		<rect x="45" y="00" width="15" height="15" style="fill:#0000ff;stroke-width:1;stroke:#000000" onclick="farbwahl('#0000ff')"></rect>
  		<rect x="60" y="00" width="15" height="15" style="fill:#ffffff;stroke-width:1;stroke:#000000" onclick="farbwahl('#ffffff')"></rect>
		
		<rect id="aktuelle_farbe" x="23" y="23" width="23" height="23" style="fill:#000000;stroke-width:1;stroke:#ffffff"></rect>  		
	</svg>
</div>
<!-- PinselStaerkeSVG -->
<div id="pinsel">
	<span style="font-size:23px; font-family:Arial; color:#AAAAAA">Pinsel:</span>
	<svg id="PinselGroesse" width=80 height=150>
		<rect x="00" y="00" width="15" height="15" style="fill:#000000;stroke-width:12;stroke:#ffffff" onclick="pinselwahl('03')"></rect>
		<rect x="15" y="00" width="15" height="15" style="fill:#000000;stroke-width:10;stroke:#ffffff" onclick="pinselwahl('05')"></rect>
		<rect x="30" y="00" width="15" height="15" style="fill:#000000;stroke-width:08;stroke:#ffffff" onclick="pinselwahl('07')"></rect>
		<rect x="45" y="00" width="15" height="15" style="fill:#000000;stroke-width:06;stroke:#ffffff" onclick="pinselwahl('09')"></rect>
		<rect x="60" y="00" width="15" height="15" style="fill:#000000;stroke-width:03;stroke:#ffffff" onclick="pinselwahl('12')"></rect>

		<rect id="aktueller_pinsel" x="23" y="23" width="15" height="15" style="fill:#000000;stroke-width:12;stroke:#ffffff"></rect>  		
	</svg>
</div>
<!-- Funktionen -->
<div id="funktionen">
	<svg id="funktionsKnoebbe"width=80 height=150>
		<!-- Undo -->
		<rect x="00" y="10" rx="10" ry="10" width="42" height="15" style="fill:#ffffff;stroke-width:1;stroke:#000000;opacity:0.5" ></rect> 		
		<text x="04" y="21" font-family="Arial" font-size="12" fill="#000000" onclick="undo()">undo</text>
		<!-- Undo Ende -->
	</svg>
</div>
<!-- Speichern -->
<div id="dateifunktionen">
	<svg id="dateiknoebbe"width=80 height=150>
		<rect x="00" y="10" rx="10" ry="10" width="60" height="15" style="fill:#ffffff;stroke-width:1;stroke:#000000;opacity:0.5" ></rect> 		
		<text x="04" y="21" font-family="Arial" font-size="12" fill="#000000" onclick="speichern()">speichern</text>
		<rect x="00" y="25" rx="10" ry="23" width="60" height="15" style="fill:#ffffff;stroke-width:1;stroke:#000000;opacity:0.5" ></rect> 		
		<text x="04" y="36" font-family="Arial" font-size="12" fill="#000000" onclick="laden()">laden</text>
	</svg>
	<br><a href="?aktion=laden">laden</a>
</div>
<!-- Speichern Ende -->
<!-- Zeichenflaeche -->
<div id="leinwand">
	<svg id="svg" width="800px"; height="600px";>
	<!-- MauszeigerSVG.start -->
	  <line id="PinselX" x1="0" y1="0" x2="0" y2="0" style="stroke:rgb(200,200,200);stroke-width:2" />
	  <line id="PinselY" x1="0" y1="0" x2="0" y2="0" style="stroke:rgb(200,200,200);stroke-width:2" />
	<!-- MauszeigerSVG.ende -->
	</svg>
</div>

</body>
</html>
