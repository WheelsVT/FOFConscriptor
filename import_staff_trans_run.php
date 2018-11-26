<?

include "includes/classes.inc.php";
// This function imports the staff transaction file

global $settings;

if ($login->is_admin()) {
  $admin = true;
 } else {
  $admin = false;
 }

// Check to make sure we have a file
if ($admin) {
  if (!file_exists($_FILES['transactions']['tmp_name'])) {
    $_SESSION['message'] = "I did not receive the required files.";
    header("Location: ./import_draft.php");
    exit;
  }
  $trans_file = $_FILES['transactions']['tmp_name'];
 } else {
    header("Location: ./selections.php");
    exit;
  }



// Now for the import
include "includes/fof7_export_columns.inc.php";
//define (ktrans_stage,0);
//define (ktrans_player_id,1);
//define (ktrans_trans,2);
//define (ktrans_team_id,3);

//figure out the last year imported
$statement = "select * from staff_trans_history order by staff_trans_year desc limit 1";
$row = mysql_fetch_array(mysql_query($statement));
$year = $row['staff_trans_year'];
if ( $year==NULL )
  $year = 0;

$file = file_get_contents($trans_file);
$lines = preg_split("/[\n\r]+/", $file);
$header = true;
$upload_count = 0;
$staff = false;
foreach($lines as $line) {
  if ($header) {
    //check to see if we have a rookie file or a staff file
    if ($line != $valid_fof7_transaction) {
      $_SESSION['message'] = "The file you imported does not appear to be a transaction csv export file, or is the wrong version.
Please verify that you are uploading the correct file and that you have the current version of FOF7.";
      header("Location: import_draft.php");
      exit;
    }
  }
  //if we are dealing with a transaction file
  if ($line && !$header) {
     //time to import the trans data
    preg_match_all('/("(?:[^"]|"")*"|[^",\r\n]*)(,|\r\n?|\n)?/', $line, $matches);
    $columns = $matches[0];
    foreach($columns as $key=>$value) {
      // Remove the field qualifiers, if any
      $columns[$key] = preg_replace("/^\"|\"$|\"?,$/", "", $value);
    }
    if ( strcmp($columns[ktrans_stage],"Staff Draft")==0 ){
       //we are dealing with a staff draft transaction that we want to record
       $col = array();
       $col["staff_trans_year"] = $columns[ktrans_year];
       $col["staff_id"] = $columns[ktrans_player_id];
       //convert the transaction text to an id number
       $transstatement = "select staff_trans_id from staff_trans_types where staff_trans_name='".trim($columns[ktrans_trans])."'";
       $transid = mysql_fetch_array(mysql_query($transstatement));
       $col["staff_trans_id"] = $transid["staff_trans_id"];
       $col["staff_team_id"] = $columns[ktrans_team_id];
       $tables = array();
       $values = array();
       foreach($col as $key=>$value) {
         $tables[] = $key;
         if ($value || $value=='0') {
           $values[] = "'".$value."'";
         } else {
           $values[] = "'0'";
         }
       }
       $statement = "insert into staff_trans_history (".implode(",",$tables).") values (".implode(",",$values).")";
       mysql_query($statement);
    }
  } else {
    $header = false;
  }
}

// Transaction import is complete!
$_SESSION['message'] = "Transaction import complete.";
header("Location: import_draft.php");

?>