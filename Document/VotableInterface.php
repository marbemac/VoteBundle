<?php

namespace Marbemac\VoteBundle\Document;

interface VotableInterface
{
    function getVotes();
//    {
//        $votes = array();
//        foreach ($this->votes as $key => $vote)
//        {
//            $votes[] = new \MongoId($key);
//        }
//
//        return $votes;
//    }

    /*
     * This must return an array of MongoId's that
     * Represents the IDs of the voters (use the keys of the votes array)
     */
    function getVoters();
//    {
//        return $this->voters;
//    }

    /* A non-managed field that can be filled with an array of voter information */
    function setVoters($voters);
//    {
//        $this->voters = $voters;
//    }

    function findVote($voterId);
//    {
//        $voterId = is_object($voterId) ? $voterId->__toString() : $voterId;
//        if ($this->votes)
//        {
//            return isset($this->votes[$voterId]) ? $this->votes[$voterId] : false;
//        }
//
//        return false;
//    }

    function setScore($score);
//    {
//        $this->score = $score;
//    }

    function getScore();
//    {
//        return $this->score;
//    }
}