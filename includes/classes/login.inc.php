<?
/***************************************************************************
 *                                login.inc.php
 *                            -------------------
 *   begin                : Friday, Mar 28, 2008
 *   copyright            : (C) 2008 J. David Baker
 *   email                : me@jdavidbaker.com
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

define(kOptionNoEmail, 0);
define(kOptionAllEmail, 1);
define(kOptionMyEmail, 2);
define(kOptionMyEmail1Away, 3);
define(kOptionMyEmail2Away, 4);
define(kOptionMyEmail3Away, 5);
define(kOptionMyEmail4Away, 6);
define(kOptionMyEmail5Away, 7);
define(kOptionNoSMS, 0);
define(kOptionAllSMS, 1);
define(kOptionMySMS, 2);

class login {
  function login() {
    global $settings;
    // Check for stored login info
    if ($_COOKIE['fof_draft_login_team_name']) {
      $_SESSION['fof_draft_login_team_name'] = $_COOKIE['fof_draft_login_team_name'];
      $_SESSION['fof_draft_login_team_password'] = $_COOKIE['fof_draft_login_team_password'];
    }
    $statement = "select * from team where team_name like '".mysql_real_escape_string($_SESSION['fof_draft_login_team_name'])."' and
team_password = '".mysql_real_escape_string($_SESSION['fof_draft_login_team_password'])."'";
    $this->data = mysql_fetch_array(mysql_query($statement));
    if (!$this->data['team_id'] && kRedirect) {
      if ($_SESSION['fof_draft_login_team_name']) {
	$_SESSION['message'] = "That login is not valid.";
	unset($_SESSION['fof_draft_login_team_name']);
	unset($_SESSION['fof_draft_login_team_password']);
	setcookie("fof_draft_login_team_name", "", time() - 3600);
	setcookie("fof_draft_login_team_password", "", time() - 3600);
	header("Location: selections.php");
	exit;
      }
    }
    // Update the chat time stamp for this user
    $statement = "update team set team_chat_time = '".date("Y-m-d H:i:s")."' where team_id = '".$this->team_id()."'";
    mysql_query($statement);
    if ( $settings->get_value(kSettingChatType)==1 ){
        $statement = "select * from last_update";
        $array = mysql_fetch_array(mysql_query($statement));
        $this->data['latest_message']=$array['latest_message'];
        if (!$_SESSION['latest_message']) {
              $_SESSION['latest_message'] = $array['latest_message'];
        }
    }
  }

  function team_id() {
    return $this->data['team_id'];
  }

  function team_name() {
    return $this->data['team_name'];
  }

  function team_owner() {
    return $this->data['team_owner'];
  }

  function team_is_draft_admin() {
    return $this->data['draft_admin'];
  }

  function latest_message() {
    return $this->data['latest_message'];
  }
  function set_latest_message() {
    $statement = "select * from last_update";
    $array = mysql_fetch_array(mysql_query($statement));
    $this->data['latest_message']=$array['latest_message'];
  }
  function is_admin() {
    if ($this->data['team_id'] == kAdminUser || $this->data['draft_admin']==1 ) {
      return true;
    } else {
      return false;
    }
  }
  function is_site_admin() {
    if ($this->data['team_id'] == kAdminUser ) {
      return true;
    } else {
      return false;
    }
  }
  function pick_method() {
    return $this->data['pick_method_id'];
  }

  function draw_options() {
    global $settings;
    if ($this->is_site_admin()) {
      header("Location: selections.php");
      exit;
    }
    $html .= '
<h3>GM Options</h3>';
      $html .= '
<p>Set the options for your account.  Enter your e-mail address if you would like to have each pick
e-mailed to you as the selections are made, leave it blank to not receive an e-mail.  Enter your phone number
(US, 10 digits no dashes, no leading 1) and select a carrier to have each pick SMS text messaged to your phone.  Note: Standard
text messaging data rates apply and are up to the user to manage.  We are NOT responsible for overages.<p>';
      if (!$settings->get_value(kSettingSendMails) || $settings->get_value(kSettingSendMails)==kEmailOffAdminAll ) {
	$html .= ' <b>NOTE:</b> The admin has disabled the e-mail and SMS notification.';
      }
      $html .= '
<p>If you turn "Auto pick" off, the system will require you to manually make your draft picks.
When you are on the clock, the list under the "Priority" tab will have a "Select Player" link
to choose that player.  If "Auto pick" is on (the default), the system will select the highest-priority
player in your list when you are on the clock..</p>
<p>By default, when you select a player, all other players of the same position in your queue have their
priority reset to zero.  This will prevent you from accidentally selecting two players of the same position
unintentionally.  If you want to turn this feature off, uncheck the "Zero out priority of players with same
position when selecting a player" option.';
    $html .= '
<form method="post" action="options_set.php">
  <table class="data">
    <tr>
      <td align="right" class="light" width="40%">GM Name:</td>
      <td class="light"><input type="text" name="team_owner" value="'.$this->data['team_owner'].'"></td>
    </tr>
    <tr>
      <td align="right" class="light" width="40%">Profile Link:</td>
      <td class="light"><input type="text" name="team_user_link" value="'.$this->data['team_user_link'].'"></td>
    </tr>
    <tr>
      <td align="right" class="light" width="40%">E-mail:</td>
      <td class="light"><input type="text" name="team_email" value="'.$this->data['team_email'].'"></td>
    </tr>';
    if (!$this->is_site_admin()) {
      switch($this->data['team_email_prefs']) {
      case kOptionNoEmail:
	$no_email = ' selected';
	break;
      case kOptionAllEmail:
	$all_email = ' selected';
	break;
      case kOptionMyEmail:
	$me_email = ' selected';
	break;
      case kOptionMyEmail1Away:
	$me_email1 = ' selected';
	break;
      case kOptionMyEmail2Away:
	$me_email2 = ' selected';
	break;
      case kOptionMyEmail3Away:
	$me_email3 = ' selected';
	break;
      case kOptionMyEmail4Away:
	$me_email4 = ' selected';
	break;
      case kOptionMyEmail5Away:
	$me_email5 = ' selected';
	break;
      }
      $html .= '
    <tr>
      <td align="right" class="light" width="40%">E-mail options:</td>
      <td class="light">
        <select name="team_email_prefs"';
      if ($settings->get_value(kSettingSendMails)==kEmailOff )
          $html.=' disabled';
      $html.='>
          <option value="'.kOptionNoEmail.'"'.$no_email.'>No E-mail</option>
          <option value="'.kOptionAllEmail.'"'.$all_email.'>All Picks</option>
          <option value="'.kOptionMyEmail.'"'.$me_email.'>When I\'m on the clock</option>';
//          <option value="'.kOptionMyEmail1Away.'"'.$me_email1.'>When I\'m on the clock & 1 pick away</option>
//          <option value="'.kOptionMyEmail2Away.'"'.$me_email2.'>When I\'m on the clock & 2 picks away</option>
//          <option value="'.kOptionMyEmail3Away.'"'.$me_email3.'>When I\'m on the clock & 3 picks away</option>
//          <option value="'.kOptionMyEmail4Away.'"'.$me_email4.'>When I\'m on the clock & 4 picks away</option>
//          <option value="'.kOptionMyEmail5Away.'"'.$me_email5.'>When I\'m on the clock & 5 picks away</option>
      $html.='
        </select>
      </td>
    </tr>';
      $html .= '
    <tr>
	<td class="light" align="right">Phone Number (10 digit, no dashes, no leading 1):</td>
     <td class="light"><input type="text" name="team_phone" value="'.$this->data['team_phone'].'"';
      if ($settings->get_value(kSettingSendMails)==kEmailOff || $settings->get_value(kSettingSendMails)==kEmailOffAdminAll )
          $html.=' disabled';
      $html.='></td>
    </tr>
	<tr>
	<td class="light" align="right">Phone Carrier:</td>
	<td class="light"><select name="team_carrier"';
      if ($settings->get_value(kSettingSendMails)==kEmailOff || $settings->get_value(kSettingSendMails)==kEmailOffAdminAll )
          $html.=' disabled';
      $html.='>';
	if ( $this->data['team_carrier']=="@txt.att.net" ){
		$att = ' selected';
	} else if ($this->data['team_carrier']=="@vtext.com" ){
		$verizon = ' selected';
	} else if ($this->data['team_carrier']=="@messaging.sprintpcs.com" ){
		$sprint = ' selected';
	} else if ($this->data['team_carrier']=="@page.nextel.com" ){
		$nextel = ' selected';
	} else if ($this->data['team_carrier']=="@tmomail.net" ){
		$tmobile = ' selected';
	} else if ($this->data['team_carrier']=="@email.uscc.net" ){
		$uscellular = ' selected';
	}
	$html .= '
	  <option value="@txt.att.net"'.$att.'>AT&T Wireless</option>
	  <option value="@page.nextel.com"'.$nextel.'>Nextel (Sprint)</option>
	  <option value="@messaging.sprintpcs.com"'.$sprint.'>Sprint (PCS)</option>
	  <option value="@tmomail.net"'.$tmobile.'>TMobile</option>
	  <option value="@email.uscc.net"'.$uscellular.'>US Cellular</option>
	  <option value="@vtext.com"'.$verizon.'>Verizon Wireless</option>
	</select></td>
    </tr>';
  if (!$this->is_site_admin()) {
      switch($this->data['team_sms_setting']) {
      case kOptionNoSMS:
	$no_sms = ' selected';
	break;
      case kOptionAllSMS:
	$all_sms = ' selected';
	break;
      case kOptionMySMS:
	$me_sms = ' selected';
	break;
	}
	}
	$html.='
    <tr>
      <td align="right" class="light" width="40%">SMS Text Message Options:</td>
      <td class="light">
        <select name="team_sms_setting"';
        if ($settings->get_value(kSettingSendMails)==kEmailOff || $settings->get_value(kSettingSendMails)==kEmailOffAdminAll )
          $html.=' disabled';
      $html.='>
          <option value="'.kOptionNoSMS.'"'.$no_sms.'>No SMS</option>
          <option value="'.kOptionAllSMS.'"'.$all_sms.'>All Picks</option>
          <option value="'.kOptionMySMS.'"'.$me_sms.'>When I\'m on the clock</option>
        </select>
      </td>
    </tr>';
	$html .='
    <tr>
      <td align="right" class="light">Pick Method:</td>
      <td class="light">
        <select name="pick_method_id">';
      $pick_method = new pick_method($this->data['pick_method_id']);
      $html .= $pick_method->option_list();
      $html .= '
        </select>
      </td>
    </tr>';
      $html .= '
    <tr>
      <td align="right" class="light">Auto pick from selections:</td>
      <td class="light">
        <input type="checkbox" name="team_autopick"';
      if ($this->data['team_autopick'] == '1') {
	$html .= ' checked';
      }
      $html .= '>
      After 
        <select name="team_autopick_wait">';
      $i = 0;
      $limit = $settings->get_value(kSettingMaxDelay);
      if (!$limit) {
	$limit = $settings->get_value(kSettingPickTimeLimit);
      }
      if ($limit) {
	$limit = $limit * $this->data['team_clock_adj'];
      } else {
	$limit = 30;
      }
      while ($i <= $limit) {
	if ($i == $this->data['team_autopick_wait']) {
	  $selected = " selected";
	} else {
	  $selected = "";
	}
	$html .= '
          <option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	$i+=5;
      }
      $html .= '
        </select>
      minutes
    </tr>
    <tr>
      <td align="right" class="light">Zero out priority of players with same position when selecting a player:</td>
      <td class="light">
        <input type="checkbox" name="team_multipos"';
      if ($this->data['team_multipos'] == '0') {
	$html .= ' checked';
      }
      $html .= '>
    </tr>';
    }
    $html .= '
    <tr>
      <td align="right" class="light" valign="top">Show Columns in Player Lists (15 max):
<p>Click on a column name to select/deselect it.</p>';
    if ($this->is_site_admin()) {
      $html .= '<p><i>(this will only affect your lists,<br>
each team has their own ability to set this for themselves)</i></p>';
    }
    $html .= '</td>
      <td class="light">';
    $html .= '
<div id="column_detail">';
    $html .= $this->draw_column_selections();
    $html .= '
</div>';
    $html .= '
      </td>
    </tr>';
    $html .= '
    <tr>
      <td colspan="2" class="light" align="center">
        <p>Use the following code to embed a widget with the current draft status:</p>
        <textarea cols="80" rows="3" readonly>';
    $widget_location = "http://".$_SERVER['SERVER_NAME'];
    $path = explode("/",$_SERVER['REQUEST_URI']);
    $path[count($path)-1] = "widget.php";
    $widget_location .= implode("/",$path);
    $html .= htmlentities('<center><iframe src="'.$widget_location.'" height="310" width="925"></iframe></center>');
    $html .= '</textarea>
      </td>
    </tr>';
    $html .= '
  </table>
  <p><input type="submit" value="Save">
</form>';
    return $html;
  }

  function draw_draft_options() {
   if (!$this->is_admin()) {
      header("Location: selections.php");
      exit;
   }
    global $settings;
    $html .= '
<h3>Draft Options</h3>';
    if ($this->is_admin()) {
      $html .= '
<p>To stop or start the draft at a certain pick, use the "Stop draft at" option.</p>';
    } 
    $html .= '
<form method="post" action="draft_options_set.php">
  <table class="data">
    <tr>
      <td align="right" class="light" width="40%">Admin E-mail:</td>';
    if ($this->is_site_admin()){
       $html.='<td class="light"><input type="text" name="team_email" value="'.$this->data['team_email'].'"></td>';
    } else {
       $html.='<td class="light"><input disabled type="text" name="blank_email" value="ADMIN EMAIL"></td>';
    }
    $html.='<td class="light">Enter an e-mail address to send notification from.  You must have an address here.</td></tr>';
    if ($this->is_admin()) {
      // First check to see if the draft is stopped
      $statement = "select * from pick where player_id is NULL";
      if(mysql_num_rows(mysql_query($statement))) {
	$stopped = false;
	$time_access = " disabled";
	$time_access_value = "0";
      } else {
	$stopped = true;
	$time_access = "";
	$time_access_value = "1";
      }
      
    $html.='
    <tr>
      <td align="right" class="light">League Name:</td>
      <td class="light"><input type="text" name="league_name" value="'.$settings->get_value(kSettingLeagueName).'"></td>
      <td>Used create unique page titles, email subjects, and flash chat rooms.</td>
    </tr>
    <tr>';
        switch($settings->get_value(kSettingChatType)) {
        case 0:
          $flashtype = ' selected';
          break;
        case 1:
          $phptype = ' selected';
          break;
        case 2:
          $offtype = ' selected';
          break;
        case 3:
          $templatetype = ' selected';
          break;
        }
        $html .= '
    <tr>
      <td align="right" class="light" width="40%">Chat System:</td>
      <td class="light">
        <select name="chat_type">
          <option value="0"'.$flashtype.'>Off-site Flash (low server load)</option>
          <option value="1"'.$phptype.'>Integrated PHP-DB (high server load)</option>
          <option value="3"'.$templatetype.'>User-Defined Embed Code (low server load)</option>
          <option value="2"'.$offtype.'>Chat Off</option>
        </select>
      </td>
      <td class="light">If the integrated chat causes server resource issues then switch to the flash version.
      Or if you have code to embed your own chat, like www.addonchat.com, select and save the template option and
      paste the chat code in the input box below.</td>
    </tr>';
         if ( $settings->get_value(kSettingChatType)==kChatTypeTemplate )
             $html.='
        <tr>
            <td align="right" class="light">Chatroom code to embed:</td>
            <td class="light"><textarea id="chat_template_code" name="chat_template_code" cols="40" rows="4">'.$settings->get_value(kSettingChatTemplateCode).'
            </textarea></td>
            <td>The HTML code to embed your own chat room if the user-defined chat system is selected.</td>
       </tr>';
    $html.='
      <td align="right" class="light">Send E-mails and SMS Texts:</td>
      <td class="light">';
     if ($this->is_site_admin() && $settings->get_value(kSettingEmailType)!=kEmailTypeOff)
        $html.='<select name="send_emails">';
     else
        $html.='<select name="send_emails" disabled>';
     if ( $settings->get_value(kSettingEmailType)==kEmailTypeOff )
     {
         $all = "";
         $next = "";
         $off = " selected";
     }
     else{
      switch ($settings->get_value(kSettingSendMails)) {
      case kEmailAll:
	$off = "";
	$all = " selected";
	$next = "";
	break;
      case kEmailNextPick:
	$off = "";
	$all = "";
	$next = " selected";
	break;
      case kEmailOff:
	$off = " selected";
	$all = "";
	$next = "";
	break;
      default:
        $off = " selected";
	$all = "";
	$next = "";
	break;
      }
     }
      $html .= '
          <option value="'.kEmailAll.'"'.$all.'>Allow GMs to elect to receive ALL notifications</option>
          <option value="'.kEmailNextPick.'"'.$next.'>Only allow GMs to elect to receive on the clock notifications</option>
          <option value="'.kEmailOff.'"'.$off.'>Off for GMs.</option>
        </select>
      </td>
      <td>Select amount of email the utility will allow to be sent if the email system is enabled below.</td>
    </tr>';
    $html.='
      <td align="right" class="light">Send Admin email:</td>
      <td class="light">';
     if ($this->is_site_admin() && $settings->get_value(kSettingEmailType)!=kEmailTypeOff)
        $html.='<select name="send_admin_emails">';
     else
        $html.='<select name="send_admin_emails" disabled>';
     if ( $settings->get_value(kSettingEmailType)==kEmailTypeOff )
     {
         $all = "";
         $rollback = "";
         $off = " selected";
     }
     else{
            switch ($settings->get_value(kSettingAdminEmail)) {
            case kAdminEmailRollback:
              $off = "";
              $all = "";
              $rollback = " selected";
              break;
            case kAdminEmailOff:
              $off = " selected";
              $all = "";
              $rollback = "";
              break;
            case kAdminEmailAll:
            default:
              $off = "";
              $all = " selected";
              $rollback = "";
              break;
            }
     }
      $html .= '
          <option value="'.kAdminEmailAll.'"'.$all.'>Admin receives notification of every pick</option>
          <option value="'.kAdminEmailRollback.'"'.$rollback.'>Admin only receives rollback notifications</option>
          <option value="'.kAdminEmailOff.'"'.$off.'>Off.  Admin receives NO email</option>
        </select>
      </td>
      <td>Select amount of email to send to the admin if the email system is enabled below.</td>
    </tr>';
    if ( $this->is_admin()){
            switch($settings->get_value(kSettingEmailType)) {
            case kEmailTypeMail:
              $sysmailtype = ' selected';
              break;
            case kEmailTypeSendmail:
              $syssendmailtype = ' selected';
              break;
            case kEmailTypeSMTP:
              $syssmtptype = ' selected';
              break;
            case kEmailTypeOff:
            default:
               $sysofftype = ' selected';
               break;
            }
            $html .= '
    <tr>
      <td align="right" class="light" width="40%">Email System:</td>
      <td class="light">
        <select name="email_type"';
            if ( !$this->is_site_admin() )
                $html.=' disabled';
            $html.='>
          <option value="0"'.$sysmailtype.'>mail - easy but unpredictable</option>
          <option value="1"'.$syssendmailtype.'>sendmail - better queuing if it is available</option>
          <option value="2"'.$syssmtptype.'>SMTP Relay - requires email server settings</option>
          <option value="3"'.$sysofftype.'>Email Disabled</option>
        </select>
      </td>
      <td class="light">Only site admins can change email server settings.  Try sendmail first, if it doesn\'t work on your server then use SMTP 
      to send email from an off-site account (SMTP options will appear once you save), only use \'mail\' as a last resort. 
      Click the \'Test Email\' button at the bottom of the page to try your last saved selection out.</td>
    </tr>';
            if ( $this->is_site_admin() && $settings->get_value(kSettingEmailType)==kEmailTypeSMTP ){
                $html .= '
    <tr>
      <td align="right" class="light">SMTP Server:</td>
      <td class="light"><input type="text" name="smtp_server" value="'.$settings->get_value(kSettingSMTPServer).'"></td>
      <td>The SMTP server to connect with (ex: smtp.gmail.com)</td>
    </tr>
    <tr>
      <td align="right" class="light">SMTP Server Port:</td>
      <td class="light"><input type="text" name="smtp_port" value="'.$settings->get_value(kSettingSMTPPort).'"></td>
      <td>The TCP port for connecting.  Usually 25 unless encryption is used in which case it\'s usually 465 for SSL or 587 for TLS.</td>
    </tr>
    <tr>
      <td align="right" class="light">SMTP Username:</td>
      <td class="light"><input type="text" name="smtp_user" value="'.$settings->get_value(kSettingSMTPUser).'"></td>
      <td>Your email username to login.</td>
    </tr>
    <tr>
      <td align="right" class="light">SMTP Password (will be stored cleartext in DB):</td>
      <td class="light"><input type="text" name="smtp_password" value="********"></td>
      <td>Email account password, only visible to site admin(s) and those with DB access. Must be stored this way to allow 
      the utility to send emails without your interaction.</td>
    </tr>
    <tr>
      <td align="right" class="light" width="40%">SMTP Encryption:</td>
      <td class="light">';
                switch($settings->get_value(kSettingSMTPEncryptType)) {
                    case kSettingSMTPEncryptTypeNone:
                      $none = ' selected';
                      break;
                    case kSettingSMTPEncryptTypeSSL:
                      $ssl = ' selected';
                      break;
                    case kSettingSMTPEncryptTypeTLS:
                      $tls = ' selected';
                      break;
                    }
                    $html.='
        <select name="smtp_encryption_type">
          <option value="0"'.$none.'>None</option>
          <option value="1"'.$ssl.'>SSL</option>
          <option value="2"'.$tls.'>TLS</option>
        </select>
      </td>
      <td>Encryption used for the SMTP connection if any.</td>
    </tr>';
            }
    }
    
      $html.='
    <tr>
      <td align="right" class="light">Maximum delay for autopick:</td>
      <td class="light">
        <select name="max_autopick_delay">
          <option value="">Pick time limit/30 min</option>';
      $time = 5;
      if ($settings->get_value(kSettingPickTimeLimit)) {
	$max = $settings->get_value(kSettingPickTimeLimit);
      } else {
	$max = 30;
      }
      while ($time <= $max) {
	if ($time == $settings->get_value(kSettingMaxDelay)) {
	  $selected = " selected";
	} else {
	  $selected = "";
	}
	$html .= '
          <option value="'.$time.'"'.$selected.'>'.$time.'</option>';
	$time += 5;
      }
      $html .= '
      </td>
      <td class="light">The maximum delay for autopick is the longest time a team can delay their pick without turning
their autopick off.  This can be up to the pick time limit or 30 minutes if no pick time limit is set.</td>
    </tr>';
      $statement = "select * from pick where pick_id is not NULL order by pick_id desc limit 1";
      $result = mysql_query($statement);
	 $result = mysql_fetch_array($result);
	$rounds = $result['pick_id']/32;

    $draft_type = $settings->get_value(kSettingDraftType);

    $html .= "<tr>";
    $html .= "<td align='right' class='light'>Draft type</td>";
    $html .= "<td class='light'>";
    $html .= "<input type='radio' name='draft_type' value='timed_draft' ". (!$draft_type || $draft_type == 'timed_draft' ? 'checked' : '')."/>Timed draft&nbsp;";
    $html .= "<input type='radio' name='draft_type' value='slotted_draft' ". ($draft_type == 'slotted_draft' ? 'checked' : '')."/>Slotted draft";
    $html .= "</td>";
    $html.='<td class="light">In a timed draft the Pick time limit is used as the amount of time each team has to make a pick.<br/>';
    $html.='In a slotted draft each team as a fixed date and time within which to make the pick.<br/>';
    $html.='(Eg. if the draft starts at 8am and the pick time limit for that round is 1 hour: Team 1 as until 9am to make the pick, team 2 has until 10am, ...)<br/>';
    $html.='The expiring date for each pick can be edited on the selection screen</td>';
    $html .= "</tr>";
	for ( $i=1; $i<$rounds+1; $i++ ){
		$html .= '
    <tr>
      <td align="right" class="light">Round '.$i.' Pick Time Limit (0:00 for no limit):</td>
      <td class="light">
        <input type="hidden" name="time_access" value="'.$time_access_value.'">
        <select name="round_'.$i.'_pick_limit_hour"'.$time_access.'>';
	 //Changing to round specific timeout settings here...
      //$limit = $settings->get_value(kSettingPickTimeLimit);
      $limit = $settings->get_value(100+$i);

      $hour = floor($limit/60);
      $min = $limit % 60;
      $h = 0;
      while ($h <= 24) {
	if ($h == $hour)  {
	  $selected = " selected";
	} else {
	  $selected = "";
	}
	$html .= '
          <option value="'.$h.'"'.$selected.'>'.$h.'</option>';
	$h++;
      }
      $html .= '
        </select>
        :
        <select name="round_'.$i.'_pick_limit_min"'.$time_access.'>';
      $m = 0;
      while($m < 60) {
        if ($m == $min) {
          $selected = " selected";
        } else {
          $selected = "";
        }
        $html .= '
          <option value="'.$m.'"'.$selected.'>'.sprintf("%02d", $m).'</option>';
            $m += 5;
      }
      $html .= '</select>';
      if (!$stopped) {
        $html .= '(Stop the draft to change)';
      }
      $html .= '</td>';

      if ( $i==1 ){
          $html.='<td class="light">The pick time limit will cause the current pick to be skipped or the 
              BPA selected at the end of the time limit, and the clock in the menu will count 
              down rather than up. Set the limit to 0:00 to turn this option off.</td>';
      }
      $html.='
    </tr>';
	}
	//END OF ROUND LOOP
	$html .='
    <tr>
      <td align="right" class="light">Daily Start Time (0:00 for 24-hour clock):</td>
      <td class="light">
        <select name="start_hour"'.$time_access.'>';
      $time = $settings->get_value(kSettingStartTime);
      if ($time) {
	$hour = date("g", $time);
	$min = date("i", $time);
	$ampm = date("A", $time);
      } else {
	$hour = 0;
	$min = 0;
	$ampm = '';
      }
      $h = 0;
      while ($h <= 12) {
	if ($h == $hour)  {
	  $selected = " selected";
	} else {
	  $selected = "";
	}
	$html .= '
          <option value="'.$h.'"'.$selected.'>'.$h.'</option>';
	$h++;
      }
      $html .= '
        </select>
        :
        <select name="start_min"'.$time_access.'>';
      $m = 0;
      while($m < 60) {
	if ($m == $min) {
	  $selected = " selected";
	} else {
	  $selected = "";
	}
	$html .= '
          <option value="'.$m.'"'.$selected.'>'.sprintf("%02d", $m).'</option>';
	$m += 5;
      }
      $html .= '
        </select>
        <select name="start_ampm"'.$time_access.'>';
      if ($ampm == 'PM') {
	$pm = ' selected';
	$am = '';
      } else {
	$pm = '';
	$am = ' selected';
      }
      $html .= '
          <option value="AM"'.$am.'>AM</option>
          <option value="PM"'.$pm.'>PM</option>
        </select>
      </td>
      <td class="light">You can set the time of day that the pick time limit is active for. 
      This setting only affects the pick time limit, teams will still be able to make selections outside 
      of this time rage unless the draft is stopped.</td>
    </tr>
    <tr>
      <td align="right" class="light">Daily End Time (0:00 for 24-hour clock):</td>
      <td class="light">
        <select name="end_hour"'.$time_access.'>';
      $time = $settings->get_value(kSettingEndTime);
      if ($time) {
	$hour = date("g", $time);
	$min = date("i", $time);
	$ampm = date("A", $time);
      } else {
	$hour = 0;
	$min = 0;
	$ampm = '';
      }
      $h = 0;
      while ($h <= 12) {
	if ($h == $hour)  {
	  $selected = " selected";
	} else {
	  $selected = "";
	}
	$html .= '
          <option value="'.$h.'"'.$selected.'>'.$h.'</option>';
	$h++;
      }
      $html .= '
        </select>
        :
        <select name="end_min"'.$time_access.'>';
      $m = 0;
      while($m < 60) {
	if ($m == $min) {
	  $selected = " selected";
	} else {
	  $selected = "";
	}
	$html .= '
          <option value="'.$m.'"'.$selected.'>'.sprintf("%02d", $m).'</option>';
	$m += 5;
      }
      $html .= '
        </select>
        <select name="end_ampm"'.$time_access.'>';
      if ($ampm == 'PM') {
	$pm = ' selected';
	$am = '';
      } else {
	$pm = '';
	$am = ' selected';
      }
      $html .= '
          <option value="AM"'.$am.'>AM</option>
          <option value="PM"'.$pm.'>PM</option>
        </select>
        Time zone:
        <select name="time_zone"'.$time_access.'>';
      $time_zone = $settings->get_value(kSettingTimeZone);
      $html .= '
          <option value="">Use Server\'s Time Zone</option>';
      $statement = "select * from time_zone where time_zone_title is not null
order by time_zone_id";
      $result = mysql_query($statement);
      while($row = mysql_fetch_array($result)) {
	if ($row['time_zone_id'] == $time_zone) {
	  $selected = " selected";
	} else {
	  $selected = "";
	}
	$html .= '
          <option value="'.$row['time_zone_id'].'"'.$selected.'>'.$row['time_zone_title'].'</option>';
      }
      $html .= '
        </select>
      </td>
    </tr>
    <tr>
    ';
/*    $autowclockoff = $settings->get_value(kSettingAutoPickWhenClockOff);
      if ($autowclockoff) {
	$autoclockon = ' selected';
        $autoclockoff = '';
      } else {
        $autoclockon = '';
	$autoclockoff = ' selected';
      }
      $html.='
        <td align="right" class="light">Autopick when clock is off:</td>
        <td class="light">
           <select name="auto_when_clock_off">
             <option value="1"'.$autoclockon.'>Enabled</option>
             <option value="0"'.$autoclockoff.'>Disabled</option>
           </select>
        </td>
        <td class="light">By default, autopicks are allowed to fire even when the clock is off for the day.  Set this
        to disabled to prevent autopicks from firing when the draft clock is not running.</td>
      </tr>';
*/
      $html.='
      <tr>
      <td align="right" class="light">If a team goes on the clock with less than the time limit for the day:</td>
      <td class="light">
        <select name="rollover_method"'.$time_access.'>';
      if ($settings->get_value(kSettingRolloverMethod) == kFinishToday) {
	$today = " selected";
      } else {
	$today = "";
      }
      $html .= '
          <option value="'.kRollIntoTomorrow.'">Add the time that the clock is off to the limit</option>
          <option value="'.kFinishToday.'"'.$today.'>Keep the limit for that pick</option>
        </select>
      </td>
    </tr>
    <tr>
      <td align="right" class="light">When a team\'s time expires, reduce their limit percentage by</td>
      <td class="light">
        <select name="autopick_reduction">';
      $autopick_reduction = $settings->get_value(kSettingAutopickReduction);
      $percent = 0;
      while ($percent <= 100) {
	if ($percent == $autopick_reduction) {
	  $selected = " selected";
	} else {
	  $selected = "";
	}
	$html .= '
          <option value="'.$percent.'"'.$selected.'>'.$percent.'%</option>';
	$percent += 5;
      }
      $html .= '
        </select>
      </td>
    </tr>
    <tr>
      <td align="right" class="light">When a team\'s time expires:</td>
      <td class="light">
        <select name="team_expire"';
      if ( $settings->get_value(kSettingStaffDraftOn)==1 )
          $html .= ' disabled ';
      $html .='>';
      if ($settings->get_value(kSettingExpiredPick) == kExpireMakePick) {
	$make_pick = " selected";
      } else {
	$make_pick = "";
      }
      $html .= '
          <option value="'.kExpireSkipPick.'">Skip Pick</option>
          <option value="'.kExpireMakePick.'"'.$make_pick.'>Choose BPA/Scout Pick</option>';
      $html .= '
        </select>
      </td>';
      if ( $settings->get_value(kSettingStaffDraftOn)==1 )
          $html.='<td class="light">In a staff draft when a team\'s time expires the utility will try to decline the pick, but
              if the team must fill the position it will pick the amenable person with the highest suitability for the
              position.</td>';
      else
          $html.='<td class="light">If the setting for "When a team\'s time expires" is set to "Choose BPA/Scout Pick," 
      the system will first see if that team has a priority list and just has autopick turned off, and will 
      choose from that list. If there is not a player with that method, if a mock draft has been performed, 
      it will use the team\'s roster data to do a scout pick. If no mock draft has been performed, it will 
      choose the best player available based on the adjusted grade or, if the adjusted grade is not uploaded, 
      the order of players as uploaded, without selecting a position that has already been selected for that team.</td>';
      $html.='
    <tr>
      <td align="right" class="light">Stop draft at:</td>
      <td class="light">
        <select name="pick_id">
          <option value="">Run Unimpeded</option>';
      $statement = "select * from pick where player_id is NULL or player_id = '".kDraftHalt."' order by pick_id";
      $result = mysql_query($statement);
      $found = false;
      while ($row = mysql_fetch_array($result)) {
	if (!$found && $row['player_id'] == kDraftHalt) {
	  $selected = " selected";
	  $found = true;
	} else {
	  $selected = "";
	}
	$html .= '
          <option value="'.$row['pick_id'].'"'.$selected.'>'.calculate_pick($row['pick_id']).'</option>';
      }
      $html .= '
        </select>
      </td>
      <td>When a pick is selected in the "Stop draft at" option, the selected pick will not run.<br>
Stopping and restarting the draft will reset the clock for the current pick.</td>
    </tr>';
    }
    $html .= '
    <tr>
      <td colspan="2" class="light" align="center">
        <p>Use the following code to embed a widget with the current draft status:</p>
        <textarea cols="80" rows="3" readonly>';
    $widget_location = "http://".$_SERVER['SERVER_NAME'];
    $path = explode("/",$_SERVER['REQUEST_URI']);
    $path[count($path)-1] = "widget.php";
    $widget_location .= implode("/",$path);
    $html .= htmlentities('<center><iframe src="'.$widget_location.'" height="310" width="925"></iframe></center>');
    $html .= '</textarea>
      </td>
    </tr>';
    $html .= '
  </table>
  <p><input type="submit" value="SAVE DRAFT OPTIONS">
</form>';
    if ($settings->get_value(kSettingEmailType)==kEmailTypeOff )
        return $html;
    $html.='
<form method="post" action="email_test.php">
  <table class="data">
    <tr>
    <td align="right" class="light">Send email to the admin to confirm email settings</td>';
    if ( $settings->get_value(kSettingEmailType)==kEmailTypeMail )
        $email='PHP mail() command';
    else if ( $settings->get_value(kSettingEmailType)==kEmailTypeSendmail )
        $email='sendmail() command';
    else if ( $settings->get_value(kSettingEmailType)==kEmailTypeSMTP )
        $email='SMTP settings';
    $html.='
      <td class="light">
        <input type="submit" value="Test Email with '.$email.'">
      </td>
    </tr>
  </table>
</form>';
    return $html;
  }

  function select_column() {
    $statement = "select count(*) num from team_to_column where team_id = '".$this->data['team_id']."'";
    $row = mysql_fetch_array(mysql_query($statement));
    if ($row['num'] < 15) {
      if ($_GET['column_id']) {
	$statement = "insert into team_to_column (team_id, column_id)
values
('".$this->data['team_id']."', '".$_GET['column_id']."')";
	mysql_query($statement);
      }
    }
    return $this->draw_column_selections();
  }

  function deselect_column() {
    $statement = "delete from team_to_column where
team_id = '".$this->data['team_id']."' and column_id = '".mysql_real_escape_string($_GET['column_id'])."'";
    mysql_query($statement);
    return $this->draw_column_selections();
  }

  function draw_column_selections() {
    global $settings;
    $staff = false;
    if ($settings->get_value(kSettingStaffDraftOn)==1 )
        $staff = true;
    $html .= '
<div class="option_box_holder">
  Available Columns:
  <div class="option_box" id="unselected">';
    $html .= $this->draw_unselected_columns($staff);
    $html .= '
  </div>
</div>';
    $html .= '
<div class="option_box_holder">
  Selected Columns:
  <div class="option_box" id="selected">';
    $html .= $this->draw_selected_columns($staff);
    $html .= '
  </div>
</div>';
    return $html;
  }

  function draw_unselected_columns($staff) {
    $statement = "select `column`.* from `column`
left join team_to_column on team_to_column.team_id = '".$this->data['team_id']."'
and team_to_column.column_id = `column`.column_id
where team_to_column.column_id is NULL and column_order<285
order by `column`.column_order";
    if ( $staff )
        $statement = "select `column`.* from `column`
left join team_to_column on team_to_column.team_id = '".$this->data['team_id']."'
and team_to_column.column_id = `column`.column_id
where team_to_column.column_id is NULL and column_order>280
order by `column`.column_order";
    $result = mysql_query($statement);
    $col = array();
    while ($row = mysql_fetch_array($result)) {
      $col[] = '<a href="javascript:select_column(\''.$row['column_id'].'\')">'.$row['column_header'].'</a>';
    }
    return implode("<br>", $col);
  }

  function draw_selected_columns($staff) {
    $statement = "select * from `column`
left join team_to_column on team_to_column.team_id = '".$this->data['team_id']."'
and team_to_column.column_id = `column`.column_id
where team_to_column.column_id is not NULL  and column_order<285
order by `column`.column_order";
    if ( $staff )
        $statement = "select * from `column`
left join team_to_column on team_to_column.team_id = '".$this->data['team_id']."'
and team_to_column.column_id = `column`.column_id
where team_to_column.column_id is not NULL and column_order>280
order by `column`.column_order";
    $result = mysql_query($statement);
    $col = array();
    while ($row = mysql_fetch_array($result)) {
      $col[] = '<a href="javascript:deselect_column(\''.$row['column_id'].'\')">'.$row['column_header'].'</a>';
    }
    return implode("<br>", $col);
  }

  function auto_pick() {
    if ($this->data['team_autopick'] == 1) {
      return true;
    } else {
      return false;
    }
  }

  function skipped() {
    // See if this team has been skipped
    // first find the current pick
    $statement = "select pick_id
from pick where
pick.player_id is NULL
order by pick_id
limit 1";
    $row = mysql_fetch_array(mysql_query($statement));
    // Now find if our team has been skipped
    $statement = "select * from pick where pick_id < '".$row['pick_id']."' and
pick.team_id = '".$this->data['team_id']."' and
pick.player_id = '".kSkipPick."'";
    if (mysql_num_rows(mysql_query($statement))) {
      return true;
    } else {
      return false;
    }
  }

  function can_pick() {
    $statement = "select * from pick where player_id is NULL order by pick_id";
    $row = mysql_fetch_array(mysql_query($statement));
    if ($row['team_id'] == $this->data['team_id']) {
      return true;
    } else {
      return $this->skipped();
    }
  }

  function get_columns(&$col, &$list, $allow_sort = true) {
    // Generate the column list for the logged in user, values must be passed by referenc
    $statement = "select * from `column`, team_to_column where
team_to_column.team_id = '".$this->data['team_id']."' and
team_to_column.column_id = `column`.column_id
order by `column`.column_order";
    $result = mysql_query($statement);
    echo mysql_error();
    while ($row = mysql_fetch_array($result)) {
      $col_name = 'q'.md5($row['column_query']);
      $col[] = $row['column_query'].' '.$col_name;
      $list->set_header($col_name, $row['column_header'], $allow_sort, $allow_sort, $allow_sort);
      if ($row['column_style']) {
	$col[] = $row['column_style'].' '.$col_name.'_style';
	$list->set_cell_style($col_name, $col_name.'_style');
      }
      if ($row['column_exec']) {
	$list->set_exec($col_name, $row['column_exec']);
      }
      if ($row['column_date_format']) {
	$list->set_date_format($col_name, $row['column_date_format']);
      }
      if ($row['column_number_format']) {
	$list->set_number_format($col_name, $row['column_number_format']);
      }
    }
  }

  function get_comments() {
    return $this->data['team_comments'];
  }
}
?>