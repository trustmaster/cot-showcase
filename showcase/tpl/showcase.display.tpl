<!-- BEGIN: MAIN -->
	<div class="mboxHD">{TITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{PLUGIN_SUBTITLE}</div>

		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

		<form action="{ACTION}" method="post">
		<h3>{ITEM_TITLE}</h3>
		<a href="{ITEM_URL}">{ITEM_URL}</a><br />
		{ITEM_IMAGE}<br />
		<strong>{php.L.Tags}:</strong> {ITEM_TAGS}<br />
		{ITEM_DESCR}<br />
		{ITEM_SAVE} &nbsp;&nbsp; {ITEM_REMOVE} &nbsp;&nbsp; {ITEM_VALIDATE}
		</form>
		{php.L.Owner}: {ITEM_OWNER} {ITEM_COMMENTS}
		{ITEM_RATINGS} <br />

		<h3>{php.L.Comments}</h3>
		{ITEM_COMMENTS_DISPLAY}
	</div>
<!-- END: MAIN -->