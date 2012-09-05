<?php

class EP extends RedBean_Facade{
    

    
}

class Model_Activity extends RedBean_SimpleModel {
    
            public function getFirstImage() {
                
                    $data = EP::findOne("image"," activity_id = :activity_id ",
                            array('activity_id'=>$this->id));
                    
                    if($data){
                        return $data;
                    } else {
                        return false;
                    }
            }
}