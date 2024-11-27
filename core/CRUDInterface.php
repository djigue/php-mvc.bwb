<?php

namespace Core;

interface CRUDInterface
{
    /**
     * Récupère une entité par son ID.
     * 
     * @param int $id
     * @return object|null
     */
    public function retrieve(int $id): ?object;

    /**
     * Crée une nouvelle entité à partir des données fournies.
     * 
     * @param array $data
     * @return object|null
     */
    public function create(array $data): ?object;

    /**
     * Met à jour une entité existante par son ID.
     * 
     * @param int $id
     * @return bool
     */
    public function update(int $id,array $data): bool;

    /**
     * Supprime une entité par son ID.
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

}
