<?php require(dirname(__FILE__).'/../include/ep.start.php');?>
<html>
<title>Demo page</title>

<body>
<h3>Country And CityList</h3>

<ul>
<?php $countries = EP::findAll('country');?>

<?php foreach($countries as $country):?>
    <li><a href="country.php?id=<?php echo $country->id?>"><?php echo $country->name?> (<?php echo $country->activities?>)</a>
        <ul>
            <?php foreach($country->ownCity as $city):?>
                <li><a href="city.php?id=<?php echo $city->id?>"><?php echo $city->name ?>(<?php echo $city->activities?>)</a>                
            <? endforeach;?>
        </ul>


<?php endforeach; ?>
    </ul>

</body>
</html>