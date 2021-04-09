<?php

function redirectTo($newLocation)
{
  header("Location: {$newLocation}");
  exit;
}


function getUserRankName($rankNo)
{
  if($rankNo == 1)
  {
    return "Member";
  }
  else if($rankNo == 2)
  {
    return "Moderator";
  }
  else if($rankNo == 3)
  {
    return "Admin";
  }
  else {
    return "Guest";
  }
}


function get_time_ago($datetime)
{
  $time_difference =  time() - strtotime($datetime);

  if ($time_difference < 1) { return 'now'; }

  $condition = [
    12 * 30 * 24 * 60 * 60 => 'y',
    30 * 24 * 60 * 60 => 'mth',
    24 * 60 * 60 => 'd',
    60 * 60 => 'h',
    60 => 'm',
    1 => 's'
  ];

  foreach ($condition as $secs => $str)
  {
    $timeInSecond = $time_difference / $secs;
    if($timeInSecond >= 1)
    {
      $time = round($timeInSecond);
      return "{$time}{$str}" . ($time > 1 ? 's' : "");
    }
  }
}






?>
