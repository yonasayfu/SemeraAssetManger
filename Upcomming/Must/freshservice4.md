<div class="leftcontent">
				   <!-- Hide Error message  if the application is load via iframe (in nested app flow of marketplace)-->
					    
					<div class="alert notice" id="noticeajax" style="display:none;"></div>

					<div id="account-restricted-modal-container" class="hide" data-backdrop="static" data-target="#account-restricted-modal" data-reset-on-close="false" data-show-close="false" data-classes="itil_new_dialog modal-large" rel="freshdialog">
					</div>

					

					<div class="pagearea" id="Pagearea">
						

   <div id="sticky_header" rel="sticky" data-scroll-top="true" class="tkt-details-sticky clearfix collapsed" data-collapsed="true" data-sticky-bottom="true">
         <div class="pull-right buttons_container">
            <a href="javascript:void(0)" class="padding6 export_contract">Export</a>
               <a href="/cmdb/contracts/new" class="btn btn-primary add_new">Add New</a>
         </div>
   <div class="page-filter">
         <div style="display: none;">
            <a class="slider-btn btn ficon-filter active" href="#leftcol" data-parent="#rightCol" id="sliding"></a>
         </div>
   </div>
   <div id="page-slider">
         <div>
               <a class="slider-btn btn-primary btn ficon-filter" data-dismiss="slider"></a>
         </div>
      </div>
   </div>
   <div class="containerFull contracts-container" style="padding-right: 300px;">

   <div id="leftcol" class="page-sidebar-neutral" style="display: block; width: 300px;">
      <div class="contract-fields-container">
            <label class="attach-heading">
                Filter
            </label>
         <form autocomplete="off">  <!-- Added form to remove selected dropdown options from select2 on browser back button-->


            <div id="div_ff_contract_type" class="ff_item show" condition="contract_type" operator="is_in" ff_name="default">
               <label class="title">
                  Contract Type
               </label>

               <div class="select2-container show select2" id="s2id_contract_type"><a href="javascript:void(0)" class="select2-choice" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-129">All</span><abbr class="select2-search-choice-close"></abbr>   <div class="select2-arrow" role="presentation"><b role="presentation"></b></div></a><label for="s2id_autogen129" class="select2-offscreen"></label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" aria-labelledby="select2-chosen-129" id="s2id_autogen129"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen129_search" class="select2-offscreen"></label>       <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-129" id="s2id_autogen129_search">       <i class="ficon-search"></i>       <i class="ficon-close close-icon"></i>   </div>   <ul class="select2-results" role="listbox" id="select2-results-129">   </ul></div></div><select name="contract_type" id="contract_type" class="show select2 select2-offscreen" tabindex="-1" title=""><option value="">All</option><option value="1">Lease</option>
<option value="2">Maintenance</option>
<option value="3">Software License</option>
<option value="4">Warranty</option></select>
            </div>

            <div id="load_filter_cont" class="hide">
               <div class="sloading loading-circle loading-align"><div class="spinner" role="progressbar" style="position: relative; width: 0px; z-index: 2000000000; left: -22px; top: 0px;"><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-0-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(1deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-1-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(22deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-2-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(43deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-3-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(64deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-4-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(85deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-5-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(106deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-6-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(128deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-7-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(149deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-8-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(170deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-9-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(191deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-10-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(212deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-11-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(233deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-12-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(255deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-13-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(276deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-14-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(297deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-15-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(318deg) translate(6px, 0px); border-radius: 2px;"></div></div><div style="position: absolute; top: -2px; opacity: 0.25; animation: 0.666667s linear 0s infinite normal none running opacity-100-25-16-17;"><div style="position: absolute; width: 5px; height: 5px; background: rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 1px; transform-origin: left center; transform: rotate(339deg) translate(6px, 0px); border-radius: 2px;"></div></div></div></div>
            </div>

            <div id="custom_contract_fields"> 
                 <div id="div_ff_cmdb_contract_renewals.status" class="ff_item clear show" condition="cmdb_contract_renewals.status" operator="is_in" ff_name="default" container="multi_select">
      <label class="title">Contract Status</label>
      
<div class="select2-container select2-container-multi select2 filter_item" id="s2id_Contract_Status"><ul class="select2-choices">  <li class="select2-search-choice">    <div>Active</div>    <a href="#" class="select2-search-choice-close" tabindex="-1"></a></li><li class="select2-search-field">    <label for="s2id_autogen130" class="select2-offscreen"></label>    <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" id="s2id_autogen130" placeholder="" style="width: 35px;">    <i class="ficon-close close-icon"></i>  </li></ul><div class="select2-drop select2-drop-multi select2-display-none">   <ul class="select2-results">   <li class="select2-no-results">No matches found</li></ul></div></div><select name="Contract Status[]" id="Contract_Status" multiple="multiple" class="select2 filter_item select2-offscreen" placeholder="any" tabindex="-1"><option value=""></option>
<option value="1">Draft</option>
<option value="2">Pending Approval</option>
<option value="3">Approved</option>
<option selected="selected" value="4">Active</option>
<option value="5">Expired</option>
<option value="6">Rejected</option>
<option value="7">Terminated</option></select>

</div>  <div id="div_ff_vendor_id" class="ff_item clear show" condition="vendor_id" operator="is_in" ff_name="default" container="multi_select">
      <label class="title">Vendor</label>
      
<div class="select2-container select2-container-multi select2 filter_item" id="s2id_Vendor"><ul class="select2-choices">  <li class="select2-search-field">    <label for="s2id_autogen131" class="select2-offscreen"></label>    <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" id="s2id_autogen131" placeholder="-- Choose --" style="width: 261px;">    <i class="ficon-close close-icon"></i>  </li></ul><div class="select2-drop select2-drop-multi select2-display-none">   <ul class="select2-results">   <li class="select2-no-results">No matches found</li></ul></div></div><select name="Vendor[]" id="Vendor" multiple="multiple" class="select2 filter_item select2-offscreen" placeholder="-- Choose --" tabindex="-1"><option value=""></option>
<option value="54000101440">Adobe</option>
<option value="54000101405">Adobe Inc.</option>
<option value="54000101406">Adobe Systems Incorporated</option>
<option value="54000128954">AO Kaspersky Lab</option>
<option value="54000104081">AOMEI International Network Limited.</option>
<option value="54000057179">Apple</option>
<option value="54000101408">Audacity Team</option>
<option value="54000101457">Aura</option>
<option value="54000101460">BetterNet LLC</option>
<option value="54000104089">BigAntSoft</option>
<option value="54000104092">Bitnami</option>
<option value="54000101400">Blackmagic Design</option>
<option value="54000128951">Business Objects</option>
<option value="54000101456">CANON INC.</option>
<option value="54000101463">D-Link Corporation</option>
<option value="54000101443">David Carpenter</option>
<option value="54000057180">Dell</option>
<option value="54000101471">DisplayLink Corp.</option>
<option value="54000101472">Docker Inc.</option>
<option value="54000101483">Dominique Ottello aka Otomatic</option>
<option value="54000187633">Dominique Ottello alias Otomatic</option>
<option value="54000101439">elm-lang.org</option>
<option value="54000101470">EnterpriseDB</option>
<option value="54000187631">Epson America, Inc.</option>
<option value="54000101464">epubfilereader.com</option>
<option value="54000187630">ETH Zürich</option>
<option value="54000101447">Fresco Logic</option>
<option value="54000101402">Freshdesk</option>
<option value="54000057182">Freshworks</option>
<option value="54000101462">getcomposer.org</option>
<option value="54000119002">Godaddy</option>
<option value="54000104083">Google Inc.</option>
<option value="54000101410">Google LLC</option>
<option value="54000104084">Google, Inc.</option>
<option value="54000101481">Grammarly</option>
<option value="54000131794">gravitykit.com</option>
<option value="54000101469">HANSoft, Inc.</option>
<option value="54000104088">Hewlett Packard Development Company, L.P.</option>
<option value="54000101394">Hewlett-Packard</option>
<option value="54000101396">Hewlett-Packard Co.</option>
<option value="54000104082">Hewlett-Packard Company</option>
<option value="54000104087">Hewlett-Packard Development Company, L.P.</option>
<option value="54000101404">hp</option>
<option value="54000101395">HP Inc.</option>
<option value="54000101450">Huawei Technologies Co.,Ltd</option>
<option value="54000101441">Inkscape</option>
<option value="54000101442">InSTEDD</option>
<option value="54000128960">Intel</option>
<option value="54000101451">Intel Corporation</option>
<option value="54000128950">Intel(R) Corporation</option>
<option value="54000104085">Intuit Inc.</option>
<option value="54000101465">iSpring Solutions Inc.</option>
<option value="54000101444">Kakao Corp.</option>
<option value="54000152718">Kaspersky Lab</option>
<option value="54000101458">Lavasoft</option>
<option value="54000057181">Logitech</option>
<option value="54000128949">LunarG, Inc.</option>
<option value="54000128952">Macromedia</option>
<option value="54000104090">ManageEngine</option>
<option value="54000104086">Matrox Graphics Inc.</option>
<option value="54000057183">Microsoft</option>
<option value="54000101449">Microsoft Corp.</option>
<option value="54000101399">Microsoft Corporation</option>
<option value="54000101401">Microsoft Garage</option>
<option value="54000101393">MicrosoftP</option>
<option value="54000101461">Mozilla</option>
<option value="54000101475">Nitro</option>
<option value="54000104080">Nmap Project</option>
<option value="54000101448">Node.js Foundation</option>
<option value="54000101459">Notepad++ Team</option>
<option value="54000101403">OBS Project</option>
<option value="54000101482">Odoo S.A.</option>
<option value="54000101411">Oracle and/or its affiliates</option>
<option value="54000101397">Oracle Corporation</option>
<option value="54000101467">PassFab, Inc.</option>
<option value="54000101454">pdfforge GmbH</option>
<option value="54000165897">Posit Software</option>
<option value="54000101476">PostgreSQL Global Development Group</option>
<option value="54000101477">Power Software Ltd</option>
<option value="54000101455">Python Software Foundation</option>
<option value="54000101453">QGIS.org</option>
<option value="54000101478">R Core Team</option>
<option value="54000128955">Realtek</option>
<option value="54000128948">Realtek Semiconductor Corp.</option>
<option value="54000101445">Samsung Electronics Co., Ltd.</option>
<option value="54000104091">Seagate</option>
<option value="54000187627">Seiko Epson Corporation</option>
<option value="54000101407">SIL International</option>
<option value="54000187628">Simon Tatham</option>
<option value="54000128956">Skype Technologies S.A.</option>
<option value="54000101479">SMSC</option>
<option value="54000187632">Softdeluxe</option>
<option value="54000101398">Sophos Limited</option>
<option value="54000101480">SysTools Software Pvt. Ltd.</option>
<option value="54000101452">Tableau Software, LLC</option>
<option value="54000101412">TeamViewer</option>
<option value="54000101446">The Apache Software Foundation</option>
<option value="54000101409">The Git Development Community</option>
<option value="54000101473">Tim Kosse</option>
<option value="54000128957">TP-LINK</option>
<option value="54000101413">VideoLAN</option>
<option value="54000128958">Waves Audio Ltd.</option>
<option value="54000101414">win.rar GmbH</option>
<option value="54000165273">xc360</option>
<option value="54000165277">xpandIT</option>
<option value="54000187629">Yarn Contributors</option>
<option value="54000101466">Your Company Name</option>
<option value="54000101468">Zebra Technologies Corporation</option>
<option value="54000101474">Zoomer Analytics LLC</option>
<option value="54000128953">ZTE</option>
<option value="54000128959">ZTE Corporation</option></select>

</div>  <div id="div_ff_group_id" class="ff_item clear show" condition="group_id" operator="is_in" ff_name="default" container="multi_select">
      <label class="title">Group</label>
      
<div class="select2-container select2-container-multi select2 filter_item" id="s2id_Group"><ul class="select2-choices">  <li class="select2-search-field">    <label for="s2id_autogen132" class="select2-offscreen"></label>    <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" id="s2id_autogen132" placeholder="-- Choose --" style="width: 261px;">    <i class="ficon-close close-icon"></i>  </li></ul><div class="select2-drop select2-drop-multi select2-display-none">   <ul class="select2-results">   <li class="select2-no-results">No matches found</li></ul></div></div><select name="Group[]" id="Group" multiple="multiple" class="select2 filter_item select2-offscreen" placeholder="-- Choose --" tabindex="-1"><option value="-1">None</option>
<option value="54000078825">Change Team</option>
<option value="54000078827">Database Team</option>
<option value="54000078828">Hardware Team</option>
<option value="54000078834">Helpdesk Monitoring Team</option>
<option value="54000078821">IT Support Team</option>
<option value="54000078822">Major Incident Team</option>
<option value="54000078833">Network Team</option>
<option value="54000078824">Problem Management Team</option>
<option value="54000078826">Release Team</option>
<option value="54000078831">Service Design Team</option>
<option value="54000078823">Service Request Fulfillment Team</option>
<option value="54000180989">SharePoint Dev Team</option>
<option value="54000078832">Software Team</option>
<option value="54000078830">Supplier Management Team</option></select>

</div>  <div id="div_ff_cmdb_contract_renewals.end_date" class="ff_item clear show" condition="cmdb_contract_renewals.end_date" operator="is_greater_than" ff_name="default" container="single_select">
      <label class="title">Expiry</label>
      <div class="select2-container select2 filter_item date_options" id="s2id_Expiry"><a href="javascript:void(0)" class="select2-choice select2-default" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-133">-- Choose --</span><abbr class="select2-search-choice-close"></abbr>   <div class="select2-arrow" role="presentation"><b role="presentation"></b></div></a><label for="s2id_autogen133" class="select2-offscreen"></label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" aria-labelledby="select2-chosen-133" id="s2id_autogen133"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen133_search" class="select2-offscreen"></label>       <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-133" id="s2id_autogen133_search">       <i class="ficon-search"></i>       <i class="ficon-close close-icon"></i>   </div>   <ul class="select2-results" role="listbox" id="select2-results-133">   </ul></div></div><select name="Expiry" id="Expiry" class="select2 filter_item date_options select2-offscreen" data-minimum-results-for-search="100" data-placeholder="-- Choose --" tabindex="-1" title=""><option></option><option value="-1">None</option>
<option value="">Any time</option>
<option value="today">Today</option>
<option value="yesterday">Yesterday</option>
<option value="week">This Week</option>
<option value="month">This Month</option>
<option value="next_30_days">Next 30 days</option>
<option value="next_60_days">Next 60 days</option>
<option value="next_90_days">Next 90 days</option>
<option value="next_180_days">Next 180 days</option>
<option value="set_date">Select Time Period</option></select>

<div id="div_ff_created_date_range" class="date_value_container" style="display: none;">
	<label class="title">Time period</label>
	<input class="text date_value" id="created_date_range" name="created_date_range" type="text">
</div>
</div>  <div id="div_ff_cmdb_contract_renewals.cost" class="ff_item clear hide" condition="cmdb_contract_renewals.cost" operator="is" ff_name="default" container="numeric">
      <label class="title">Cost</label>
      <div class="select2-container select2 filter_item number_operator" id="s2id_Cost"><a href="javascript:void(0)" class="select2-choice" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-134">Equals</span><abbr class="select2-search-choice-close"></abbr>   <div class="select2-arrow" role="presentation"><b role="presentation"></b></div></a><label for="s2id_autogen134" class="select2-offscreen"></label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" aria-labelledby="select2-chosen-134" id="s2id_autogen134"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen134_search" class="select2-offscreen"></label>       <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-134" id="s2id_autogen134_search">       <i class="ficon-search"></i>       <i class="ficon-close close-icon"></i>   </div>   <ul class="select2-results" role="listbox" id="select2-results-134">   </ul></div></div><select name="Cost" id="Cost" class="select2 filter_item number_operator select2-offscreen" data-minimum-results-for-search="100" tabindex="-1" title=""><option value="is">Equals</option>
<option value="is_greater_than">Greater than</option>
<option value="is_greater_than_or_equal">Greater than or Equal to</option>
<option value="is_less_than">Less than</option>
<option value="is_less_than_or_equal">Less than or Equal to</option></select>

<div id="div_ff_numeric&quot; %&gt;" class="cost_value_field">
	<input class="text number_field" id="contract_numeric" type="text">
</div>
</div><select name="conditions_container" id="more-options" class="hide" data-placeholder="Choose field"><option></option><option value="cmdb_contract_renewals.cost">Cost</option></select>

            </div>

            <div id="more" class="mt10">
               <i class="ficon-add-white"></i>
               <a>More...</a>
            </div>
         <input type="hidden" name="authenticity_token" value="xg6ONjsut7dVaH8vMdQBEuQHTAt8EkqvgwVDOf8JmFYOGGsvEQbbNhOL3hlx+LBL39OmkfW9oTCcEtgtXkJFgg=="></form>
      </div>
   </div>

   <div id="rightCol" style="width: 100%; margin-right: 302px;"> 
   <div class="cmdb-contracts-list-body">
   <div class="clearfix">
      <div class="custom_buttons pull-left">
            <input class="btn tooltip" id="delete" onclick="if(confirm('Are you sure you want to delete the selected contract(s)?')){helpdesk_submit('/cmdb/contracts/multiple', 'delete');}" type="button" value="Delete" disabled="disabled">
      </div>
   </div>

   <form id="tickets-expanded" class="contracts_export_wrapper" method="post">
      <div>
         <input name="_method" type="hidden" value="put">
      </div>
   <div id="cmdb_contract_list">
            <div id="cmdb_contracts">
                </div><div id="contract_data_table_wrapper" class="dataTables_wrapper" role="grid"><table class="row-fluid cmdb-item-list dataTable contract_data_table contract_list" id="contract_data_table">
    <thead>
        <tr role="row"><th class="contract_checkbox sorting_disabled" role="columnheader" rowspan="1" colspan="1" aria-label=""><input id="select_all" type="checkbox"></th><th id="name" title="Contract Name" class="sorting" role="columnheader" tabindex="0" aria-controls="contract_data_table" rowspan="1" colspan="1" aria-label="Contract Name: activate to sort column ascending">Contract Name</th><th id="type" title="Type" class="sorting" role="columnheader" tabindex="0" aria-controls="contract_data_table" rowspan="1" colspan="1" aria-label="Type: activate to sort column ascending">Type</th><th id="status" class="contract_status sorting" title="Status" role="columnheader" tabindex="0" aria-controls="contract_data_table" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending">Status</th><th id="renewal_status" class="tooltip contract_renewal_status sorting_disabled" data-original-title="Renewal Status" role="columnheader" rowspan="1" colspan="1" aria-label="Renewal Status">Renewal Status</th><th id="contract_id" class="tooltip sorting" data-original-title="Contract Number" role="columnheader" tabindex="0" aria-controls="contract_data_table" rowspan="1" colspan="1" aria-label="Contract Number: activate to sort column ascending">Contract Number</th><th id="vendor" title="Vendor" class="sorting" role="columnheader" tabindex="0" aria-controls="contract_data_table" rowspan="1" colspan="1" aria-label="Vendor: activate to sort column ascending">Vendor</th><th id="expiry" title="Expiry" class="sorting" role="columnheader" tabindex="0" aria-controls="contract_data_table" rowspan="1" colspan="1" aria-label="Expiry: activate to sort column ascending">Expiry</th></tr>
    </thead>
    
<tbody role="alert" aria-live="polite" aria-relevant="all"><tr class="contract odd">
                     <td class="  sorting_1"><input type="checkbox" class="chkbox" id="ids_" name="ids[]" value="16"></td>
                 <td class=" "><a title="Sophos - XS310012ZZRGAA - Xstream Protection for XGS 3100" href="/cmdb/contracts/16">Sophos - XS310012ZZRGAA - Xstream Protection for XGS 3100</a></td>
                 <td title="Lease" class=" ">Lease</td>
                 <td title="Active" class=" "><span class="active status">Active</span></td>
                    <td title="" class=" ">--</td>
                  <td title="MGR00135723" class=" ">MGR00135723</td>
                          <td title="xpandIT" class=" ">xpandIT</td>
                 <td class=" ">Within 60 Days</td>
                </tr><tr class="contract even">
                     <td class="  sorting_1"><input type="checkbox" class="chkbox" id="ids_" name="ids[]" value="15"></td>
                 <td class=" "><a title="Sophos - Intercept X Advanced for Server	" href="/cmdb/contracts/15">Sophos - Intercept X Advanced for Server	</a></td>
                 <td title="Lease" class=" ">Lease</td>
                 <td title="Active" class=" "><span class="active status">Active</span></td>
                    <td title="" class=" ">--</td>
                  <td title="LN1000425195" class=" ">LN1000425195</td>
                          <td title="xpandIT" class=" ">xpandIT</td>
                 <td class=" ">2 May, 2026</td>
                </tr><tr class="contract odd">
                     <td class="  sorting_1"><input type="checkbox" class="chkbox" id="ids_" name="ids[]" value="13"></td>
                 <td class=" "><a title="Godaddy - aslm.org main site : 246836886" href="/cmdb/contracts/13">Godaddy - aslm.org main site : 246836886</a></td>
                 <td title="Lease" class=" ">Lease</td>
                 <td title="Active" class=" "><span class="active status">Active</span></td>
                    <td title="" class=" ">--</td>
                  <td title="Customer #: 246836886" class=" ">Customer #: 246836886</td>
                          <td title="Godaddy" class=" ">Godaddy</td>
                 <td class=" ">Within 30 Days</td>
                </tr><tr class="contract even">
                     <td class="  sorting_1"><input type="checkbox" class="chkbox" id="ids_" name="ids[]" value="11"></td>
                 <td class=" "><a title="Godaddy -  65760391" href="/cmdb/contracts/11">Godaddy -  65760391</a></td>
                 <td title="Lease" class=" ">Lease</td>
                 <td title="Active" class=" "><span class="active status">Active</span></td>
                    <td title="" class=" ">--</td>
                  <td title="65760391 - Standard Wildcard SSL *.aslm.org" class=" ">65760391 - Standard Wildcard SSL *.aslm.org</td>
                          <td title="Google LLC" class=" ">Google LLC</td>
                 <td class=" ">25 Jun, 2026</td>
                </tr><tr class="contract odd">
                     <td class="  sorting_1"><input type="checkbox" class="chkbox" id="ids_" name="ids[]" value="10"></td>
                 <td class=" "><a title="Godaddy -  65760391" href="/cmdb/contracts/10">Godaddy -  65760391</a></td>
                 <td title="Lease" class=" ">Lease</td>
                 <td title="Active" class=" "><span class="active status">Active</span></td>
                    <td title="" class=" ">--</td>
                  <td title="65760391 - Web Hosting Ultimate aslm.org" class=" ">65760391 - Web Hosting Ultimate aslm.org</td>
                          <td title="Google LLC" class=" ">Google LLC</td>
                 <td class=" ">5 Jun, 2027</td>
                </tr><tr class="contract even">
                     <td class="  sorting_1"><input type="checkbox" class="chkbox" id="ids_" name="ids[]" value="9"></td>
                 <td class=" "><a title="Godaddy -  65760391" href="/cmdb/contracts/9">Godaddy -  65760391</a></td>
                 <td title="Lease" class=" ">Lease</td>
                 <td title="Active" class=" "><span class="active status">Active</span></td>
                    <td title="" class=" ">--</td>
                  <td title="65760391 - aslmexpertdb.org and Web Security" class=" ">65760391 - aslmexpertdb.org and Web Security</td>
                          <td title="Godaddy" class=" ">Godaddy</td>
                 <td class=" ">27 Jan, 2026</td>
                </tr><tr class="contract odd">
                     <td class="  sorting_1"><input type="checkbox" class="chkbox" id="ids_" name="ids[]" value="8"></td>
                 <td class=" "><a title="Godaddy -  65760391" href="/cmdb/contracts/8">Godaddy -  65760391</a></td>
                 <td title="Lease" class=" ">Lease</td>
                 <td title="Active" class=" "><span class="active status">Active</span></td>
                    <td title="" class=" ">--</td>
                  <td title="65760391 - Standard SSL aslm.org" class=" ">65760391 - Standard SSL aslm.org</td>
                          <td title="Google LLC" class=" ">Google LLC</td>
                 <td class=" ">22 Aug, 2026</td>
                </tr><tr class="contract even">
                     <td class="  sorting_1"><input type="checkbox" class="chkbox" id="ids_" name="ids[]" value="6"></td>
                 <td class=" "><a title="Godaddy -  65760391" href="/cmdb/contracts/6">Godaddy -  65760391</a></td>
                 <td title="Lease" class=" ">Lease</td>
                 <td title="Active" class=" "><span class="active status">Active</span></td>
                    <td title="" class=" ">--</td>
                  <td title="65760391 - Website Backup 10GB" class=" ">65760391 - Website Backup 10GB</td>
                          <td title="Godaddy" class=" ">Godaddy</td>
                 <td class=" ">7 Jun, 2026</td>
                </tr><tr class="contract odd">
                     <td class="  sorting_1"><input type="checkbox" class="chkbox" id="ids_" name="ids[]" value="5"></td>
                 <td class=" "><a title="Godaddy - aslm.org 65760391" href="/cmdb/contracts/5">Godaddy - aslm.org 65760391</a></td>
                 <td title="Lease" class=" ">Lease</td>
                 <td title="Active" class=" "><span class="active status">Active</span></td>
                    <td title="" class=" ">--</td>
                  <td title="65760391 - aslm.org" class=" ">65760391 - aslm.org</td>
                          <td title="Godaddy" class=" ">Godaddy</td>
                 <td class=" ">9 May, 2026</td>
                </tr><tr class="contract even">
                     <td class="  sorting_1"><input type="checkbox" class="chkbox" id="ids_" name="ids[]" value="4"></td>
                 <td class=" "><a title="Godaddy - aslm2021.org 65760391" href="/cmdb/contracts/4">Godaddy - aslm2021.org 65760391</a></td>
                 <td title="Lease" class=" ">Lease</td>
                 <td title="Active" class=" "><span class="active status">Active</span></td>
                    <td title="" class=" ">--</td>
                  <td title="65760391 - aslm2021" class=" ">65760391 - aslm2021</td>
                          <td title="Godaddy" class=" ">Godaddy</td>
                 <td class=" ">5 Apr, 2026</td>
                </tr></tbody></table></div>
<input type="hidden" id="contract-export-view" value="active">
<input type="hidden" id="contract-export-filter" value="">

<script>
//<![CDATA[
 

jQuery(document).ready(function(){

var filterName = "active";
var notVendorList = true
var filterHash = null;
var messageType = "There is no contracts present.";
var link="";
var linkName="";
if(!filterHash && filterName === 'active'){
  messageType = "There is no active contracts present";
  link = "/cmdb/contracts/filter/all";
  linkName = "View All";
}
if(!filterHash && filterName === 'all' && true){
   link = "/cmdb/contracts/new";
   linkName = 'Create New';
}


    jQuery(".pagearea").off('ajax:beforeSend', '.pjax-pagination a');
    jQuery(".pagearea").off('ajax:success', '.pjax-pagination a');
    jQuery(".pagearea").on('ajax:beforeSend', '.pjax-pagination a', function(evt, xhr, settings){
      loading_target = evt.target.getAttribute("data-loading-target");
      if(loading_target) {
        loading_box('#'+ loading_target);
      }
    }).on('ajax:success', '.pjax-pagination a', function(evt, data, status, xhr){
      target_element = evt.target.getAttribute("data-element");
      jQuery('#'+ target_element).html(data);
    });

if(notVendorList){ //not to show sort icon in vendor list page
   jQuery(function($){
     var bsort = true;
     var column = [];
     var columns_to_disable = ["contract_checkbox", "contract_renewal_status"];
     column = [{ bSortable: false, aTargets: columns_to_disable }]
    $LAB.script(LAB_URL_LIST.data_table).wait()
    .script(LAB_URL_LIST.data_table_plugins).wait(function(){
         jQuery("table.cmdb-item-list").dataTable({
         "bPaginate": false,
         "bInfo": false,
         "bFilter": false,
         "bRetrieve": true,
         "bSort": bsort,
         "bAutoWidth": false,
         "aoColumnDefs": column,
         "oLanguage":{"sEmptyTable": "<div class='empty-contracts-div'><div class='no-contracts-icon'></div><div class='empty-contract-message'>"+messageType+"</div><div class='empty-contract-message'><a href="+link+">"+linkName+"</a></div></div>"}
        });
  
     var $table = jQuery("table.cmdb-item-list");
     var tableSortOrder = "" || "ASC" ; // Current Sorting Order, defaults to ASC
     var tableSortColumn = "" || "" ; // Current Sorting column

      // Initializing custom sorting action to the datatable 
     jQuery.when( ItilUtil.dataTableColumnSortAction({ 
       tableSortOrder: tableSortOrder, 
       $table: $table, 
       module: "contract",
       tableSortCol: tableSortColumn,     // To keep track of which column is being selected for sorting
       currentFilter: filterName,
       filterParam: filterHash
     })).done(function() {
     jQuery(".cmdb-item-list #contract_id").addClass("tooltip"); //to add tooltip which was removed
     jQuery(".twipsy").remove();   //remove existing twipsy
    });
   });
  });
 }
   
  // Independent vendor column sort control (event-driven, with fallback)
  jQuery(function($){
    var disableVendorSort = false
    
    if(disableVendorSort) {
      var $table = jQuery("table.cmdb-item-list");

      var disableVendor = function(){
        var $vendorHeader = $table.find('#vendor');
        if($vendorHeader.length > 0) {
          $vendorHeader.removeClass('sorting sorting_asc sorting_desc')
                       .addClass('sorting_disabled')
                       .attr('aria-sort', null);
          $vendorHeader.off('click');
        }
      };

      if (jQuery.fn.dataTable && ((jQuery.fn.dataTable.isDataTable && jQuery.fn.dataTable.isDataTable($table[0])) || $table.hasClass('dataTable'))) {
        disableVendor();
      }

      $table.on('init.dt draw.dt', function(){
        disableVendor();
      });
    }
  });
  
  jQuery("#contract-export-view").val(filterName);
  jQuery("#contract-export-filter").val(filterHash);

var chk_boxes = new Array();
jQuery('#select_all').click(function() {
    var checkAll = jQuery(this).prop('checked');
    if(checkAll){
      enableDeleteBtn();
      jQuery('.chkbox').prop('checked', true);
    }
    else {
      disableDeleteBtn();
      jQuery('.chkbox').prop('checked', false);
      chk_boxes = [];
    }
  });

jQuery('.chkbox').on('click', function(){
    jQuery('.chkbox:checked').length == 0 ? disableDeleteBtn() : enableDeleteBtn();
        if(jQuery('.chkbox').length == jQuery('.chkbox:checked').length){
            jQuery('#select_all').prop('checked', true);
        }   else    {
            chk_boxes = [];
            jQuery('#select_all').prop('checked', false);
        }
});

function disableDeleteBtn() {
    jQuery('#delete').prop('disabled','true')
}

function enableDeleteBtn()  {
    jQuery('#delete').enable();
}

checkedItems = function(){
    jQuery('.chkbox:checked').each(function(index){
        chk_boxes[index] = jQuery(this).attr('id');
    });
    return chk_boxes;
}
});


//]]>
</script>                                    
   </div>
   <input type="hidden" name="authenticity_token" value="xg6ONjsut7dVaH8vMdQBEuQHTAt8EkqvgwVDOf8JmFYOGGsvEQbbNhOL3hlx+LBL39OmkfW9oTCcEtgtXkJFgg=="></form>

   <div class="hide" id="contracts_export_cont">
      <form action="/contract_export/export_csv" accept-charset="UTF-8" method="post"><input name="utf8" type="hidden" value="✓" autocomplete="off"><input type="hidden" name="authenticity_token" value="EEaLDiaoxvThBpMcvFJlsj9c5d0EaTh/vyI4DJEDRoPYUG4XDICqdaflMir8ftTrBIgPR43G0+CgNaMYMEibVw==" autocomplete="off">
      <input type="hidden" name="export_type" value="contract">
      <input type="hidden" name="contract_filter" id="contract_filter">
      <input type="hidden" name="contract_filter_hash" id="contract_filter_hash">
</form>   </div>
   </div>

   </div>
   </div>

<script>
//<![CDATA[


jQuery(".export_contract").on('click',function(){
  var exportView = jQuery("#contract-export-view").val();
  jQuery("#contract_filter").val(exportView);
  var filterDetail = jQuery("#contract-export-filter").val();
  if(filterDetail!== "null"){
    jQuery("#contract_filter_hash").val(filterDetail);
  }
	jQuery("#contracts_export_cont form").trigger("submit");
});

 

jQuery(document).ready(function(){
   
   jQuery("#sliding").invokeitilslide({ cookieName: "contract-filter-closed" });

   function loadFilter(){
      var accFormat = getDateFormat("filter_format");
      var dates;

         var value = moment().locale('en').format(accFormat) + " - " + moment().locale('en').add(1, 'month').format(accFormat);
         
      jQuery(".date_value_container").hide();
      jQuery(".date_value").each(function(index, item) {
      item.value = value;
      dates = item.value.split("-");
      var options = {
        ranges: {},
        startDate: new Date(dates[0]),
        endDate: new Date(dates[1]),
        minDate: new Date(dates[0]),
        format: accFormat
      };
      if(item.id === 'custom'){
        options["minDate"] = Date.parse('1/1/2010');
      }

      jQuery(item).bootstrapDaterangepicker(options);
      });

      jQuery(".date_value").bind("keypress keyup keydown", function(ev) {
         ev.preventDefault();
         return false;
      });

      jQuery(".date_value").on("show",function(){
         jQuery(".daterangepicker").find("select").select2("destroy");
         jQuery(".ranges .active").click();
      });

      jQuery(".range_inputs, .ranges").hide();

    }

    function drawContractTable(){
       var data = { };
       var hash=[];
       jQuery(".contract-fields-container .ff_item.show").each(function(){
          var $item = jQuery(this);
          var val = $item.find("select").val();
          var container = this.getAttribute("container");
          var operator = this.getAttribute("operator");
          if(val === "set_date"){
             val = $item.find(".date_value").val();
          }
          else if (container === "numeric"){
             operator = $item.find('select').val();
             val = $item.find('input.number_field').val();
          }
          else if (container === "application"){
             val = $item.find('input#software_filter').val();
          }
          else if (container === "d42_application"){
            val = $item.find('input#d42_software_filter').val();
          }
          else if (container === "d42_vendor"){
            val = $item.find('input#d42_vendor_filter').val();
          }
          if(Array.isArray(val) ? val.length : !!val){
             hash.push(queryParams(this,val,operator));
          }
       });
       data["data_hash"] = hash.fs_toJSON();
       jQuery.ajax({   // renders response via RJS ()
          url: "/cmdb/contracts/filter/all",
          type: "GET",
          data: data,
          dataType: "script",
          error: function(){
            ItilUtil.showToastr('Something went wrong. Please try again.','error');
          }
       });   
    }

   function queryParams(element,value,operator){
      return{
         condition   : element.getAttribute("condition"),
         operator    : operator,
         value       : value.toString()
      }
   }

   loadFilter()

   jQuery(".contract-fields-container #contract_type").bind("change", function(){
      var params = {};
      params.contract_type_id = this.value;
      jQuery("#contract_type").prop('disabled',true);
      jQuery("#more, #custom_contract_fields").hide();
      jQuery("#load_filter_cont").show();
      jQuery.ajax({                                  // renders response via RJS ()
          url: "/cmdb/contracts/filterable_fields",
          type: "GET",
          data: params,
          dataType: "script",
          success: function(result){
             jQuery("#more, #custom_contract_fields").show();
             jQuery("#load_filter_cont").hide();
             jQuery("#contract_type").prop('disabled',false);
             loadFilter();
             jQuery("#div_ff_cmdb_contract_renewals\\.status select").val(4).trigger("change");
           },
           error: function(){
           ItilUtil.showToastr('Something went wrong. Please try again.','error');
           }
      });
   });

   jQuery(".contract-fields-container").on("change","select.filter_item, input#software_filter", function(event){
      var $item = jQuery(this);
         if(this.classList.contains('date_options')){
           if(this.value === "set_date"){
              $item.next().show();
              $item.next().children().focus();
           }
           else{
              $item.next().hide();
           }
         }
         if(this.classList.contains('number_operator')){
           if(!$item.next(".cost_value_field").find("input").val()){
             return false;
           }  
         }
         drawContractTable();
      });

   jQuery(".contract-fields-container").on("change","select.filter_item, input#d42_software_filter", function(event){
      var $item = jQuery(this);
         if(this.classList.contains('date_options')){
           if(this.value === "set_date"){
              $item.next().show();
              $item.next().children().focus();
           }
           else{
              $item.next().hide();
           }
         }
         if(this.classList.contains('number_operator')){
           if(!$item.next(".cost_value_field").find("input").val()){
             return false;
           }  
         }
         drawContractTable();
      });

   jQuery(".contract-fields-container").on("change","select.filter_item, input#d42_vendor_filter", function(event){
         drawContractTable();
      });

   jQuery(".contract-fields-container").on("clickdate",".date_value",function(){
      drawContractTable();
   });

   jQuery(".contract-fields-container").on("keyup", ".number_field",function(event){
      event.stopPropagation();
      var $item = jQuery(this);
      var attribute = $item.closest(".ff_item").attr("ff_name");
      var pattern = new RegExp("[^0-9\.]","g");
      if (attribute === 'custom'){
        pattern = new RegExp("[^0-9]","g");
      }
      var val = $item.val();
      val = val.replace(pattern,'');
      if(attribute === 'default'){
        if(val.split('.').length > 2){
          var i = val.indexOf('.');
          val = val.substring(0,i+1) + (val.substring(i+1).replace(/[^0-9]/g,'')); //allow only one decimal pt.
        }
        if(val.split('.')[1] && val.split('.')[1].length > 2){
          var k = val.indexOf('.');
          val = val.substring(0,k+1)+ val.substring(k+1, k+3); //allow only two decimal places
        }
      }
      $item.val(val);  
      drawContractTable();      
   });

   jQuery(".contract-fields-container").on("change", "#more-options", function(){
      var $item = jQuery(this);
      var length = jQuery(".ff_item.show").length;
      var target = jQuery(jQuery(".ff_item.show")[length-1]);
      var condition_id = $item.val();
      target.after(jQuery("[condition='"+condition_id+"']"));
      target.next().removeClass("hide").addClass("show");
      jQuery("[condition='"+condition_id+"']").find(".select2").select2("open");
      $item.find("option:selected").remove();
      $item.select2("val","");
      $item.prev(".select2-container").addClass("hide");
      if($item.find("option").length == 1){
         jQuery("#more").hide();
      }
   });


   jQuery("#more").click(function() {
      jQuery("#more-options").select2({
         minimumResultsForSearch: 5
      });
      jQuery("#more-options").prev(".select2-container").removeClass("hide").show();
      jQuery("#more-options").select2("open");
   });

  });


//]]>
</script>
					</div>
					<div id="ticket_panel" class="modal_task_container"></div>
				</div>