<?
define (kName, 0);
define (kPosition, 1);
define (kPosGrp, 2);
define (kSchool, 3);
define (kTeam, 4);
define (kBorn, 5);
define (kHomeTown, 6);
define (kAgent, 7);
define (kDesignation, 8);
define (kHeight, 9);
define (kWeight, 10);
define (kExperience, 11);
define (kVolatility, 12);
define (kJersey, 13);
define (kLoyalty, 14);
define (kWinner, 15);
define (kLeader, 16);
define (kIntelligence, 17);
define (kPersonality, 18);
define (kPopularity, 19);
define (kMentorTo, 20);
define (kSolecismic, 21);
define (kForty, 22);
define (kBenchPress, 23);
define (kAgility, 24);
define (kBroadJump, 25);
define (kPositionDrill, 26);
define (kDeveloped, 27);
define (kInterviewed, 28);
define (kImpression, 29);
define (kCurrent, 30);
define (kFuture, 31);
define (kConflicts, 32);
define (kAffinities, 33);
define (kCharacter, 34);

$valid_extractor = 'Name,Position,PosGrp,College,Team,Born,HomeTown,Agent,Designation,Height,Weight,Experience,Volatility,Jersey,Loyalty,Winner,Leader,Intelligence,Personality,Popularity,MentorTo,Solecismic,40Yard,Bench,Agility,BroadJump,PosDrill,PctDev,Intvwd,Impress,Cur,Fut,Conflicts,Affinities,Character';

// Preload the position mapping
$positions = array();
$statement = "select * from position_to_alias";
$result = mysql_query($statement);
while ($row = mysql_fetch_array($result)) {
  $positions[$row['alias_name']] = $row['position_id'];
 }
?>