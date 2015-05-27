<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Manager;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Document;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DuplicatedDocumentException;

abstract class AbstractDocumentManager
{
    protected $documentName;
    protected $documentManager;

    /**
     * @param ManagerRegistry $documentManager
     */
    public function __construct(ManagerRegistry $documentManager)
    {
        $this->documentManager = $documentManager->getManager();
    }

    /**
     * @param array $documentData
     * @return Document
     */
    public function createDocument(array $documentData, array $checkIfExistsBy)
    {
        $document = $this->documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:'.$this->documentName)
            ->findOneBy($checkIfExistsBy);

        if (!empty($document)) {
            throw new DuplicatedDocumentException($this->documentName);
        }

        try {
            $document = $this->createDocumentFromRequest($documentData);
            $this->documentManager->persist($document);
            $this->documentManager->flush();
        } catch (\Exception $e) {
            return null;
        }

        return $document;
    }

    /**
     * @param Document $document
     * @param array $documentData
     * @return Document
     */
    public function updateDocument(Document $document, array $documentData)
    {
        try {
            $this->updateDocumentFromRequest($document, $documentData);
            $this->documentManager->persist($document);
            $this->documentManager->flush();
        } catch (\Exception $e) {
            return null;
        }

        return $document;
    }

    /**
     * @param Document $document
     */
    public function deleteDocument(Document $document)
    {
        $this->documentManager->remove($document);
        $this->documentManager->flush();
    }

    abstract protected function createDocumentFromRequest(array $documentData);

    abstract protected function updateDocumentFromRequest(Document $document, array $documentData);
}