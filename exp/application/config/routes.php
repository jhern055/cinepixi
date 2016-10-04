<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route["default_controller"] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route["404_override"] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route["translate_uri_dashes"] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route["default_controller"] = 'login';
$route["404_override"] = '';
$route["translate_uri_dashes"] = FALSE;

// <details> GLOBAL
$route["article/details"]    ="stock/catalog/article/article/details";            
$route["article/add_detail"] ="stock/catalog/article/article/add_detail";            
$route["article/add_detail_do"] ="stock/catalog/article/article/add_detail_do";            
$route["article/tokeninput?(:any)"] ="stock/catalog/article/article/tokeninput";            
// </details>

// <admin>
	$route["admin"]          ="admin/admin/index";            
	$route["admin/children"] ="admin/admin/index";            
	// <client>
	// $route["client/clientView/(:num)"]  ="client/client/clientView/$1"; 
	$route["admin/client"]                    ="admin/client/client/index";            
	$route["admin/client/children"]           ="admin/client/client/children";            
	$route["admin/client/client_ajax"]        ="admin/client/client/client_ajax";       
	$route["admin/client/client_ajax/(:num)"] ="admin/client/client/client_ajax/$1";
	$route["admin/client/clientView"]         ="admin/client/client/clientView";        
	$route["admin/client/clientView/(:num)"]  ="admin/client/client/clientView/$1"; 
	$route["admin/client/client_delete"]      ="admin/client/client/client_delete";	

	$route["admin/client/clientSubsidiaryView"]    ="admin/client/client/clientSubsidiaryView";            
	$route["admin/client/clientSubsidiary_delete"] ="admin/client/client/clientSubsidiary_delete";            
	// </client>

	// <provider>
	$route["admin/provider"]                      ="admin/provider/provider/index";            
	$route["admin/provider/children"]             ="admin/provider/provider/children";            
	$route["admin/provider/provider_ajax"]        ="admin/provider/provider/provider_ajax";       
	$route["admin/provider/provider_ajax/(:num)"] ="admin/provider/provider/provider_ajax/$1";
	$route["admin/provider/providerView"]         ="admin/provider/provider/providerView";        
	$route["admin/provider/providerView/(:num)"]  ="admin/provider/provider/providerView/$1"; 
	$route["admin/provider/provider_delete"]      ="admin/provider/provider/provider_delete";	

	$route["admin/provider/providerSubsidiaryView"]    ="admin/provider/provider/providerSubsidiaryView";            
	$route["admin/provider/providerSubsidiary_delete"] ="admin/provider/provider/providerSubsidiary_delete";  
	// </provider>

	// <stock>
	$route["admin/stock"]          ="admin/stock/stock/index";            
	$route["admin/stock/children"] ="admin/stock/stock/children";            
	$route["admin/stock/catalog"]  ="admin/stock/catalog/catalog/index";            
	$route["admin/stock/catalog/children"]  ="admin/stock/catalog/catalog/children";            

	$route["admin/stock/catalog/article"]                     ="admin/stock/catalog/article/article/index";            
	$route["admin/stock/catalog/article/children"]            ="admin/stock/catalog/article/article/children";            
	$route["admin/stock/catalog/article/article_ajax"]        ="admin/stock/catalog/article/article/article_ajax";       
	$route["admin/stock/catalog/article/article_ajax/(:num)"] ="admin/stock/catalog/article/article/article_ajax/$1";
	$route["admin/stock/catalog/article/articleView"]         ="admin/stock/catalog/article/article/articleView";        
	$route["admin/stock/catalog/article/articleView/(:num)"]  ="admin/stock/catalog/article/article/articleView/$1"; 
	$route["admin/stock/catalog/article/article_delete"]      ="admin/stock/catalog/article/article/article_delete";	

	$route["admin/stock/catalog/category"]                      ="admin/stock/catalog/category/category/index";            
	$route["admin/stock/catalog/category/children"]             ="admin/stock/catalog/category/category/children";            
	$route["admin/stock/catalog/category/category_ajax"]        ="admin/stock/catalog/category/category/category_ajax";       
	$route["admin/stock/catalog/category/category_ajax/(:num)"] ="admin/stock/catalog/category/category/category_ajax/$1";
	$route["admin/stock/catalog/category/categoryView"]         ="admin/stock/catalog/category/category/categoryView";        
	$route["admin/stock/catalog/category/categoryView/(:num)"]  ="admin/stock/catalog/category/category/categoryView/$1"; 
	$route["admin/stock/catalog/category/category_delete"]      ="admin/stock/catalog/category/category/category_delete";	
	$route["category/category_tokeninput?(:any)"]               ="admin/stock/catalog/category/category/category_tokeninput/";            
	
	// </stock>

	// <purchase>
	$route["admin/purchase"]                      ="admin/purchase/purchase/index";            
	$route["admin/purchase/children"]             ="admin/purchase/purchase/children";            
	$route["admin/purchase/purchase_ajax"]        ="admin/purchase/purchase/purchase_ajax";       
	$route["admin/purchase/purchase_ajax/(:num)"] ="admin/purchase/purchase/purchase_ajax/$1";
	$route["admin/purchase/purchaseView"]         ="admin/purchase/purchase/purchaseView";        
	$route["admin/purchase/purchaseView/(:num)"]  ="admin/purchase/purchase/purchaseView/$1"; 
	$route["admin/purchase/purchase_delete"]      ="admin/purchase/purchase/purchase_delete";	

	$route["admin/purchase/order"]                            ="admin/purchase/order/PurchaseOrder/index";            
	$route["admin/purchase/order/children"]                   ="admin/purchase/order/PurchaseOrder/children";            
	$route["admin/purchase/order/purchase_order_ajax"]        ="admin/purchase/order/PurchaseOrder/purchase_order_ajax";       
	$route["admin/purchase/order/purchase_order_ajax/(:num)"] ="admin/purchase/order/PurchaseOrder/purchase_order_ajax/$1";
	$route["admin/purchase/order/orderView"]                  ="admin/purchase/order/PurchaseOrder/orderView";        
	$route["admin/purchase/order/orderView/(:num)"]           ="admin/purchase/order/PurchaseOrder/orderView/$1"; 
	$route["admin/purchase/order/purchase_order_delete"]      ="admin/purchase/order/PurchaseOrder/purchase_order_delete";	
	
	// </purchase>

	// <sale>
	$route["admin/sale"]                  ="admin/sale/sale/index";            
	$route["admin/sale/children"]         ="admin/sale/sale/children";            
	$route["admin/sale/sale_ajax"]        ="admin/sale/sale/sale_ajax";       
	$route["admin/sale/sale_ajax/(:num)"] ="admin/sale/sale/sale_ajax/$1";
	$route["admin/sale/saleView"]         ="admin/sale/sale/saleView";        
	$route["admin/sale/saleView/(:num)"]  ="admin/sale/sale/saleView/$1"; 
	$route["admin/sale/sale_delete"]      ="admin/sale/sale/sale_delete";	

	$route["admin/sale/remission"]                       ="admin/sale/remission/index";            
	$route["admin/sale/remission/children"]              ="admin/sale/remission/children";            
	$route["admin/sale/remission/remission_ajax"]        ="admin/sale/remission/remission_ajax";       
	$route["admin/sale/remission/remission_ajax/(:num)"] ="admin/sale/remission/remission_ajax/$1";
	$route["admin/sale/remission/remissionView"]         ="admin/sale/remission/remissionView";        
	$route["admin/sale/remission/remissionView/(:num)"]  ="admin/sale/remission/remissionView/$1"; 
	$route["admin/sale/remission/remission_delete"]      ="admin/sale/remission/remission_delete";

	$route["admin/sale/request"]                     ="admin/sale/request/index";            
	$route["admin/sale/request/children"]            ="admin/sale/request/children";            
	$route["admin/sale/request/request_ajax"]        ="admin/sale/request/request_ajax";       
	$route["admin/sale/request/request_ajax/(:num)"] ="admin/sale/request/request_ajax/$1";
	$route["admin/sale/request/requestView"]         ="admin/sale/request/requestView";        
	$route["admin/sale/request/requestView/(:num)"]  ="admin/sale/request/requestView/$1"; 
	$route["admin/sale/request/request_delete"]      ="admin/sale/request/request_delete";	

	$route["admin/sale/quatition"]                       ="admin/sale/quatition/index";            
	$route["admin/sale/quatition/children"]              ="admin/sale/quatition/children";            
	$route["admin/sale/quatition/quatition_ajax"]        ="admin/sale/quatition/quatition_ajax";       
	$route["admin/sale/quatition/quatition_ajax/(:num)"] ="admin/sale/quatition/quatition_ajax/$1";
	$route["admin/sale/quatition/quatitionView"]         ="admin/sale/quatition/quatitionView";        
	$route["admin/sale/quatition/quatitionView/(:num)"]  ="admin/sale/quatition/quatitionView/$1"; 
	$route["admin/sale/quatition/quatition_delete"]      ="admin/sale/quatition/quatition_delete";	

	$route["admin/sale/creditNote"]                        ="admin/sale/creditNote/index";            
	$route["admin/sale/creditNote/children"]               ="admin/sale/creditNote/children";            
	$route["admin/sale/creditNote/creditNote_ajax"]        ="admin/sale/creditNote/creditNote_ajax";       
	$route["admin/sale/creditNote/creditNote_ajax/(:num)"] ="admin/sale/creditNote/creditNote_ajax/$1";
	$route["admin/sale/creditNote/creditNoteView"]         ="admin/sale/creditNote/creditNoteView";        
	$route["admin/sale/creditNote/creditNoteView/(:num)"]  ="admin/sale/creditNote/creditNoteView/$1"; 
	$route["admin/sale/creditNote/creditNote_delete"]      ="admin/sale/creditNote/creditNote_delete";	

	$route["admin/sale/openingBalance"]                            ="admin/sale/openingBalance/index";            
	$route["admin/sale/openingBalance/children"]                   ="admin/sale/openingBalance/children";            
	$route["admin/sale/openingBalance/openingBalance_ajax"]        ="admin/sale/openingBalance/openingBalance_ajax";       
	$route["admin/sale/openingBalance/openingBalance_ajax/(:num)"] ="admin/sale/openingBalance/openingBalance_ajax/$1";
	$route["admin/sale/openingBalance/openingBalanceView"]         ="admin/sale/openingBalance/openingBalanceView";        
	$route["admin/sale/openingBalance/openingBalanceView/(:num)"]  ="admin/sale/openingBalance/openingBalanceView/$1"; 
	$route["admin/sale/openingBalance/openingBalance_delete"]      ="admin/sale/openingBalance/OpeningBalance_delete";	
	
	// </sale>
	// <payment>
	$route["admin/payment/admin"] ="admin/payment/payment/admin";            
		// <payment>
		$route["admin/payment"]                     ="admin/payment/payment/index";            
		$route["admin/payment/children"]            ="admin/payment/payment/children";            
		$route["admin/payment/payment_ajax"]        ="admin/payment/payment/payment_ajax";       
		$route["admin/payment/payment_ajax/(:num)"] ="admin/payment/payment/payment_ajax/$1";
		$route["admin/payment/paymentView"]         ="admin/payment/payment/paymentView";        
		$route["admin/payment/paymentView/(:num)"]  ="admin/payment/payment/paymentView/$1"; 
		$route["admin/payment/payment_delete"]      ="admin/payment/payment/payment_delete";	
		$route["admin/payment/get_payment_by"]      ="admin/payment/payment/get_payment_by";	
		$route["payment/admin"]                     ="admin/payment/payment/admin";	
		$route["payment/payment_details"]           ="admin/payment/payment/payment_details";	
		$route["payment/add_payment"]               ="admin/payment/payment/add_payment";	
		$route["payment/add_payment_do"]            ="admin/payment/payment/add_payment_do";	
		// </payment>
	// </payment>
// <admin>


// <config>

	$route["config"]          ="config/config/index";            
	$route["config/children"] ="config/config/index"; 

	// INVOICE
	$route["config/invoice"]="config/config/invoice/children";      
	$route["config/invoice/children"]="config/config/invoice/children";      

	// <shcp_file>
	$route["config/invoice/shcp_file"]                       ="config/invoice/shcp_file/Shcpfile/index";            
	$route["config/invoice/shcp_file/children"]              ="config/invoice/shcp_file/Shcpfile/children";            
	$route["config/invoice/shcp_file/shcp_file_ajax"]        ="config/invoice/shcp_file/Shcpfile/shcp_file_ajax";       
	$route["config/invoice/shcp_file/shcp_file_ajax/(:num)"] ="config/invoice/shcp_file/Shcpfile/shcp_file_ajax/$1";
	$route["config/invoice/shcp_file/shcp_fileView"]         ="config/invoice/shcp_file/Shcpfile/shcp_fileView";        
	$route["config/invoice/shcp_file/shcp_fileView/(:num)"]  ="config/invoice/shcp_file/Shcpfile/shcp_fileView/$1"; 
	$route["config/invoice/shcp_file/shcp_file_delete"]      ="config/invoice/shcp_file/Shcpfile/shcp_file_delete";

	// </shcp_file>

	// <series>
	$route["config/invoice/series"]                    ="config/invoice/series/series/index";            
	$route["config/invoice/series/children"]           ="config/invoice/series/series/children";            
	$route["config/invoice/series/series_ajax"]        ="config/invoice/series/series/series_ajax";       
	$route["config/invoice/series/series_ajax/(:num)"] ="config/invoice/series/series/series_ajax/$1";
	$route["config/invoice/series/seriesView"]         ="config/invoice/series/series/seriesView";        
	$route["config/invoice/series/seriesView/(:num)"]  ="config/invoice/series/series/seriesView/$1"; 
	$route["config/invoice/series/series_delete"]      ="config/invoice/series/series/series_delete";
	// </series>

	// <pac>
	$route["config/invoice/pac"]                 ="config/invoice/pac/pac/index";            
	$route["config/invoice/pac/children"]        ="config/invoice/pac/pac/children";            
	$route["config/invoice/pac/pac_ajax"]        ="config/invoice/pac/pac/pac_ajax";       
	$route["config/invoice/pac/pac_ajax/(:num)"] ="config/invoice/pac/pac/pac_ajax/$1";
	$route["config/invoice/pac/pacView"]         ="config/invoice/pac/pac/pacView";        
	$route["config/invoice/pac/pacView/(:num)"]  ="config/invoice/pac/pac/pacView/$1"; 
	$route["config/invoice/pac/pac_delete"]      ="config/invoice/pac/pac/pac_delete";	
	// </pac>

// / INVOICE
	// <enterprise>
	$route["config/enterprise"]                        ="config/enterprise/enterprise/index";            
	$route["config/enterprise/children"]               ="config/enterprise/enterprise/children";            
	$route["config/enterprise/enterprise_ajax"]        ="config/enterprise/enterprise/enterprise_ajax";       
	$route["config/enterprise/enterprise_ajax/(:num)"] ="config/enterprise/enterprise/enterprise_ajax/$1";
	$route["config/enterprise/enterpriseView"]         ="config/enterprise/enterprise/enterpriseView";        
	$route["config/enterprise/enterpriseView/(:num)"]  ="config/enterprise/enterprise/enterpriseView/$1"; 
	$route["config/enterprise/enterprise_delete"]      ="config/enterprise/enterprise/enterprise_delete";	
	// </enterprise>

	// <subsidiary>
	$route["config/subsidiary"]                        ="config/subsidiary/subsidiary/index";            
	$route["config/subsidiary/children"]               ="config/subsidiary/subsidiary/children";            
	$route["config/subsidiary/subsidiary_ajax"]        ="config/subsidiary/subsidiary/subsidiary_ajax";       
	$route["config/subsidiary/subsidiary_ajax/(:num)"] ="config/subsidiary/subsidiary/subsidiary_ajax/$1";
	$route["config/subsidiary/subsidiaryView"]         ="config/subsidiary/subsidiary/subsidiaryView";        
	$route["config/subsidiary/subsidiaryView/(:num)"]  ="config/subsidiary/subsidiary/subsidiaryView/$1"; 
	$route["config/subsidiary/subsidiary_delete"]      ="config/subsidiary/subsidiary/subsidiary_delete";	
	// </subsidiary>

	// <email>
	$route["config/email"]                   ="config/email/email/index";            
	$route["config/email/children"]          ="config/email/email/children";            
	$route["config/email/email_ajax"]        ="config/email/email/email_ajax";       
	$route["config/email/email_ajax/(:num)"] ="config/email/email/email_ajax/$1";
	$route["config/email/emailView"]         ="config/email/email/emailView";        
	$route["config/email/emailView/(:num)"]  ="config/email/email/emailView/$1"; 
	$route["config/email/email_delete"]      ="config/email/email/email_delete";	
	$route["email/send"]                     ="config/email/email/send";	
	// </email>			
// </config>


// Commerce
	$route["commerce"]                 ="commerce/commerce/index";            
	$route["commerce/category/(:any)"] ="commerce/commerce/category/$1";
// ...

// Cienevelo
	$route["cinepixi"]          ="cinepixi/cinepixi/index";            
	$route["cinepixi/children"] ="cinepixi/cinepixi/index"; 
	
	$route["cinepixi/movie"]                   ="cinepixi/movie/movie/index";            
	$route["cinepixi/movie/children"]          ="cinepixi/movie/movie/children";            
	$route["cinepixi/movie/movie_ajax"]        ="cinepixi/movie/movie/movie_ajax";       
	$route["cinepixi/movie/movie_ajax/(:num)"] ="cinepixi/movie/movie/movie_ajax/$1";
	$route["cinepixi/movie/movieView"]         ="cinepixi/movie/movie/movieView";        
	$route["cinepixi/movie/movieView/(:num)"]  ="cinepixi/movie/movie/movieView/$1"; 
	$route["cinepixi/movie/movie_delete"]      ="cinepixi/movie/movie/movie_delete";

	$route["cinepixi/movie/category"]                      ="cinepixi/movie/category/category/index";            
	$route["cinepixi/movie/category/children"]             ="cinepixi/movie/category/category/children";            
	$route["cinepixi/movie/category/category_ajax"]        ="cinepixi/movie/category/category/category_ajax";       
	$route["cinepixi/movie/category/category_ajax/(:num)"] ="cinepixi/movie/category/category/category_ajax/$1";
	$route["cinepixi/movie/category/categoryView"]         ="cinepixi/movie/category/category/categoryView";        
	$route["cinepixi/movie/category/categoryView/(:num)"]  ="cinepixi/movie/category/category/categoryView/$1"; 
	$route["cinepixi/movie/category/category_delete"]      ="cinepixi/movie/category/category/category_delete";
	$route["cinepixi/movie/category/category_tokeninput?(:any)"] ="cinepixi/movie/category/category/category_tokeninput";            

	$route["cinepixi/pathFile"]                             ="cinepixi/pathFile/pathFile/index";            
	$route["cinepixi/pathFile/children"]                    ="cinepixi/pathFile/pathFile/children";            
	$route["cinepixi/pathFile/pathFile_ajax"]              ="cinepixi/pathFile/pathFile/pathFile_ajax";       
	$route["cinepixi/pathFile/pathFile_ajax/(:num)"]       ="cinepixi/pathFile/pathFile/pathFile_ajax/$1";
	$route["cinepixi/pathFile/pathFileView"]               ="cinepixi/pathFile/pathFile/pathFileView";        
	$route["cinepixi/pathFile/pathFileView/(:num)"]        ="cinepixi/pathFile/pathFile/pathFileView/$1"; 
	$route["cinepixi/pathFile/pathFile_delete"]            ="cinepixi/pathFile/pathFile/pathFile_delete";
	$route["cinepixi/pathFile/pathFile_tokeninput?(:any)"] ="cinepixi/pathFile/pathFile/pathFile_tokeninput";            

// ...
// WEB web

	$route["web"]          ="web/web/index";            
	$route["web/children"] ="web/web/index"; 

	// MENUS 

	$route["web/menus"] ="web/menus/menus/children";

	// Menu primario 
	$route["web/menus/primary"]                      ="web/menus/primary/menuPrimary/index";            
	$route["web/menus/primary/children"]             ="web/menus/primary/menuPrimary/children";            
	$route["web/menus/primary/menuPrimary_ajax"]        ="web/menus/primary/menuPrimary/menuPrimary_ajax";       
	$route["web/menus/primary/menuPrimary_ajax/(:num)"] ="web/menus/primary/menuPrimary/menuPrimary_ajax/$1";
	$route["web/menus/primary/menuPrimaryView"]         ="web/menus/primary/menuPrimary/menuPrimaryView";        
	$route["web/menus/primary/menuPrimaryView/(:num)"]  ="web/menus/primary/menuPrimary/menuPrimaryView/$1"; 
	$route["web/menus/primary/menuPrimary_delete"]      ="web/menus/primary/menuPrimary/menuPrimary_delete";

	// Menu Secundario 
	$route["web/menus/secundary"]                      ="web/menus/secundary/menuSecundary/index";            
	$route["web/menus/secundary/children"]             ="web/menus/secundary/menuSecundary/children";            
	$route["web/menus/secundary/menuSecundary_ajax"]        ="web/menus/secundary/menuSecundary/menuSecundary_ajax";       
	$route["web/menus/secundary/menuSecundary_ajax/(:num)"] ="web/menus/secundary/menuSecundary/menuSecundary_ajax/$1";
	$route["web/menus/secundary/menuSecundaryView"]         ="web/menus/secundary/menuSecundary/menuSecundaryView";        
	$route["web/menus/secundary/menuSecundaryView/(:num)"]  ="web/menus/secundary/menuSecundary/menuSecundaryView/$1"; 
	$route["web/menus/secundary/menuSecundary_delete"]      ="web/menus/secundary/menuSecundary/menuSecundary_delete";

	// Menu Secundario 
	$route["web/menus/main"]                      ="web/menus/main/menuMain/index";            
	$route["web/menus/main/children"]             ="web/menus/main/menuMain/children";            
	$route["web/menus/main/menuMain_ajax"]        ="web/menus/main/menuMain/menuMain_ajax";       
	$route["web/menus/main/menuMain_ajax/(:num)"] ="web/menus/main/menuMain/menuMain_ajax/$1";
	$route["web/menus/main/menuMainView"]         ="web/menus/main/menuMain/menuMainView";        
	$route["web/menus/main/menuMainView/(:num)"]  ="web/menus/main/menuMain/menuMainView/$1"; 
	$route["web/menus/main/menuMain_delete"]      ="web/menus/main/menuMain/menuMain_delete";

		// Menu Secundario 
	$route["web/information"]                      ="web/information/information/index";            
	$route["web/information/children"]             ="web/information/information/children";            
	$route["web/information/information_ajax"]        ="web/information/information/information_ajax";       
	$route["web/information/information_ajax/(:num)"] ="web/information/information/information_ajax/$1";
	$route["web/information/informationView"]         ="web/information/information/informationView";        
	$route["web/information/informationView/(:num)"]  ="web/information/information/informationView/$1"; 
	$route["web/information/information_delete"]      ="web/information/information/information_delete";

// < / WEB web >
	