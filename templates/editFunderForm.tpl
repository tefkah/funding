{**
 * plugins/generic/funding/templates/editFunderForm.tpl
 *
 * Copyright (c) 2014-2017 Simon Fraser University
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Form for editing a funder item
 *}

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#funderForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});

	$(document).ready(function(){ldelim}

		$(".funderNameIdentification").tagit({ldelim}
			fieldName: 'funderNameIdentification[]',
			allowSpaces: true,
			tagLimit: 1,
			tagSource: function(search, response){ldelim}
				$.ajax({ldelim}
					url: 'http://api.crossref.org/funders',
					dataType: 'json',
					cache: true,
					data: {ldelim}
						query: search.term + '*'
					{rdelim},
					success: 
						function( data ) {ldelim}
							var output = data.message.items;
							response($.map(output, function(item) {ldelim}
								return {ldelim}
									label: item.name + ' [' + item['alt-names'] + ']',
									value: item.name + ' [' + item.uri + ']'
								{rdelim}
							{rdelim}));
						{rdelim}	
				{rdelim});
			{rdelim}	
		{rdelim});

		$(".funderGrants").tagit({ldelim}
			fieldName: 'funderGrants[]',
			allowSpaces: true,
			singleField: true,
			singleFieldDelimiter: ";",
		{rdelim});

	{rdelim});	

</script>

{url|assign:actionUrl router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.funding.controllers.grid.FunderGridHandler" op="updateFunder" submissionId=$submissionId escape=false}
<form class="pkp_form" id="funderForm" method="post" action="{$actionUrl}">
	{csrf}
	{if $funderId}
		<input type="hidden" name="funderId" value="{$funderId|escape}" />
	{/if}
	{fbvFormArea id="funderFormArea" class="border"}
		{fbvFormSection}
			{fbvElement type="hidden" class="funderNameIdentification" label="plugins.generic.funding.funderNameIdentification" id="funderNameIdentification" value=$funderNameIdentification maxlength="255" inline=true size=$fbvStyles.size.LARGE}
			<span>{translate key="plugins.generic.funding.funderNameIdentification"}</span>
		{/fbvFormSection}
		{fbvFormSection}
			{fbvElement type="hidden" class="funderGrants" label="plugins.generic.funding.funderGrants" id="funderGrants" value=$funderGrants maxlength="255" inline=true size=$fbvStyles.size.LARGE}
			<span>{translate key="plugins.generic.funding.funderGrants"}</span>
		{/fbvFormSection}				
	{/fbvFormArea}
	{fbvFormSection class="formButtons"}
		{assign var=buttonId value="submitFormButton"|concat:"-"|uniqid}
		{fbvElement type="submit" class="submitFormButton" id=$buttonId label="common.save"}
	{/fbvFormSection}
</form>