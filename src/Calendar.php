<?php
namespace booosta\calendar;

use \booosta\Framework as b;
b::init_module('calendar');

abstract class Calendar extends \booosta\ui\UI
{
  use moduletrait_calendar;

  protected $events;
  protected $startdate, $enddate;
  protected $lang;

  public function __construct($name = null, $events = null, $events_url = null)
  {
    parent::__construct();
    if($events === null) $this->events = []; else $this->events = $events;
    $this->id = "ui_datepicker_$name";
    $this->lang = $this->config('language');
  }

  public function set_startdate($date) { $this->startdate = date('Y-m-d', strtotime($date)); }
  public function set_enddate($date) { $this->enddate = date('Y-m-d', strtotime($date)); }
  public function set_lang($lang) { $this->lang = $lang; }
  public function get_lang() { return $this->lang; }

  public function add_events($events) 
  { 
    if(!is_array($events)) return false;
    foreach($events as $event) $this->add_event($event);
    return true;
  }

  public function add_event($event, $background = false)
  {
    if(is_object($event)): 
      $this->events[$event->sortkey()] = $event;
    elseif(is_array($event)):
      #\booosta\debug($event);
      $obj = $this->makeInstance("\\booosta\\calendar\\Event", $event['name'], $event['startdate']);
      if($event['id']) $obj->set_id($event['id']);
      if($event['enddate']) $obj->set_enddate($event['enddate']);
      if($event['link']) $obj->set_link($event['link']);
      if($event['link_target']) $obj->set_link_target($event['link_target']);
      if($event['description']) $obj->set_description($event['description']);
      if($event['color']) $obj->set_color($event['color']);
      if($event['readonly']) $obj->set_readonly($event['readonly']);
      if($background) $obj->set_background(true);
      if(is_array($event['settings'])) $obj->set_eventsettings($event['settings']);;

      $this->events[$obj->sortkey()] = $obj;
    endif;
  }

  public function set_events_url($url) { $this->events_url = $url; }
}


class Event extends \booosta\Base\base
{
  protected $id, $name, $startdate, $enddate, $link, $link_target, $description, $settings;

  public function __construct($name, $startdate, $link = null, $link_target = null, $description = null)
  {
    parent::__construct();
    $this->name = $name;
    $this->startdate = $startdate;
    $this->link = $link;
    $this->link_target = $link_target;
    $this->description = $description;
    $this->settings = [];
  }

  public function get_id() { return $this->id; }
  public function get_name() { return $this->name; }
  public function get_startdate() { return $this->startdate; }
  public function get_enddate() { return $this->enddate; }
  public function get_link() { return $this->link; }
  public function get_link_target() { return $this->link_target; }
  public function get_description() { return $this->description; }
  public function get_event_settings() { return $this->settings; }
  public function set_name($val) { $this->name = $val; }
  public function set_id($val) { $this->id = $val; }
  public function set_startdate($val) { $this->startdate = $val; }
  public function set_enddate($val) { $this->enddate = $val; }
  public function set_link($val) { $this->link = $val; }
  public function set_link_target($val) { $this->link_target = $val; }
  public function set_description($val) { $this->description = $val; }
  public function set_settings($val) { $this->settings = $val; }

  public function get_event_setting($key) { return $this->settings[$key]; }
  public function set_event_setting($key, $val) { $this->settings[$key] = $val; }

  public function sortkey() { return date('YmdHis', strtotime($this->date)) . uniqid(); }

  public function get_data() 
  { 
    $data = get_object_vars($this);
    unset($data['parentobj']);
    unset($data['topobj']);
    unset($data['CONFIG']);

    return $data;
  }

  public function is_at_day($date)
  { 
    $date = date('Y-m-d', strtotime($date));
    return $this->is_between("$date 00:00:00", "$date 23:59:59");
  }

  public function is_between($from, $until)
  { 
    $from = strtotime($from);
    $until = strtotime($until);
    return strtotime($this->date) >= $from && strtotime($this->date) <= $until;
  }

  public function is_before($date)
  { 
    $date = strtotime($date);
    return strtotime($this->date) < $date;
  }

  public function is_after($date)
  {
    $date = strtotime($date);
    return strtotime($this->date) > $date;
  }
}

