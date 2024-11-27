<?php

namespace Core;

interface RepositoryInterface extends CRUDInterface {

    /**
     * Récupère tutes les entités
     * 
     * 
     * @return array
     */
    public function getAll() : array; 

    /**
     * Récupère toutes les entités correspondant aux clauses WHERE et AND.
     * 
     * @param array $conditions
     * @return array
     */
    public function getAllBy(array $conditions): array;

}

