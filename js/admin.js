function toggleMultiuserDetail(check) {
  if (check.checked) jQuery("#multiuser_details").show();
  else jQuery("#multiuser_details").hide();
}

function changePageStatus(page_id, status) {
  //jQuery.post("#", {"act":"update_page_status", "page_id":page_id, "status":status});
}
function changeCategoryStatus(category_id, status) {
  //jQuery.post("#", {"act":"update_category_status", "category_id":category_id, "status":status});
}

jQuery(document).ready(function(){
  var tabs = jQuery("#fotomoto_tabs").tabs();
  
  if (document.location.hash == "#users_div") {
  	tabs.tabs('select', "#users_div");
 	}
 	
 	if (document.location.hash == "#pages_div") {
  	tabs.tabs('select', "#pages_div");
 	}
 	
 	if (document.location.hash == "#categories_div") {
  	tabs.tabs('select', "#categories_div");
 	}
});