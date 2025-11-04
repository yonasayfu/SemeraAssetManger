<div class="leftcontent">
				   <!-- Hide Error message  if the application is load via iframe (in nested app flow of marketplace)-->
					    
					<div class="alert notice" id="noticeajax" style="display:none;"><a class="close" href="#"></a></div>

					<div id="account-restricted-modal-container" class="hide" data-backdrop="static" data-target="#account-restricted-modal" data-reset-on-close="false" data-show-close="false" data-classes="itil_new_dialog modal-large" rel="freshdialog">
					</div>

					

					<div class="pagearea" id="Pagearea" style="min-height: 1058px;">
						


<form class="ui-form" id="new_cmdb_config_item" enctype="multipart/form-data" action="/cmdb/items" accept-charset="UTF-8" method="post" novalidate="novalidate"><input name="utf8" type="hidden" value="✓" autocomplete="off"><input type="hidden" name="authenticity_token" value="xxY7Cuhzb9homBScm7vgi7sIPZY9XG4uTZKv10xBPhsPAN4TwlsDWS57tarbl1HSgNzXDLTzhbFShTTD7Qrjzw==" autocomplete="off"> 
  <div class="tkt-details-sticky collapsed" id="sticky_header" rel="sticky" style="">
    <div class="pull-left">
      <h2 class="title">Add New</h2>
    </div>
    <div class="pull-right buttons_container">
      <a class="btn" href="/cmdb/items/">Cancel</a>
      <input type="submit" name="commit" value="Save" id="cmdb_config_item_submit" class="btn btn-primary has_attachment_form has_attachment_form">
    </div>
  </div>
	<div id="cmdb_new_item">
  <div class="form_wrapper">
      <div class="row-fluid">
        <span class="span6">
          <label class="inline pull-left" for="cmdb_config_item_name">Display Name<sup class="required_star">*</sup></label>
          <input class="required txt-field" type="text" name="cmdb_config_item[name]" id="cmdb_config_item_name" aria-required="true">
        </span>
        <span class="span6">
          <label class="inline pull-left" for="cmdb_config_item_asset_tag">Asset Tag</label>
          <input class="" type="text" name="cmdb_config_item[asset_tag]" id="cmdb_config_item_asset_tag">
        </span>
      </div>
      <div class="row-fluid">
        <span class="span6">
          <label class="inline pull-left required" for="cmdb_config_item_ci_type_id" aria-required="true">Asset Type<sup class="required_star">*</sup></label>
          <div class="select2-container dropdown select2 w100 required" id="s2id_cmdb_config_item_ci_type_id" title="Asset Type*" style="width: 100%;"><a href="javascript:void(0)" class="select2-choice select2-default" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-11">-- Choose --</span><abbr class="select2-search-choice-close"></abbr>   <div class="select2-arrow" role="presentation"><b role="presentation"></b></div></a><label for="s2id_autogen11" class="select2-offscreen">Asset Type*</label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" aria-labelledby="select2-chosen-11" id="s2id_autogen11"><div class="select2-drop select2-display-none select2-with-searchbox" style="">   <div class="select2-search">       <label for="s2id_autogen11_search" class="select2-offscreen">Asset Type*</label>       <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-11" id="s2id_autogen11_search">       <i class="ficon-search"></i>       <i class="ficon-close close-icon"></i>   </div>   <ul class="select2-results" role="listbox" id="select2-results-11">   </ul></div></div><select class="dropdown select2 w100 required select2-offscreen" name="cmdb_config_item[ci_type_id]" id="cmdb_config_item_ci_type_id" title="Asset Type*" aria-required="true" tabindex="-1"><option></option><option style="padding-left:0em" level="0" class="indend0" data-path="" data-type="ci_type" value="54000471295"> <i data-name="services" data-id="54000471295" class=" services "></i><div class="citype-container ">Services</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Services" data-type="ci_type" value="54000471315"> <i data-name="business_service" data-id="54000471315" class=" services businessservice "></i><div class="citype-container ">Business Service</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Services / Business Service" data-type="ci_type" value="54000471346"> <i data-name="sales_service" data-id="54000471346" class=" services businessservice salesservice "></i><div class="citype-container ">Sales Service</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Services / Business Service" data-type="ci_type" value="54000471347"> <i data-name="support_service" data-id="54000471347" class=" services businessservice supportservice "></i><div class="citype-container ">Support Service</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Services" data-type="ci_type" value="54000471316"> <i data-name="it_service" data-id="54000471316" class=" services itservice "></i><div class="citype-container ">IT Service</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Services / IT Service" data-type="ci_type" value="54000471348"> <i data-name="email_service" data-id="54000471348" class=" services itservice emailservice "></i><div class="citype-container ">Email Service</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Services / IT Service" data-type="ci_type" value="54000471349"> <i data-name="backup_service" data-id="54000471349" class=" services itservice backupservice "></i><div class="citype-container ">Backup Service</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Services / IT Service" data-type="ci_type" value="54000471350"> <i data-name="hosting_service" data-id="54000471350" class=" services itservice hostingservice "></i><div class="citype-container ">Hosting Service</div></option><option style="padding-left:0em" level="0" class="indend0" data-path="" data-type="ci_type" value="54000471296"> <i data-name="cloud" data-id="54000471296" class=" cloud "></i><div class="citype-container ">Cloud</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471317"> <i data-name="virtual_machine-deprecated" data-id="54000471317" class=" cloud "></i><div class="citype-container ">Virtual Machine-deprecated</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Virtual Machine-deprecated" data-type="ci_type" value="54000471352"> <i data-name="aws_vm-deprecated" data-id="54000471352" class=" cloud "></i><div class="citype-container ">AWS VM-deprecated</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Virtual Machine-deprecated" data-type="ci_type" value="54000471353"> <i data-name="azure_vm-deprecated" data-id="54000471353" class=" cloud "></i><div class="citype-container ">Azure VM-deprecated</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Virtual Machine-deprecated" data-type="ci_type" value="54000471385"> <i data-name="gcp_vm-deprecated" data-id="54000471385" class=" cloud "></i><div class="citype-container ">GCP VM-deprecated</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Virtual Machine-deprecated" data-type="ci_type" value="54000471357"> <i data-name="vmware_vcenter_vm-deprecated" data-id="54000471357" class=" cloud "></i><div class="citype-container ">VMware VCenter VM-deprecated</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471318"> <i data-name="volume" data-id="54000471318" class=" cloud volume "></i><div class="citype-container ">Volume</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Volume" data-type="ci_type" value="54000471354"> <i data-name="aws_disk" data-id="54000471354" class=" cloud volume awsdisk "></i><div class="citype-container ">AWS Disk</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Volume" data-type="ci_type" value="54000471355"> <i data-name="azure_disk" data-id="54000471355" class=" cloud volume azuredisk "></i><div class="citype-container ">Azure Disk</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Volume" data-type="ci_type" value="54000471386"> <i data-name="gcp_disk" data-id="54000471386" class=" cloud volume gcpdisk "></i><div class="citype-container ">GCP Disk</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Volume" data-type="ci_type" value="54000471358"> <i data-name="vmware_vcenter_disk" data-id="54000471358" class=" cloud volume vmwarevcenterdisk "></i><div class="citype-container ">VMware VCenter Disk</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471319"> <i data-name="host-deprecated" data-id="54000471319" class=" cloud "></i><div class="citype-container ">Host-deprecated</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Host-deprecated" data-type="ci_type" value="54000471356"> <i data-name="vmware_vcenter_host-deprecated" data-id="54000471356" class=" cloud "></i><div class="citype-container ">VMware VCenter Host-deprecated</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471328"> <i data-name="datacenter" data-id="54000471328" class=" cloud datacenter "></i><div class="citype-container ">Datacenter</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Datacenter" data-type="ci_type" value="54000471374"> <i data-name="vmware_vcenter_datacenter" data-id="54000471374" class=" cloud datacenter vmwarevcenterdatacenter "></i><div class="citype-container ">VMware VCenter Datacenter</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471329"> <i data-name="datastore" data-id="54000471329" class=" cloud datastore "></i><div class="citype-container ">Datastore</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Datastore" data-type="ci_type" value="54000471375"> <i data-name="vmware_vcenter_datastore" data-id="54000471375" class=" cloud datastore vmwarevcenterdatastore "></i><div class="citype-container ">VMware VCenter Datastore</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471320"> <i data-name="image" data-id="54000471320" class=" cloud image "></i><div class="citype-container ">Image</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Image" data-type="ci_type" value="54000471359"> <i data-name="aws_image" data-id="54000471359" class=" cloud image awsimage "></i><div class="citype-container ">AWS Image</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Image" data-type="ci_type" value="54000471360"> <i data-name="azure_image" data-id="54000471360" class=" cloud image azureimage "></i><div class="citype-container ">Azure Image</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Image" data-type="ci_type" value="54000471378"> <i data-name="vmware_vcenter_image" data-id="54000471378" class=" cloud image vmwarevcenterimage "></i><div class="citype-container ">VMware VCenter Image</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Image" data-type="ci_type" value="54000471387"> <i data-name="gcp_image" data-id="54000471387" class=" cloud image gcpimage "></i><div class="citype-container ">GCP Image</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471321"> <i data-name="load_balancer" data-id="54000471321" class=" cloud loadbalancer "></i><div class="citype-container ">Load Balancer</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Load Balancer" data-type="ci_type" value="54000471361"> <i data-name="aws_lb" data-id="54000471361" class=" cloud loadbalancer awslb "></i><div class="citype-container ">AWS LB</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Load Balancer" data-type="ci_type" value="54000471362"> <i data-name="azure_lb" data-id="54000471362" class=" cloud loadbalancer azurelb "></i><div class="citype-container ">Azure LB</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Load Balancer" data-type="ci_type" value="54000471393"> <i data-name="gcp_lb" data-id="54000471393" class=" cloud loadbalancer gcplb "></i><div class="citype-container ">GCP LB</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471322"> <i data-name="security_group" data-id="54000471322" class=" cloud securitygroup "></i><div class="citype-container ">Security Group</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Security Group" data-type="ci_type" value="54000471363"> <i data-name="aws_security_group" data-id="54000471363" class=" cloud securitygroup awssecuritygroup "></i><div class="citype-container ">AWS Security Group</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Security Group" data-type="ci_type" value="54000471364"> <i data-name="azure_security_group" data-id="54000471364" class=" cloud securitygroup azuresecuritygroup "></i><div class="citype-container ">Azure Security Group</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Security Group" data-type="ci_type" value="54000471388"> <i data-name="gcp_security_group" data-id="54000471388" class=" cloud securitygroup gcpsecuritygroup "></i><div class="citype-container ">GCP Security Group</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471330"> <i data-name="network" data-id="54000471330" class=" cloud network "></i><div class="citype-container ">Network</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Network" data-type="ci_type" value="54000471376"> <i data-name="vmware_vcenter_network" data-id="54000471376" class=" cloud network vmwarevcenternetwork "></i><div class="citype-container ">VMware VCenter Network</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471331"> <i data-name="resource_pool" data-id="54000471331" class=" cloud "></i><div class="citype-container ">Resource Pool</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Resource Pool" data-type="ci_type" value="54000471377"> <i data-name="vmware_vcenter_resource_pool" data-id="54000471377" class=" cloud "></i><div class="citype-container ">VMware VCenter Resource Pool</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471323"> <i data-name="network_interface" data-id="54000471323" class=" cloud networkinterface "></i><div class="citype-container ">Network Interface</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Network Interface" data-type="ci_type" value="54000471365"> <i data-name="aws_network_interface" data-id="54000471365" class=" cloud networkinterface awsnetworkinterface "></i><div class="citype-container ">AWS Network Interface</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Network Interface" data-type="ci_type" value="54000471366"> <i data-name="azure_network_interface" data-id="54000471366" class=" cloud networkinterface azurenetworkinterface "></i><div class="citype-container ">Azure Network Interface</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Network Interface" data-type="ci_type" value="54000471392"> <i data-name="gcp_network_interface" data-id="54000471392" class=" cloud networkinterface gcpnetworkinterface "></i><div class="citype-container ">GCP Network Interface</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471324"> <i data-name="public_ip_address" data-id="54000471324" class=" cloud publicipaddress "></i><div class="citype-container ">Public IP Address</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Public IP Address" data-type="ci_type" value="54000471367"> <i data-name="aws_public_ip_address" data-id="54000471367" class=" cloud publicipaddress awspublicipaddress "></i><div class="citype-container ">AWS Public IP Address</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Public IP Address" data-type="ci_type" value="54000471368"> <i data-name="azure_public_ip_address" data-id="54000471368" class=" cloud publicipaddress azurepublicipaddress "></i><div class="citype-container ">Azure Public IP Address</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Public IP Address" data-type="ci_type" value="54000471391"> <i data-name="gcp_public_ip_address" data-id="54000471391" class=" cloud publicipaddress gcppublicipaddress "></i><div class="citype-container ">GCP Public IP Address</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471326"> <i data-name="vpc" data-id="54000471326" class=" cloud vpc "></i><div class="citype-container ">VPC</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / VPC" data-type="ci_type" value="54000471370"> <i data-name="aws_vpc" data-id="54000471370" class=" cloud vpc awsvpc "></i><div class="citype-container ">AWS VPC</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / VPC" data-type="ci_type" value="54000471371"> <i data-name="azure_vpc" data-id="54000471371" class=" cloud vpc azurevpc "></i><div class="citype-container ">Azure VPC</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / VPC" data-type="ci_type" value="54000471389"> <i data-name="gcp_vpc" data-id="54000471389" class=" cloud vpc gcpvpc "></i><div class="citype-container ">GCP VPC</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471327"> <i data-name="subnet" data-id="54000471327" class=" cloud subnet "></i><div class="citype-container ">Subnet</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Subnet" data-type="ci_type" value="54000471372"> <i data-name="aws_subnet" data-id="54000471372" class=" cloud subnet awssubnet "></i><div class="citype-container ">AWS Subnet</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Subnet" data-type="ci_type" value="54000471373"> <i data-name="azure_subnet" data-id="54000471373" class=" cloud subnet azuresubnet "></i><div class="citype-container ">Azure Subnet</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Subnet" data-type="ci_type" value="54000471390"> <i data-name="gcp_subnet" data-id="54000471390" class=" cloud subnet gcpsubnet "></i><div class="citype-container ">GCP Subnet</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471325"> <i data-name="database" data-id="54000471325" class=" cloud database "></i><div class="citype-container ">Database</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Database" data-type="ci_type" value="54000471369"> <i data-name="aws_rds" data-id="54000471369" class=" cloud database awsrds "></i><div class="citype-container ">AWS RDS</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Database" data-type="ci_type" value="54000471394"> <i data-name="gcp_bigquery_table" data-id="54000471394" class=" cloud database gcpbigquerytable "></i><div class="citype-container ">GCP BigQuery Table</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471332"> <i data-name="key_pair" data-id="54000471332" class=" cloud keypair "></i><div class="citype-container ">Key Pair</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Key Pair" data-type="ci_type" value="54000471379"> <i data-name="aws_key_pair" data-id="54000471379" class=" cloud keypair awskeypair "></i><div class="citype-container ">AWS Key Pair</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471333"> <i data-name="object_storage" data-id="54000471333" class=" cloud objectstorage "></i><div class="citype-container ">Object Storage</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Object Storage" data-type="ci_type" value="54000471380"> <i data-name="aws_s3_bucket" data-id="54000471380" class=" cloud objectstorage awss3bucket "></i><div class="citype-container ">AWS S3 Bucket</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471334"> <i data-name="key_vault" data-id="54000471334" class=" cloud keyvault "></i><div class="citype-container ">Key Vault</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Key Vault" data-type="ci_type" value="54000471381"> <i data-name="azure_key_vault" data-id="54000471381" class=" cloud keyvault azurekeyvault "></i><div class="citype-container ">Azure Key Vault</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471335"> <i data-name="subscription" data-id="54000471335" class=" cloud subscription "></i><div class="citype-container ">Subscription</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Subscription" data-type="ci_type" value="54000471382"> <i data-name="azure_subscription" data-id="54000471382" class=" cloud subscription azuresubscription "></i><div class="citype-container ">Azure Subscription</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471336"> <i data-name="resource_group" data-id="54000471336" class=" cloud resourcegroup "></i><div class="citype-container ">Resource Group</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Resource Group" data-type="ci_type" value="54000471383"> <i data-name="azure_resource_group" data-id="54000471383" class=" cloud resourcegroup azureresourcegroup "></i><div class="citype-container ">Azure Resource Group</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000471337"> <i data-name="application_gateway" data-id="54000471337" class=" cloud applicationgateway "></i><div class="citype-container ">Application Gateway</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / Application Gateway" data-type="ci_type" value="54000471384"> <i data-name="azure_application_gateway" data-id="54000471384" class=" cloud applicationgateway azureapplicationgateway "></i><div class="citype-container ">Azure Application Gateway</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000998763"> <i data-name="k8s_deployment" data-id="54000998763" class=" cloud "></i><div class="citype-container ">K8s Deployment</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / K8s Deployment" data-type="ci_type" value="54000998774"> <i data-name="aws_k8s_deployment" data-id="54000998774" class=" cloud "></i><div class="citype-container ">AWS K8s Deployment</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000998764"> <i data-name="k8s_namespace" data-id="54000998764" class=" cloud "></i><div class="citype-container ">K8s Namespace</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / K8s Namespace" data-type="ci_type" value="54000998775"> <i data-name="aws_k8s_namespace" data-id="54000998775" class=" cloud "></i><div class="citype-container ">AWS K8s Namespace</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000998765"> <i data-name="k8s_node-deprecated" data-id="54000998765" class=" cloud "></i><div class="citype-container ">K8s Node-deprecated</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / K8s Node-deprecated" data-type="ci_type" value="54000998776"> <i data-name="aws_k8s_node-deprecated" data-id="54000998776" class=" cloud "></i><div class="citype-container ">AWS K8s Node-deprecated</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000998766"> <i data-name="k8s_pod" data-id="54000998766" class=" cloud "></i><div class="citype-container ">K8s Pod</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / K8s Pod" data-type="ci_type" value="54000998777"> <i data-name="aws_k8s_pod" data-id="54000998777" class=" cloud "></i><div class="citype-container ">AWS K8s Pod</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000998767"> <i data-name="k8s_replica_set" data-id="54000998767" class=" cloud "></i><div class="citype-container ">K8s Replica Set</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / K8s Replica Set" data-type="ci_type" value="54000998778"> <i data-name="aws_k8s_replica_set" data-id="54000998778" class=" cloud "></i><div class="citype-container ">AWS K8s Replica Set</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000998768"> <i data-name="k8s_service" data-id="54000998768" class=" cloud "></i><div class="citype-container ">K8s Service</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / K8s Service" data-type="ci_type" value="54000998779"> <i data-name="aws_k8s_service" data-id="54000998779" class=" cloud "></i><div class="citype-container ">AWS K8s Service</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000998769"> <i data-name="k8s_config_map" data-id="54000998769" class=" cloud "></i><div class="citype-container ">K8s Config Map</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / K8s Config Map" data-type="ci_type" value="54000998780"> <i data-name="aws_k8s_config_map" data-id="54000998780" class=" cloud "></i><div class="citype-container ">AWS K8s Config Map</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000998770"> <i data-name="k8s_job" data-id="54000998770" class=" cloud "></i><div class="citype-container ">K8s Job</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / K8s Job" data-type="ci_type" value="54000998781"> <i data-name="aws_k8s_job" data-id="54000998781" class=" cloud "></i><div class="citype-container ">AWS K8s Job</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000998771"> <i data-name="k8s_cron_job" data-id="54000998771" class=" cloud "></i><div class="citype-container ">K8s Cron Job</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / K8s Cron Job" data-type="ci_type" value="54000998782"> <i data-name="aws_k8s_cron_job" data-id="54000998782" class=" cloud "></i><div class="citype-container ">AWS K8s Cron Job</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000998772"> <i data-name="k8s_daemon_set" data-id="54000998772" class=" cloud "></i><div class="citype-container ">K8s Daemon Set</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / K8s Daemon Set" data-type="ci_type" value="54000998783"> <i data-name="aws_k8s_daemon_set" data-id="54000998783" class=" cloud "></i><div class="citype-container ">AWS K8s Daemon Set</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Cloud" data-type="ci_type" value="54000998773"> <i data-name="k8s_stateful_set" data-id="54000998773" class=" cloud "></i><div class="citype-container ">K8s Stateful Set</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Cloud / K8s Stateful Set" data-type="ci_type" value="54000998784"> <i data-name="aws_k8s_stateful_set" data-id="54000998784" class=" cloud "></i><div class="citype-container ">AWS K8s Stateful Set</div></option><option style="padding-left:0em" level="0" class="indend0" data-path="" data-type="ci_type" value="54000471297"> <i data-name="hardware" data-id="54000471297" class=" hardware "></i><div class="citype-container ">Hardware</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Hardware" data-type="ci_type" value="54000471302"> <i data-name="computer" data-id="54000471302" class=" hardware computer "></i><div class="citype-container ">Computer</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Hardware / Computer" data-type="ci_type" value="54000471338"> <i data-name="desktop" data-id="54000471338" class=" hardware computer desktop "></i><div class="citype-container ">Desktop</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Hardware / Computer" data-type="ci_type" value="54000471339"> <i data-name="laptop" data-id="54000471339" class=" hardware computer laptop "></i><div class="citype-container ">Laptop</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Hardware / Computer" data-type="ci_type" value="54000471340"> <i data-name="server" data-id="54000471340" class=" hardware computer server "></i><div class="citype-container ">Server</div></option><option style="padding-left:3em" level="3" class="indend3" data-path="Hardware / Computer / Server" data-type="ci_type" value="54000471395"> <i data-name="unix_server" data-id="54000471395" class=" hardware computer server unixserver "></i><div class="citype-container ">Unix Server</div></option><option style="padding-left:3em" level="3" class="indend3" data-path="Hardware / Computer / Server" data-type="ci_type" value="54000471396"> <i data-name="aix_server" data-id="54000471396" class=" hardware computer server aixserver "></i><div class="citype-container ">Aix Server</div></option><option style="padding-left:3em" level="3" class="indend3" data-path="Hardware / Computer / Server" data-type="ci_type" value="54000471397"> <i data-name="solaris_server" data-id="54000471397" class=" hardware computer server solarisserver "></i><div class="citype-container ">Solaris Server</div></option><option style="padding-left:3em" level="3" class="indend3" data-path="Hardware / Computer / Server" data-type="ci_type" value="54000471398"> <i data-name="vmware_server" data-id="54000471398" class=" hardware computer server vmwareserver "></i><div class="citype-container ">VMware Server</div></option><option style="padding-left:3em" level="3" class="indend3" data-path="Hardware / Computer / Server" data-type="ci_type" value="54000471399"> <i data-name="windows_server" data-id="54000471399" class=" hardware computer server windowsserver "></i><div class="citype-container ">Windows Server</div></option><option style="padding-left:3em" level="3" class="indend3" data-path="Hardware / Computer / Server" data-type="ci_type" value="54001127124"> <i data-name="aws_vm" data-id="54001127124" class=" hardware computer server awsvm "></i><div class="citype-container ">AWS VM</div></option><option style="padding-left:3em" level="3" class="indend3" data-path="Hardware / Computer / Server" data-type="ci_type" value="54001127125"> <i data-name="azure_vm" data-id="54001127125" class=" hardware computer server azurevm "></i><div class="citype-container ">Azure VM</div></option><option style="padding-left:3em" level="3" class="indend3" data-path="Hardware / Computer / Server" data-type="ci_type" value="54001127126"> <i data-name="gcp_vm" data-id="54001127126" class=" hardware computer server gcpvm "></i><div class="citype-container ">GCP VM</div></option><option style="padding-left:3em" level="3" class="indend3" data-path="Hardware / Computer / Server" data-type="ci_type" value="54001127127"> <i data-name="vmware_vcenter_vm" data-id="54001127127" class=" hardware computer server vmwarevcentervm "></i><div class="citype-container ">VMware VCenter VM</div></option><option style="padding-left:3em" level="3" class="indend3" data-path="Hardware / Computer / Server" data-type="ci_type" value="54001127128"> <i data-name="k8s_node" data-id="54001127128" class=" hardware computer server "></i><div class="citype-container ">K8s Node</div></option><option style="padding-left:4em" level="4" class="indend4" data-path="Hardware / Computer / Server / K8s Node" data-type="ci_type" value="54001127130"> <i data-name="aws_k8s_node" data-id="54001127130" class=" hardware computer server "></i><div class="citype-container ">AWS K8s Node</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Hardware / Computer" data-type="ci_type" value="54001127123"> <i data-name="host" data-id="54001127123" class=" hardware computer host "></i><div class="citype-container ">Host</div></option><option style="padding-left:3em" level="3" class="indend3" data-path="Hardware / Computer / Host" data-type="ci_type" value="54001127129"> <i data-name="vmware_vcenter_host" data-id="54001127129" class=" hardware computer host vmwarevcenterhost "></i><div class="citype-container ">VMware VCenter Host</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Hardware" data-type="ci_type" value="54000471303"> <i data-name="storage" data-id="54000471303" class=" hardware storage "></i><div class="citype-container ">Storage</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Hardware / Storage" data-type="ci_type" value="54000471341"> <i data-name="disk" data-id="54000471341" class=" hardware storage disk "></i><div class="citype-container ">Disk</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Hardware" data-type="ci_type" value="54000471304"> <i data-name="datacenter" data-id="54000471304" class=" hardware datacenter "></i><div class="citype-container ">DataCenter</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Hardware / DataCenter" data-type="ci_type" value="54000471342"> <i data-name="rack" data-id="54000471342" class=" hardware datacenter rack "></i><div class="citype-container ">Rack</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Hardware / DataCenter" data-type="ci_type" value="54000471343"> <i data-name="ups" data-id="54000471343" class=" hardware datacenter ups "></i><div class="citype-container ">UPS</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Hardware" data-type="ci_type" value="54000471305"> <i data-name="mobile_devices" data-id="54000471305" class=" hardware mobiledevices "></i><div class="citype-container ">Mobile Devices</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Hardware / Mobile Devices" data-type="ci_type" value="54000471344"> <i data-name="mobile" data-id="54000471344" class=" hardware mobiledevices mobile "></i><div class="citype-container ">Mobile</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Hardware / Mobile Devices" data-type="ci_type" value="54000471345"> <i data-name="tablet" data-id="54000471345" class=" hardware mobiledevices tablet "></i><div class="citype-container ">Tablet</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Hardware" data-type="ci_type" value="54000471306"> <i data-name="monitor" data-id="54000471306" class=" hardware monitor "></i><div class="citype-container ">Monitor</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Hardware" data-type="ci_type" value="54000471307"> <i data-name="printer" data-id="54000471307" class=" hardware printer "></i><div class="citype-container ">Printer</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Hardware" data-type="ci_type" value="54000471308"> <i data-name="projector" data-id="54000471308" class=" hardware projector "></i><div class="citype-container ">Projector</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Hardware" data-type="ci_type" value="54000471309"> <i data-name="scanner" data-id="54000471309" class=" hardware scanner "></i><div class="citype-container ">Scanner</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Hardware" data-type="ci_type" value="54000471310"> <i data-name="router" data-id="54000471310" class=" hardware router "></i><div class="citype-container ">Router</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Hardware" data-type="ci_type" value="54000471311"> <i data-name="switch" data-id="54000471311" class=" hardware switch "></i><div class="citype-container ">Switch</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Hardware" data-type="ci_type" value="54000471312"> <i data-name="access_point" data-id="54000471312" class=" hardware accesspoint "></i><div class="citype-container ">Access Point</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Hardware" data-type="ci_type" value="54000471313"> <i data-name="firewall" data-id="54000471313" class=" hardware firewall "></i><div class="citype-container ">Firewall</div></option><option style="padding-left:1em" level="1" class="indend1" data-path="Hardware" data-type="ci_type" value="54000471314"> <i data-name="other_devices" data-id="54000471314" class=" hardware otherdevices "></i><div class="citype-container ">Other Devices</div></option><option style="padding-left:2em" level="2" class="indend2" data-path="Hardware / Other Devices" data-type="ci_type" value="54000471351"> <i data-name="snmp" data-id="54000471351" class=" hardware otherdevices snmp "></i><div class="citype-container ">SNMP</div></option><option style="padding-left:0em" level="0" class="indend0" data-path="" data-type="ci_type" value="54000471298"> <i data-name="consumable" data-id="54000471298" class=" consumable "></i><div class="citype-container ">Consumable</div></option><option style="padding-left:0em" level="0" class="indend0" data-path="" data-type="ci_type" value="54000471299"> <i data-name="network" data-id="54000471299" class=" network "></i><div class="citype-container ">Network</div></option><option style="padding-left:0em" level="0" class="indend0" data-path="" data-type="ci_type" value="54000471300"> <i data-name="document" data-id="54000471300" class=" document "></i><div class="citype-container ">Document</div></option><option style="padding-left:0em" level="0" class="indend0" data-path="" data-type="ci_type" value="54000471301"> <i data-name="others" data-id="54000471301" class=" others "></i><div class="citype-container ">Others</div></option></select>
          <h5 class="ci_type_notify hide" style="display: none;"><i class="ficon-info_icon"></i>Software assets created/updated manually will not reflect in the new Software module.</h5>
        </span>
        <span class="span6">
          <label class="inline pull-left" for="cmdb_config_item_impact">Impact</label>
          <div class="select2-container select2 w100" id="s2id_cmdb_config_item_impact"><a href="javascript:void(0)" class="select2-choice" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-2">Low</span><abbr class="select2-search-choice-close"></abbr>   <div class="select2-arrow" role="presentation"><b role="presentation"></b></div></a><label for="s2id_autogen2" class="select2-offscreen">Impact</label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" aria-labelledby="select2-chosen-2" id="s2id_autogen2"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen2_search" class="select2-offscreen">Impact</label>       <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-2" id="s2id_autogen2_search">       <i class="ficon-search"></i>       <i class="ficon-close close-icon"></i>   </div>   <ul class="select2-results" role="listbox" id="select2-results-2">   </ul></div></div><select class="select2 w100 select2-offscreen" placeholder="-- Choose --" name="cmdb_config_item[impact]" id="cmdb_config_item_impact" tabindex="-1" title="Impact"><option value="1">Low</option>
<option value="2">Medium</option>
<option value="3">High</option></select>
        </span>
      </div>
		<div class="row-fluid usage_type" style="display: none;">
      <span>
        <label class="inline pull-left required" for="cmdb_config_item_usage_type" aria-required="true">Usage type</label>
        <div class="select2-container dropdown select2 required w100" id="s2id_cmdb_config_item_usage_type" aria-required="true"><a href="javascript:void(0)" class="select2-choice" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-3">Permanent</span><abbr class="select2-search-choice-close"></abbr>   <div class="select2-arrow" role="presentation"><b role="presentation"></b></div></a><label for="s2id_autogen3" class="select2-offscreen">Usage type</label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" aria-labelledby="select2-chosen-3" id="s2id_autogen3"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen3_search" class="select2-offscreen">Usage type</label>       <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-3" id="s2id_autogen3_search">       <i class="ficon-search"></i>       <i class="ficon-close close-icon"></i>   </div>   <ul class="select2-results" role="listbox" id="select2-results-3">   </ul></div></div><select class="dropdown select2 required w100 select2-offscreen" placeholder="-- Choose --" name="cmdb_config_item[usage_type]" id="cmdb_config_item_usage_type" tabindex="-1" title="Usage type" aria-required="true"><option selected="selected" value="1">Permanent</option>
<option value="2">Loaner</option></select>
      </span>
		</div>
		<div class="row-fluid">
			<span class="span12">
				<label class="inline" for="cmdb_config_item_description">Description</label>
        <div class="redactor_box"><ul class="redactor_toolbar"><li><a href="javascript:void(null);" title="Bold (⌘B)" class="redactor_btn_bold" tabindex="-1"></a></li><li><a href="javascript:void(null);" title="Italic (⌘I)" class="redactor_btn_italic" tabindex="-1"></a></li><li><a href="javascript:void(null);" title="Underline (⌘U)" class="redactor_btn_underline" tabindex="-1"></a></li><li class="redactor_separator"></li><li><a href="javascript:void(null);" title="• Unordered List (⌘⇧7)" class="redactor_btn_unorderedlist" tabindex="-1"></a></li><li><a href="javascript:void(null);" title="1. Ordered List (⌘⇧8)" class="redactor_btn_orderedlist" tabindex="-1"></a></li><li class="redactor_separator"></li><li><a href="javascript:void(null);" title="Font Color" class="redactor_btn_fontcolor" tabindex="-1"></a></li><li><a href="javascript:void(null);" title="Back Color" class="redactor_btn_backcolor" tabindex="-1"></a></li><li class="redactor_separator"></li><li><a href="javascript:void(null);" title="Link" class="redactor_btn_link" tabindex="-1"></a></li><li><a href="javascript:void(null);" title="Remove formatting (⌘\)" class="redactor_btn_removeFormat" tabindex="-1"></a></li></ul><div rel="getPasteImage" contenteditable="true" style="width:50px;height:0px; overflow: hidden;"></div><div class="redactor_editor" contenteditable="true" dir="ltr" style="height: 140px; font-size: 14px; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, sans-serif;"></div><textarea class="redactor html_paragraph " name="cmdb_config_item[description]" id="cmdb_config_item_description" style="display: none;"></textarea></div>
			</span>
    </div>
    <div class="row-fluid">
      <span class="span6">
        <label class="inline pull-left" for="cmdb_config_item_end_of_life">End of Life</label>
        <div class="date_time_box"><span class="clear_date_time ">Clear</span><input class="datepair date itil_default_date hasDatepicker" placeholder="DD-MM-YYYY" value="" type="text" name="cmdb_config_item[end_of_life]" id="cmdb_config_item_end_of_life" readonly="readonly"></div>
      </span>
    </div>
		<div id="accordion">
			<script>
//<![CDATA[

			var productSelectorType = 'input.new_product';
	jQuery(function(){				
			var id = jQuery('input.new_product').val();
			var vendor_id = jQuery(".vendor").select2('val');
			if(id !== undefined && id != "")	{
				jQuery.ajax({
				url: "/itil/products/fetch_vendor_details",
				data: { product_id : id },
				type: "post",
    			success: function(result){
    		  		var option = "<option></option>";
    		  		var vendorId = jQuery("select.vendor").val();
    		  		for(var i=0;i< result.result.length; i++)	{
    		  		if(vendorId != "" && result.result[i].vendor.id == vendorId)	{
    		  			option+= "<option value="+vendorId+" selected='selected'> "+result.result[i].vendor.name+"</option>";
    		  		}
    		  		else {
    		  			option+= "<option value="+result.result[i].vendor.id+"> "+result.result[i].vendor.name+"</option>";
    		  		}
    		  		}
    		  		jQuery("select.vendor").select2().html(option);
              		jQuery("#new_vendor").show();
            	}
			});
		}
		else {
      jQuery("#new_vendor").hide();
    }
	});

	jQuery(productSelectorType).change(function (event){
    jQuery("#cmdb_config_item_submit").attr("disabled","disabled");
		var id = jQuery(productSelectorType).select2('val');
    jQuery(productSelectorType).valid();
    	jQuery(".vendor").html("<div class='sloading loading-small'></div>");
		jQuery.ajax({
			url: "/itil/products/fetch_vendor_details",
			data: { product_id : id },
			type: "post",
    		success: function(result){
  		  	var option = "<option></option>";
  		  	for(var i=0;i< result.result.length; i++)	{
  		  		option+= "<option value="+result.result[i].vendor.id+"> "+result.result[i].vendor.name+"</option>";
  		  	}
          jQuery("#cmdb_config_item_depreciation_id").select2('val',result.depreciation);
  		  	jQuery("select.vendor").select2().html(option);
  		  	jQuery("select.vendor").select2().html();
        	jQuery("#new_vendor").show();
        	jQuery(".cost").val('');
        	jQuery("select#warranty_years").select2('val',null);
        	jQuery("select#warranty_months").select2('val',null);
          jQuery("select#validity_years").select2('val',null);
          jQuery("select#validity_months").select2('val',null);
        	if(jQuery(".acquisition").length > 0 && is_date_present(".acquisition")){
        		jQuery(".expiry").datepicker('setDate', jQuery(".acquisition").datepicker('getDate').addMonths(0)).next().val(jQuery(".acquisition").next().val());
        	}
          if(jQuery(".software_installation").length > 0 && is_date_present(".software_installation")){
            jQuery(".software_expiry").datepicker('setDate', jQuery(".software_installation").datepicker('getDate').addMonths(0)).next().val(jQuery(".software_installation").next().val());
          }
          jQuery("#cmdb_config_item_submit").removeAttr("disabled");
        }
		});
	});

	jQuery(".vendor").change(function ()
  	{
      jQuery("#cmdb_config_item_submit").attr("disabled","disabled");
    	var _vendor_id = jQuery("select.vendor").val();
    	var _product_id = jQuery(productSelectorType).val();
    	jQuery.ajax({
      		url: "/itil/products/fetch_product_vendor_info",
	      	data: { product_id: _product_id, vendor_id: _vendor_id } ,
      		type: "post",
		    success: function(result){
				var cost = result.product_vendor.price;
				var quantity = result.product_vendor.quantity;
				jQuery(".cost").val((quantity > 1) ? (cost/quantity) : cost);
        		jQuery("#warranty_years").select2('val',Math.floor(result.product_vendor.warranty / 12));
        		jQuery("#warranty_months").select2('val',result.product_vendor.warranty % 12);
        		if(jQuery(".acquisition").length > 0 && is_date_present(".acquisition")){
        			jQuery(".expiry").datepicker('setDate', jQuery(".acquisition").datepicker('getDate').addMonths(result.product_vendor.warranty)).next().val(jQuery(".acquisition").next().val());
        		}
            jQuery("#validity_years").select2('val',Math.floor(result.product_vendor.warranty / 12));
            jQuery("#validity_months").select2('val',result.product_vendor.warranty % 12);
            if(jQuery(".software_installation").length > 0 && is_date_present(".software_installation")){
              jQuery(".software_expiry").datepicker('setDate', jQuery(".software_installation").datepicker('getDate').addMonths(result.product_vendor.warranty)).next().val(jQuery(".software_installation").next().val());
            }
            jQuery("#cmdb_config_item_submit").removeAttr("disabled");
      		} 
    	});
  	});

  	jQuery("select#warranty_years, select#warranty_months").change(function(){
  		updateExpiry(jQuery(".acquisition"), jQuery("select#warranty_years"), jQuery("select#warranty_months"), jQuery(".expiry"));
  	});

    jQuery(".acquisition").blur(function(){
      updateExpiry(jQuery(".acquisition"), jQuery("select#warranty_years"), jQuery("select#warranty_months"), jQuery(".expiry"));
    });

    jQuery("select#validity_years, select#validity_months").change(function(){
      updateExpiry(jQuery(".software_installation"), jQuery("select#validity_years"), jQuery("select#validity_months"), jQuery(".software_expiry"));
    });

    jQuery(".software_installation").blur(function(){
      updateExpiry(jQuery(".software_installation"), jQuery("select#validity_years"), jQuery("select#validity_months"), jQuery(".software_expiry"));
    });

	jQuery(document).on('click', '#new_vendor', function(){
		jQuery('#itil_vendor_product_vendor_warranty_years, #itil_vendor_product_vendor_warranty_months').select2({minimumResultsForSearch: 10});
		var productSelectorTypeDOM = jQuery(productSelectorType).select2("data");
		var p_name = productSelectorTypeDOM.value;
 		jQuery('#product_name_title').text('Product: '+p_name);
	});

	jQuery("#new_itil_vendor").submit(function(){
 		var p_id = jQuery(productSelectorType).val();
 		jQuery("#itil_vendor_product_vendor_product_id").val(p_id);
	});

	jQuery(".ci_type").click(function(){
		jQuery(this).next("div").toggle();
		jQuery("span",this).toggleClass('icon-caret-right icon-caret-down');
	});

  jQuery(document).ready(function(){
    datetime_pair_automation('.acquisition', '.acquisition ~ .time', '.expiry', '.expiry ~ .time');
    datetime_pair_automation('.software_installation', '.software_installation ~ .time', '.software_expiry', '.software_expiry ~ .time');
      jQuery('.ci_type_notify').hide();
  });

  jQuery('select.required').on('change',function(){
    jQuery(this).parent().find('label.error').hide();
  });


//]]>
</script>
		</div>
		<div>
		    <h3 class="field_header">Assignment</h3>
		    <div>
				<div class="row-fluid">
					<span class="span6">
            <label class="inline pull-left" for="cmdb_config_item_location_id">Location</label>
            
<div class="select2-container location-dropdown select2 w100" id="s2id_cmdb_config_item[location_id]"><a href="javascript:void(0)" class="select2-choice select2-default" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-9">-- Choose --</span><abbr class="select2-search-choice-close"></abbr>   <div class="select2-arrow" role="presentation"><b role="presentation"></b></div></a><label for="s2id_autogen9" class="select2-offscreen"></label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" aria-labelledby="select2-chosen-9" id="s2id_autogen9"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen9_search" class="select2-offscreen"></label>       <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-9" id="s2id_autogen9_search">       <i class="ficon-search"></i>       <i class="ficon-close close-icon"></i>   </div>   <ul class="select2-results" role="listbox" id="select2-results-9">   </ul></div></div><input id="cmdb_config_item[location_id]" class="location-dropdown select2 w100 select2-offscreen" data-pagination="true" name="cmdb_config_item[location_id]" data-remote-url="/api/_/locations?location_dropdown=true" data-search-param="q" data-result-name="locations" data-result-format="customTemplateForLocation" data-per-page="30" value="" data-default-value="" data-default-id="" placeholder="-- Choose --" tabindex="-1" title="">

<script>
    function customTemplateForLocation(result, container, query) { 
        return '<div class="location-name semi-bold">' + result.text + '</div><div class="location-hierarchy fsize-12">' + (result.ancestry ? result.ancestry.join(' / ') : '') + '</div>';
    }
</script>
					</span>
          <span class="span6" id="cmdb_group">
            <label class="inline pull-left" for="cmdb_config_item_group_id">Managed By Group</label>
            <div class="select2-container select2" id="s2id_cmdb_config_item_group_id" style="width:100%"><a href="javascript:void(0)" class="select2-choice" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-4">...</span><abbr class="select2-search-choice-close"></abbr>   <div class="select2-arrow" role="presentation"><b role="presentation"></b></div></a><label for="s2id_autogen4" class="select2-offscreen">Managed By Group</label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" aria-labelledby="select2-chosen-4" id="s2id_autogen4"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen4_search" class="select2-offscreen">Managed By Group</label>       <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-4" id="s2id_autogen4_search">       <i class="ficon-search"></i>       <i class="ficon-close close-icon"></i>   </div>   <ul class="select2-results" role="listbox" id="select2-results-4">   </ul></div></div><select class="select2 select2-offscreen" style="width:100%" placeholder="-- Choose --" name="cmdb_config_item[group_id]" id="cmdb_config_item_group_id" tabindex="-1" title="Managed By Group"><option value="">...</option>
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
          </span> 
				</div>
 				<div class="row-fluid">
          <span class="span6" id="cmdb_department">
            <label class="inline pull-left" for="cmdb_config_item_department_id">Department</label>
            <div class="select2-container select2" id="s2id_cmdb_config_item_department_id" style="width:100%"><a href="javascript:void(0)" class="select2-choice" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-5">...</span><abbr class="select2-search-choice-close"></abbr>   <div class="select2-arrow" role="presentation"><b role="presentation"></b></div></a><label for="s2id_autogen5" class="select2-offscreen">Department</label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" aria-labelledby="select2-chosen-5" id="s2id_autogen5"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen5_search" class="select2-offscreen">Department</label>       <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-5" id="s2id_autogen5_search">       <i class="ficon-search"></i>       <i class="ficon-close close-icon"></i>   </div>   <ul class="select2-results" role="listbox" id="select2-results-5">   </ul></div></div><select class="select2 select2-offscreen" style="width:100%" placeholder="-- Choose --" name="cmdb_config_item[department_id]" id="cmdb_config_item_department_id" tabindex="-1" title="Department"><option value="">...</option><option value="54000193085">Admin and Finance</option>
<option value="54000193088">Administrative</option>
<option value="54000193087">Business Development </option>
<option value="54000193094">Communication </option>
<option value="54000193084">communications</option>
<option value="54000064858">Customer Support</option>
<option value="54000064856">Development</option>
<option value="54000193078">EQA and QWArS Project Administrative Officer</option>
<option value="54000064859">Finance</option>
<option value="54000193076">Finance and Admin</option>
<option value="54000064860">HR</option>
<option value="54000193096">Human Resources</option>
<option value="54000193089">IDDS Lab Advisor</option>
<option value="54000193083">In-Country Project Coordinator</option>
<option value="54000064862">IT</option>
<option value="54000193086">LabCoP </option>
<option value="54000193091">M&amp;E</option>
<option value="54000064861">Operations</option>
<option value="54000193097">Operations/IT</option>
<option value="54000193081">Procurement</option>
<option value="54000193082">Program </option>
<option value="54000193095">Programms</option>
<option value="54000193077">Programs</option>
<option value="54000193090">Project</option>
<option value="54000193080">Project Accountant</option>
<option value="54000193092">Project lead</option>
<option value="54000193093">Project Manager</option>
<option value="54000064857">Sales</option>
<option value="54000193079">Science </option></select>
          </span>
          <span class="span6" id="cmdb_Used_by">
            <label class="inline pull-left" for="cmdb_config_item_user_id">Used By</label>
            <div>
<div class="autocomplete_filter " data-domhelper-name="used_by">
  <div class="select2-container field" id="s2id_cmdb_config_item_user_id"><a href="javascript:void(0)" class="select2-choice select2-default" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-10">Search for Users</span><abbr class="select2-search-choice-close"></abbr>   <div class="select2-arrow" role="presentation"><b role="presentation"></b></div></a><label for="s2id_autogen10" class="select2-offscreen">Used By</label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" aria-labelledby="select2-chosen-10" id="s2id_autogen10"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen10_search" class="select2-offscreen">Used By</label>       <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-10" id="s2id_autogen10_search">       <i class="ficon-search"></i>       <i class="ficon-close close-icon"></i>   </div>   <ul class="select2-results" role="listbox" id="select2-results-10">   </ul></div></div><input type="hidden" id="cmdb_config_item_user_id" name="cmdb_config_item[user_id]" class="field select2-offscreen" tabindex="-1" title="Used By" value="">
</div>
<script>
//<![CDATA[


jQuery(document).ready(function() {
  var select2_format = function(result, container, query) {
    return result.value;
  }

  var select2_results = function(result, container, query) {
    var name = result.value, detail,
        display_result = "<b>"+ name + "</b>";
    if(result.info != undefined){
      var split_info = result.info.split(/<|\(/);
      if(split_info[1])
        detail = split_info[1].slice(0,-1);
      else
        detail = result.info
      detail = escapeHtml(detail);
      if(detail.length > 25)
        detail = split_info[1].substring(0,26)+ "...";
      display_result += "<br><span class='select2_list_detail'>" + detail + "</span>";
    }
    return display_result;
  }
  var department_element = "#cmdb_config_item_department_id"
  jQuery("#cmdb_config_item_user_id").select2({
    placeholder: 'Search for Users',
    minimumInputLength: 2,
    multiple: false,
    allowClear: true,
    maximumSelectionSize: 1,
    data: [],
    ajax: { 
        url: '/search/autocomplete/requesters',
        dataType: 'json',
        data: function (term) {
          return { 
            q: term,
            name: term,
            dept_id: jQuery(department_element).val()
          };
        },
        results: function (data) {
          var results = [];
          jQuery.each(data.results, function(i, item){
            results.push({id: item.id, value: escapeHtml(item.value), info: item.details});
          });
          return { results: results }

        }
    },
    initSelection : function (element, callback) {
      callback( {} );
    },
    specialFormatting: true,
    formatResult: select2_results,
    formatSelection: select2_format,
    formatNoMatches: function () { return "No Users found"; },
    formatInputTooShort: function (input, min) { 
      return I18n.t("js_translations.enter_characters", {char_count: (min - input.length)}); },
    formatSelectionTooBig: function (limit) { 
      return "Only one User can be selected"; },
  }).select2("val",[]);
});

//]]>
</script></div>
          </span>
				</div>
        <div class="row-fluid">
          <span class="span6" id="cmdb_managed_by">
            <label class="inline pull-left" for="cmdb_config_item_agent_id">Managed By</label>
            <div class="select2-container select2" id="s2id_cmdb_config_item_agent_id" style="width:100%"><a href="javascript:void(0)" class="select2-choice" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-6">...</span><abbr class="select2-search-choice-close"></abbr>   <div class="select2-arrow" role="presentation"><b role="presentation"></b></div></a><label for="s2id_autogen6" class="select2-offscreen">Managed By</label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" aria-labelledby="select2-chosen-6" id="s2id_autogen6"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen6_search" class="select2-offscreen">Managed By</label>       <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-6" id="s2id_autogen6_search">       <i class="ficon-search"></i>       <i class="ficon-close close-icon"></i>   </div>   <ul class="select2-results" role="listbox" id="select2-results-6">   </ul></div></div><select class="select2 select2-offscreen" style="width:100%" placeholder="-- Choose --" name="cmdb_config_item[agent_id]" id="cmdb_config_item_agent_id" tabindex="-1" title="Managed By"><option value="">...</option><option value="54001680924">Daniel Tesfaye</option>
<option value="54001551401">Fitsum Abebe</option>
<option value="54001991792">Yonas Sayfu (Me)</option></select>
          </span>
          <span class="span6">
          <label for="cmdb_config_item_assigned_on">Assigned on</label>
            <div class="date_time_box"><span class="clear_date_time ">Clear</span><input class="datepair date itil_default_date hasDatepicker" placeholder="DD-MM-YYYY" value="" type="text" name="cmdb_config_item[assigned_on_date]" id="cmdb_config_item_assigned_on_date" readonly="readonly"><input class="datepair time ui-timepicker-input" placeholder="HH:MM" value="" type="text" name="cmdb_config_item[assigned_on_time]" id="cmdb_config_item_assigned_on_time" autocomplete="off"></div>
          </span>
        </div>
			</div>
		</div>
</div>
		<script>
//<![CDATA[

    jQuery(document).ready(function() {
      invokeRedactor('cmdb_config_item_description', 'task');
    });
  jQuery("#cmdb_config_item_ci_type_id").change(function(){
    jQuery("#cmdb_config_item_submit").attr("disabled","disabled");
    jQuery("#cmdb_config_item_ci_type_id").valid();
    var ci = jQuery("#cmdb_config_item_ci_type_id").val();
    var ci_id = '';
    var _data = {
      ci_type_id : ci
    };
    if(ci_id)
      _data.id = ci_id;
    jQuery("#add_new_product").html("<div class='sloading loading-small'></div>");
    jQuery("#accordion").html("<div class='sloading loading-small loading-block'></div>");
      jQuery.ajax({
        url: "/cmdb/items/fetch_fields",
        data: _data,
        type: "get",
        complete: function(){
          setTimeout(function(){
            jQuery("#cmdb_config_item_level_field_attributes_warranty_expiry_date_2_date").datepicker("option", "minDate", jQuery('#cmdb_config_item_level_field_attributes_acquisition_date_2_date').datepicker("getDate"))}, 500);
        }
      });
  });
  var department = jQuery("#cmdb_config_item_department_id"),used_by = jQuery("#cmdb_Used_by");
  jQuery(document).ready(function(){
    if(department.val()){
      used_by.addClass('add_nested_cmdb_create');
    }

    var placeholder = "-- Choose --";
    var options = { width:"100%", placeholder:placeholder};
    var select = jQuery("#cmdb_config_item_ci_type_id");
    ItilUtil.addPathInfoToAssetTypeSelect2(select, options);

  });

  jQuery('body').on('change', "#cmdb_config_item_group_id", function(ev){
    groupchange(jQuery(this).val());
  });

  function groupchange(value){
    var $agentIdField = jQuery('#cmdb_config_item_agent_id');
    $agentIdField.html("<option value=''>...</option>");
    jQuery.ajax({
      type: 'GET',
      url: '/helpdesk/commons/group_agents/'+value,
      contentType: 'application/text',
      success: function(data){ 
        $agentIdField.select2('val', ''); 
        $agentIdField.html(data); 
      }
    });
  }

  department.change(function(){
    jQuery("#cmdb_config_item_user_id").val('').trigger('change');
    used_by.toggleClass('add_nested_cmdb_create',department.val() !== "");
  });

  jQuery(document).on("submit", "#new_itil_product", function(e){
      e.preventDefault();
      var _this = jQuery(this);
      if (_this.valid()){
        jQuery.ajax({
           url: "/itil/products/create_product",
           data: _this.serialize(),
           type: "POST",
           dataType: "json",
           success: function(result){
           	  if(result.status)	{
              	var _message =  "<p>"+I18n.t('js_translations.cmdb.item_success', {item_name: result.item.product.name}) + "</p>";
              	_this.find("input[type=text], textarea").val("");
              	jQuery("#add_product").modal('hide');
								jQuery('input.new_product').select2('data', {id: result.item.product.id, value: result.item.product.name});
              	jQuery("select.vendor").select2().html('<option></option>');
              	jQuery("select.vendor").select2().html();
              	jQuery("#new_vendor").show();
                toastr.success(_message);
                } else {
                jQuery("#add_product-submit").button("reset");	
                jQuery(".modal-body p").remove();
                var _message = "";
                for (name in result.errors)
                  _message += name +" "+result.errors[name];
                jQuery(".modal-body").prepend(jQuery("<p>").addClass("alert alert-error").html(_message));
                setTimeout(function() {jQuery(".modal-body p").remove();},3000);
          	}
           }
        });
      }
      return false;
	});

  jQuery(document).on("blur", ".cost", function(){
      if(jQuery(".salvage").val().trim() && (+jQuery(".salvage").val() < +jQuery(".cost").val()))
        jQuery("#cmdb_config_item_salvage-error").remove();
  });

  jQuery(document).on("submit", "#new_itil_vendor", function(e){
	e.preventDefault();
      var _this = jQuery(this);
      var cost = jQuery("#itil_vendor_product_vendor_price").val();
      var warranty_years = +jQuery("#itil_vendor_product_vendor_warranty_years").val();
      var warranty_months = +jQuery("#itil_vendor_product_vendor_warranty_months").val();
      
      if (_this.valid()){
        jQuery.ajax({
           url: _this.attr("action"),
           data: _this.serialize(),
           type: "POST",
           dataType: "json",
           success: function(result){
              if(result.status) {
                var _message = "<p>"+I18n.t('js_translations.cmdb.item_success', {item_name: result.item.vendor.name}) + "</p>";
                _this.find("input[type=text], textarea").val("");
                jQuery("#add_vendor").modal('hide');
                jQuery('select.vendor').append("<option value='"+result.item.vendor.id+"'>"+result.item.vendor.name+"</option>");
                jQuery("select.vendor").select2('val',result.item.vendor.id);
                toastr.success(_message);
                jQuery("#warranty_years").select2('val',warranty_years);
                jQuery("#warranty_months").select2('val',warranty_months);
                jQuery("#validity_years").select2('val',warranty_years);
                jQuery("#validity_months").select2('val',warranty_months);
                jQuery(".cost").val(cost);
                if(jQuery(".acquisition").length > 0){
                 updateExpiry(jQuery(".acquisition"), jQuery("select#warranty_years"), jQuery("select#warranty_months"), jQuery(".expiry"));
                }
                if(jQuery(".software_installation").length > 0){
                  updateExpiry(jQuery(".software_installation"), jQuery("select#validity_years"), jQuery("select#validity_months"), jQuery(".software_expiry"));
                }
              } else {
                jQuery("#add_vendor-submit").button("reset");
                jQuery(".modal-body p").remove();
                var _message = "";
                for (name in result.errors)
                  _message += name +" "+result.errors[name];
                jQuery(".modal-body").prepend(jQuery("<p>").addClass("alert alert-error").html(_message));
                setTimeout(function() {jQuery(".modal-body p").remove();},3000);
              }
           }
        });
      }
      return false;
	});

  jQuery.validator.addMethod("required_cost", function(value, element) {
    return(jQuery(element).val().trim() != "");
  }, 'This is a required field, if depreciation/salvage is provided.');

  jQuery.validator.addMethod("salvage", function(value, element) {
    return(!(jQuery(".cost").val() != "" && (+value > +jQuery(".cost").val())));
  }, 'Salvage should be lesser than cost.' );

  used_by.change(function(){
    var assigned_on_time = jQuery('#cmdb_config_item_assigned_on_time'), assigned_on_date = jQuery('#cmdb_config_item_assigned_on_date'), user_id = jQuery('#cmdb_config_item_user_id');
    if(user_id.val()) {
        var date = moment().utcOffset('2025-11-04 16:20:44 +0300');
      assigned_on_date.val(date.format(getDateFormat('datetime_placeholder')));
      assigned_on_time.val(date.format(current_user_time_format['moment']));
    }
    else if(assigned_on_time.val()) {
      assigned_on_date.val('');
      assigned_on_time.val('');
    }
    assigned_on_time.trigger('change');
  });

  jQuery("#new_cmdb_config_item, .edit_cmdb_config_item").submit(function(e){
      disableButton(this.id);
      jQuery("input[type='file'][id*='_file']").prop("disabled", true);

      var description = jQuery("#cmdb_config_item_description");
      description.val(description.val().trim());

      if(jQuery("#cmdb_config_item_agent_id.dataLabel").val() == "")  {
        jQuery("#cmdb_config_item_agent_id.data").val("");
      }
      if(jQuery("#cmdb_config_item_user_id.dataLabel").val() == "")  {
        jQuery("#cmdb_config_item_user_id.data").val("");
      }
      
      if(jQuery("#warranty_years").length > 0)  {
        var warranty_yrs = +jQuery("#warranty_years").val() || 0;
        var warranty_mths = +jQuery("#warranty_months").val() || 0;
        var warranty = (warranty_yrs*12) + (warranty_mths);
        jQuery("#warranty_years").parent().parent().next().val(warranty);
      }
      if(jQuery("#validity_years").length > 0)  {
        var validity_yrs = +jQuery("#validity_years").val() || 0;
        var validity_mths = +jQuery("#validity_months").val() || 0;
        var validity = (validity_yrs*12) + (validity_mths);
        jQuery("#validity_years").parent().parent().next().val(validity);
      }

      var salvage = jQuery(".salvage");
      var cost = jQuery(".cost");
      var depreciation = jQuery(".depreciation");
      if(salvage.length > 0)  {
        if(salvage.val() != "" || depreciation.select2('val') != "")  {
          jQuery('.cost,.salvage,.depreciation,.install_acquisition.date,.install_acquisition.date+.time').addClass('required_cost');
        }
        if(salvage.val() == "" && depreciation.select2('val') == "")  {
          jQuery('.cost,.salvage,.depreciation,.install_acquisition.date+.time').removeClass('required_cost');
        }
      }
 
      if(!jQuery(".ui-form").valid()) {
        enableButton(this.id);
        jQuery("input[type='file'][id*='_file']").prop("disabled", false);
        jQuery("#accordion .field_header").next("div").show();
        return false;
      }
  });

  jQuery(document).on('click', "#new_product", function(e){
    jQuery("#new_itil_product").resetForm();
    if (jQuery("#add_product .modal-header").length > 1) {
      jQuery("#add_product .modal-header").first().remove();
      jQuery("#add_product .modal-footer").last().remove();
      if(jQuery("#add_product .modal-header").parent().hasClass("modal fade"))  {
        jQuery("#add_product .modal-header").unwrap();
        jQuery("#add_product .modal-header").unwrap();
      }
    jQuery("#add_product-content").children().first().removeClass('modal fade').addClass('dialog hide').removeAttr('role').removeAttr('aria-hidden').removeAttr('style').css('display','block');
    }
  });

  jQuery(document).on('click', "#new_vendor", function(e){
    jQuery("#new_itil_vendor").resetForm();
    jQuery("#new_itil_vendor .select2-container").select2('data',null);
    if (jQuery("#add_vendor .modal-header").length > 1) {
      jQuery("#add_vendor .modal-header").first().remove();
      jQuery("#add_vendor .modal-footer").last().remove();
      if(jQuery("#add_vendor .modal-header").parent().hasClass("modal fade"))  {
        jQuery("#add_vendor .modal-header").unwrap();
        jQuery("#add_vendor .modal-header").unwrap();
      }
    }
    jQuery("#add_vendor-content").children().first().removeClass('modal fade').addClass('dialog hide').removeAttr('role').removeAttr('aria-hidden').removeAttr('style').css('display','block');
  });

  var form = jQuery("#new_cmdb_config_item, .edit_cmdb_config_item");
    form.submit(function(){
      if(!form.valid())  {
        jQuery("#accordion .field_header").next("div").show();
      }
    });  

  function disableButton(form_id){
    jQuery("#cmdb_config_item_submit").attr("disabled","disabled");
    if(form_id == "new_cmdb_config_item")
      jQuery("#cmdb_config_item_submit").val('Saving...')
    else
      jQuery("#cmdb_config_item_submit").val('Updating...');
  }

  function enableButton(form_id){
    jQuery("#cmdb_config_item_submit").removeAttr("disabled");
    if(form_id == "new_cmdb_config_item")
      jQuery("#cmdb_config_item_submit").val('Save')
    else
      jQuery("#cmdb_config_item_submit").val('Update'); 
  }

  jQuery("body").on("change", ".datepair", function(){
    if(jQuery(this).val() && !jQuery(this).next().val())
      jQuery(this).next().val(current_user_time_format['set_midnight'])
  });


//]]>
</script>

<div id="add_product" class="dialog hide">
	<form class="new_itil_product ui-form" id="new_itil_product" action="/itil/products" accept-charset="UTF-8" method="post" novalidate="novalidate"><input name="utf8" type="hidden" value="✓" autocomplete="off"><input type="hidden" name="authenticity_token" value="xxY7Cuhzb9homBScm7vgi7sIPZY9XG4uTZKv10xBPhsPAN4TwlsDWS57tarbl1HSgNzXDLTzhbFShTTD7Qrjzw==" autocomplete="off"> 
	
	<div class="form_wrapper">
		<div class="row-fluid">
			<span class="span6">
				<label class="inline pull-left" for="itil_product_name">Product Name<sup class="required_star">*</sup></label>
				<input class="required txt-field ignore_on_hidden" type="text" name="itil_product[name]" id="itil_product_name" aria-required="true">
			</span>
			<span class="span6">
				<label class="inline pull-left" for="itil_product_manufacturer">Manufacturer</label>
				<input class="txt-field" type="text" name="itil_product[manufacturer]" id="itil_product_manufacturer">
			</span>
		</div>
		<input type="hidden" name="cmdb_ci_type_id" id="cmdb_ci_type_id" autocomplete="off">
	</div>

</form>
</div>

<div id="add_vendor" class="dialog hide">
	<form id="new_itil_vendor" novalidate="novalidate">
  <div id="errorExplanation" class="errorExplanation hide"> </div>
  <ul class="ui-form">
      <li class="field text">
        <!-- Render the label with or without star.. -->
        <label for="itil_vendor_name">Name<sup class="required_star">*</sup></label>
        <!-- Render the appropriate ui field type here -->
          <input type="text" name="itil_vendor[name]" id="itil_vendor_name" class="required ignore_on_hidden text " minlength="2" maxlength="255" placeholder="Enter Name" aria-required="true">
      </li>
      <li class="field text">
        <!-- Render the label with or without star.. -->
        <label for="itil_vendor_contact_name">Contact Name</label>
        <!-- Render the appropriate ui field type here -->
          <input type="text" name="itil_vendor[contact_name]" id="itil_vendor_contact_name" class="itil_vendor[contact_name] text " minlength="2" maxlength="255" placeholder="Enter Contact Name">
      </li>
      <li class="field phone_number">
        <!-- Render the label with or without star.. -->
        <label for="itil_vendor_phone">Phone</label>
        <!-- Render the appropriate ui field type here -->
          <input type="text" name="itil_vendor[phone]" id="itil_vendor_phone" class="itil_vendor[phone] phone_number " minlength="2" maxlength="255" placeholder="Enter Phone">
      </li>
      <li class="field phone_number">
        <!-- Render the label with or without star.. -->
        <label for="itil_vendor_mobile">Mobile</label>
        <!-- Render the appropriate ui field type here -->
          <input type="text" name="itil_vendor[mobile]" id="itil_vendor_mobile" class="itil_vendor[mobile] phone_number " minlength="2" maxlength="255" placeholder="Enter Mobile">
      </li>
      <li class="field email">
        <!-- Render the label with or without star.. -->
        <label for="itil_vendor_email">Email</label>
        <!-- Render the appropriate ui field type here -->
          <input type="text" name="itil_vendor[email]" id="itil_vendor_email" class="itil_vendor[email] email " minlength="2" maxlength="255" placeholder="Enter Email">
      </li>
      <li class="field paragraph">
        <!-- Render the label with or without star.. -->
        <label for="itil_vendor_description">Description</label>
        <!-- Render the appropriate ui field type here -->
          <textarea name="itil_vendor[description]" id="itil_vendor_description" rows="5" class="itil_vendor[description] paragraph " placeholder="Enter Description"></textarea>
      </li>
      <li class="field paragraph">
        <!-- Render the label with or without star.. -->
        <label for="itil_vendor_address_attributes_address1">Address</label>
        <!-- Render the appropriate ui field type here -->
          <textarea name="itil_vendor[address_attributes][address1]" id="itil_vendor_address_attributes_address1" rows="5" class="itil_vendor[address_attributes][address1] paragraph " placeholder="Enter Address"></textarea>
      </li>
      <li class="field text">
        <!-- Render the label with or without star.. -->
        <label for="itil_vendor_address_attributes_country">Country</label>
        <!-- Render the appropriate ui field type here -->
          <input type="text" name="itil_vendor[address_attributes][country]" id="itil_vendor_address_attributes_country" class="itil_vendor[address_attributes][country] text " minlength="2" maxlength="255" placeholder="Enter Country">
      </li>
      <li class="field text">
        <!-- Render the label with or without star.. -->
        <label for="itil_vendor_address_attributes_state">State</label>
        <!-- Render the appropriate ui field type here -->
          <input type="text" name="itil_vendor[address_attributes][state]" id="itil_vendor_address_attributes_state" class="itil_vendor[address_attributes][state] text " minlength="2" maxlength="255" placeholder="Enter State">
      </li>
      <li class="field text">
        <!-- Render the label with or without star.. -->
        <label for="itil_vendor_address_attributes_city">City</label>
        <!-- Render the appropriate ui field type here -->
          <input type="text" name="itil_vendor[address_attributes][city]" id="itil_vendor_address_attributes_city" class="itil_vendor[address_attributes][city] text " minlength="2" maxlength="255" placeholder="Enter City">
      </li>
      <li class="field text">
        <!-- Render the label with or without star.. -->
        <label for="itil_vendor_address_attributes_zip">ZipCode</label>
        <!-- Render the appropriate ui field type here -->
          <input type="text" name="itil_vendor[address_attributes][zip]" id="itil_vendor_address_attributes_zip" class="itil_vendor[address_attributes][zip] text " minlength="2" maxlength="255" placeholder="Enter ZipCode">
      </li>
    <input type="hidden" name="from_list" id="from_list" value="false" autocomplete="off">
      <li class="row-fluid product_type_form clearfix mb20">
        <h3 id="product_name_title">Product :function(e){return Ne(this,function(e){return void 0===e?ge.text(this):this.empty().each(function(){1!==this.nodeType&amp;&amp;11!==this.nodeType&amp;&amp;9!==this.nodeType||(this.textContent=e)})},null,e,arguments.length)}</h3>
      </li>
      <li class="field">
        <input type="hidden" name="itil_vendor[product_vendor][product_id]" id="itil_vendor_product_vendor_product_id">
        <label for="itil_vendor_product_vendor_price">Price ($)<sup class="required_star">*</sup></label>
        <input type="text" name="itil_vendor[product_vendor][price]" id="itil_vendor_product_vendor_price" class="required text ignore_on_hidden" aria-required="true">
      </li>
      <li class="field">
        <label class="light-color" for="itil_vendor_product_vendor_warranty">Warranty/Validity</label>
        <div class="select2-container waranty-period select2 vendor_dropdown" id="s2id_itil_vendor_product_vendor_warranty_years"><a href="javascript:void(0)" class="select2-choice" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-7">...</span><abbr class="select2-search-choice-close"></abbr>   <div class="select2-arrow" role="presentation"><b role="presentation"></b></div></a><label for="s2id_autogen7" class="select2-offscreen"></label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" aria-labelledby="select2-chosen-7" id="s2id_autogen7"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen7_search" class="select2-offscreen"></label>       <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-7" id="s2id_autogen7_search">       <i class="ficon-search"></i>       <i class="ficon-close close-icon"></i>   </div>   <ul class="select2-results" role="listbox" id="select2-results-7">   </ul></div></div><select name="itil_vendor[product_vendor][warranty_years]" id="itil_vendor_product_vendor_warranty_years" class="waranty-period select2 vendor_dropdown select2-offscreen" tabindex="-1" title=""><option value="">...</option><option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option></select>
        <div class="select2-container waranty-period select2 vendor_dropdown" id="s2id_itil_vendor_product_vendor_warranty_months"><a href="javascript:void(0)" class="select2-choice" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-8">...</span><abbr class="select2-search-choice-close"></abbr>   <div class="select2-arrow" role="presentation"><b role="presentation"></b></div></a><label for="s2id_autogen8" class="select2-offscreen"></label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" aria-labelledby="select2-chosen-8" id="s2id_autogen8"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen8_search" class="select2-offscreen"></label>       <input type="text" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-8" id="s2id_autogen8_search">       <i class="ficon-search"></i>       <i class="ficon-close close-icon"></i>   </div>   <ul class="select2-results" role="listbox" id="select2-results-8">   </ul></div></div><select name="itil_vendor[product_vendor][warranty_months]" id="itil_vendor_product_vendor_warranty_months" class="waranty-period select2 vendor_dropdown select2-offscreen" tabindex="-1" title=""><option value="">...</option><option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option></select>
      </li>
  </ul>
  <input type="hidden" name="itil_vendor[update_from]" id="itil_vendor_update_from">
<input type="hidden" name="authenticity_token" value="xxY7Cuhzb9homBScm7vgi7sIPZY9XG4uTZKv10xBPhsPAN4TwlsDWS57tarbl1HSgNzXDLTzhbFShTTD7Qrjzw=="></form>

<script>
    jQuery(document).ready(function () {

      // Variables
      var fromAssociation = "true";

      function initUserAutocomplete() {
        jQuery('#itil_vendor_contact_name').attr('maxlength','511').autocomplete({
              source: function(request, response) {
                  jQuery.ajax({
                      url: "/search/autocomplete/itil_requesters",
                      data: {
                          q: request.term
                      },
                      success: function(data) {
                          response(jQuery.map(data.results,
                              function(item) {
                                  return {
                                      value: item.name,
                                      id: item.id,
                                      email: item.email,
                                      phone: item.phone,
                                      mobile: item.mobile
                                  }
                              }));
                      }
                  });
              },
              select: function(event, ui) {
                  jQuery('#itil_vendor_contact_name').val(ui.item.value);
                  jQuery("#itil_vendor_email").val(ui.item.email);
                  jQuery("#itil_vendor_phone").val(ui.item.phone);
                  jQuery("#itil_vendor_mobile").val(ui.item.mobile);
              },
              open: function() {
                  jQuery(this).removeClass('ui-corner-all');
              }
          }).data("autocomplete")
              ._renderItem = function(ul, item) {
              return jQuery("<li></li>")
                  .data("item.autocomplete", item)
                  .append("<a>" + item.value + "&lt;" + (item.email || item.phone) + "&gt;</a>")
                  .appendTo(ul);
          }
      }
      
      jQuery("#product_name_title").text('Product'+ ' :'+ ( jQuery('select.new_product').select2("data") ? jQuery('select.new_product').select2("data").text : ''));

      // Events 
      jQuery('#add_vendor').on('shown.bs.modal', initUserAutocomplete);
      jQuery('#new-vendor').on('loaded.bs.modal', initUserAutocomplete);

      jQuery('#itil_vendor_contact_name').bind("keydown", function(ev) {}).trigger("keydown");

      jQuery(document).off('submit', '#new_itil_vendor').on('submit', '#new_itil_vendor', function(e) {
          e.preventDefault();
          if (!jQuery(this).valid()) {
              return false;
          }
          jQuery('#new-vendor-submit').addClass('disabled');
          var dialog_id = "add_vendor";
          var isUpdate = false;

          var url, method, reference, message;
          if(isUpdate) {
              url =  "/itil/vendors/", method = "PUT", message = "update_msg";
          } else {
              url = "/itil/vendors", method = "POST", message = "create_msg";
          }

          if(fromAssociation == 'true') {
              var p_id = jQuery("select.new_product").val();
              jQuery("#itil_vendor_product_vendor_product_id").val(p_id);
          }
          
          jQuery("#itil_vendor_update_from").val("");
          if(false) {
            reference = jQuery(".vendor-details-div");
          } else {
            reference = jQuery(".vendor-list");
          }
          jQuery.ajax({
              type     : method,
              url      : url,
              data     : jQuery('#new_itil_vendor').serialize(),
              
              success  : function(resp) {
                          if(fromAssociation == 'true') {
                              jQuery('select.vendor').append("<option value='" + resp.item.vendor.id + "'>" + resp.item.vendor.name + "</option>");
                              jQuery("select.vendor").select2('val', resp.item.vendor.id);
                          } else {
                              reference.html(resp);
                              var updatedVendorEle = jQuery('#vendor_id');
                              if(updatedVendorEle.length){
                                jQuery('.itil_vendor_wrapper #sticky_header .vendor_header .title, .vendor_header span').text(updatedVendorEle.data('vendorName'));
                              }
                          }
                          jQuery('.modal#'+dialog_id).modal("hide");
                          toastr.success( I18n.t("js_translations.vendors."+message));
                          jQuery('#new-vendor-submit').removeClass('disabled');
                        }, 
              error: function(error) {
                        var errorText = JSON.parse(error.responseText).error;
                        if (errorText === "name_uniq_error" ) {
                            var element = jQuery('#itil_vendor_name');
                            element.after('<label id="itil_vendor_name-error" class="error" for="itil_vendor_name">' +
                                I18n.t("js_translations.vendors.name_error")+'</label>');
                            element.addClass("error");
                        } else {
                            toastr.error(I18n.t("js_translations.vendors.error"));
                            jQuery('.modal#new-vendor').modal("hide");
                            reference.html(resp);
                        }
                        jQuery('#new-vendor-submit').removeClass('disabled');
                      }
            });
        });

      });
</script>
</div>

<div class="control-group mt20"><div class="form_wrapper wrapper-attach-file">        
  <div class="attachments" id="cmdb_config_item-container" style="display:none">
    <div id="cmdb_config_item-attachments" class="shared_attachment_list"></div>
  </div>
    <div class="attach_wrapper clearfix">
      <div class="attach_file">
        <label class="ficon-attachment">
          Attach a file  
          <span class="info"> 
            (File size &lt; 40 MB) 
          <span>
        </span></span></label>
        <div class="uploader" id="uniform-undefined">
          <input type="file" id="cmdb_config_item_file" name="emptyfile" namewhenfilled="cmdb_config_item[attachments][][resource]" filecontainer="cmdb_config_item-container" filelist="cmdb_config_item-attachments" sendfocusto="cmdb_config_item-body" style="opacity:0" class="original_input">
          <span class="filename">No Files Selected</span>
          <span class="action btn">Choose File</span>
        </div>
      </div>
    </div>
    
  
  <script type="text/javascript">
    // Fix for Firefox/IE - To override :hover style persistance after click on input[type=file] element
    jQuery('div.attach-wrapper a[data-toggle="dropdown"]').bind('click', function(){
      jQuery(this).parents('div.attach-wrapper').find('a.attach-link-wrap').first().css({
            'background-color': 'inherit',
            'background-image': 'inherit',
            'color': 'inherit',
            'box-shadow': 'inherit'
        });
    });
          
    jQuery('li.portal-attach a.attach-link-wrap')
      .bind('mouseover', function(){
        jQuery(this).removeAttr('style');
      })
      .bind('mousemove', function(event){
        // Fix to move "Browse" button along with mouse pointer - fix for IE.
        p=jQuery(this).find('div').first();
        newLeft = Math.min(175, Math.max(event.clientX - p.offset().left + p.position().left - 5, 0));
        window.title = newLeft;
        p.css({left: newLeft, top: 0});
      })
  </script>
 
  <script>
     function removefile(){
            var attachmentLen = jQuery('.attachments .item').length;
            if(jQuery(".shared_attachment_list").children().length==0)
            {
                jQuery(".attachments").css("display","none");
            }
            if(attachmentLen == 0)
            {
               jQuery(".filename").text("No files selected");
            }
            else
            {
              jQuery(".filename").text(attachmentLen + " file(s) selected");
            }
        }
</script>

<script id="file-list-template" type="text/x-jquery-tmpl">			
  <div class="item">
  	<span class="{{if (!file_valid)}} invalid_attachment {{else}} valid_attachment {{/if}}">
  		${name} {{if (size != '0' && size != '0.00 KB ') }} ( ${size} ){{/if}}
    </span>
    <span> 
      {{if (file_valid) }}
        <a class="attachment-close" href="javascript:void(0)" onclick="Helpdesk.Multifile.remove(this); return false;" inputId="${inputId}"></a>
      {{else}}
        <span class="alert-text">
          {{if (invalid_file_extension) }}
            You cannot attach these files as your administrator has blocked these file types.
          {{else}}
            This file exceeds the 40 MB limit. Make sure it is less than 40 MB.
          {{/if}} 
        </span>
      {{/if}}
    </span>
  </div>
</script></div> </div>

<script>
//<![CDATA[

jQuery(document).ready(function(){
    jQuery('.usage_type').hide();
    jQuery('.modern-ui .wrapper-attach-file .attach_file label').on('click', function(){
      jQuery('.wrapper-attach-file .attach_file .uploader input[name="emptyfile"]').click();
    });
     //When action is create, retaining the form values. In case of error.
      jQuery("#new_product").hide();
      if(jQuery("#cmdb_config_item_ci_type_id").val() != "")
      {
        jQuery("#cmdb_config_item_ci_type_id").trigger('change');
      }
      jQuery('.usage_type').hide();
});

//]]>
</script>
					</div><input type="hidden" name="redactor_image_field" value="&quot;[]&quot;"></form>
					<div id="ticket_panel" class="modal_task_container"></div>
				</div>
		</div>