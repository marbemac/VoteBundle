<?php

namespace Marbemac\VoteBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;

class VoteManager
{
    protected $dm;
    protected $m;
    protected $documentStem;

    public function __construct(DocumentManager $dm, $documentStem)
    {
        $this->dm = $dm;

        $this->m = $dm->getConnection()->selectDatabase($dm->getConfiguration()->getDefaultDB());

        $this->documentStem = $documentStem;
    }

    /*
     * Add a vote to this object in the designated collection.
     * Remove a vote if the new vote amount is the same as the old vote amount.
     * Users cannot vote on their own objects.
     */
    public function addVote($user, $oid, $collection, $amount)
    {
        $object = $this->dm->createQueryBuilder($this->documentStem.'\\'.$collection)
            ->field('id')->equals($oid)
            ->getQuery()
            ->getSingleResult();

        $return = array();

        // You can't score your own posts!
        if ($object && $user->getId()->__toString() == $object->getCreatedBy())
        {
            $return['status'] = 'error';
            $return['message'] = 'You may not vote on your own posts!';
        }
        else if ($object)
        {
            $return['status'] = 'success';

            $oldVote = $object->findVote($user->getId()->__toString());

            $oldAmount = $oldVote ? $oldVote['a'] : 0;

            // If it's the same amount, we are undoing the action!
            if ($oldAmount == $amount)
            {
                $amount = 0;
            }

            $change = (-1 * $oldAmount) + $amount;

            // Are we removing a vote?
            if ($amount == 0)
            {
                $this->m->$collection->update(
                    array('_id' => $object->getId()),
                    array(
                        '$inc' =>
                            array(
                                'score' => $change
                            ),
                        '$unset' =>
                            array(
                                'votes.'.$user->getId()->__toString() => 1,
                            )
                    )
                );
            }
            // Else we are adding/replacing a vote
            else
            {
                // Create the replacement vote
                $newVote = array();
                $newVote['_id'] = $user->getId();
                $newVote['a'] = $amount;
                $newVote['ca'] = new \MongoDate();
                $newVote['ts'] = time();

                $this->m->$collection->update(
                    array('_id' => $object->getId()),
                    array(
                        '$inc' =>
                            array(
                                'score' => $change
                            ),
                        '$set' =>
                            array(
                                'votes.'.$user->getId()->__toString() => $newVote,
                            )
                    )
                );
            }

            // Increment the score of the user that created the object
            $this->m->User->update(
                array('_id' => new \MongoId($object->getCreatedBy())),
                array(
                    '$inc' =>
                        array(
                            'score' => $change
                        )
                )
            );

            $object->setScore($object->getScore() + $change);

            $return['object'] = $object;
        }
        else
        {
            $return['status'] = 'error';
            $return['message'] = 'Woops, this object was not found! [e-V01]';
        }

        return $return;
    }
}