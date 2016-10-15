/**
 * @author  	: Saravana Kumar K
 * @author url 	: http://iamsark.com
 * @url			: http://sarkware.com/
 * @copyrights	: SARKWARE
 * @purpose 	: wcff Controller Object.
 */
(function($) {	

	var mask = null;
	var wcff = function() {
		/* used to holds next request's data (most likely to be transported to server) */
		this.request = null;
		/* used to holds last operation's response from server */
		this.response = null;
		/* to prevetn Ajax conflict. */
		this.ajaxFlaQ = true;
		/* Holds currently selected fields */
		this.activeField = null;
		
		this.initialize = function() {
			this.registerEvents();
		};
		
		this.registerEvents = function() {
			$(document).on( "click", "a.condition-add-rule", this, function(e) {
				e.data.addCondition( $(this) );
				e.preventDefault();
			});			
			$(document).on( "click", "a.condition-remove-rule", this, function(e) {
				e.data.removeRule( $(this) );
				e.preventDefault();
			});
			$(document).on( "click", "a.condition-add-group", this, function(e) {
				e.data.addConditionGroup( $(this) );
				e.preventDefault();
			});
			$(document).on( "click", "a.location-add-rule", this, function(e) {
				e.data.addLocation( $(this) );
				e.preventDefault();
			});			
			$(document).on( "click", "a.location-remove-rule", this, function(e) {
				e.data.removeRule( $(this) );
				e.preventDefault();
			});
			$(document).on( "click", "a.location-add-group", this, function(e) {
				e.data.addLocationGroup( $(this) );
				e.preventDefault();
			});
			
			$(document).on( "click", "a.wcff-meta-option-delete", this, function(e) {
				mask.doMask( $(this).parent().parent().parent().parent().parent() );
				e.data.prepareRequest( "DELETE", "wcff_fields", { field_key : $(this).attr("data-key") } );
				e.data.dock( "wcff_fields", $(this) );
				e.preventDefault();
			});
			$(document).on( "change", "#wcff-field-type-meta-type", this, function(e) {				
				e.data.prepareRequest( "GET", "wcff_meta_fields", { type : $(this).val() } );
				e.data.dock( "wcff_meta_fields", $(this) );
			});
			$(document).on( "click", ".wcff-field-label", this, function() {
				$(this).next().find("a.wcff-meta-option-edit").trigger("click");
			});		
			$(document).on( "click", ".wcff-meta-option-edit", this, function(e) {
				$(".wcff-meta-row").removeClass("active");
				$(this).parent().parent().parent().parent().parent().parent().addClass("active");
				mask.doMask( $(this).parent().parent().parent().parent().parent() );
				e.data.prepareRequest( "GET", "wcff_fields", { field_key : $(this).attr("data-key") } );
				e.data.dock( "wcff_fields", $(this) );
				
				e.preventDefault();
			});
			$(document).on( "keyup", "#wcff-field-type-meta-label", this, function(e){
				$( "#wcff-field-type-meta-name" ).val( e.data.sanitizeStr( $(this).val() ) );
				if( $(this).val() != "" ) {
					$(this).removeClass("wcff-form-invalid");
				}
			});	
			$(document).on( "change", ".wcff_condition_param", this, function(e) {
				e.data.prepareRequest( "GET", $(this).val(), "" );
				e.data.dock( $(this).val(), $(this) );
			});
			$(document).on( "change", ".wcff_location_param", this, function(e) {
				e.data.prepareRequest( "GET", $(this).val(), "" );
				e.data.dock( $(this).val(), $(this) );
			});			
			$(document).on( "click", "a.wcff-cancel-update-field-btn", this, function(e) {
				$(".wcff-add-new-field").html("+ Add Field");
				$("#wcff_fields_factory").attr( "action", "POST");
				$("#wcff-field-factory-footer").hide();
				
				$("#wcff-field-type-meta-label").val("");
				$("#wcff-field-type-meta-name").val("");				
				$("#wcff-field-type-meta-type").trigger("change");
				
				$(".wcff-meta-row").removeClass("active");
				e.preventDefault();
			});
			$(document).on( "click", "a.wcff-add-new-field", this, function(e) {
				e.data.onFieldSubmit( $(this) );
				e.preventDefault();
			});
			$(document).on( "submit", "form#post", this, function(e) {			
				return e.data.onPostSubmit( $(this));
			});
		};
		
		this.addCondition = function( target ) {
			var ruleTr = $( '<tr></tr>' );			
			ruleTr.html( target.parent().parent().parent().find("tr").last().html() );				
			if( target.parent().parent().parent().children().length == 1 ) {
				ruleTr.find("td.remove").html( '<a href="#" class="condition-remove-rule wcff-button-remove"></a>' );
			}			
			target.parent().parent().parent().append( ruleTr );		
			ruleTr.find( "select.wcff_condition_param" ).trigger( "change" );
		};
		
		this.addLocation = function( target ) {
			var locationTr = $( '<tr></tr>' );
			locationTr.html( target.parent().parent().parent().find("tr").last().html() );
			if( target.parent().parent().parent().children().length == 1 ) {
				locationTr.find("td.remove").html( '<a href="#" class="location-remove-rule wcff-button-remove"></a>' );
			}	
			target.parent().parent().parent().append( locationTr );			
			locationTr.find( "select.wcff_location_param" ).trigger( "change" );
		};
		
		this.removeRule = function( target ) {		
			var parentTable = target.parent().parent().parent().parent(),
			rows = parentTable.find( 'tr' );		
			if( rows.size() == 1 ) {
				parentTable.parent().remove();
			} else {
				target.parent().parent().remove();
			}
		}; 
		
		this.addConditionGroup = function( target ) {
			var groupDiv = $( 'div.wcff_logic_group:first' ).clone( true );
			var rulestr = groupDiv.find("tr");			
			if( rulestr.size() > 1 ) {
				var firstTr = groupDiv.find("tr:first").clone( true );
				groupDiv.find("tbody").html("").append( firstTr );				
			}
			groupDiv.find("h4").html( "or" );
			target.prev().before( groupDiv );			
			groupDiv.find("td.remove").html( '<a href="#" class="condition-remove-rule wcff-button-remove"></a>' );
			groupDiv.find( "select.wcff_condition_param" ).trigger( "change" );
		};
		
		this.addLocationGroup = function( target ) {
			var groupDiv = $( 'div.wcff_location_logic_group:first' ).clone( true );
			var rulestr = groupDiv.find("tr");			
			if( rulestr.size() > 1 ) {
				var firstTr = groupDiv.find("tr:first").clone( true );
				groupDiv.find("tbody").html("").append( firstTr );				
			}
			groupDiv.find("h4").html( "or" );
			target.prev().before( groupDiv );			
			groupDiv.find("td.remove").html( '<a href="#" class="location-remove-rule wcff-button-remove"></a>' );
			groupDiv.find( "select.wcff_condition_param" ).trigger( "change" );
		};
		
		this.renderSingleView = function( _target ) {
			/* Store meta key in to activeField */
			this.activeField["key"] = _target.attr( "data-key" );
			/* Scroll down to Field Factory Container */
			$('html,body').animate(
				{ scrollTop: $("#wcff_factory").offset().top - 50  },
		        'slow'
		    );
			/* Update fields with corresponding values */
			$("#wcff-field-type-meta-label").val( this.unEscapeQuote( this.activeField["label"] ) );
			$("#wcff-field-type-meta-name").val( this.unEscapeQuote( this.activeField["name"] ) );
			$("#wcff-field-type-meta-type").val( this.unEscapeQuote( this.activeField["type"] ) );
			
			var me = this;		
			$("#wcff-field-types-meta-body div.wcff-field-types-meta").each(function() {
				if( $(this).attr("data-param") == "choices" || $(this).attr("data-param") == "default_value"  || $(this).attr("data-param") == "palettes" ) {
					me.activeField[ $(this).attr("data-param") ] = me.activeField[ $(this).attr("data-param") ].replace( /;/g, "\n" );
				}			
				if( $(this).attr("data-type") == "check" ) {
					var choices = me.activeField[ $(this).attr("data-param") ];				
					for( var i = 0; i < choices.length; i++ ) {					
						$("input[name=wcff-field-type-meta-"+ $(this).attr("data-param") +"][value="+ choices[i] +"]" ).prop( 'checked', true );	
					}
				} else if( $(this).attr("data-type") == "radio" ) {
					$("input[name=wcff-field-type-meta-"+ $(this).attr("data-param") +"][value="+ me.activeField[ $(this).attr("data-param") ] +"]" ).prop( 'checked', true );				
				} else {
					$("#wcff-field-type-meta-"+$(this).attr("data-param")).val( me.unEscapeQuote( me.activeField[ $(this).attr("data-param") ] ) );	
				}
			});		
			
			/* Set Fields Factory mode to PUT */
			$(".wcff-add-new-field").html("Update");
			$("#wcff_fields_factory").attr("action", "PUT");
			$("#wcff-field-factory-footer").show();
			$("#wcff-field-factory-footer").find( "a.wcff-meta-option-delete" ).attr( "data-key", _target.attr( "data-key" ) );
		};
		
		this.onFieldSubmit = function( target ) {
			var me = this, 
			payload = {};
			payload.type = me.escapeQuote( $("#wcff-field-type-meta-type").val() );
			payload.label = me.escapeQuote( $("#wcff-field-type-meta-label").val() );
			payload.name = me.escapeQuote( $("#wcff-field-type-meta-name").val() );
			
			if( payload.label != "" ) {
				
				$("#wcff-field-types-meta-body div.wcff-field-types-meta").each(function() {				
					if( $(this).attr("data-type") == "check" ) {			
						payload[ $(this).attr("data-param") ] = $("input[name=wcff-field-type-meta-"+ $(this).attr("data-param") +"]:checked").map(function() {
						    return this.value;
						}).get();
					} else if( $(this).attr("data-type") == "radio" ) {
						payload[ $(this).attr("data-param") ] = me.escapeQuote( $("input[name=wcff-field-type-meta-"+ $(this).attr("data-param") +"]:checked" ).val() );			
					} else {				
						payload[ $(this).attr("data-param") ] = me.escapeQuote( $("#wcff-field-type-meta-"+ $(this).attr("data-param") ).val() );				
						if( $(this).attr("data-param") == "choices" || $(this).attr("data-param") == "default_value" || $(this).attr("data-param") == "palettes" ) {
							payload[ $(this).attr("data-param") ] = payload[ $(this).attr("data-param") ].replace( /\n/g, ";" );
						}
					}
				});	
				
				if( $("#wcff_fields_factory").attr("action") == "POST" ) {
					payload["order"] = $('.wcff-meta-row').length;
				} else if( $("#wcff_fields_factory").attr("action") == "PUT" ) {
					payload["key"] = this.activeField["key"];
					payload["order"] = $('input[name='+ this.activeField["key"] +'_order]').val();
				}
				mask.doMask( target );
				this.prepareRequest( $("#wcff_fields_factory").attr("action"), "wcff_fields", payload );
				this.dock( "wcff_fields", target );
				
			} else {
				$("#wcff-field-type-meta-label").addClass("wcff-form-invalid");
			}
		};
		
		this.onPostSubmit = function( _target ) {		
			var condition_rules_group = [];
			var location_rules_group = [];
			$(".wcff_logic_group").each(function() {
				var rules = [];
				$(this).find("table.wcff_rules_table tr").each(function() {
					rule = {};
					rule["context"] = $(this).find("select.wcff_condition_param").val();
					rule["logic"] = $(this).find("select.wcff_condition_operator").val();
					rule["endpoint"] = $(this).find("select.wcff_condition_value").val();
					rules.push( rule );
				});
				condition_rules_group.push( rules );
			});
			$(".wcff_location_logic_group").each(function() {
				var rules = [];
				$(this).find("table.wcff_location_rules_table tr").each(function() {
					rule = {};
					rule["context"] = $(this).find("select.wcff_location_param").val();
					rule["logic"] = $(this).find("select.wcff_location_operator").val();					
					if( $(this).find("select.wcff_location_param").val() != "location_product_data" ) {
						rule["endpoint"] = { 
							"context" : $(".wcff_location_metabox_context_value").val(),
							"priority": $(".wcff_location_metabox_priorities_value").val()
						}
					} else {
						rule["endpoint"] = $(this).find("select.wcff_location_product_data_value").val();
					}					
					rules.push( rule );
				});				
				location_rules_group.push( rules );
			});					
			$("#wcff_condition_rules").val( JSON.stringify( condition_rules_group ) );
			if( location_rules_group.length > 0 ) {
				$("#wcff_location_rules").val( JSON.stringify( location_rules_group ) );
			}
			return true;
		};	
				
		this.reloadHtml = function( _where ) {
			_where.html( this.response.payload );
		};
		
		/* convert string to url slug */
		this.sanitizeStr = function( str ) {
			if( str ) {
				return str.toLowerCase().replace(/[^\w ]+/g,'').replace(/ +/g,'_');
			}
			return str;
		};	 
		
		this.escapeQuote = function( str ) {	
			if( str ) {
				str = str.replace( /[']/g, '&#39;' );
				str = str.replace( /["]/g, '&#34;' );
			}			
			return str;
		};
		
		this.unEscapeQuote = function( str ) {
			if( str ) {
				str = str.replace( '&#39;', "'" );
				str = str.replace( '&#34;', '"' );
			}
			return str;
		};
		
		this.prepareRequest = function( _request, _context, _payload ) {
			this.request = {
				request 	: _request,
				context 	: _context,
				post 		: wcff_var.post_id,
				post_type 	: wcff_var.post_type,
				payload 	: _payload
			};
		};
		
		this.prepareResponse = function( _status, _msg, _data ) {
			this.response = {
				status : _status,
				message : _msg,
				payload : _data
			};
		};
		
		this.dock = function( _action, _target ) {		
			var me = this;
			/* see the ajax handler is free */
			if( !this.ajaxFlaQ ) {
				return;
			}		
			
			$.ajax({  
				type       : "POST",  
				data       : { action : "wcff_ajax", wcff_param : JSON.stringify(this.request)},  
				dataType   : "json",  
				url        : wcff_var.ajaxurl,  
				beforeSend : function(){  				
					/* enable the ajax lock - actually it disable the dock */
					me.ajaxFlaQ = false;				
				},  
				success    : function(data) {				
					/* disable the ajax lock */
					me.ajaxFlaQ = true;				
					me.prepareResponse( data.status, data.message, data.data );		               
	
					/* handle the response and route to appropriate target */
					if( me.response.status ) {
						me.responseHandler( _action, _target );
					} else {
						/* alert the user that some thing went wrong */
						//me.responseHandler( _action, _target );
					}				
				},  
				error      : function(jqXHR, textStatus, errorThrown) {                    
					/* disable the ajax lock */
					me.ajaxFlaQ = true;
				},
				complete   : function() {
					mask.doUnMask();
				}   
			});		
		};
		
		this.responseHandler = function( _action, _target ){		
			if( _action == "product" ||
				_action == "product_cat" ||
				_action == "product_tag" ||
				_action == "product_type" ) {
				this.reloadHtml( _target.parent().parent().find("td.condition_value_td") );
			} else if(  _action == "location_product_data" ||
						_action == "location_product" ||
						_action == "location_product_cat" ) {
				this.reloadHtml( _target.parent().parent().find("td.location_value_td") );
			} else if( _action == "wcff_meta_fields" ) {
				this.reloadHtml( $("#wcff-field-types-meta-body") );
			} else if( _action == "wcff_fields" ) {			
				if( this.request.request == "GET" ) {	
					this.activeField = JSON.parse( this.response.payload );				
					if( this.activeField["type"] == $("#wcff-field-type-meta-type").val() ) {
						this.renderSingleView( _target );
					} else {
						this.prepareRequest( "GET", "wcff_meta_fields", { type : this.activeField["type"] } );
						this.dock( "single", _target );
					}				
				} else {
					if(this.response.status ) {
						/* Set Fields Factory to POST mode, on successfull completeion of any operation */
						$("#wcff-empty-field-set").hide();
						$("#wcff-field-factory-footer").hide();
						$(".wcff-add-new-field").html("+ Add Field");
						$("#wcff_fields_factory").attr("action","POST");					
					}				
					if( this.request.request == "DELETE" ) {						
						if( $(".wcff-meta-row").length <= 1 ) {										
							$("#wcff-empty-field-set").show();
						} else {
							$("#wcff-empty-field-set").hide();
						}
					}								
					this.reloadHtml( $("#wcff-fields-set") );				
					$("#wcff-field-type-meta-label").val("");
					$("#wcff-field-type-meta-name").val("");				
					$("#wcff-field-type-meta-type").trigger("change");
				}
			} else if( _action == "single" ) {
				this.reloadHtml( $("#wcff-field-types-meta-body") );
				this.renderSingleView( _target );
			} 	
		};
	};
	
	/* Masking object ( used to mask any container whichever being refreshed ) */
	var wcffMask = function() {
		this.top = 0;
		this.left = 0;
		this.bottom = 0;
		this.right = 0;
		
		this.target = null;
		this.mask = null;
		
		this.getPosition = function( target ) {
			this.target = target;		
			
			var position = this.target.position();
			var offset = this.target.offset();
		
			this.top = offset.top;
			this.left = offset.left;
			this.bottom = $( window ).width() - position.left - this.target.width();
			this.right = $( window ).height() - position.right - this.target.height();
		};

		this.doMask = function( target ) {
			this.target = target;
			this.mask = $('<div class="wcff-dock-loader"></div>');						
			this.target.append( this.mask );

			this.mask.css("left", "0px");
			this.mask.css("top", "0px");
			this.mask.css("right", this.target.innerWidth()+"px");
			this.mask.css("bottom", this.target.innerHeight()+"px");
			this.mask.css("width", this.target.innerWidth()+"px");
			this.mask.css("height", this.target.innerHeight()+"px");
		};

		this.doUnMask = function() {
			if( this.mask ) {
				this.mask.remove();
			}			
		};
	};
		
	$(document).ready( function() {
		$('#wcff-fields-set').sortable({
			update : function(){
				var order = 0;
				$('.wcff-meta-row').each(function(){
					$(this).find("input.wcff-field-order-index").val(order);
					order++;
				});
			}
		});
	});
	
	mask = new wcffMask();
	var wcffObj = new wcff();
	wcffObj.initialize();
	
})(jQuery);