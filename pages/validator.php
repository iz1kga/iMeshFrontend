<div class="row">
    <div class="col-md-12">
	<H1> Validatore identificativo nodo LoRa IT </H1>
	<hr/>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        Nome Nodo: <input type="text" id="myinput" value="NodeName GW 868 2af0"/>
	<button id="testBtn" onclick=test()>verifica</button>
        <div id="result"></div>
    </div>
    <div class="col-md-8">
        <img src="frontend/namingConvention/namingConvention.jpg"/>
    </div>
</div>

<script>

function getValue()  {
    return document.getElementById("myinput").value;
}

function test() {

    const validRegex = /^([A-Za-z0-9_-]+\s?)((((GW\s)?(433\s?|868\s?)([A-Fa-f0-9]{4})?))|([A-Fa-f0-9]{4}))/;
    const groupRegex = /^([A-Za-z0-9-]+\s?)(GW\s?)?(433\s?|868\s?)?([A-Fa-f0-9]{4})?/;
    //alert(validRegex.test(getValue()));
    var nodeName = getValue(); 
    var stripped = nodeName.replaceAll("_", " ");
    console.log(stripped);
    matches = stripped.match(groupRegex);
    var cont=0;
    if (validRegex.test(getValue())){
	$("#result").html("Il nome: <b>"+nodeName+"</b> è valido<BR/>");
        if (matches[2] !== undefined)
	    $("#result").append("É un <b>gateway</b><BR/>");
        if (matches[3] !== undefined)
	    $("#result").append("Opera sulla frequenza: <b>"+matches[3]+"MHz </b><BR/>");
        if (matches[4] !== undefined)
	    $("#result").append("Ha token: <b>"+matches[4]+"</b>");
    }
    else {
	    $("#result").html("Il nome: <b>"+nodeName+"</b> non è valido<BR/>");
    }
}

function match() {
    alert(getValue().match(regex));    
}

</script>
