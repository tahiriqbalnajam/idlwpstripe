(function( $ ) {
	'use strict';
	$(function(){
		var elementStyles = {
			base: {
			  color: '#32325D',
			  fontWeight: 500,
			  fontFamily: 'Source Code Pro, Consolas, Menlo, monospace',
			  fontSize: '16px',
			  fontSmoothing: 'antialiased',
		
			  '::placeholder': {
				color: '#CFD7DF',
			  },
			  ':-webkit-autofill': {
				color: '#e39f48',
			  },
			},
			invalid: {
			  color: '#E25950',
		
			  '::placeholder': {
				color: '#FFCCA5',
			  },
			},
		  };
		  var elementClasses = {
			focus: 'focused',
			empty: 'empty',
			invalid: 'invalid',
		  };
		var stripe = Stripe(idl_ajax.stipe_key);
		var elements = stripe.elements({
			fonts: [
			  {
				cssSrc: 'https://fonts.googleapis.com/css?family=Source+Code+Pro',
			  },
			],
			locale: 'auto'
		  });
        var card = elements.create("card", {
			hidePostalCode: true,
			style: elementStyles,
			classes: elementClasses,
		  });
		  var inputs = document.querySelectorAll('.cell.example.example2 .input');
		  Array.prototype.forEach.call(inputs, function(input) {
			input.addEventListener('focus', function() {
			  input.classList.add('focused');
			});
			input.addEventListener('blur', function() {
			  input.classList.remove('focused');
			});
			input.addEventListener('keyup', function() {
			  if (input.value.length === 0) {
				input.classList.add('empty');
			  } else {
				input.classList.remove('empty');
			  }
			});
		  });
        if($("#card-field").length)
           card.mount("#card-field");
        let errors = document.getElementById('card-errors');
		var $form = $("#idlusersingups");
		var btnsbm = $("#submit_form");


		$("#submit_form").click(function(){
			event.preventDefault(); 
			btnsbm.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Validating..');
			stripe.createPaymentMethod('card', card).then(function(result) {
				if (result.error) {
					btnsbm.prop('disabled', false).html('Pay');
					errors.textContent = result.error.message;
					return;
				}
				errors.textContent = "";
				$.ajax({
					url: idl_ajax.ajax_url, 
					data: $("#idlusersingups").serialize()+"&payment_method_id="+result.paymentMethod.id,
					method:'post',
					dataType: 'json',
					success: function(response){
						handleServerResponse(response);							
					}
				});
			});
		})

		// Callback to handle the response from stripe
		function handleServerResponse(response) {
			if (response.status == "error") {
				$.notify(response.msg , {autoHide: false,className: "error",style: 'bootstrap'});
				btnsbm.prop('disabled', false).html('Pay');
				//$("#payment-errors").textContent = response.error.message;
			} else if (response.status == "requires_action") {
				//document.getElementById("payment-errors").textContent = "Requires action";
				$.notify("Requires action" , {autoHide: true,className: "warn",style: 'bootstrap'});
				btnsbm.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Authenticating..');
				handleAction(response);
			} else if(response.status == "success") {
				$.notify("Payment Done" , {autoHide: true,className: "sucess",style: 'bootstrap'});
				btnsbm.prop('disabled', false).html('Payment Done');
			}
		}

		function handleAction(response) {
			btnsbm.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Payment..');
			stripe.handleCardAction(
				response.msg
			).then(function(result) {
				if (result.error) {
					$.notify("Payment Done" , {autoHide: true,className: "sucess",style: 'bootstrap'});
					btnsbm.prop('disabled', false).html('Pay');
				} else {
					$.ajax({
						url: idl_ajax.ajax_url, 
						data: "action=confirmpayment&payment_intent_id="+result.paymentIntent.id,
						method:'post',
						dataType: 'json',
						success: function(res){
							if(res.status == "success")
								handleServerResponse(res);	
							else{
								btnsbm.prop('disabled', false).html('Pay');
								$.notify(res.mgs , {autoHide: false,className: "error",style: 'bootstrap'});
							}					
								
						}
					});
				}
			});
		}
	})
})( jQuery );