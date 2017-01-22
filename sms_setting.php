<?php
function edugorilla_sms_setting()
{
?>
    <div class="wrap">
        <h1>Ghupshup SMS Setting</h1>
            <?php
                $sms_setting_form = $_POST['sms_setting_form'];
                if ($sms_setting_form1 == "self") {
                    $errors = array();
                    $ghupshup_username = $_POST['ghupshup_username'];
                    $ghupshup_password = $_POST['ghupshup_password'];
                    $ghupshup_message = $_POST['ghupshup_message'];
                
                    if (empty($ghupshup_username)) $errors['ghupshup_username'] = "Empty";
                    if (empty($ghupshup_password)) $errors['ghupshup_password'] = "Empty";
                    if (empty($ghupshup_message)) $errors['ghupshup_message'] = "Empty";
            
                    if (empty($errors)) {
                        $edugorilla_sms_setting = array(
														'username' => stripslashes($ghupshup_username),
														'password' => stripslashes($ghupshup_password),
														'message' => stripslashes($ghupshup_message)
														);
						
						update_option("smsapi", $edugorilla_sms_setting);
						$success = "Ghupshup Settings Saved Successfully.";
						$sms_setting_options = get_option('smsapi');
						
						$ghupshup_username = stripslashes($sms_setting_options['username']);
						$ghupshup_password = stripslashes($sms_setting_options['password']);
						$ghupshup_message = stripslashes($sms_setting_options['message']);
                    }
                } else {
						$sms_setting_options = get_option('smsapi');
						
						$ghupshup_username = stripslashes($sms_setting_options['username']);
						$ghupshup_password = stripslashes($sms_setting_options['password']);
						$ghupshup_message = stripslashes($sms_setting_options['message']);
            
                }
     
                if ($success) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success; ?></p>
                    </div>
                    <?php
                }
            ?>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th>Username <sup><font color="red">*</font></sup></th>
                            <td>
                                <input name="ghupshup_username" value="<?php echo stripslashes($ghupshup_username); ?>"
                                       placeholder="Type Ghupshup username here...">
                                <font color="red"><?php echo $errors['ghupshup_username']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th>Password <sup><font color="red">*</font></sup></th>
                            <td>
                                <input name="ghupshup_password" value="<?php echo stripslashes($ghupshup_password); ?>"
                                       placeholder="Type Ghupshup Password here...">
                                <font color="red"><?php echo $errors['ghupshup_password']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th>Message<sup><font color="red">*</font></sup></th>
                            <td>
                               <?php
									$content = $ghupshup_message;
									$editor_id = 'ghupshup_message';

									wp_editor( $content, $editor_id );
								?>
                                <font color="red"><?php echo $errors['ghupshup_message']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="sms_setting_form" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
    </div>
<?php
}
?>
