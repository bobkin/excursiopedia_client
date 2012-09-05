<?php
include(dirname(__FILE__). '/../include/ep.start.php');
include(dirname(__FILE__).'/../include/ep.loader.class.php');

//EP::nuke();
//EP::debug(true);


$loader = new EP_Loader();
echo "Update Regions\n";
$loader->updateRegions();


$activities = $loader->getActivitiesList();

echo "Update All Activity\n";
foreach($activities->activities->activity  as $activity){
       $loader->checkAndSaveActivity($activity);		
}

echo "Delete old activity\n";
$loader->leaveThisActivities($activities);

echo "DONE\n";



