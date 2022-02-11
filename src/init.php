<?php
namespace booosta\calendar;

trait actions
{
  protected function action_calendar_move()
  {
    $newstart = date('Y-m-d H:i:s', strtotime($this->VAR['startdate']));
    #\booosta\debug("id: $this->id, newstart: $newstart");

    $obj = $this->get_dbobject();
    $oldstart = $obj->get('startdate');
    $oldend = $obj->get('enddate');
    $duration = strtotime($oldend) - strtotime($oldstart);
    if($duration <= 0) $duration = 3600;

    $obj->set('startdate', $newstart);
    $obj->set('enddate', date('Y-m-d H:i:s', strtotime($newstart) + $duration));
    $obj->update();

    \booosta\ajax\Ajax::print_response(null, ['result' => '']);
    $this->no_output = true;
  }

  protected function action_calendar_resize()
  {
    $newend = date('Y-m-d H:i:s', strtotime($this->VAR['enddate']));
    #\booosta\debug("id: $this->id, newend: $newend");

    $obj = $this->get_dbobject();
    $obj->set('enddate', $newend);
    $obj->update();

    \booosta\ajax\Ajax::print_response(null, ['result' => '']);
    $this->no_output = true;
  }
}
