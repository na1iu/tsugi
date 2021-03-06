<?php
require_once "../../config.php";
require_once $CFG->dirroot."/pdo.php";
require_once $CFG->dirroot."/lib/lms_lib.php";
require_once "peer_util.php";

use \Tsugi\Core\LTIX;

headerJson();

// Sanity checks
$LTI = LTIX::requireData();
if ( ! $USER->instructor ) die("Requires instructor");
$p = $CFG->dbprefix;

$assn = loadAssignment($LTI);
$assn_json = null;
$assn_id = false;
if ( $assn === false ) {
    die("Not a peer-graded assignment");
} else {
    $assn_json = json_decode($assn['json']);
    $assn_id = $assn['assn_id'];
}

// Check how much work we have to do
$row = $PDOX->rowDie(
    "SELECT COUNT(submit_id) AS count FROM {$p}peer_submit AS S
    WHERE assn_id = :AID AND regrade IS NULL",
    array(":AID" => $assn_id)
);
$total = $row['count'];

echo(json_encode(array("total" => $total)));
