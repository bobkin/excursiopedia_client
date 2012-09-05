<?php require(dirname(__FILE__).'/../include/ep.start.php');?>

<title>Demo page</title>

<h3>Activity  Page</h3>
<a href=".">Start page</a>

<?php $activity = EP::findOne('activity','id = ? ',array(intval($_GET['id'])));?>
    
<?php if($activity):?>
   <h1><?php echo $activity->country_name?> / <?php echo $activity->city_name?></h1>
   <h3><?php echo $activity->title ?></h3>

   <div style="float:left;width:600px">
       <?php echo $activity->description ?>
       <div>
           <?php $images = $activity->ownImage ?>
           <?php if(count($images)>0):?>
                <?php foreach($images as $i):?>
                    <a href="<?php echo $i->standart_url?>" targrt="_blank"><img src="<?php echo $i->mid_url?>"></a>
                <? endforeach?>
           <?php endif?>
       </div>
   </div>    
   <div style="float:left;width:300px;margin-left: 50px">

<div>
    <b>Guide</b>       
    <p><img src=<?php echo $activity->guide_image_small ?>></p>
    <p><?php echo $activity->guide_name ?></p>
</div>
<div>
    <a href="<?php echo $activity->link_view ?>">Order Now!</a>
</div>       
       <div>
           <h4><?php echo $activity->price ?> <?php echo $activity->currency ?></h4>
       </div>

    <?php echo $activity->type_people_name ?>
       </div>

<?php endif?>
