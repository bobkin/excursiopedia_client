<?php

class EP_Loader {

    private $country_data;
    private $city_data;

    public function updateRegions() {

        $regions_data = $this->loadRegions();

        $this->updateCountry($regions_data);
        $this->updateCity($regions_data);
        
    }

    public function checkAndSaveActivity(SimpleXMLElement $activity) {

        $a = EP::findOne('activity', 'activity_id = ?', array((int) $activity['id']));

        if ($a) {
            if ($a->hash == (string) $activity['hash']) {
                $this->log('skip ' . $a->activity_id);
            } else {
                // hash record change: update records
                $this->log('update ' . $a->activity_id);
                $a->hash = (string) $activity['hash'];
                $this->updateActivity($a);
            }
        } else {            
            $this->log('new ' . (int) $activity['id']);
            // new activity create and update it;
            $a = EP::dispense('activity');
            $a->activity_id = (int) $activity['id'];
            $a->hash = (string) $activity['hash'];
            $this->updateActivity($a);
        }
    }

    public function leaveThisActivities($activities) {

        $upload_activity = array();

        foreach ($activities->activities->activity as $activity) {
            $upload_activity[] = (int) $activity['id'];
        }

        if (count($upload_activity) > 0) {

            $delete_activity = EP::find('activity', 'activity_id not in (' . EP::genSlots($upload_activity) . ')', $upload_activity);

            if ($delete_activity) {
                EP::trashAll($delete_activity);
            }
        }
    }
    

    private function loadData($action, $params = Array()) {

        $path = 'http://service.excursiopedia.com/' . $action;

        $params['lang'] = EP_LANG;
        $params['username'] = EP_LOGIN;
        $params['api_key'] = EP_API_KEY;

        $auth_path = $path . '?' . http_build_query($params);

        try {
            return $this->getResponse($auth_path); 
        } catch (Exception $e) {
            $this->stop($e->getMessage());
        }

        
    }

    private function getResponse($url) {
        
        $data = @file_get_contents($url);
        if(!$data) throw new Exception('Http connection error');
        
        //turn on user simplexml errors
        libxml_use_internal_errors(TRUE);
        
        $response = simplexml_load_string($data);
        
        if(!$response) throw new Exception('No XML data');
        
        if($response instanceof SimpleXMLElement and $response['type'] == 'success'){
            return $response;
        } else {            
            throw new Exception('Response error: '.$response->error);
            
        }
        
    }

    private function loadRegions() {
        return $this->loadData('geo');
    }

    private function updateCountry($regions) {

        foreach ($regions->countries->country as $country) {
            
            $country_model = EP::findOne('country', 'country_id = :id', array('id' => (int) $country['id']));

            if (!$country_model) {
                
                $this->log('New country '.$country['id']);
                
                $country_model = EP::dispense('country');
                $country_model->country_id = (int) $country['id'];
                $country_model->setMeta('buildcommand.unique' , array(array('country_id')));                
            }
            
            $country_model->name = (string) $country;
            $country_model->activities = (int) $country['activities'];
            
            $country_id = EP::store($country_model);
                       
            $this->country_data[$country_model->country_id] = $country_model;
            
        }
    }

    private function updateCity($regions) {

        $country_data = $this->getCountryData();


        foreach ($regions->cities->city as $city) {

            $city_model = EP::findOne('city', 'city_id = :id', array('id' => (int) $city['id']));
            if (!$city_model) {

                $this->log("New city ".(int) $city['id']);
                $city_model = EP::dispense('city');
                $city_model->city_id = (int) $city['id'];                
                $city_model->setMeta('buildcommand.unique' , array(array('slug'), array('city_id')));
                                
            }

            $city_model->name = (string) $city;
            $city_model->slug = (string) $city['slug'];
            $city_model->activities = (int) $city['activities'];
            $city_model->country = $country_data[(int) $city['country_id']];
            $city_id = EP::store($city_model);
            $this->city_data[$city_model->city_id] = $city_model;
        }
    }

    private function getCountryData() {
        if (count($this->country_data) > 0)
            return $this->country_data;
        else
            $this->stop("Need run updateCountry first");
    }

    private function getCityData() {
        if (count($this->city_data) > 0)
            return $this->city_data;
        else
            $this->stop("Need run updateCity first");
    }

    private function updateImages($images, $activity) {

        $upload_images = Array();
        foreach ($images->image as $image) {
            $upload_images[] = (int) $image['id'];

            $i = EP::findOne('image', 'image_id = ?', array((int) $image['id']));
            if (!$i) {
                $i = EP::dispense('image');
                $i->image_id = (int) $image['id'];
            }
            $i->small_url = (string) $image->small;
            $i->mid_url = (string) $image->mid;
            $i->standart_url = (string) $image->standart;
            $i->activity = $activity;
            EP::store($i);
        }

        // Delete not updating images
        if (count($upload_images) > 0) {
            $delete_images = R::find('image', ' activity = ' . $activity->id . ' 
                                            and image_id NOT IN (' . R::genSlots($upload_images) . ') ', $upload_images);

            if ($delete_images) {
                EP::trashAll($delete_images);
            }
        }
    }

    public function getActivitiesList() {
        return $this->loadData('list');
    }

    private function updateActivity($activity) {

        $country_data = $this->getCountryData();
        $city_data = $this->getCityData();

        $xml_obj = $this->loadData('activity', array('id' => $activity->activity_id));
        $params = $xml_obj->activity;
        if (isset($country_data[(int) $params->country['id']]) and
                isset($city_data[(int) $params->city['id']])) {
            

            $activity->title = (string) $params->title;
            $activity->link_view = (string) $params->links->view;
            $activity->link_order = (string) $params->links->order;
            $activity->guide_id = (int) $params->guide['id'];
            $activity->guide_name = (string) $params->guide->name;
            $activity->guide_image_small = (string) $params->guide->images->small;
            $activity->guide_image_standart = (string) $params->guide->images->standart;
            $activity->description = (string) $params->description;
            $activity->country = $country_data[(int) $params->country['id']];
            $activity->country_name = (string) $params->country;
            $activity->city = $city_data[(int) $params->city['id']];
            $activity->city_name = (string) $params->city;
            $activity->duration = (string) $params->duration;
            $activity->duration_type = (string) $params->duration['type'];
            $activity->price = (string) $params->price->show;
            $activity->currency = (string) $params->price['currency'];
            $activity->reservation_min = (int) $params->reservation_min;
            $activity->type_object = (string) $params->type_object;
            $activity->type_move = (string) $params->type_move;
            $activity->type_people = (string) $params->type_people['type'];
            $activity->type_people_name = (string) $params->type_people;

            EP::store($activity);

            if (count($params->images) > 0) {
                $this->updateImages($params->images, $activity);
            }
        }
    }
    
    
    private function stop($msg = 'error'){
        echo "== Upload Stop\n";
        echo "== ".$msg."\n";
        exit();
    }
    
    private function log($msg){
        echo $msg."\n";
        
    }

}
