<?php require(dirname(__FILE__).'/../include/ep.start.php');?>

<title>Demo page</title>

<h3>City Page</h3>
<a href=".">Start page</a>

<?php $city = EP::findOne('city','id = ? ',array(intval($_GET['id'])));?>
    
<?php if($city):?>
   <h1><?php echo $city->country->name?> / <?php echo $city->name?></h1>
   
   <ul> 
       <?php foreach($city->ownActivity as $activity):?>
           <li><a href="activity.php?id=<?php echo $activity->id?>"><?php echo $activity->title?></a>
       <?php endforeach;?>
   </ul>
   

<h3>Random 5 with images in city</h3>  

<?php
/* MySQL  code for random use RAND() function
 */?>

    
<?php $rand_activity = EP::findAll('activity', 'where city_id = :city_id order by RANDOM() limit 5 ', 
                                array('city_id' => $city->id));?>

<?php foreach($rand_activity as $activity):?>

    <?php $image = $activity->getFirstImage()?>
    <?php if($image):?>
        <a href="activity.php?id=<?php echo $activity->id?>"><img src="<?=$image->mid_url?>" alt="<?php echo $activity->title?>"></a>
    <?php endif ?>


    
<?php endforeach ?>

   
<? //print_r($best_activity);?>
<?php endif;?>