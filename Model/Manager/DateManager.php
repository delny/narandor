<?php


class DateManager
{
  /*----------*/
  /*	Date	  */
  /*----------*/

  public function formaterdate($dateaformater)
  {
    $tab = explode(" ",$dateaformater);
    $date1 = $tab[0]; //2016-11-06
    $date2 = $tab[1]; // 13:20:59

    $tab_date1 = explode("-",$date1);
    $tab_date2 = explode("-",$date2);
    $hier = date('d')-1;

    if ($date1==date('Y-m-d'))
    {
      return $date2;
    }
    elseif ($date1==date('Y-m-').$hier)
    {
      return 'hier '.$date2;
    }
    else
    {
      return ''.$tab_date1[2].'/'.$tab_date1[1].'/'.$tab_date1[0].' '.$date2;
    }
  }
}
