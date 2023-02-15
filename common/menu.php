<div class="container h-100">
	<div class="row h-100">
		<div class="col-md-12">
			<nav class="navbar navbar-expand-lg navbar-light bg-light">
                            <a class="navbar-brand" href="https://hub.iz1kga.it">
                                <img src="frontend/logos/lorait_small.png" width="50" height="50" alt="">
                                 LoRa Italia Live Map
                             </a>
                            <ul class="navbar-nav">
                                <li class="nav-item active">
                                    <a class="nav-link" href="https://t.me/meshtastic_italia">Join telegram</a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link" href="https://meshwiki.iz1kga.it">Docs</a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link" href="?page=packetRate">PacketeRate</a>
                                </li>
                             </ul>
			</nav>
			<div class="h-75 row">
				<div class="col-md-8" style="min-height:350px">
					<div id='map'></div>
				</div>
				<div class="col-md-4">
                                        <div id="tableDiv" class="tbodyDiv">
					    <table class="table table-striped" id="nodeTable">
                                                <thead class="sticky-top bg-white"><tr><th scope="col">Name</th><th scope="col">ID</th><th scope="col">L.H.</th></tr></thead>
    					    </table>
                                        </div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<textarea class="form-control" id="updateBox" rows="5"></textarea>
				</div>
			</div>
		</div>
	</div>
</div>
