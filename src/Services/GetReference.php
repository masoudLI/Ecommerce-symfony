<?php
namespace App\Services;

use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;

class GetReference 
{
    private $objectManager;

    /**
     * __construct
     *
     * @param  mixed $objectManager
     *
     * @return void
     */
    public function __construct(EntityManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }
    
    public function reference()
    {
        $reference = $this->objectManager->getRepository(CommandeRepository::class)->findOneBy(array('valider' => 1), array('id' => 'DESC'),1,1);
        if (!$reference)
            return 1;
        else 
            return $reference->getReference() +1;
    }
}