<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://tahir.codes/
 * @since      1.0.0
 *
 * @package    Idlwpstripe
 * @subpackage Idlwpstripe/public/partials
 */
?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Montserrat|Varela+Round&display=swap" rel="stylesheet">
<script src="<?php echo plugin_dir_url(dirname( __FILE__ ))?>js/stripe_custom.js"></script>
<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" name="idlusersingups" id="idlusersingups">
   <input type="hidden" name="action" value="create_strip_payment">
   <!-- Midd area -->
   <section id="intro">
      <div class="container">
         <div class="row">
            <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-xs-f12">
               <div class="tab-content" id="myTabContent">
                  <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                     <div class="tab-styling">
                           <div class="row">
                              <div class="form-group col-md-12">
                                 <label for="CardB">Your Card Info</label>
                                 <div class="id-cc-card">
                                    <div id="card-field"></div>
                                    <span id="card-errors"></span>
                                 </div>
                              </div>
                           </div>
                           <div class="text-center w-100 mt-30">
                              <button type="button" class="btn-web btn-purple" id="submit_form">Pay</button>
                            </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- Midd area End -->
</form>
<div style="height:30px; width:100%"></div>