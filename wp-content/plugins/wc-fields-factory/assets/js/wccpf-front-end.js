(function($) {	
	
	$(document).on( "submit", "form.cart", function(){		
		
		if( typeof( wccpf_opt.location ) != "undefined" && 
				wccpf_opt.location != "woocommerce_before_add_to_cart_button" && 
				wccpf_opt.location != "woocommerce_after_add_to_cart_button" ) {
			
			var me = $(this);
			
			me.find(".wccpf_fields_table").each(function(){
				$(this).remove();	
			});		
			
			$(".wccpf_fields_table").each(function(){
				var cloned = $(this).clone( true );
				cloned.css("display", "none");
				me.append( cloned );	
			});
			
		}
	});
	
	var wcffCloner = function(){
		this.initialize = function(){
			$( document ).on( "change", "input[name=quantity]", function() {
				var product_count = $(this).val();
				var fields_count = parseInt( $("#wccpf_fields_clone_count").val() );
				$("#wccpf_fields_clone_count").val( product_count );
				
				if( fields_count < product_count ) {
					for( var i = fields_count + 1; i <= product_count; i++ ) {
						var cloned = $('.wccpf-fields-group:first').clone( true );
						cloned.find("script").remove();				
						cloned.find("div.sp-replacer").remove();
						cloned.find("span.wccpf-fields-group-title-index").html( i );
						
						cloned.find(".wccpf-field").each(function(){
							var name_attr = $(this).attr("name");					
							if( name_attr.indexOf("[]") != -1 ) {
								var temp_name = name_attr.substring( 0, name_attr.lastIndexOf("_") );							
								name_attr = temp_name + "_" + i + "[]";						
							} else {
								name_attr = name_attr.slice( 0, -1 ) + i;
							}
							$(this).attr( "name", name_attr );
						});
						
						$("#wccpf-fields-container").append( cloned );		
						
						setTimeout( function(){ if( typeof( wccpf_init_color_pickers ) == 'function' ) { wccpf_init_color_pickers(); } }, 500 );
					}					
				} else {					
					$("div.wccpf-fields-group:eq("+ ( product_count - 1 ) +")").nextAll().remove();
				}
				
				if( $(this).val() == 1 ) {
		            $(".wccpf-fields-group-title-index").hide();
		        } else {
		            $(".wccpf-fields-group-title-index").show();
		        }
				
			});			
			/* Trigger to change event - fix for min product quantity */
			setTimeout( function(){ $( "input[name=quantity]" ).trigger("change"); }, 300 );
		};
	};
	
	var wcffValidator = function() {
		
		this.isValid = true;
		
		this.initialize = function(){
			var me = this;
			if( wccpf_opt.validation_type == "blur" ) {
				$( document ).on( "blur", ".wccpf-field", me, function(e) {				
					e.data.doValidate( $(this) );
				});
			}			
			$( document ).on( "submit", "form.cart", me, function(e) {
				var me = e.data; 
				e.data.isValid = true;
				$( ".wccpf-field" ).each(function(){
					me.doValidate( $(this) );
				});				
				return e.data.isValid;
			});
		};
		
		this.doValidate = function( field ) {
			if( field.attr("wccpf-type") != "radio" && field.attr("wccpf-type") != "checkbox" ) {				
				if( field.attr("wccpf-mandatory") == "yes" ) {					
					if( this.doPatterns( field.attr("wccpf-pattern"), field.val() ) ) {						
						field.next().hide();
					} else {						
						this.isValid = false;
						field.next().show();
					}
				}
			} else if( field.attr("wccpf-type") == "radio" ) {
				if( field.attr("wccpf-mandatory") == "yes" ) {	
					if( $("input[name="+ field.attr("name") +"]").is(':checked') ) {
						field.next().show();
					} else {
						this.isValid = false;
						field.next().hide();
					}	 
				}
			} else if( field.attr("wccpf-type") == "checkbox" ) {
				if( field.attr("wccpf-mandatory") == "yes" ) {	
					var values = $("input[name="+ field.attr("name") +"]").serializeArray();
					if( values.length == 0 ) {
						field.next().show();
					} else {
						this.isValid = false;
						field.next().hide();
					}
				}
			} else if( field.attr("wccpf-type") == "file" ) {
				if( field.attr("wccpf-mandatory") == "yes" ) {	
					if( field.val() == "" ) {
						field.next().show();
					} else {
						field.next().hide();
					}
				}
			}
		}
		
		this.doPatterns = function( patt, val ){
			var pattern = {
				mandatory	: /\S/, 
				number		: /^\d+\.\d{0,2}$/,
				email		: /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i,	      	
			};			    
		    return pattern[ patt ].test(val);	
		};
		
	};
	
	$(document).ready(function(){
		if( typeof( wccpf_opt.cloning ) !== "undefined" && wccpf_opt.cloning == "yes" ) {
			var wcff_cloner_obj = new wcffCloner();
			wcff_cloner_obj.initialize();
		}
		if( typeof( wccpf_opt.validation ) !== "undefined" && wccpf_opt.validation == "yes" ) {			
			var wcff_validator_obj = new wcffValidator();
			wcff_validator_obj.initialize();
		}
	});	
	
})(jQuery);