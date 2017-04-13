<?php
  /*adding Menu and submenu in admin*/
    function pepocampaigns_integration_admin_menu()
      {
        add_options_page(__('pepocampaigns-registration', 'pepocampaigns_table'), __('Pepocampaigns wordpress woocommerce', 'ac-pepocampaigns_table'), 'activate_plugins', 'pepocampaigns', 'create_api_form');
        }
        
        add_action('admin_menu', 'pepocampaigns_integration_admin_menu');

        /*adding Menu and submenu in admin end */
        function create_api_form()
        {
          if(isset($_POST['submit'])):
          	if($_POST['apikey']==''||$_POST['listid']==''||$_POST['secretkey']==''):
          		$errormessage='Please fill all the required field';
          	else:
            $apikey=$_POST['apikey'];
            $secretkey=$_POST['secretkey'];
            $listid=$_POST['listid'];
             update_option( 'pepocampaigns_apikey', $apikey);
            update_option( 'pepocampaigns_secretkey', $secretkey);
             update_option( 'pepocampaigns_listid', $listid);
             $message="option opdated sucessfully";
            endif;
             endif;
          ?>
          <?php 
          $integrationkey=get_option( 'pepocampaigns_apikey');
          $integrationsecretkey=get_option( 'pepocampaigns_secretkey');
          $integration_listid=get_option( 'pepocampaigns_listid');

        ?>
        <?php if(isset($message)):?>
        <div id="message" class="updated notice notice-success is-dismissible"><p><?php echo $message;?>.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>


        <?php endif;?>
        <?php if(isset($errormessage)):?>
        <div id="message" class="updated notice notice-success is-dismissible"><p style="color:red"><?php echo $errormessage;?>.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
        <?php endif;?>
        <form method="post">

         <table class="form-table">

        <tbody>
         <tr><td colspan="2"><h2>Api Credential</h2></td>
</tr>
       
          <tr class="form-field form-required">

          <th scope="row"><label for="apikey">Pepocampaigns apikey<span class="description">(required)</span></label></th>

          <td><input type="text" id="apikey" required="" name="apikey" value="<?php echo $integrationkey;?>"></td>

          </tr>
           <tr class="form-field form-required">

          <th scope="row"><label for="secretkey">Pepocampaigns secretkey<span class="description">(required)</span></label></th>

          <td><input type="text" id="secretkey" required="" name="secretkey" value="<?php echo $integrationsecretkey;?>"></td>

          </tr>

          <tr class="form-field form-required">

        <th scope="row"><label for="listid">Pepocampaigns list id<span class="description">(required)</span></label></th>

        <td><input type="text" id="listid" required="" name="listid" value="<?php echo $integration_listid;?>"></td>

          </tr>

          </tbody>

          </table>
         <p class="submit" style="float: right; padding-right: 50px;"><input type="submit" name="submit" id="submit" class="button button-primary" value="SUBMIT "></p>
       </form>
         <?php
       }
       ?>