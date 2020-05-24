<?php

try {

  $client = new \RankingsDB\RankingsClient('1b9c63bf-98dd4735-a9d58085-e4add43f', 731872);

  // Get a member's details
  $member = $client->getMemberDetails(731872);

  pre($member);

  // Get their all time PBs
  $options = new GetTimesBuilder($member->MemberID());
  $times = $client->getTimes($options);

  pre($times);

  // Get their times from the past year
  $options->setFromDate((new DateTime())->sub(new DateInterval("P1Y")));

  $times = $client->getTimes($options);

  pre($times);

} catch (Exception $e) {
  pre($e);
}