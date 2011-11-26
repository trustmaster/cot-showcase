<!-- BEGIN: MAIN -->
<div id="lSide">
	<div class="lboxHD">{TITLE}</div>
	<div class="lboxBody">
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
		
		<table>
			<!-- BEGIN: ROW -->
			<tr>
				<!-- BEGIN: CELL -->
				<td class="{ODDEVEN}" style="width:30%;text-align:center">
					<h4><a href="{ITEM_LINK}">{ITEM_TITLE}</a></h4>
						{ITEM_IMAGE}<br />
					<strong>{php.L.Tags}:</strong> {ITEM_TAGS}<br />
					{ITEM_RATINGS}{ITEM_COMMENTS}<br />
				</td>
				<!-- END: CELL -->
			</tr>
			<!-- END: ROW -->
		</table>
		<div class="pagnav">{PAGEPREV} {PAGENAV} {PAGENEXT}</div>

		<!-- BEGIN: FORM -->
		<h4>{php.L.Add_another}</h4>
		<form action="{ACTION}" method="post">
			<input type="text" name="title" maxlength="{php.cfg.showcase.length}" value="{php.L.Title}" />
			http://<input type="text" name="domain" value="domain.tld" />
			<textarea name="descr" maxlength="255">{php.L.Short_descr}</textarea>
			<input type="text" name="tags" value="{php.L.Tags_input}" /><br />
			<input type="submit" value="{php.L.Submit}" />
		</form>
		<!-- END: FORM -->
	</div>

</div>
<div id="rSide">

	<div class="rboxHD">{php.L.Order_by}:</div>
	<div class="rboxBody">
		<a href="{BYRATE_URL}">{php.L.Rating}</a> |
		<a href="{BYDATE_URL}">{php.L.Date}
	</div>

	<div class="rboxHD">{php.L.Tags}:</div>
	<div class="rboxBody">{TAGCLOUD}</div>

	<!-- BEGIN: VALIDATION -->
	<div class="rboxHD">{php.L.Validation_queue}:</div>
	<div class="rboxBody">
		<ul>
		<!-- BEGIN: ROW -->
			<li><a href="{ITEM_LINK}">{ITEM_TITLE}</a></li>
		<!-- END: ROW -->
		</ul>
	</div>
	<!-- END: VALIDATION -->
</div>
<!-- END: MAIN -->