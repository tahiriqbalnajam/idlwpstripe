<?php

class Idlwpstripe_Public {

	private $plugin_name;
    private $version;
    private $option_name = 'idlwpstripe';
    public  $product_price;
    public  $currency;
    private $strip_publishable_key;
    private $strip_api_key;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->currency = get_option( $this->option_name . '_currency' );
        $this->product_price = get_option( $this->option_name . '_product_price' );
        $this->strip_publishable_key = get_option( $this->option_name . '_strip_publishable_key' );
        $this->strip_api_key = get_option( $this->option_name . '_strip_api_key' );

	}

	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/idlwpstripe-public.css', array(), $this->version, 'all' );

	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name.'stripe', 'https://js.stripe.com/v3/', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/idlwpstripe-public.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( $this->plugin_name.'_notify', plugin_dir_url( __FILE__ ) . 'js/notify.min.js.txt', array( 'jquery' ), $this->version, false );

	}

	public function loadstripeform() {
		include_once 'partials/stripe_form.php';
	}

	function json_response($status, $msg) {
        echo  json_encode([
            "status" => $status,
            "msg" => $msg
        ]);
        exit;
    }

    public function create_strip_payment() {
        try {
            \Stripe\Stripe::setApiKey($this->strip_api_key); 
            $customer = null;
            $firstname = $_POST['first_name'];
            $lastname = $_POST['last_name'];
            $email = $_POST['email'];
            $paymentid = $_POST['payment_method_id'];

            if (isset($_SESSION['customer_id'])) {
                // Customer already exists, update

                $customer = \Stripe\Customer::update(
                    $_SESSION['customer_id'],
                    [
                        'email' => $email,
                        'metadata' => [
                            'firstname' => $firstname,
                            'lastname' => $lastname,
                            'user_id' => $this->userid
                        ],
                    ]
                );
            }
            else {
                // Customer doesn't exist yet, create

                $customer = \Stripe\Customer::create([
                    'email' => $json_obj->email,
                    'metadata' => [
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'user_id' => $this->userid,
                    ],
                ]);
                $_SESSION['customer_id'] = $customer->id;
            }

            if ($customer === null) {
                $this->json_response("error","Error creating or updating customer");
            }

            $Total = $this->product_price;
            $ProductName = "Findmy Theory Test";
            // ====================================================================
            // Step 4: Create PaymentIntent and confirm
            // ====================================================================

            $metadata = [
                "first_name" => $firstname,
                "last_name" => $lastname,
                "product_name" => $ProductName,
            ];

            $intent = \Stripe\PaymentIntent::create([
                'payment_method' => $paymentid,
                'amount' => $Total,
                'currency' => 'GBP',
                'customer' => $customer,
                'metadata' => $metadata,
                'description' => $email,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);

            $_SESSION['payment_intent_id'] = $intent->id;
            
            if ($intent->status == 'requires_source_action' &&
                $intent->next_action->type == 'use_stripe_sdk') {
                # Tell the client to handle the action
                $this->json_response("requires_action", $intent->client_secret);
            } else if ($intent->status == 'succeeded') {
                # The payment didn’t need any additional actions and completed!
                # Handle post-payment fulfillment
                $this->json_response("success","true");
            } else {
                # Invalid status
                $this->json_response("error", "Invalid PaymentIntent status");
            }

        }
        catch (\Stripe\Exception\CardException $e) {
            $this->json_response("error", $e->getMessage());
        } catch (\Stripe\Exception\RateLimitException $e) {
            $this->json_response("error", $e->getMessage());
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $this->json_response("error", $e->getMessage());
        } catch (\Stripe\Exception\AuthenticationException $e) {
            $this->json_response("error", $e->getMessage());
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            $this->json_response("error", $e->getMessage());
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $this->json_response("error", $e->getMessage());
        } catch (Exception $e) {
            $this->json_response("error", $e->getMessage());
        }

    }


    public function confirmpayment() {
        $payment_intent_id = $_POST['payment_intent_id'];
        \Stripe\Stripe::setApiKey($this->strip_api_key); 
        $intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
        try {
            $intent->confirm();
        }
        catch (\Stripe\Error\InvalidRequest $err) {
            $this->json_response("error", $err->getMessage());
        }
        catch (\Stripe\Error\Card $err) {
            $this->json_response("error", $err->getMessage());
        }

        if ($intent->status == 'requires_source_action' &&
            $intent->next_action->type == 'use_stripe_sdk') {
                $this->json_response("requires_action", $intent->client_secret);
        } else if ($intent->status == 'succeeded') {
            $this->json_response("success","true");
        } else {
            $this->json_response("error", "Invalid PaymentIntent status");
        }
    }

    function stripe_payment()
    { 
        $success = 0; 
        try {
            // Set API key 
            \Stripe\Stripe::setApiKey($this->strip_api_key); 
            
            // Add customer to stripe 
            $customer = \Stripe\Customer::create(array( 
                'email' => $this->email, 
                'source'  => $this->token 
            )); 
            
            // Unique order ID 
            $orderID = strtoupper(str_replace('.','',uniqid('', true))); 
            $itemPrice = $this->product_price;
            
            // Charge a credit or a debit card 
            $charge = \Stripe\Charge::create(array( 
                'customer' => $customer->id, 
                'amount'   => $itemPrice, 
                'currency' => $this->currency, 
                'description' => $this->description, 
                'metadata' => array( 
                    'order_id' => $orderID 
                ) 
            )); 
            
            // Retrieve charge details 
            $chargeJson = $charge->jsonSerialize(); 
        
            // Check whether the charge is successful 
            if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1){ 
                // Order details  
                $transactionID = $chargeJson['balance_transaction']; 
                $paidAmount = $chargeJson['amount']; 
                $paidCurrency = $chargeJson['currency']; 
                $payment_status = $chargeJson['status']; 
                // If the order is successful 
                if($payment_status == 'succeeded'){ 
                    $ordStatus = 'success'; 
                    $statusMsg = 'Your Payment has been Successful!'; 
                    return true;
                }else{ 
                    return false;
                    $statusMsg = "Your Payment has Failed!"; 
                } 
            }else{ 
                return false;
                $statusMsg = "Transaction has been failed!"; 
            } 
            $success = 1;
        }   
        catch(Stripe_CardError $e) {
            $error1 = $e->getMessage();
          } catch (Stripe_InvalidRequestError $e) {
            // Invalid parameters were supplied to Stripe's API
            $error2 = $e->getMessage();
          } catch (Stripe_AuthenticationError $e) {
            // Authentication with Stripe's API failed
            $error3 = $e->getMessage();
          } catch (Stripe_ApiConnectionError $e) {
            // Network communication with Stripe failed
            $error4 = $e->getMessage();
          } catch (Stripe_Error $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            $error5 = $e->getMessage();
          } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
            $error6 = $e->getMessage();
          }   

          if ($success!=1)
            {
                echo json_encode(array('msg'=>'error','error' => "Error1=".$error1." Error2=".$error2." Error3=".$error3." Error4=".$error4." Error5=".$error5." Error6=".$error6));
                exit;
            }
    }

}
