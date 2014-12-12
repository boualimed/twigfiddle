<?php

namespace Fuz\AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Fuz\AppBundle\Entity\Fiddle;

/**
 * FiddleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FiddleRepository extends EntityRepository
{

    public function getFiddle($hash, $version, UserInterface $user = null)
    {
        if (!is_null($hash))
        {
            $qb = $this->createQueryBuilder();
            $qb
               ->select('f')
               ->from('Fiddle', 'f')
               ->where()

               // todo ..

               ;

            try
            {


            }
            catch (NoResultException $e)
            {
                $fiddle = new Fiddle();
                $fiddle->setHash($hash);
            }
        }
        else
        {
            $fiddle = new Fiddle();
        }
        return $fiddle;
    }

}
