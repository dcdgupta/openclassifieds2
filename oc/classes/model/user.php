<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User model
 *
 * @author		Chema <chema@open-classifieds.com>
 * @package		OC
 * @copyright	(c) 2009-2013 Open Classifieds Team
 * @license		GPL v3
 * *
 */
class Model_User extends Model_OC_User {

    /**
     * saves the user review rates recalculating it
     * @return [type] [description]
     */
    public function recalculate_rate()
    {
        if($this->loaded())
        {
            //get all the rates and divide by them
            $this->rate = Model_Review::get_user_rate($this);
            $this->save();
            return $this->rate;
        }
        return FALSE;
    }

    /**
     * Deletes a single record while ignoring relationships.
     *
     * @chainable
     * @throws Kohana_Exception
     * @return ORM
     */
    public function delete()
    {
        if ( ! $this->_loaded)
            throw new Kohana_Exception('Cannot delete :model model because it is not loaded.', array(':model' => $this->_object_name));

        //remove image
        $this->delete_image();

        //remove ads, will remove reviews, images etc...
        $ads = new Model_Ad();
        $ads = $ads->where('id_user','=',$this->id_user)->find_all();
       
        foreach ($ads as $ad) 
            $ad->delete();
        
        //delete favorites
        DB::delete('favorites')->where('id_user', '=',$this->id_user)->execute();
        
        //delete reviews
        DB::delete('reviews')->where('id_user', '=',$this->id_user)->execute();

        //delete orders
        DB::delete('orders')->where('id_user', '=',$this->id_user)->execute();

        //remove visits ads
        DB::update('visits')->set(array('id_user' => NULL))->where('id_user', '=',$this->id_user)->execute();

        //delete subscribtions
        DB::delete('subscribers')->where('id_user', '=',$this->id_user)->execute();

        //delete posts
        DB::delete('posts')->where('id_user', '=',$this->id_user)->execute();

        parent::delete();
    }
    
    /**
     * get user ad contacts
     * @return array [description]
     */
    public function contacts()
    {
        if($this->loaded())
        {
            //get cookie contact_notification
            $theme = (isset($_COOKIE['contact_notification']))? $_COOKIE['contact_notification']:0;
            
            $query = DB::select('a.id_ad')
                        ->select('a.title')
                        ->select('a.seotitle')
                        ->select('v.id_visit')
                        ->select('v.created')
                        ->from(array('ads', 'a'))
                        ->join(array('visits', 'v'),'INNER')
                        ->on('a.id_ad','=','v.id_ad')
                        ->where('a.id_user','=',$this->id_user)
                        ->where('v.contacted','=','1')
                        ->where('v.created','>',Date::unix2mysql($theme))
                        ->order_by('v.created', 'DESC');
            
            if (!isset($_COOKIE['contact_notification']))
                $query->limit(5);
            
            return $query->execute();
        }
        return FALSE;
    }
    
    /**
     * checks if we have stored user's lat/lng
     * @return array/boolean
     */
    public static function get_userlatlng()
    {
        if (isset($_COOKIE['mylat'])
            AND is_numeric($_COOKIE['mylat'])
            AND isset($_COOKIE['mylng'])
            AND is_numeric($_COOKIE['mylng']))
        {
            return array(   "lat" => $_COOKIE['mylat'],
                            "lng" => $_COOKIE['mylng'],
                        );
        }
        else return FALSE;
    }


    /**
    * returns a list with custom field values of this user
    * @param  boolean $show_profile only those fields that needs to be displayed on the user profile show_profile===TRUE
    * @param  boolean $hide_admin hide those fields that are reserved for the admin hide_admin===TRUE
    * @return array else false 
    */
    public function custom_columns($show_profile = FALSE, $hide_admin = TRUE)
    {
        if($this->loaded())
        {
            //custom fields config, label, name and order
            $cf_config = Model_UserField::get_all(($hide_admin === TRUE)? TRUE:FALSE,FALSE);

            if(!isset($cf_config))
                return array();
            
            //getting the custom fields this uaser has and his value          
            $active_custom_fields = array();
            foreach($this->_table_columns as $value)
            {   
                //we want only those that are custom fields
                if(strpos($value['column_name'],'cf_') !== FALSE) 
                {
                    $cf_name  = str_replace('cf_', '', $value['column_name']);
                    $cf_value = $this->$value['column_name'];

                    if(isset($cf_value) AND isset($cf_config->$cf_name))
                    {   
                        //formating the value depending on the type
                        switch ($cf_config->$cf_name->type) 
                        {   
                            case 'checkbox':
                                $cf_value = ($cf_value)?'checkbox_'.$cf_value:NULL;
                                break;
                            case 'radio':
                                $cf_value = $cf_config->$cf_name->values[$cf_value-1];
                                break;
                            case 'date':
                                $cf_value = Date::format($cf_value, core::config('general.date_format'));
                                break;
                        }      
                        
                        //should it be added to the profile?
                        if ($show_profile == TRUE AND isset($cf_config->$cf_name->show_profile)) 
                        {
                            //only to the profile
                            if ($cf_config->$cf_name->show_profile==TRUE)
                            {
                                $active_custom_fields[$cf_name] = $cf_value;
                            }                            
                        }
                        else
                            $active_custom_fields[$cf_name] = $cf_value;
                    }
       
                }
            }

            // sorting using json order
            $user_custom_vals = array();
            foreach ($cf_config as $name => $value) 
            {
                if(isset($active_custom_fields[$name]))
                    $user_custom_vals[$value->label] = $active_custom_fields[$name];
            }


            return $user_custom_vals;
            
        }
        return array();
    }

} // END Model_User