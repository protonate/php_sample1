jQuery(document).ready(function($){
	
	$(function() {
		// development use: remove flavorfinder cookie
		//$.cookie("flavorfinder", null);
			   
		// setup sliders
		// check for persisted filters values in cookie
		var ff_cookie = eval($.cookie("flavorfinder"));
		if(ff_cookie != null){
			$("#criteria1").text((ff_cookie.meaty == "%") ? 0 : (Math.ceil(ff_cookie.meaty) / 5.0 * 100) );
			$("#criteria2").text((ff_cookie.salty == "%") ? 0 : (Math.ceil(ff_cookie.salty) / 5.0 * 100) );
			$("#criteria3").text((ff_cookie.umami == "%") ? 0 : (Math.ceil(ff_cookie.umami) / 5.0 * 100) );
			$("#criteria4").text((ff_cookie.roasted == "%") ? 0 : (Math.ceil(ff_cookie.roasted) / 5.0 * 100) );
			$("#criteria5").text((ff_cookie.brothy == "%") ? 0 : (Math.ceil(ff_cookie.brothy) / 5.0 * 100) );
			
			if(ff_cookie.halal == "1"){
				$(".ff-cert #cert_halal").attr("ff-filter", "1").css("background-image", "url(/wp-content/themes/savoury/images/checkbox-checked.png)");
			}
			if(ff_cookie.kosher == "1"){
				$(".ff-cert #cert_kosher").attr("ff-filter", "1").css("background-image", "url(/wp-content/themes/savoury/images/checkbox-checked.png)");
			}
			if(ff_cookie.natural == "1"){
				$(".ff-cert #cert_natural").attr("ff-filter", "1").css("background-image", "url(/wp-content/themes/savoury/images/checkbox-checked.png)");
			}
			if(ff_cookie.organic == "1"){
				$(".ff-cert #cert_organic").attr("ff-filter", "1").css("background-image", "url(/wp-content/themes/savoury/images/checkbox-checked.png)");
			}
			if(ff_cookie.vegetarian == "1"){
				$(".ff-cert #cert_vegetarian").attr("ff-filter", "1").css("background-image", "url(/wp-content/themes/savoury/images/checkbox-checked.png)");
			}
		}
		$("#ff-slider-container > span").each(function() {
			// read initial values from markup and remove that
			var value = parseInt($(this).text());
			$(this).empty().slider({
				value: value,
				range: "min",
				animate: 250,
				orientation: "vertical",
				stop: function(event, ui){
					call_update_results($(this));}		
			});
			$(this).addClass("slider-style");
			$(this).children(".ui-slider-range").addClass("slider-style");
			$(this).children("div").append("<div class='slider-footer'></div>");
			

		});	
		$("#ff-slider-container #criteria1 .ui-slider-range").css({"background-color" : "#97c54a", "background-image" : "none"});
		$("#ff-slider-container #criteria2 .ui-slider-range").css({"background-color" : "#00937f", "background-image" : "none"});
		$("#ff-slider-container #criteria3 .ui-slider-range").css({"background-color" : "#f58e32", "background-image" : "none"});
		$("#ff-slider-container #criteria4 .ui-slider-range").css({"background-color" : "#569fd3", "background-image" : "none"});
		$("#ff-slider-container #criteria5 .ui-slider-range").css({"background-color" : "#d6203f", "background-image" : "none"});
		call_update_results();		
	});
	
	$(".ff-cert-checkbox").toggle(function(){
			$(this).css("background-image", "url(/wp-content/themes/savoury/images/checkbox-checked.png)");
			$(this).attr("ff-filter", "1");
			call_update_results();
		},function() {
			$(this).css("background-image", "url(/wp-content/themes/savoury/images/checkbox.png)");
			$(this).attr("ff-filter", "%");
			call_update_results();
	});
		
	$(".ff-cert-checkbox").hover(function(){
			$(this).addClass("cursor-pointer");
		},function(){
			$(this).removeClass("cursor-pointer");		
	});

	function call_update_results(objSlider){
		// check if sliderstop event passed a jquery slider object, if so, snap to zero if value less then 16
		if(objSlider != null){
			if(objSlider.slider("option", "value") < 12) {
				objSlider.slider("option", "value", 0);
			}
		}
		var options = {};
		// delay update results to allow for slider animation to complete
		setTimeout(update_results, 250);
		}
	function update_results(sliderObj){
		$("#results-status").removeAttr('style').hide().fadeIn();		
		var cert_halal = $("#cert_halal").attr("ff-filter");
		var cert_kosher = $("#cert_kosher").attr("ff-filter");
		var cert_natural = $("#cert_natural").attr("ff-filter");
		var cert_organic = $("#cert_organic").attr("ff-filter");
		var cert_vegetarian = $("#cert_vegetarian").attr("ff-filter");
		var meaty = get_criteria(parseInt($("#criteria1 .ui-slider-range").css("height")));
		var salty = get_criteria(parseInt($("#criteria2 .ui-slider-range").css("height")));
		var umami = get_criteria(parseInt($("#criteria3 .ui-slider-range").css("height")));
		var roasted = get_criteria(parseInt($("#criteria4 .ui-slider-range").css("height")));
		var brothy = get_criteria(parseInt($("#criteria5 .ui-slider-range").css("height")));
		
		//		persist current filters in a cookie
		$.cookie("flavorfinder",'({halal:"' + cert_halal + '",kosher:"' + cert_kosher + '",natural:"' + cert_natural + '",organic:"' + cert_organic + '",vegetarian:"' + cert_vegetarian + '",meaty:"' + meaty + '",salty:"' + salty + '",umami:"' + umami + '",roasted:"' + roasted + '",brothy:"' + brothy + '"})', {expires: 14});
		// development use: remove flavorfinder cookie
		//$.cookie("flavorfinder", null);

		


		$.post(MyAjax.ajaxurl,
			   {
				   action: "ff_update_results",
					ff_request: "true",
					halal: cert_halal,
					kosher: cert_kosher,
					natural: cert_natural,
					organic: cert_organic,
					vegetarian: cert_vegetarian,
					meaty: meaty,
					salty: salty,
					umami: umami,
					roasted: roasted,
					brothy: brothy					
			   },
			   function(data) {
				  // alert(data);
				  var options = {};
				  $("#results-status").hide();
				  $("#ff-results-panel").empty().append(data);
			   }				   
		  );
		
	}

	function get_criteria(val){
		var result;
		switch(true){
			case (val < 16):
				result = "%";
				break;
			case ((val >=17) && (val <= 23)):
				result = "0.5";
				break;
			case ((val >=24) && (val <= 34)):
				result = "1.0";
				break;
			case ((val >=35) && (val <= 43)):
				result = "1.5";
				break;
			case ((val >=44) && (val <= 53)):
				result = "2.0";
				break;
			case ((val >=54) && (val <= 63)):
				result = "2.5";
				break;
			case ((val >=64) && (val <= 73)):
				result = "3.0";
				break;
			case ((val >=74) && (val <= 83)):
				result = "3.5";
				break;
			case ((val >=84) && (val <= 93)):
				result = "4.0";
				break;
			case ((val >=94) && (val <= 103)):
				result = "4.5";
				break;
			case ((val >=104) && (val <= 113)):
				result = "5.0";
				break;
			}
		return(result);
	}
	
	$(".save-anchor, .cancel-anchor").hide();
	
	$(".edit-anchor").click(function(){	
		// alert('edit clicked');	
		$(".edit-anchor").hide();		
		$(this).parent().parent().attr("id", "edit-row").css("background-color","#f9f890");
		$("#edit-row .save-anchor, #edit-row .cancel-anchor").show();
		$("#edit-row input").not("#productID").not("#productName").removeAttr("disabled");
		$("#edit-row #similarIDs").focus(function(){
			// alert('similars');
		});
	});
	
	$(".cancel-anchor").click(function(){
		$("#edit-row .cancel-anchor, #edit-row .save-anchor").hide();
		$("#edit-row input").not("#productID").not("#productName").attr("disabled", "disabled");
		$("#edit-row").css("background-color", "").removeAttr("id");
		$(".edit-anchor").show();
		location.reload();
	});
	
	$(".save-anchor").click(function(){
		var url = $("#ff-form #formscript").val();
		var productID = $("#edit-row #productID").val();
		var vegan = $("#edit-row #vegan").is(':checked');
		var kosher = $("#edit-row #kosher").is(':checked');
		var halal = $("#edit-row #halal").is(':checked');
		var natural = $("#edit-row #natural").is(':checked');
		var organic = $("#edit-row #organic").is(':checked');
		var similarIDs = $("#edit-row #similarIDs").val();
		var meaty = $("#edit-row #meaty").val();
		var salty = $("#edit-row #salty").val();
		var umami = $("#edit-row #umami").val();
		var roasted = $("#edit-row #roasted").val();
		var brothy = $("#edit-row #brothy").val();
		var test = "vegan: " + vegan + "\r\n";
		test = test + "kosher: " + kosher + "\r\n";
		test = test + "halal: " + halal + "\r\n";
		test = test + "natural: " + natural + "\r\n";
		// alert(test);
		
		function validate_similars(){
			var valid = true;
			var array_similars = similarIDs.split(',');			
			for(var i = 0; i < array_similars.length; i++){
				this_similar = array_similars[i].replace(/^\s\s*/, '').replace(/\s\s*$/, '');
				if(this_similar.length != 4) { valid = false; }
				array_ints = this_similar.split();
				for(var j = 0; j < array_ints.length; j++){
					if(isNaN(array_ints[j])) { valid = false; }
				}
			}
			return valid;
		}
		
		if(isNaN(meaty)) { alert('meaty value is not a number'); }
		else if((meaty < 0) || (meaty > 5)) { alert('meaty value is not between 0 and 5'); }
		
		else if(isNaN(salty)) { alert('salty value is not a number'); }
		else if((salty < 0) || (salty > 5)) { alert('salty value is not between 0 and 5'); }
		
		else if(isNaN(umami)) { alert('umami value is not a number'); }
		else if((umami < 0) || (umami > 5)) { alert('umami value is not between 0 and 5'); }
		
		else if(isNaN(roasted)) { alert('roasted value is not a number'); }
		else if((roasted < 0) || (roasted > 5)) { alert('roasted value is not between 0 and 5'); }
		
		else if(isNaN(brothy)) { alert('brothy value is not a number'); }
		else if((brothy < 0) || (brothy > 5)) { alert('brothy value is not between 0 and 5'); }
		
		else if(!validate_similars()) { alert('similar flavors must be four digits and comma separated')}
		
		else {
			$.post(
				url,
				{
					productID: productID,
					vegan: vegan,
					kosher: kosher,
					halal: halal,
					natural: natural,
					organic: organic,
					similarIDs: similarIDs,
					meaty: meaty,
					salty: salty,
					umami: umami,
					roasted: roasted,
					brothy: brothy
				},
				function(data){
					if(data == "ok"){
						// alert("record saved");
						location.reload();					
					}
					else if(data == "ok discards"){
						alert("record saved, invalid similar flavor IDs discarded");
						location.reload();
					}
					else {
						alert("an error occured, please note the following error message:\r\n" + data);
						location.reload();
					}
				});	
			$("#edit-row .cancel-anchor, #edit-row .save-anchor").hide();
			$("#edit-row input").not("#productID").not("#productName").attr("disabled", "disabled");
			$("#edit-row").css("background-color", "").removeAttr("id");
			$(".edit-anchor").show();
								
		}
	});
});

